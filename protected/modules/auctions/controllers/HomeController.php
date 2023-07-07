<?php
/**
 * Home controller
 *
 * PUBLIC:                  PRIVATE:
 * ---------------          ---------------
 * __construct
 * indexAction
 *
 */

namespace Modules\Auctions\Controllers;

// Modules
use \Modules\Auctions\Components\AuctionsComponent;
use \Modules\Auctions\Models\Auctions;
use \Modules\Auctions\Models\AuctionImages;
use \Modules\Auctions\Models\AuctionTypes;
use \Modules\Auctions\Models\BidsHistory;
use \Modules\Banners\Models\Banners;

// Framework
use \A,
    \CAuth,
    \CLoader,
    \CArray;

// Application
use \AuctionType,
    \Bootstrap,
    \CConfig,
    \CController,
    \CLocale,
    \LocalTime,
    \Modules,
    \ModulesSettings,
    \Website;


class HomeController extends CController
{
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
	
		// Block access if the module is not installed
		if(!Modules::model()->isInstalled('auctions')){
			if(CAuth::isLoggedInAsAdmin()){
				$this->redirect($this->_backendPath.'modules/index');
			}else{
				$this->redirect(Website::getDefaultPage());
			}
		}

        $appendCode  = '';
        $prependCode = '';
        $symbol      = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if($symbolPlace == 'before'){
            $prependCode = $symbol;
        }else{
            $appendCode = $symbol;
        }

        $settings                       = Bootstrap::init()->getSettings();
        $this->_view->dateFormat        = $settings->date_format;
        $this->_view->timeFormat        = $settings->time_format;
        $this->_view->dateTimeFormat    = $settings->datetime_format;
        $this->_view->typeFormat        = $settings->number_format;
        $this->_view->pricePrependCode  = $prependCode;
        $this->_view->priceAppendCode   = $appendCode;
        $this->_view->categoriesList    = AuctionsComponent::categoriesList(true, true, false);
        $this->_view->auctionTypesList  = AuctionsComponent::auctionTypesList();
        $this->_view->baseUrl           = A::app()->getRequest()->getBaseUrl();
        $this->_view->actionMessage     = '';
        $this->_view->errorField        = '';

        A::app()->view->setTemplate('frontend');
        A::app()->view->setLayout('homepage');

        // Set frontend mode
        Website::setFrontend();
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {

        $auctionsTabs = array();
        $auctionTypeIds = array();

        // -------------------------------------------------------
        // BANNERS
        // -------------------------------------------------------
        $this->_view->banners = array();
        if(Modules::model()->isInstalled('banners') && Modules::model()->param('banners', 'is_active')){
            $viewerType = ModulesSettings::model()->param('banners', 'viewer_type');
            $showBanners = false;
            if($viewerType == 'all'){
                $showBanners = true;
            }elseif($viewerType == 'registered only' && CAuth::isLoggedIn()){
                $showBanners = true;
            }elseif($viewerType == 'visitors only' && !CAuth::isLoggedIn()){
                $showBanners = true;
            }

            if($showBanners){
                $this->_view->banners = Banners::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'));
            }
        }

        // -------------------------------------------------------
        // AUCTIONS
        // -------------------------------------------------------
        $newAuctions            = array();
        $closingSoon            = array();
        $noBids                 = array();
        $hotAuctions            = array();
        $allAuctions            = array();
        $auctionsIds            = array();
        $allAuctionImages       = array();
        $auctionImages          = array();
        $callTimer              = '';
        $auctionsPerPage        = 10;
        $newAuctionsCountDays   = (int)ModulesSettings::model()->param('auctions', 'new_auctions_count_days');
        $newAuctionsCountDays   = '-'.$newAuctionsCountDays.' days';
        $closingSoonCountDays   = (int)ModulesSettings::model()->param('auctions', 'closing_soon_count_days');
        $closingSoonCountDays   = '+'.$closingSoonCountDays.' days';
        $noBidsAuctionsIds      = '';
        if(Modules::model()->isInstalled('auctions')){
            $tableNameBidsHistory = CConfig::get('db.prefix').BidsHistory::model()->getTableName();
            $bidsHistory = BidsHistory::model()->count(array('condition'=>'', 'select'=>$tableNameBidsHistory.'.auction_id', 'groupBy'=>'auction_id', 'allRows'=>true));
            foreach($bidsHistory as $key=>$bidHistory){
                $noBidsAuctionsIds .= $bidHistory['auction_id'];
                if(isset($bidsHistory[$key+1])) {$noBidsAuctionsIds .= ',';}
            }

            $tableNameAuctions = CConfig::get('db.prefix').Auctions::model()->getTableName();
            $newAuctions    = Auctions::model()->findAll(array('condition'=>$tableNameAuctions.'.date_from >= "'.CLocale::date('Y-m-d H:i:s', strtotime($newAuctionsCountDays), true).'" AND '.$tableNameAuctions.'.date_from <= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND '.$tableNameAuctions.'.date_to >= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND status = 1', 'orderBy'=>$tableNameAuctions.'.date_from DESC'));
            $closingSoon    = Auctions::model()->findAll(array('condition'=>$tableNameAuctions.'.date_to <= "'.CLocale::date('Y-m-d H:i:s', strtotime($closingSoonCountDays), true).'" AND '.$tableNameAuctions.'.date_from <= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND '.$tableNameAuctions.'.date_to >= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND status = 1', 'orderBy'=>$tableNameAuctions.'.date_to ASC'));
            $noBids         = Auctions::model()->findAll(array('condition'=>$tableNameAuctions.'.id NOT IN('.(!empty($noBidsAuctionsIds) ? $noBidsAuctionsIds : '""').') AND '.$tableNameAuctions.'.date_from <= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND '.$tableNameAuctions.'.date_to >= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND status = 1', 'orderBy'=>$tableNameAuctions.'.date_from DESC'));
            $hotAuctions    = Auctions::model()->findAll(array('condition'=>$tableNameAuctions.'.date_from <= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND '.$tableNameAuctions.'.date_to >= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND status = 1', 'orderBy'=>$tableNameAuctions.'.hits DESC', 'limit'=>'0, 20'));
            $allAuctions    = Auctions::model()->findAll(array('condition'=>$tableNameAuctions.'.date_from <= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND '.$tableNameAuctions.'.date_to >= "'.LocalTime::currentDateTime('Y-m-d H:i:s').'" AND status = 1', 'orderBy'=>$tableNameAuctions.'.hits DESC', 'limit'=>'0, '.$auctionsPerPage));

            //Get Auctions IDs in $newAuctions
            if(!empty($newAuctions) && is_array($newAuctions)){
                foreach($newAuctions as $newAuction){
                    if(!in_array($newAuction['id'], $auctionsIds)){
                        $auctionsIds[] = $newAuction['id'];
                    }
                    if(!in_array($newAuction['auction_type_id'], $auctionTypeIds)){
                        $auctionTypeIds[] = $newAuction['auction_type_id'];
                    }
                }
            }
            //Get Auctions IDs in $closingSoon
            if(!empty($closingSoon) && is_array($closingSoon)){
                foreach($closingSoon as $closing){
                    if(!in_array($closing['id'], $auctionsIds)){
                        $auctionsIds[] = $closing['id'];
                    }
                    if(!in_array($closing['auction_type_id'], $auctionTypeIds)){
                        $auctionTypeIds[] = $closing['auction_type_id'];
                    }
                }
            }

            //Get Auctions IDs in $noBids
            if(!empty($noBids) && is_array($noBids)){
                foreach($noBids as $noBid){
                    if(!in_array($noBid['id'], $auctionsIds)){
                        $auctionsIds[] = $noBid['id'];
                    }
                    if(!in_array($noBid['auction_type_id'], $auctionTypeIds)){
                        $auctionTypeIds[] = $noBid['auction_type_id'];
                    }
                }
            }
            //Get Auctions IDs in $hotAuctions
            if(!empty($hotAuctions) && is_array($hotAuctions)){
                foreach($hotAuctions as $hotAuction){
                    if(!in_array($hotAuction['id'], $auctionsIds)){
                        $auctionsIds[] = $hotAuction['id'];
                    }
                    if(!in_array($hotAuction['auction_type_id'], $auctionTypeIds)){
                        $auctionTypeIds[] = $hotAuction['auction_type_id'];
                    }
                }
            }

            //Get Auctions IDs in $allAuctions
            if(!empty($allAuctions) && is_array($allAuctions)){
                foreach($allAuctions as $auction){
                    if(!in_array($auction['id'], $auctionsIds)){
                        $auctionsIds[] = $auction['id'];
                    }
                    if(!in_array($auction['auction_type_id'], $auctionTypeIds)){
                        $auctionTypeIds[] = $auction['auction_type_id'];
                    }

                    // Set id element and end date for Timer
                    $idElement = 'timer-all-auction-'.$auction['id'];
                    $dateTo = strtotime($auction['date_to']) * 1000;
                    $callTimer .= 'auctions_Timer("'.$idElement.'", '.$dateTo.');';
                }
            }

            if(!empty($auctionsIds) && is_array($auctionsIds)){
                $auctionImages = AuctionsComponent::getAuctionImages($auctionsIds);
            }
        }


        // Auction Tabs Content
        if(!empty($newAuctions)){
            $auctionsTabs[] = array(
                'id' => 'new',
                'tab_name' => A::t('auctions', 'New Arrivals'),
                'tab_content' => $newAuctions,
            );
        }
        if(!empty($closingSoon)){
            $auctionsTabs[] = array(
                'id' => 'closing_soon',
                'tab_name' => A::t('auctions', 'Closing Soon'),
                'tab_content' => $closingSoon,
            );
        }
        if(!empty($hotAuctions)){
            $auctionsTabs[] = array(
                'id' => 'hot_auctions',
                'tab_name' => A::t('auctions', 'Hot Auctions'),
                'tab_content' => $hotAuctions,
            );
        }
        if(!empty($noBids)){
            $auctionsTabs[] = array(
                'id' => 'no_bids',
                'tab_name' => A::t('auctions', 'No Bids'),
                'tab_content' => $noBids,
            );
        }

        //Register Script Files For libraries/classes
        if(!empty($auctionTypeIds) && is_array($auctionTypeIds)){
            AuctionsComponent::registerAuctionTypeScriptFiles($auctionTypeIds);
        }

        // Set id element and end date for Timer
        if(!empty($auctionsTabs) && is_array($auctionsTabs)){
            foreach($auctionsTabs as $key=>$activeAuctionsTab){
                foreach($activeAuctionsTab['tab_content'] as $auction){
                    $idElement = 'timer-auction-'.$activeAuctionsTab['id'].'-'.$auction['id'];
                    $dateTo = strtotime($auction['date_to']) * 1000;
                    $callTimer .= 'auctions_Timer("'.$idElement.'", '.$dateTo.');';
                }
            }
        }

        $this->_view->allAuctions           = $allAuctions;
        $this->_view->activeAuctionsTabs    = $auctionsTabs;
        $this->_view->categoriesList        = AuctionsComponent::categoriesList(false, false, false);
        $this->_view->drawTimerScript       = AuctionsComponent::drawTimerScript();
        $this->_view->callDrawTimerScript   = AuctionsComponent::callDrawTimerScript($callTimer);
        $this->_view->auctionImages         = $auctionImages;
        $this->_view->render('home/index');
    }
}

