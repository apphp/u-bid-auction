<?php
/**
 * Shipments controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct
 * indexAction
 * manageAction
 * editAction
 * myShipmentsAction
 * stepsShipmentAction
 * confirmReceivedAction
 *
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
    \Modules\Auctions\Models\Auctions,
    \Modules\Auctions\Models\Shipments,
    \Modules\Auctions\Models\Members;

// Framework
use \Modules,
    \ModulesSettings,
    \CAuth,
    \CConfig,
    \CController,
    \CDatabase,
    \CLocale,
    \Website,
    \CWidget;

// Application
use \Bootstrap,
    \Accounts,
    \A;

class ShipmentsController extends CController
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
            // Set meta tags according to active auctions
            Website::setMetaTags(array('title' => A::t('auctions', 'Shipments Management')));
            // Set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            $this->_view->backendPath = $this->_backendPath;

            $this->_view->tabs = AuctionsComponent::prepareTab('auctions');
        }

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
     * @param int $auctionId
     */
    public function manageAction($auctionId = 0)
    {
        Website::prepareBackendAction('manage', 'auction', 'shipments/manage');
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        if (!in_array($auction->status, array(3, 4))) {
            $this->redirect('auctions/edit/id/' . $auction->id);
        }

        $paramSubTabs = array(
            'parentTab' => 'shipments',
            'activeTab' => 'auction',
            'additionText' => A::t('auctions', 'Shipments'),
            'id' => $auction->id,
            'name' => $auction->auction_name,
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->auctionName = $auction->auction_name;
        $this->_view->auctionId = $auction->id;
        $this->_view->render('shipments/backend/manage');
    }

    /**
     * Add shipment action handler
     * @param int $auctionId
     * @return void
     */
    // public function addAction($auctionId = 0)
    // {
    //     Website::setMetaTags(array('title' => A::t('auctions', 'Add Shipment')));
    //     Website::prepareBackendAction('manage', 'auction', 'auctions/manage/');

    //     $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
    //     if (!in_array($auction->status, array(3, 4))) { // 3 - won, 4 - closed
    //         $this->redirect('shipments/manage/auctionId/' . $auctionId);
    //     }

    //     $paramSubTabs = array(
    //         'parentTab' => 'add_or_edit_shipment',
    //         'activeTab' => 'shipment',
    //         'additionText' => A::t('auctions', 'Add Shipment'),
    //         'id' => $auction->id,
    //         'name' => $auction->auction_name,
    //     );

    //     $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
    //     $this->_view->auctionId = $auction->id;
    //     $this->_view->auctionName = $auction->auction_name;
    //     $this->_view->imageMaxSize = ModulesSettings::model()->param('auctions', 'image_max_size');
    //     $this->_view->render('shipments/backend/add');
    // }

    /**
     * Edit shipments action handler
     * @param int $auctionId
     * @param int $id
     * @return void
     */
    public function editAction($auctionId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'auction', 'shipments/manage');
        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        $findParams = array(
            'auction_id' => $auctionId,
        );

        if (!in_array($auction->status, array(3, 4))) {
            $this->redirect('auctions/edit/id/' . $auction->id);
        }

        $shipment = AuctionsComponent::checkRecordAccess($id, 'Shipments', true, 'auctions/manage', $findParams);

        $paramSubTabs = array(
            'parentTab' => 'add_or_edit_shipment',
            'activeTab' => 'shipment',
            'additionText' => A::t('auctions', 'Edit Shipment'),
            'id' => $auction->id,
            'name' => $auction->auction_name,
        );

        $this->_view->subTabs = AuctionsComponent::prepareSubTab($paramSubTabs);
        $this->_view->id = $id;
        $this->_view->auctionId = $auction->id;
        $this->_view->auctionName = $auction->auction_name;
        $this->_view->shipment = $shipment;
        $this->_view->render('shipments/backend/edit');
    }

    /**
     * Delete action handler
     * @param int $auctionId
     * @param int $id
     * @param int $page
     * @return void
     */
    // public function deleteAction($auctionId = 0, $id = 0, $page = 1)
    // {
    //     Website::prepareBackendAction('delete', 'auction', 'shipments/manage');

    //     $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
    //     $findParams = array(
    //         'auction_id' => $auctionId,
    //     );
    //     $shipment = AuctionsComponent::checkRecordAccess($id, 'Shipments', true, 'auctions/manage', $findParams);

    //     $alert = '';
    //     $alertType = '';

    //     if ($shipment->delete()) {
    //         if ($shipment->getError()) {
    //             $alert = $shipment->getErrorMessage();
    //             $alert = empty($alert) ? A::t('app', 'Delete Error Message') : $alert;
    //             $alertType = 'warning';
    //         } else {
    //             $alert = A::t('app', 'Delete Success Message');
    //             $alertType = 'success';
    //         }
    //     } else {
    //         if (APPHP_MODE == 'demo') {
    //             $alert = CDatabase::init()->getErrorMessage();
    //             $alertType = 'warning';
    //         } else {
    //             $alert = $shipment->getError() ? $shipment->getErrorMessage() : A::t('app', 'Delete Error Message');
    //             $alertType = 'error';
    //         }
    //     }

    //     if (!empty($alert)) {
    //         A::app()->getSession()->setFlash('alert', $alert);
    //         A::app()->getSession()->setFlash('alertType', $alertType);
    //     }

    //     $this->redirect('shipments/manage/auctionId/' . $auctionId . (!empty($page) ? '?page=' . (int)$page : 1));
    // }


    /*   FRONTEND ACTIONS   */

    /**
     * Manage action handler
     * @param int $auctionId
     */
    public function myShipmentsAction()
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Account')));
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

        $this->_view->actionMessage = $actionMessage;
        $this->_view->memberId = $member->id;
        A::app()->view->setLayout('no_columns');
        $this->_view->render('shipments/myShipments');
    }

    /**
     * Steps shipment action handler
     * @param int $shipmentId
     * @param int $auctionId
     */
    public function stepsShipmentAction($shipmentId = 0, $auctionId = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Account')));
        // set frontend settings
        Website::setFrontend();

        $auction = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/manage');
        if (!in_array($auction->status, array(3, 4))) {
            $this->redirect('shipments/myShipments');
        }

        $actionMessage = '';
        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'members/dashboard');
        $shipment = AuctionsComponent::checkRecordAccess($shipmentId, 'Shipments', true, 'members/dashboard');

        if ($shipment->member_id !== $member->id) {
            $this->redirect('members/dashboard');
        }

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');

        if (!empty($alert)) {
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $this->_view->actionMessage = $actionMessage;
        $this->_view->shipmentId = $shipmentId;
        $this->_view->drawStepShipment = AuctionsComponent::drawStepShipment($shipmentId);
        $this->_view->render('shipments/stepsShipment');
    }

    /**
     * Confirm received for the member action handler
     * @param int $shipmentId
     */
    public function confirmReceivedAction($shipmentId = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set frontend settings
        Website::setFrontend();

        $alert = '';
        $alertType = '';

        $shipment = AuctionsComponent::checkRecordAccess($shipmentId, 'Shipments', true, 'members/dashboard');
        $auction = AuctionsComponent::checkRecordAccess($shipment->auction_id, 'Auctions', true, 'auctions/manage');
        $tableNameAccount = CConfig::get('db.prefix') . Accounts::model()->getTableName();
        $member = Members::model()->findByPk($shipment->member_id, $tableNameAccount . '.is_active = 1 AND ' . $tableNameAccount . '.is_removed = 0');
        if (!$member || $shipment->member_id !== $member->id) {
            $this->redirect('members/dashboard');
        }

        $currentDateTime = CLocale::date('Y-m-d H:i:s');

        $shipment->shipping_status              = '2';// Received
        $shipment->last_update_shipping_status  = $currentDateTime;
        $shipment->received_date                = $currentDateTime;

        if ($shipment->save()) {
            $auction->shipping_status = '2'; // Received
            $auction->status = '4'; // Closed
            $auction->status_changed = $currentDateTime;
            if ($auction->save()) {
                $alert = A::t('auctions', 'Auction delivery successfully confirmed!');
                $alertType = 'success';
            } else {
                $alert = A::t('auctions', 'An error occurred while confirming the delivery of the auction. Please try again later.');
                $alertType = 'error';
            }
        } else {
            $alert = A::t('auctions', 'An error occurred while confirming the delivery of the auction. Please try again later.');
            $alertType = 'error';
        }
        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('shipments/myShipments');
    }

}
