<?php
/**
 * Bids History Controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct
 * indexAction
 * manageAction
 * addAction
 * editAction
 * deleteAction
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\Auctions,
	\Modules\Auctions\Models\Members,
	\Modules\Auctions\Models\BidsHistory;

// Framework
use \Modules,
	\ModulesSettings,
	\CAuth,
	\CConfig,
	\CController,
	\CDatabase,
	\Website,
	\CWidget;

// Application
use \Bootstrap,
	\A;

class BidsHistoryController extends CController
{
	
	private $_backendPath = '';
	
	/**
	 * Class default constructor
	 */
	public function __construct()
	{

		parent::__construct();
		
		// Get BackEnd path
		$this->_backendPath = Website::getBackendPath();

		// Block access if the module is not installed
		if(!Modules::model()->isInstalled('auctions')){
			if(CAuth::isLoggedInAsAdmin()){
				$this->redirect($this->_backendPath.'modules/index');
			}else{
				$this->redirect(Website::getDefaultPage());
			}
		}

		if(CAuth::isLoggedInAsAdmin()){
			// set meta tags according to active auctions
			Website::setMetaTags(array('title'=>A::t('auctions', 'Bids History Management')));
			// set backend mode
			Website::setBackend();

			$this->_view->actionMessage = '';
			$this->_view->errorField = '';
			$this->_view->backendPath = $this->_backendPath;
			
			$this->_view->tabs = AuctionsComponent::prepareTab('auctions');
		}

		$settings                       = Bootstrap::init()->getSettings();
		$this->_view->dateFormat        = $settings->date_format;
		$this->_view->timeFormat        = $settings->time_format;
		$this->_view->dateTimeFormat    = $settings->datetime_format;
	}

	/**
	 * Controller default action handler
	 */
	public function indexAction()
	{
		$this->redirect('bidsHistory/manage');
	}

	/**
	 * Manage action handler
     * @param int $auctionId
	 */
	public function manageAction($auctionId = 0)
	{
		Website::prepareBackendAction('manage', 'auction', 'bidsHistory/manage');
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

		$alert = A::app()->getSession()->getFlash('alert');
		$alertType = A::app()->getSession()->getFlash('alertType');

		if(!empty($alert)){
			$this->_view->actionMessage = CWidget::create(
				'CMessage', array($alertType, $alert, array('button'=>true))
			);
		}

        $paramSubTabs = array(
            'parentTab' => 'bids_history',
            'activeTab' => 'auctions',
            'additionText' => $auction->auction_name.' | '.A::t('auctions', 'Bids History'),
        );

        $this->_view->subTabs       = AuctionsComponent::prepareSubTab($paramSubTabs);
		$this->_view->auctionName = $auction->auction_name;
		$this->_view->auctionId = $auction->id;
		$this->_view->render('bidsHistory/backend/manage');
	}

	/**
	 * Edit Bids History action handler
	 * @param int $auctionId
	 * @param int $id
	 * @return void
	 */
	public function editAction($auctionId = 0, $id = 0)
	{
		Website::prepareBackendAction('edit', 'auction', 'bidsHistory/manage/auctionId/'.$auctionId);

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $findParams = array(
            'auction_id' => $auction->id,
        );
        $bidHistory = AuctionsComponent::checkRecordAccess($id, 'BidsHistory', true, 'auctions/manage', $findParams);

        $paramSubTabs = array(
            'parentTab' => 'add_or_edit_bid_history',
            'activeTab' => 'bid_history',
            'additionText' => A::t('auctions', 'Edit Bid History'),
            'id' => $auction->id,
            'name' => $auction->auction_name.' | '.A::t('auctions', 'Bids History'),
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->id = $id;
        $this->_view->auctionId = $auction->id;
        $this->_view->auctionName = $auction->auction_name;
		$this->_view->bidHistory = $bidHistory;
		$this->_view->render('bidsHistory/backend/edit');
	}

	/**
	 * Delete action handler
	 * @param int $auctionId
	 * @param int $id
     * @param int $page
     * @return void
	 */
	public function deleteAction($auctionId = 0, $id = 0, $page = 1)
	{
		Website::prepareBackendAction('delete', 'auction', 'bidsHistory/manage');

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $bidHistory = AuctionsComponent::checkRecordAccess($id, 'BidsHistory', true, 'auctions/manage', array('auction_id' => $auction->id));

		$alert = '';
		$alertType = '';

        // Find the last bid
        $lastBid = BidsHistory::model()->getLastBid($auction->id);

		if($bidHistory->delete()){
			if($bidHistory->getError()){
				$alert = $bidHistory->getErrorMessage();
				$alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
				$alertType = 'warning';
			}else{
			    // We deleted the last bid and have to update current bid
                if (!empty($lastBid) && $lastBid->id === $id) {
                    // Find new last bid
                    $newLastBid = BidsHistory::model()->getLastBid($auction->id);
                    $bidValue = (!empty($newLastBid)) ? $newLastBid->size_bid : $auction->start_price;
                    $auction->current_bid = $bidValue;
                    $auction->save();
                }
				$alert = A::t('app', 'Delete Success Message');
				$alertType = 'success';
			}
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
			}else{
				$alert = $bidHistory->getError() ? $bidHistory->getErrorMessage() : A::t('app', 'Delete Error Message');
				$alertType = 'error';
			}
		}

		if(!empty($alert)){
			A::app()->getSession()->setFlash('alert', $alert);
			A::app()->getSession()->setFlash('alertType', $alertType);
		}

		$this->redirect('bidsHistory/manage/auctionId/'.$auctionId.(!empty($page) ? '?page='.(int)$page : 1));
	}


    /*   FRONTEND ACTIONS   */

    /**
     * Member Bids History action handler
     * @param int $memberId
     * @return void
     */
    public function myBidsHistoryAction()
    {

        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'My Bids History')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';
        $memberId = CAuth::getLoggedRoleId();

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if(!empty($alert)){
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->memberId = $member->id;
        $this->_view->render('bidsHistory/myBidsHistory');
    }
}
