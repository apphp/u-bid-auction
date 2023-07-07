<?php
/**
 * Reviews controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                      PRIVATE
 * -----------                  ------------------
 * indexAction                  _getAuctions
 * manageAction
 * addAction
 * editAction
 * deleteAction
 * myReviewsAction
 * editMyReviewAction
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Models\Auctions;
use \Modules\Auctions\Components\AuctionsComponent;
use \Modules\Auctions\Models\Reviews;
use \Modules\Auctions\Models\Members;

// Framework
use \A,
    \CAuth,
    \CArray,
    \CLocale,
    \CFile,
    \CWidget,
    \CTime,
    \CHash,
    \CValidator,
    \CController,
    \CConfig,
    \CDatabase;

// Application
use \Website,
    \Admins,
    \Accounts,
    \Bootstrap,
    \Languages,
    \ModulesSettings,
    \Countries,
    \Modules,
    \LocalTime,
    \SocialLogin,
    \States,
    \BanLists;


class ReviewsController extends CController
{
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
        // block access if the module is not installed
        if (!Modules::model()->isInstalled('auctions')) {
            if (CAuth::isLoggedInAsAdmin()) {
                $this->redirect('modules/index');
            } else {
                $this->redirect(Website::getDefaultPage());
            }
        }

        $this->_settings = Bootstrap::init()->getSettings();
        $this->_cSession = A::app()->getSession();

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

        $this->_view->dateFormat = Bootstrap::init()->getSettings('date_format');
        $this->_view->timeFormat = Bootstrap::init()->getSettings('time_format');
        $this->_view->dateTimeFormat = Bootstrap::init()->getSettings('datetime_format');

        $this->_view->labelStatusReviews = array(
            '0' => '<span class="label-red label-square">' . A::t('auctions', 'Pending') . '</span>',
            '1' => '<span class="label-green label-square">' . A::t('auctions', 'Approved') . '</span>',
            '2' => '<span class="label-yellow label-square">' . A::t('auctions', 'Declined') . '</span>',
        );

        $this->_view->editStatusReviews = array(
            '0' => A::t('auctions', 'Pending'),
            '1' => A::t('auctions', 'Approved'),
            '2' => A::t('auctions', 'Declined'),
        );

        $imagePathPrepend = '<img src="templates/default/images/small_star/smallstar-';
        $imagePathAppend = '.png" />';
        $this->_view->ratingStars = array(
            1 => $imagePathPrepend . '1' . $imagePathAppend,
            2 => $imagePathPrepend . '2' . $imagePathAppend,
            3 => $imagePathPrepend . '3' . $imagePathAppend,
            4 => $imagePathPrepend . '4' . $imagePathAppend,
            5 => $imagePathPrepend . '5' . $imagePathAppend,
        );


        if (CAuth::isLoggedInAsAdmin()) {
            // set meta tags according to active patients
            Website::setMetaTags(array('title' => A::t('auctions', 'Reviews Management')));

            $this->_view->tabs = AuctionsComponent::prepareTab('members');
        }
    }

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        $this->redirect('reviews/manage');
    }

    /**
     * Manage action handler
     * @param int $auctionId
     * @param string $status
     * @return void
     */
    public function manageAction($auctionId = 0, $status = 'approved')
    {
        // set backend mode
        Website::setBackend();

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button' => true)));
        }

        if ($status == 'approved') {
            $statusCode = 1;
        } elseif ($status == 'declined') {
            $statusCode = 2;
        } else {
            $status = 'pending';
            $statusCode = 0;
        }

        $this->_view->status = $status;
        $this->_view->statusCode = $statusCode;

        $paramSubTabs = array(
            'parentTab' => 'reviews',
            'activeTab' => $status,
            'id' => $auction->id,
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->auctionId = $auction->id;
        $this->_view->render('reviews/backend/manage');
    }

    /**
     * Add review action handler
     * @param int $auctionId
     * @return void
     */
    public function addAction($auctionId = 0)
    {

        $cRequest = A::app()->getRequest();
        $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
        $memberId = CAuth::getLoggedRoleId();
        $member = Members::model()->findByPk($memberId, $tableNameAccount . '.is_active = 1 AND ' . $tableNameAccount . '.is_removed = 0');
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'reviews/manage');
        $reviewCount = Reviews::model()->count(array('condition' => 'auction_id = :auction_id AND member_id = :member_id'), array(':auction_id' => $auction->id, ':member_id' => $member->id,));
        if ($reviewCount > 0 || $member->id !== $auction->winner_member_id) {
            $this->redirect('auctions/myAuctions');
        }

        $this->_view->ratingValue = array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5');
        $this->_view->auctionId = $auction->id;
        $this->_view->memberId = $member->id;
        $this->_view->reviewModeration = (int)ModulesSettings::model()->param('auctions', 'review_moderation');
        $this->_view->render('reviews/add');
    }

    /**
     * Edit reviews action handler
     * @param int $auctionId
     * @param int $id
     * @param string $status
     * @return void
     */
    public function editAction($auctionId = 0, $id = 0, $status = 'pending')
    {
        // Set backend mode
        Website::setBackend();

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'reviews/manage');
        $review = AuctionsComponent::checkRecordAccess($id, 'Reviews', true, 'reviews/manage');

        $this->_view->auctionId = $auction->id;
        $this->_view->id = $id;
        $this->_view->memberFullName = $review->first_name . ' ' . $review->last_name;
        $this->_view->status = $status;
        $this->_view->render('reviews/backend/edit');
    }

    /**
     * Delete reviews action handler
     * @param int $auctionId
     * @param int $id
     * @param string $status
     * @return void
     */
    public function deleteAction($auctionId = 0, $id = 0, $status = 'pending')
    {
        // Set backend mode
        Website::setBackend();

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'reviews/manage');
        $review = AuctionsComponent::checkRecordAccess($id, 'Reviews', true, 'reviews/manage');

        $alert = '';
        $alertType = '';

        if ($review->delete()) {
            if ($review->getError()) {
                $alert = $review->getErrorMessage();
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
                $alert = $review->getError() ? $review->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('reviews/manage/auctionId/' . $auction->id . (!empty($status) ? '/status/' . $status : ''));
    }

    /**
     * My reviews action handler
     * @param string $status
     * @return void
     */
    public function myReviewsAction($status = 'approved')
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Shipment Address')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert, array('button' => true)));
        }

        if ($status == 'approved') {
            $statusCode = 1;
        } else {
            $status = 'pending';
            $statusCode = 0;
        }

        $this->_view->status = $status;
        $this->_view->statusCode = $statusCode;
        $this->_view->memberId = $member->id;
        $this->_view->arrAuctions = $this->_getAuctions();

        $this->_view->render('reviews/myReviews');
    }

    /**
     * Edit my review action handler
     * @param int $auctionId
     * @param int $id
     * @param string $status
     * @return void
     */
    public function editMyReviewAction($id = 0, $status = 'pending')
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Shipment Address')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');
        $review = AuctionsComponent::checkRecordAccess($id, 'Reviews', true, 'members/dashboard');

        if ($member->id !== $review->member_id) {
            $this->redirect('members/dashboard');
        }

        $this->_view->id = $id;
        $this->_view->status = $status;
        $this->_view->arrAuctions = $this->_getAuctions();
        $this->_view->render('reviews/editMyReview');
    }

    /**
     * Get packages
     * @return array
     */
    private function _getAuctions()
    {
        $arrAuctions = array();

        $auctions = Auctions::model()->findAll();

        if (!empty($auctions) && is_array($auctions)) {
            foreach ($auctions as $auction) {
                $arrAuctions[$auction['id']] = '<a href="'.Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']).'">'.$auction['auction_name'].'</a>';
            }
        }

        return $arrAuctions;
    }
}