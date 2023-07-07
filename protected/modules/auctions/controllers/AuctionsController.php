<?php
/**
 * Auctions controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct                          _getConditionForAuctions
 * indexAction                          _outputAjax
 * manageAction                         _outputJson
 * addAction                            _getAllWatchlistAuctions
 * editAction                           _getReviews
 * changeStatusAction
 * deleteAction
 * myAuctionsAction
 * myWatchlistAction
 * removeWatchlistAction
 * categoriesAction
 * viewAction
 * auctionMembersAction
 * ajaxAddWatchlistAction
 * ajaxAddBidAction
 * ajaxAutoUpdateAuctionAction
 *
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
    \Modules\Auctions\Models\Auctions,
    \Modules\Auctions\Models\BidsHistory,
    \Modules\Auctions\Models\Categories,
    \Modules\Auctions\Models\AuctionImages,
    \Modules\Auctions\Models\Members,
    \Modules\Auctions\Models\Reviews,
    \Modules\Auctions\Models\Shipments,
    \Modules\Auctions\Models\Watchlist;

// Framework
use \Modules,
    \ModulesSettings,
    \CAuth,
    \CArray,
    \CConfig,
    \CController,
    \CCurrency,
    \CDatabase,
    \CHtml,
    \CLoader,
    \CLocale,
    \CString,
    \Website,
    \CWidget;

// Application
use \A,
    \AuctionType,
    \Bootstrap,
    \DateTime,
    \LocalTime;

class AuctionsController extends CController
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
        if (!Modules::model()->isInstalled('auctions')) {
            if (CAuth::isLoggedInAsAdmin()) {
                $this->redirect($this->_backendPath . 'modules/index');
            } else {
                $this->redirect(Website::getDefaultPage());
            }
        }

        if (CAuth::isLoggedInAsAdmin()) {
            // set meta tags according to active auctions
            Website::setMetaTags(array('title' => A::t('auctions', 'Auctions Management')));
            // set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AuctionsComponent::prepareTab('auctions');
            $this->_view->backendPath = $this->_backendPath;
        }

        $appendCode = '';
        $prependCode = '';
        $symbol = A::app()->getCurrency('symbol');
        $symbolPlace = A::app()->getCurrency('symbol_place');

        if ($symbolPlace == 'before') {
            $prependCode = $symbol;
        } else {
            $appendCode = $symbol;
        }


        $status = array(
            '' => '',
            '0' => A::t('auctions', 'Inactive'),
            '1' => A::t('app', 'Active'),
            '2' => A::t('auctions', 'Suspended'),
            '3' => A::t('auctions', 'Won'),
            '4' => A::t('auctions', 'Closed'),
        );
        $statusLabel = array(
            '' => '',
            '0' => '<span class="label-gray label-square">' . A::t('auctions', 'Inactive') . '</span>',
            '1' => '<span class="label-green label-square">' . A::t('app', 'Active') . '</span>',
            '2' => '<span class="label-yellow label-square">' . A::t('auctions', 'Suspended') . '</span>',
            '3' => '<span class="label-blue label-square">' . A::t('auctions', 'Won') . '</span>',
            '4' => '<span class="label-red label-square">' . A::t('auctions', 'Closed') . '</span>',
        );

        $paidStatus = array(
            '0' => A::t('auctions', 'Not Approved'),
            '1' => A::t('auctions', 'Paid'),
        );

        $paidStatusLabel = array(
            '0' => '<span class="label-red label-square">' . A::t('auctions', 'Not Approved') . '</span>',
            '1' => '<span class="label-green label-square">' . A::t('auctions', 'Paid') . '</span>',
        );

        $shippingStatus = array(
            '0' => A::t('auctions', 'Pending'),
            '1' => A::t('auctions', 'Shipped'),
            '2' => A::t('auctions', 'Received'),
        );

        $shippingStatusLabel = array(
            '0' => '<span class="label-gray label-square">' . A::t('auctions', 'Pending') . '</span>',
            '1' => '<span class="label-yellow label-square">' . A::t('auctions', 'Shipped') . '</span>',
            '2' => '<span class="label-green label-square">' . A::t('auctions', 'Received') . '</span>',
        );

        $settings = Bootstrap::init()->getSettings();
        $this->_view->dateFormat = $settings->date_format;
        $this->_view->timeFormat = $settings->time_format;
        $this->_view->dateTimeFormat = $settings->datetime_format;
        $this->_view->typeFormat = $settings->number_format;
        $this->_view->pricePrependCode = $prependCode;
        $this->_view->priceAppendCode = $appendCode;
        $this->_view->categoriesList = AuctionsComponent::categoriesList(true, true, false);
        $this->_view->auctionTypesList = AuctionsComponent::auctionTypesList();
        $this->_view->status = $status;
        $this->_view->statusLabel = $statusLabel;
        $this->_view->paidStatus = $paidStatus;
        $this->_view->paidStatusLabel = $paidStatusLabel;
        $this->_view->shippingStatus = $shippingStatus;
        $this->_view->shippingStatusLabel = $shippingStatusLabel;
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('auctions/manage');
    }

    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'auction', 'auctions/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $this->_view->categoriesListManage = AuctionsComponent::categoriesList(false, false, false);
        $this->_view->render('auctions/backend/manage');
    }

    /**
     * Add new action handler
     * @return void
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'auction', 'auctions/manage');

        $this->_view->render('auctions/backend/add');
    }

    /**
     * Edit auctions action handler
     * @param int $id
     * @return void
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'auction', 'auctions/manage');
        $auction = AuctionsComponent::checkRecordAccess($id, 'Auctions', true, 'auctions/manage');

        // 3 - won, 4 - closed
        if (in_array($auction->status, array(3, 4)) && $auction->paid_status) {
            $parentTab = 'shipment_active';
            $shipmentTableName = CConfig::get('db.prefix') . Shipments::model()->getTableName();
            $shipment = Shipments::model()->find(array('condition' => $shipmentTableName . '.auction_id = :auction_id', 'orderBy' => $shipmentTableName . '.created_at DESC'), array(':auction_id' => $auction->id));
            if ($shipment) {
                $this->_view->drawStepShipment = AuctionsComponent::drawStepShipment($shipment->id);
            }
        } else {
            $parentTab = 'edit_auction';
        }
        $paramSubTabs = array(
            'parentTab' => $parentTab,
            'activeTab' => 'auctions',
            'additionText' => A::t('auctions', 'Edit Auction'),
            'id' => $auction->id,
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->id = $id;
        $this->_view->auction = $auction;
        $this->_view->render('auctions/backend/edit');
    }

    /**
     * Change status handler action
     * @param int $id
     * @param int $page
     * @return void
     */
    public function changeStatusAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('edit', 'auction', 'auctions/manage');
        $auction = AuctionsComponent::checkRecordAccess($id, 'Auctions', true, 'auctions/manage');
        if (!empty($auction)) {
            if (Auctions::model()->updateByPk($auction->id, array('is_active' => ($auction->is_active ? 0 : 1)))) {
                A::app()->getSession()->setFlash('alert', A::t('app', 'Status has been successfully changed!'));
                A::app()->getSession()->setFlash('alertType', 'success');
            } else {
                A::app()->getSession()->setFlash('alert', ((APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error')));
                A::app()->getSession()->setFlash('alertType', ((APPHP_MODE == 'demo') ? 'warning' : 'error'));
            }
        }

        $this->redirect('auctions/manage' . (!empty($page) ? '?page=' . (int)$page : 1));
    }

    /**
     * Delete action handler
     * @param int $id
     * @param int $page
     * @return void
     */
    public function deleteAction($id = 0, $page = 1)
    {
        Website::prepareBackendAction('delete', 'auction', 'auctions/manage');
        $auction = AuctionsComponent::checkRecordAccess($id, 'Auctions', true, 'auctions/manage');

        $alert = '';
        $alertType = '';

        if ($auction->delete()) {
            if ($auction->getError()) {
                $alert = $auction->getErrorMessage();
                $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
                $alertType = 'warning';
            } else {
                $alert = A::t('app', 'Delete Success Message');
                $alertType = 'success';
            }
        } else {
            if (APPHP_MODE == 'demo') {
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            } else {
                $alert = $auction->getError() ? $auction->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('auctions/manage' . (!empty($page) ? '?page=' . (int)$page : 1));
    }


    /*   FRONTEND ACTIONS   */

    /**
     * My Auctions action handler
     * @param string $statusTab ('active' - default or 'won' or 'loose')
     * @return void
     */
    public function myAuctionsAction($statusTab = 'active')
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Auctions')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';
        $memberId = CAuth::getLoggedRoleId();

        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $this->_view->categoriesListManage = AuctionsComponent::categoriesList(false, false, false);
        $this->_view->condition = $this->_getConditionForAuctions($member->id, $statusTab);
        $this->_view->statusTab = $statusTab;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->memberId = $member->id;
        A::app()->view->setLayout('no_columns');
        $this->_view->render('auctions/myAuctions');
    }

    /**
     * My Auctions action handler
     * @param string $statusTab ('active' - default or 'not_started' or 'closed')
     * @return void
     */
    public function myWatchlistAction($statusTab = 'active')
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Watchlist')));
        // set frontend settings
        Website::setFrontend();

        $alert = '';
        $alertType = '';
        $actionMessage = '';
        $callTimer = '';
        $auctionTypeIds = array();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        $watchlistAuctions = $this->_getAllWatchlistAuctions($statusTab);

        if (!empty($watchlistAuctions) && is_array($watchlistAuctions)) {
            foreach ($watchlistAuctions as $auction) {
                if (!in_array($auction['auction_type_id'], $auctionTypeIds)) {
                    $auctionTypeIds[] = $auction['auction_type_id'];
                }

                // Set id element and end date for Timer
                $idElement = 'timer-watchlist-auction-' . $auction['id'];
                $currentDate = CLocale::date('Y-m-d H:i:s');
                if ($auction['date_from'] > $currentDate) {
                    $curDate = new DateTime($currentDate);
                    $diffDate = new DateTime($auction['date_from']);
                    $difference = $curDate->diff($diffDate);

                    $dateTo = strtotime('+' . $difference->d . ' days ' . $difference->h . ' hours ' . $difference->i . ' minutes ' . $difference->s . ' seconds') * 1000;
                } else {
                    $dateTo = strtotime($auction['date_to']) * 1000;
                }
                $callTimer .= 'auctions_Timer("' . $idElement . '", ' . $dateTo . ');';


                //Register Script Files For libraries/classes
                if (!empty($auctionTypeIds) && is_array($auctionTypeIds)) {
                    AuctionsComponent::registerAuctionTypeScriptFiles($auctionTypeIds);
                }
            }
        } else {
            $alertType = 'info';
            $alert = A::t('auctions', 'Watchlist is empty!');
        }

        if (!empty($alertType)) {
            $actionMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->watchlistAuctions = $watchlistAuctions;
        $this->_view->statusTab = $statusTab;
        $this->_view->drawTimerScript = AuctionsComponent::drawTimerScript();
        $this->_view->callDrawTimerScript = AuctionsComponent::callDrawTimerScript($callTimer);
        $this->_view->render('auctions/myWatchlist');
    }

    /**
     * My Auctions action handler
     * @param int $id
     * @param string $statusTab ('active' - default or 'not_started' or 'closed')
     * @return void
     */
    public function removeWatchlistAction($id = 0, $statusTab = 'active')
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');

        $memberId = CAuth::getLoggedRoleId();
        $watchlistAuction = Watchlist::model()->find('auction_id = :auction_id AND member_id = :member_id', array(':auction_id' => $id, ':member_id' => $memberId));

        if (isset($watchlistAuction)) {
            if ($watchlistAuction->delete()) {
                A::app()->getSession()->setFlash('alert', A::t('auctions', 'The product removed from the watchlist.'));
                A::app()->getSession()->setFlash('alertType', 'success');
            } else {
                A::app()->getSession()->setFlash('alert', A::t('auctions', 'An error occurred while removing the auction from the watchlist.'));
                A::app()->getSession()->setFlash('alertType', 'error');
            }
        } else {
            A::app()->getSession()->setFlash('alert', A::t('auctions', 'The auction not found! Please try again later.'));
            A::app()->getSession()->setFlash('alertType', 'error');
        }

        $this->redirect('auctions/myWatchlist/' . (!empty($statusTab) ? 'status/' . $statusTab : ''));
    }

    /**
     * view Auction action handler
     * @param int $categoryId
     * @return void
     */
    public function categoriesAction($categoryId = 0)
    {
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'Auction Categories')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';
        $condition = '';
        $callTimer = '';
        $auctionsIds = array();
        $auctionImages = array();
        $auctionTypeIds = array();
        $auctionsPerPage = (int)ModulesSettings::model()->param('auctions', 'auctions_per_page');

        $parentCategories = AuctionsComponent::getParentCategories($categoryId);

        $cRequest = A::app()->getRequest();
        $search = (string)$cRequest->get('search');

        $page = A::app()->getRequest()->getQuery('page', 'integer', 1);

        $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
        $condition .= $tableNameAuctions . '.date_from <= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND ' . $tableNameAuctions . '.date_to >= "' . LocalTime::currentDateTime('Y-m-d H:i:s') . '" AND status = 1';

        if (!empty($search)) {
            $tableNameAuctionTranslations = CConfig::get('db.prefix') . Auctions::model()->getTableTranslationsName();
            $condition .= ' AND ' . $tableNameAuctionTranslations . '.name LIKE "%' . $search . '%"';
            $this->_view->search = $search;
        } elseif (!empty($categoryId)) {
            $tableNameCategories = CConfig::get('db.prefix') . Categories::model()->getTableName();
            $categories = Categories::model()->findAll($tableNameCategories . '.id = :category_id OR ' . $tableNameCategories . '.parent_id = :category_id', array(':category_id' => $categoryId));
            if (!empty($categories) && is_array($categories)) {
                $childCategoryIds = array($categoryId);
                foreach ($categories as $category) {
                    $childCategoryIds[] = $category['id'];
                }
                if (!empty($childCategoryIds) && is_array($childCategoryIds)) {
                    $condition .= ' AND ' . $tableNameAuctions . '.category_id IN(' . implode(',', $childCategoryIds) . ')';
                }
            } else {
                $condition .= ' AND ' . $tableNameAuctions . '.category_id = ' . $categoryId;
            }
        }
        $auctions = Auctions::model()->findAll(array('condition' => $condition, 'orderBy' => $tableNameAuctions . '.hits DESC', 'limit' => ($auctionsPerPage * ($page - 1)) . ', ' . $auctionsPerPage));
        $totalRecords = Auctions::model()->count($condition);

        if (!empty($auctions) && is_array($auctions)) {
            foreach ($auctions as $auction) {
                if (!in_array($auction['id'], $auctionsIds)) {
                    $auctionsIds[] = $auction['id'];
                }
                if (!in_array($auction['auction_type_id'], $auctionTypeIds)) {
                    $auctionTypeIds[] = $auction['auction_type_id'];
                }

                // Set id element and end date for Timer
                $idElement = 'timer-all-auction-' . $auction['id'];
                $dateTo = strtotime($auction['date_to']) * 1000;
                $callTimer .= 'auctions_Timer("' . $idElement . '", ' . $dateTo . ');';
            }
            if (!empty($auctionsIds) && is_array($auctionsIds)) {
                //Find Auction Images
                $auctionImages = AuctionsComponent::getAuctionImages($auctionsIds);
            }

            //Register Script Files For libraries/classes
            if (!empty($auctionTypeIds) && is_array($auctionTypeIds)) {
                AuctionsComponent::registerAuctionTypeScriptFiles($auctionTypeIds);
            }
        }

        A::app()->view->setLayout('left_sidebar');
        $this->_view->categoryId = $categoryId;
        $this->_view->auctions = $auctions;
        $this->_view->auctionImages = $auctionImages;
        $this->_view->currentPage = $page;
        $this->_view->auctionsPerPage = $auctionsPerPage;
        $this->_view->totalRecords = $totalRecords;
        $this->_view->parentCategories = $parentCategories;
        $this->_view->categoriesList = AuctionsComponent::categoriesList(false, true, false);
        $this->_view->drawTimerScript = AuctionsComponent::drawTimerScript();
        $this->_view->callDrawTimerScript = AuctionsComponent::callDrawTimerScript($callTimer);
        $this->_view->render('auctions/categories');
    }

    /**
     * view Auction action handler
     * @param int $id
     * @return void
     */
    public function viewAction($id = 0)
    {
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Auctions')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage = '';
        $winnerId      = '';
        $winner        = '';
        $auctionImages = array();

        $auction = AuctionsComponent::checkRecordAccess($id, 'Auctions', true, 'home/index');
        $allAuctionImages = AuctionImages::model()->findAll(array('condition' => 'auction_id = :auction_id'), array(':auction_id' => $auction->id));
        $bidsHistoryInfo = AuctionsComponent::getBidsHistoryInfo($auction->id);

        $parentCategories = AuctionsComponent::getParentCategories($auction->category_id);

        if (!empty($allAuctionImages)) {
            foreach ($allAuctionImages as $allAuctionImage) {
                $auctionImages[$allAuctionImage['id']]['title'] = $allAuctionImage['title'];
                $auctionImages[$allAuctionImage['id']]['image_file'] = $allAuctionImage['image_file'];
            }
        }

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        // Counter views for the auction
        $hitsTimeCreated = A::app()->getSession()->get('auction_hits_' . $id);
        if ($hitsTimeCreated) {
            if ($hitsTimeCreated + 600 < time()) {
                $auction->hits++;
                // Save with forcing update on demo
                $auction->save(true);
                A::app()->getSession()->set('auction_hits_' . $id, time());
            }
        } else {
            $auction->hits++;
            // Save with forcing update on demo
            $auction->save(true);
            A::app()->getSession()->set('auction_hits_' . $id, time());
        }

        if ($auction->status !== 1 && $auction->winner_member_id) {
            $member = Members::model()->findByPk($auction->winner_member_id);
            if ($member) {
                $winnerId = $auction->winner_member_id;
                $winner = $member->first_name.' '.(CString::substr($member->last_name, 1, '', false).'.');
            }
        } elseif ($auction->status == 1 && !empty($bidsHistoryInfo['winner_id'])) {
            $winnerId = $bidsHistoryInfo['winner_id'];
            $winner = $bidsHistoryInfo['winner'];
        } else {
            $winnerId = 0;
        }

        CLoader::library('libauction/AuctionType.php');
        $auctionType = AuctionType::init($auction->auction_type_id);
        $nextStep = $auction->current_bid + $auction->size_bid;
        $auctionForm = $auctionType->drawAuctionForm(array(
            'auction_id' => $auction->id,
            'auction_type_id' => $auction->auction_type_id,
            'bids' => !empty($bidsHistoryInfo['count_bids']) ? $bidsHistoryInfo['count_bids'] : 0,
            'bidders' => !empty($bidsHistoryInfo['count_bidders']) ? $bidsHistoryInfo['count_bidders'] : 0,
            'hits' => $auction->hits,
            'start_price' => $auction->start_price,
            'date_from' => $auction->date_from,
            'date_to' => $auction->date_to,
            'current_bid' => $auction->current_bid,
            'next_step' => $nextStep,
            'buy_now_price' => $auction->buy_now_price,
            'winner' => $winner,
            'winner_id' => $winnerId,
            'status' => $auction->status,
        ));


        // Set id element and end date for Timer
        $idElement = 'timer-auction-' . $auction->id;
        $currentDate = CLocale::date('Y-m-d H:i:s');
        if ($auction->date_from > $currentDate) {
            $curDate = new DateTime($currentDate);
            $diffDate = new DateTime($auction->date_from);
            $difference = $curDate->diff($diffDate);

            $dateTo = strtotime('+' . $difference->d . ' days ' . $difference->h . ' hours ' . $difference->i . ' minutes ' . $difference->s . ' seconds') * 1000;
        } else {
            $dateTo = strtotime($auction->date_to) * 1000;
        }
        $callTimer = 'auctions_Timer("' . $idElement . '", ' . $dateTo . ');';


        $this->_view->reviews = $this->_getReviews($auction->id);
        $this->_view->categoriesListManage = AuctionsComponent::categoriesList(false, false, false);
        $this->_view->auctionForm = $auctionForm;
        $this->_view->auction = $auction;
        $this->_view->auctionImages = $auctionImages;
        $this->_view->bidsHistory = !empty($bidsHistoryInfo['bids_history']) ? $bidsHistoryInfo['bids_history'] : array();
        $this->_view->actionMessage = $actionMessage;
        $this->_view->parentCategories = $parentCategories;
        $this->_view->drawTimerScript = AuctionsComponent::drawTimerScript();
        $this->_view->callDrawTimerScript = AuctionsComponent::callDrawTimerScript($callTimer);
        $this->_view->similarAuctions = AuctionsComponent::getSimilarAuction($auction->id, $auction->category_id);
        A::app()->view->setLayout('no_columns');
        $this->_view->render('auctions/view');
    }

    /**
     * view Auction action handler
     * @param int $auctionId
     * @return void
     */
    public function auctionMembersAction($auctionId = 0)
    {
        Website::prepareBackendAction('manage', 'auction', 'auctions/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $memberIds = array();


        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

        $bidsHistoryTableName = CConfig::get('db.prefix') . BidsHistory::model()->getTableName();
        $bidsHistory = BidsHistory::model()->findAll($bidsHistoryTableName . '.auction_id = :auction_id', array(':auction_id' => $auction->id));

        if (!empty($bidsHistory) && is_array($bidsHistory)) {
            foreach ($bidsHistory as $bidHistory) {
                $memberIds[] = $bidHistory['member_id'];
            }
        }

        $paramSubTabs = array(
            'parentTab' => 'auction_members',
            'activeTab' => 'auction',
            'additionText' => A::t('auctions', 'Auction Members'),
            'id' => $auction->id,
            'name' => $auction->auction_name,
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->memberIds = $memberIds;
        $this->_view->categoriesListManage = AuctionsComponent::categoriesList(false, false, false);
        $this->_view->render('auctions/backend/auctionMembers');
    }

    /**
     * Add auction in the watchlist
     * @return void
     */
    public function ajaxAddWatchlistAction()
    {
        $cRequest = A::app()->getRequest();
        if (!$cRequest->isAjaxRequest()) {
            $this->redirect(Website::getDefaultPage());
        }

        $status = false;
        $message = '';
        $operation = 'add';
        $textButton = A::te('auctions', 'Add to Watchlist');
        $result = array();

        $auctionId = !empty($cRequest->getPost('auctionId')) ? (int)$cRequest->getPost('auctionId') : 0;
        $memberId = CAuth::getLoggedRoleId();

        // Check the member is logged in.
        if (empty($memberId)) {
            $status = false;
            $message = A::t('auctions', 'To the saved product in the watchlist you must be logged in!');
        } elseif (empty($auctionId)) {
            // Check auctionId is not empty.
            $status = false;
            $message = A::t('auctions', 'The auction not found! Please try again later.');
        } else {
            // Check the auction exists in the database
            $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', false);
            if ($auction) {
                $checkAuctionInWatchlist = AuctionsComponent::checkAuctionInWatchlist($auction->id);
                if ($checkAuctionInWatchlist) {
                    if ( $checkAuctionInWatchlist->delete() ) {
                        $status = true;
                        $textButton = A::te('auctions', 'Add to Watchlist');
                        $message = A::te('auctions', 'The product removed from the watchlist.');
                    } else {
                        $status = false;
                        $textButton = A::te('auctions', 'Watching');
                        $message = ($checkAuctionInWatchlist->getError() != '') ? CHtml::encode($checkAuctionInWatchlist->getError()) : A::te('auctions', 'An error occurred while deleting auction from the your watchlist! Please try again later.');
                    }
                    $operation = 'remove';
                } else {
                    $watchlist = new Watchlist();
                    $watchlist->member_id = $memberId;
                    $watchlist->auction_id = (int)$auction->id;
                    if ($watchlist->save()) {
                        $status = true;
                        $textButton = A::te('auctions', 'Watching');
                        $message = A::te('auctions', 'The item saved in the watchlist.');
                    } else {
                        $status = false;
                        $textButton = A::te('auctions', 'Add to Watchlist');
                        $message = ($watchlist->getError() != '') ? CHtml::encode($watchlist->getError()) : A::te('auctions', 'An error occurred while adding auction to your watchlist! Please try again later.');
                    }
                    $operation = 'add';
                }
            } else {
                $status = false;
                $message = A::t('auctions', 'The auction not found! Please try again later.');
            }
        }

        $result[] = '"status": "' . ($status ? '1' : '0') . '"';
        $result[] = '"message": "' . $message . '"';
        $result[] = '"operation": "' . $operation . '"';
        $result[] = '"textButton": "' . $textButton . '"';

        $this->_outputAjax($result, false);

    }

    /**
     * Add bid to the auction
     * @return void
     */
    public function ajaxAddBidAction()
    {
        $cRequest = A::app()->getRequest();
        if (!$cRequest->isAjaxRequest()) {
            $this->redirect(Website::getDefaultPage());
        }

        $result = array();

        $auctionId = !empty($cRequest->getPost('auctionId')) ? (int)$cRequest->getPost('auctionId') : 0;
        $nextBid = !empty($cRequest->getPost('nextBid')) ? $cRequest->getPost('nextBid') : 0.00;

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', false);
        CLoader::library('libauction/AuctionType.php');
        $auctionType = AuctionType::init($auction->auction_type_id);

        $result = $auctionType->addBid($auctionId, $nextBid);

        $this->_outputAjax($result, false);

    }

    /**
     * Auto Update Auctions
     * @return void
     */
    public function ajaxAutoUpdateAuctionAction()
    {
        $cRequest = A::app()->getRequest();
        if (!$cRequest->isAjaxRequest()) {
            $this->redirect(Website::getDefaultPage());
        }

        $result = array();
        $auctionTypeId = 0;

        $auctionIds = !empty($cRequest->getPost('auctions')) ? json_decode($cRequest->getPost('auctions'), true) : array();

        if (!empty($auctionIds) && is_array($auctionIds)) {
            foreach ($auctionIds as $auctionId => $nextBid) {
                $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', false);
                if ($auction) {
                    $auctionTypeId = $auction->auction_type_id;
                    break;
                }
            }
	
	
			if (!empty($auctionTypeId)) {
				CLoader::library('libauction/AuctionType.php');
				$auctionType = AuctionType::init($auctionTypeId);
				$result = $auctionType->autoUpdateAuction($auctionIds);
			}
        }

        $this->_outputAjax($result, true);

    }

    /**
     * Get all taxes for customer
     * @param int $memberId
     * @param string $statusTab ('active' - default or 'won' or 'loose')
     * @return string
     */
    private function _getConditionForAuctions($memberId = 0, $statusTab = 'active')
    {
        $condition = '';

        $tableNameAuctions = CConfig::get('db.prefix') . Auctions::model()->getTableName();
        $tableNameBidsHistory = CConfig::get('db.prefix') . BidsHistory::model()->getTableName();
        $bidsHistory = BidsHistory::model()->count(array('condition' => $tableNameBidsHistory . '.member_id = ' . $memberId, 'select' => $tableNameBidsHistory . '.auction_id', 'groupBy' => 'auction_id', 'allRows' => true));
        if (!empty($bidsHistory) && $statusTab != 'won') {
            $condition .= $tableNameAuctions . '.id IN (';
            foreach ($bidsHistory as $key => $bidHistory) {
                $condition .= $bidHistory['auction_id'];
                if (isset($bidsHistory[$key + 1])) {
                    $condition .= ',';
                }
            }
            $condition .= ')';
        }

        if ($statusTab == 'active' && !empty($condition)) {
            $condition .= ' AND ' . $tableNameAuctions . '.status = 1';
        } elseif ($statusTab == 'won') {
            $condition .= (!empty($condition) ? ' AND ' : '') . $tableNameAuctions . '.status IN (3,4) AND ' . $tableNameAuctions . '.winner_member_id = ' . $memberId;
        } elseif ($statusTab == 'loose' && !empty($condition)) {
            $condition .= ' AND ' . $tableNameAuctions . '.status IN (3,4) AND ' . $tableNameAuctions . '.winner_member_id <> ' . $memberId;
        }

        return $condition;
    }

    /**
     * Outputs data to browser
     * @param array $data
     * @param bool $returnArray
     * @return void
     */
    private function _outputAjax($data = array(), $returnArray = true)
    {
        $json = '';
        if ($returnArray) {
            $json .= '[';
            $json .= array($data) ? implode(',', $data) : '';
            $json .= ']';
        } else {
            $json .= '{';
            $json .= implode(',', $data);
            $json .= '}';
        }

        $this->_outputJson($json);
    }

    /**
     * Outputs json to browser
     * @param string $json
     * @return void
     */
    private function _outputJson($json)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');   // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Pragma: no-cache'); // HTTP/1.0
        header('Content-Type: application/json');

        echo $json;

        exit;
    }

    /**
     * Get watchlist
     * @param string $status
     * @return array
     * */
    private function _getAllWatchlistAuctions($status = 'active')
    {
        $watchlist = array();
        $auctionIds = array();
        $auctions = array();
        $auctionImages = array();

        if (CAuth::isLoggedInAs('member')) {
            //Find all watchlist for the member
            $watchlistTmp = Watchlist::model()->findAll('member_id = :member_id', array(':member_id' => CAuth::getLoggedRoleId()));
            if (!empty($watchlistTmp) && is_array($watchlistTmp)) {
                foreach ($watchlistTmp as $product) {
                    if (!in_array($product['auction_id'], $auctionIds)) {
                        $auctionIds[] = $product['auction_id'];
                    }
                }
                //Find Auction
                if (!empty($auctionIds) && is_array($auctionIds)) {
                    $tableAuctionName = CConfig::get('db.prefix') . Auctions::model()->getTableName();
                    $condition = $tableAuctionName . '.id IN (' . implode(',', $auctionIds) . ')';
                    $currentDate = LocalTime::currentDateTime('Y-m-d H:i:s');

                    if ($status == 'active') {
                        $condition .= ' AND ' . $tableAuctionName . '.date_from <= "' . $currentDate . '" AND ' . $tableAuctionName . '.date_to >= "' . $currentDate . '" AND ' . $tableAuctionName . '.status IN(1)';
                    } elseif ($status == 'not_started') {
                        $condition .= ' AND ' . $tableAuctionName . '.date_from >= "' . $currentDate . '" AND ' . $tableAuctionName . '.date_to >= "' . $currentDate . '" AND ' . $tableAuctionName . '.status IN(1)';
                    } elseif ($status == 'closed') {
                        $condition .= ' AND ' . $tableAuctionName . '.status IN(0,2,3,4)';
                    }

                    $auctions = Auctions::model()->findAll($condition);

                    //Find Auction Images
                    $auctionImages = AuctionsComponent::getAuctionImages($auctionIds);
                }

                if (!empty($auctions) && is_array($auctions)) {
                    foreach ($auctions as $auction) {
                        $watchlist[$auction['id']] = $auction;

                        // Add auction Image
                        if (isset($auctionImages[$auction['id']])) {
                            $watchlist[$auction['id']]['image_title'] = $auctionImages[$auction['id']]['title'];
                            $watchlist[$auction['id']]['image_file'] = $auctionImages[$auction['id']]['image_file'];
                        }
                    }
                }
            }
        }

        return $watchlist;
    }

    /**
     * Get reviews for the auction
     * @param int $auctionId
     * @return array
     * */
    private function _getReviews($auctionId = 0)
    {

        $reviews = array();
        if (!empty($auctionId)) {
            $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'members/dashboard');
            $tableNameReviews = CConfig::get('db.prefix') . Reviews::model()->getTableName();
            $reviews = Reviews::model()->findAll($tableNameReviews . '.auction_id = :auction_id AND status = 1', array(':auction_id' => $auction->id));
        }

        return $reviews;
    }

}