<?php
/**
 * Orders controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct                          _getPaymentMethods
 * indexAction                          _getStatusesForOrder
 * manageAction                         _getAllStatuses
 * editAction                           _getStatusesForOrder
 * deleteAction                         _getAllStatuses
 * downloadInvoiceAction                _prepareSubTabsForEditOrder
 * myOrdersAction                       _preparePdf
 *                                      _getPaymentProviders
 *                                      _getPackages
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
    \Modules\Auctions\Models\Auctions,
    \Modules\Auctions\Models\Packages;

// Framework
use \Modules,
    \CAuth,
    \CController,
    \CDatabase,
    \CLocale,
    \CNumber,
    \CPdf,
    \CTime,
    \CWidget,
    \Website;

// Application
use \Bootstrap,
    \A,
    \Currencies,
    \PaymentProviders;

class OrdersController extends CController
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
            Website::setMetaTags(array('title' => A::t('auctions', 'Orders Management')));
            // Set backend mode
            Website::setBackend();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            $this->_view->backendPath = $this->_backendPath;

            $this->_view->tabs = AuctionsComponent::prepareTab('orders');
        }

        $settings = Bootstrap::init()->getSettings();
        $this->_view->dateFormat = $settings->date_format;
        $this->_view->timeFormat = $settings->time_format;
        $this->_view->dateTimeFormat = $settings->datetime_format;
        $this->_view->typeFormat = $settings->number_format;
        $this->_view->numberFormat = $settings->number_format;
        $this->_view->currencySymbol = A::app()->getCurrency('symbol');
        $this->_view->arrPaymentProviders = $this->_getPaymentProviders();
        $this->_view->arrPaymentMethods = $this->_getPaymentMethods();
        $this->_view->arrPackages = $this->_getPackages();
        $this->_view->arrAuctions = $this->_getAuctions();
        $this->_view->arrBidsAmount = $this->_getBidsAmount();
        $this->_view->arrStatus = array('0' => A::t('auctions', 'Preparing'), '1' => A::t('auctions', 'Pending'), '2' => A::t('auctions', 'Paid'), '3' => A::t('auctions', 'Completed'), '4' => A::t('auctions', 'Refunded'));
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('orders/manage');
    }

    /**
     * Manage action handler
     * @param int $orderType
     */
    public function manageAction($orderType = 0)
    {
        Website::prepareBackendAction('manage', 'order', 'orders/manage');

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $orderType = $orderType == 1 ? $orderType : 0;
        if (!empty($alert)) {
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        if ($orderType == 1) {
            $activeSubTab = 'auctions';
        } else {
            $orderType = 0;
            $activeSubTab = 'packages';
        }

        $this->_view->subTabs = $this->_prepareSubTabsForManageOrder($activeSubTab);
        $this->_view->orderType = $orderType;
        $this->_view->render('orders/backend/manage');
    }

    /**
     * Edit auctions action handler
     * @param int $id
     * @param int $orderType
     * @param string $tab invoice|general
     * @return void
     */
    public function editAction($id = 0, $tab = 'general')
    {
        Website::prepareBackendAction('edit', 'order', 'orders/manage');
        Website::setMetaTags(array('title' => A::t('auctions', 'Edit Order')));
        $order = AuctionsComponent::checkRecordAccess($id, 'Orders', true, 'orders/manage');
        $unknown = A::t('auctions', 'Unknown');

        $allStatus = $this->_getStatusesForOrder($order->status);
        $orderStatus = isset($allStatus[$order->status]) ? $allStatus[$order->status] : $unknown;
        $orderPaymentMethod = isset($this->_view->arrPaymentMethods[$order->payment_method]) ? $this->_view->arrPaymentMethods[$order->payment_method] : $unknown;

        $beforePrice = '';
        $afterPrice = '';
        if ($order->order_type == 1) {
            $orderName = isset($this->_view->arrAuctions[$order->auction_id]) ? $this->_view->arrAuctions[$order->auction_id] : $unknown;
        } else {
            $orderName = isset($this->_view->arrPackages[$order->package_id]) ? $this->_view->arrPackages[$order->package_id] : $unknown;
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $arrCountryNames = $countriesAndDefaultCountry['countries'];
        $arrStateNames = AuctionsComponent::getStates();

        switch ($tab) {
            case 'invoice':
                $this->_view->member = AuctionsComponent::checkRecordAccess($order->member_id, 'Members', true, 'orders/manage');
                $this->_view->orderName = $orderName;

                $tab = 'invoice';
                break;

            case 'general':
            default:
                $tab = 'general';
                $this->_view->id = $order->id;
                $this->_view->orderName = $orderName;
                $this->_view->orderPaymentMethod = $orderPaymentMethod;
                $this->_view->orderStatus = $orderStatus;
                $this->_view->allStatus = $allStatus;
                break;
        }

        $currency = Currencies::model()->find('code = :code', array(':code' => $order->currency));
        if (!empty($currency)) {
            if ($currency->symbol_place == 'before') {
                $beforePrice = $currency->symbol;
                $afterPrice = '';
            } else {
                $beforePrice = '';
                $afterPrice = $currency->symbol;
            }
        }

        $this->_view->arrCCType = array('visa' => 'Visa', 'mastercard' => 'MasterCard', 'american express' => 'American Express', 'discover' => 'Discover');
        $this->_view->id = $id;
        $this->_view->order = $order;
        $this->_view->arrCountryNames = $arrCountryNames;
        $this->_view->arrStateNames = $arrStateNames;
        $this->_view->beforePrice = $beforePrice;
        $this->_view->afterPrice = $afterPrice;
        $this->_view->allStatus = $allStatus;
        $this->_view->subTabName = $tab;
        $this->_view->subTabs = $this->_prepareSubTabsForEditOrder($tab, $id);
        $this->_view->unknown = $unknown;

        $this->_view->render('orders/backend/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     * @param int $orderType
     * @param int $page
     * @return void
     */
    public function deleteAction($id = 0, $orderType = 0, $page = 1)
    {
        Website::prepareBackendAction('delete', 'order', 'orders/manage');

        $order = AuctionsComponent::checkRecordAccess($id, 'Orders', true, 'orders/manage');

        $alert = '';
        $alertType = '';

        if ($order->delete()) {
            if ($order->getError()) {
                $alert = $order->getErrorMessage();
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
                $alert = $order->getError() ? $order->getErrorMessage() : A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
        }

        if (!empty($alert)) {
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
        }

        $this->redirect('orders/manage/orderType/' . $orderType . (!empty($page) ? '?page=' . (int)$page : 1));
    }


    /**
     * Delete action handler
     * @param int $id
     * @param int $orderType
     * @return void
     */
    public function downloadInvoiceAction($id = 0)
    {
        if (!CAuth::getLoggedId()) {
            $this->redirect('Home/index');
        } elseif (CAuth::isLoggedInAsAdmin()) {
            Website::prepareBackendAction('edit', 'order', 'orders/manage');
        }

        $order = AuctionsComponent::checkRecordAccess($id, 'Orders', true, 'orders/manage');

        $content = $this->_preparePdf($order);

        if (!empty($content)) {
            CPdf::config(array(
                'page_orientation' => 'P',             // [P=portrait, L=landscape]
                'unit' => 'mm',            // [pt=point, mm=millimeter, cm=centimeter, in=inch]
                'page_format' => 'A4',
                'unicode' => true,
                'encoding' => 'UTF-8',
                'creator' => 'auctions',
                'author' => 'ApPHP',
                'title' => 'Orders #' . $order->order_number,
                'subject' => 'Orders #' . $order->order_number,
                'keywords' => '',
                //'header_logo'     => '../../../templates/reports/images/logo.png',
                'header_logo_width' => '45',
                'header_title' => 'Orders #' . $order->order_number,
                'header_enable' => false,
                'text_shadow' => false,
                'margin_top' => '1',
                'margin_left' => '5'
            ));

            CPdf::createDocument($content, 'Orders #' . $order->order_number, 'D'); // 'I' - inline , 'D' - download
        }
    }

    /*   FRONTEND ACTIONS   */

    /**
     * My orders action handler
     * @param int $orderType
     */
    public function myOrdersAction($orderType = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Orders')));
        // set frontend settings
        Website::setFrontend();

        $alert = A::app()->getSession()->getFlash('alert');
        $alertType = A::app()->getSession()->getFlash('alertType');
        $actionMessage = '';
        $orderType = $orderType == 1 ? $orderType : 0;

        if (!empty($alert)) {
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button' => true))
            );
        }

        $memberId = CAuth::getLoggedRoleId();
        $member = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'orders/manage');

        $this->_view->memberId = $member->id;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->orderType = $orderType;
        $this->_view->render('orders/myOrders');
    }

    /**
     * Edit auctions action handler
     * @param int $id
     * @param int $orderType
     * @return void
     */
    public function invoiceMyOrderAction($id = 0)
    {
        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title' => A::t('auctions', 'My Orders')));
        // set frontend settings
        Website::setFrontend();

        $order = AuctionsComponent::checkRecordAccess($id, 'Orders', true, 'orders/manage');
        $unknown = A::t('auctions', 'Unknown');

        $allStatus = $this->_getStatusesForOrder($order->status);

        $beforePrice = '';
        $afterPrice = '';
        if ($order->order_type == 1) {
            $orderName = isset($this->_view->arrAuctions[$order->auction_id]) ? $this->_view->arrAuctions[$order->auction_id] : $unknown;
        } else {
            $orderName = isset($this->_view->arrPackages[$order->package_id]) ? $this->_view->arrPackages[$order->package_id] : $unknown;
        }

        $countriesAndDefaultCountry = AuctionsComponent::getCountries();

        $arrCountryNames = $countriesAndDefaultCountry['countries'];
        $arrStateNames = AuctionsComponent::getStates();

        $this->_view->member = AuctionsComponent::checkRecordAccess($order->member_id, 'Members', true, 'orders/myOrders');
        $this->_view->orderName = $orderName;

        $currency = Currencies::model()->find('code = :code', array(':code' => $order->currency));
        if (!empty($currency)) {
            if ($currency->symbol_place == 'before') {
                $beforePrice = $currency->symbol;
                $afterPrice = '';
            } else {
                $beforePrice = '';
                $afterPrice = $currency->symbol;
            }
        }

        $this->_view->arrCCType = array('visa' => 'Visa', 'mastercard' => 'MasterCard', 'american express' => 'American Express', 'discover' => 'Discover');
        $this->_view->id = $id;
        $this->_view->order = $order;
        $this->_view->arrCountryNames = $arrCountryNames;
        $this->_view->arrStateNames = $arrStateNames;
        $this->_view->beforePrice = $beforePrice;
        $this->_view->afterPrice = $afterPrice;
        $this->_view->allStatus = $allStatus;
        $this->_view->unknown = $unknown;

        $this->_view->render('orders/invoiceMyOrder');
    }

    /**
     * Get payment methods
     * @return array
     */
    private function _getPaymentMethods()
    {
        $paymentMethods = array(
            '0' => A::t('auctions', 'Payment Company Account'),
            '1' => A::t('auctions', 'Credit Card'),
            '2' => A::t('auctions', 'E-Check')
        );

        return $paymentMethods;
    }

    /**
     * Get payment providers
     * @return array
     */
    private function _getPaymentProviders()
    {
        $paymentProviders = array();

        $providers = PaymentProviders::model()->findAll('is_active = 1');

        if (!empty($providers) && is_array($providers)) {
            foreach ($providers as $provider) {
                $paymentProviders[$provider['id']] = $provider['name'];
            }
        }

        return $paymentProviders;
    }

    /**
     * Get packages
     * @return array
     */
    private function _getPackages()
    {
        $paymentPackages = array();

        $packages = Packages::model()->findAll('is_active = 1');

        if (!empty($packages) && is_array($packages)) {
            foreach ($packages as $package) {
                $paymentPackages[$package['id']] = $package['name'];
            }
        }

        return $paymentPackages;
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
                $arrAuctions[$auction['id']] = $auction['auction_name'];
            }
        }

        return $arrAuctions;
    }

    /**
     * Get bids amount for the packages
     * @return array
     */
    private function _getBidsAmount()
    {
        $paymentPackages = array();

        $packages = Packages::model()->findAll('is_active = 1');

        if (!empty($packages) && is_array($packages)) {
            foreach ($packages as $package) {
                $paymentPackages[$package['id']] = $package['bids_amount'];
            }
        }

        return $paymentPackages;
    }

    /**
     * Get statuses by status number for order
     * @param int $statusNumber
     * @return array
     */
    private function _getStatusesForOrder($statusNumber = 0)
    {
        $outStatuses = array();
        $allStatuses = $this->_getAllStatuses();
        switch ($statusNumber) {
            case '0':
                $outStatuses[0] = $allStatuses[0];
                break;
            case '1':
                $outStatuses[1] = $allStatuses[1];
                $outStatuses[2] = $allStatuses[2];
                $outStatuses[4] = $allStatuses[4];
                break;
            case '2':
                $outStatuses[2] = $allStatuses[2];
                $outStatuses[3] = $allStatuses[3];
                break;
            case '3':
                $outStatuses[3] = $allStatuses[3];
                break;
            case '4':
                $outStatuses[4] = $allStatuses[4];
                break;
        }

        return $outStatuses;
    }

    /**
     * Prepare subtabs for edit orders
     * @param string $activeSubTab
     * @return string
     */
    private function _prepareSubTabsForManageOrder($activeSubTab = '')
    {
        $arrSubTabs = array(0 => 'packages', 1 => 'auctions');
        $arrTabNames = array(
            'packages' => A::t('auctions', 'Packages'),
            'auctions' => A::t('auctions', 'Auctions')
        );

        $activeSubTab = in_array($activeSubTab, $arrSubTabs) ? $activeSubTab : $arrSubTabs[0];

        $outHtml = '<div class="sub-title">';
        foreach ($arrSubTabs as $key => $tabName) {
            $outHtml .= '<a class="sub-tab' . ($activeSubTab == $tabName ? ' active' : '') . '" href="' . ($activeSubTab == $tabName ? 'javascript:void(0);' : 'orders/manage/orderType/' . $key) . '">' . $arrTabNames[$tabName] . '</a>';
        }
        $outHtml .= '</div>';

        return $outHtml;
    }

    /**
     * Prepare subtabs for edit orders
     * @param string $activeSubTab
     * @param int $orderId
     * @return string
     */
    private function _prepareSubTabsForEditOrder($activeSubTab = '', $orderId = 0)
    {
        $arrSubTabs = array('general', 'invoice');
        $arrTabNames = array(
            'general' => A::t('auctions', 'General'),
            'invoice' => A::t('auctions', 'Invoice')
        );
        $activeSubTab = in_array($activeSubTab, $arrSubTabs) ? $activeSubTab : $arrSubTabs[0];

        $outHtml = '<div class="sub-title">' . A::t('auctions', 'Edit Order') . ' &raquo; &nbsp;';
        foreach ($arrSubTabs as $tabName) {
            $outHtml .= '<a class="sub-tab' . ($activeSubTab == $tabName ? ' active' : '') . '" href="orders/edit/id/' . $orderId . '/tab/' . $tabName . '">' . $arrTabNames[$tabName] . '</a>';
        }
        $outHtml .= '</div>';

        return $outHtml;
    }

    /**
     * Get all statuses
     * @return array
     */
    private function _getAllStatuses()
    {
        return array(
            '0' => A::t('auctions', 'Preparing'),
            '1' => A::t('auctions', 'Pending'),
            '2' => A::t('auctions', 'Paid'),
            '3' => A::t('auctions', 'Completed'),
            '4' => A::t('auctions', 'Refunded'),
        );
    }

    /**
     * Prepare PDF
     * @param object(Orders) orderId
     * @return html
     * */
    private function _preparePdf($order = null)
    {
        $output = '';

        if (empty($order) || !is_a($order, 'Modules\Auctions\Models\Orders')) {
            return $output;
        }

        $beforePrice = '';
        $afterPrice = '';
        $unknown = A::t('auctions', 'Unknown');
        if ($order->order_type == 1) {
            $auctionName = isset($this->_view->arrAuctions[$order->auction_id]) ? $this->_view->arrAuctions[$order->auction_id] : $unknown;
        } else {
            $packageName = isset($this->_view->arrPackages[$order->package_id]) ? $this->_view->arrPackages[$order->package_id] : $unknown;
        }
        $member = AuctionsComponent::checkRecordAccess($order->member_id, 'Members', false, 'orders/manage');
        $allStatus = $this->_getAllStatuses();
        $arrStateNames = AuctionsComponent::getStates();
        $getCountries = AuctionsComponent::getCountries();
        $arrCountryNames = $getCountries['countries'];
        $status = isset($allStatus[$order->status]) ? $allStatus[$order->status] : $unknown;
        $paymentProvider = isset($this->_view->arrPaymentProviders[$order->payment_id]) ? $this->_view->arrPaymentProviders[$order->payment_id] : $unknown;
        $paymentMethod = isset($this->_view->arrPaymentMethods[$order->payment_method]) ? $this->_view->arrPaymentMethods[$order->payment_method] : $unknown;

        $currency = Currencies::model()->find('code = :code', array(':code' => $order->currency));
        if (!empty($currency)) {
            if ($currency->symbol_place == 'before') {
                $beforePrice = $currency->symbol;
                $afterPrice = '';
            } else {
                $beforePrice = '';
                $afterPrice = $currency->symbol;
            }
        }
        $outputMember = '';
        if (!empty($member)) {
            $outputMember = '
            <div class="invoice-box">
                <table class="pb10">
                    <tr>
                        <td class="title" colspan="2">' . A::t('auctions', 'Member') . ':</td>
                    </tr>
                    <tr>
                        <td width="30%">' . A::t('auctions', 'First Name') . ': </td><td>' . $member->first_name . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Last Name') . ': </td><td>' . $member->last_name . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Email') . ': </td><td>' . $member->email . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Phone') . ': </td><td>' . ($member->phone ? $member->phone : $unknown) . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Address') . ': </td><td>' . $member->address . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'City') . ': </td><td>' . ($member->city ? $member->city : '') . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Zip Code') . ': </td><td>' . ($member->zip_code ? $member->zip_code : '') . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'State/Province') . ': </td><td>' . (isset($arrStateNames[$member->state]) ? $member->state . ' (' . $arrStateNames[$member->state] . ')' : $member->state) . '</td>
                    </tr>
                    <tr>
                        <td>' . A::t('auctions', 'Country') . ': </td><td>' . (isset($arrCountryNames[$member->country_code]) ? $arrCountryNames[$member->country_code] : $unknown) . '</td>
                    </tr>
                </table>
            </div>
        ';
        }

        $output .= '<!DOCTYPE HTML>
        <html>
            <head>
                <style>
                    .right {text-align:right;}
                    .center {text-align:center;}
                </style>
            </head>
            <body>
                <table style="width:100%;margin:0 auto;font-size:12px;padding:10px 10px 10px 10px">
                    <h2 style="text-align:center;">' . A::t('auctions', 'Invoice') . ' #' . $order->order_number . '</h2>
                        <div class="invoice-box">
                            <table class="pb10">
                                <tr>
                                    <td class="title" colspan="2">' . A::t('auctions', 'General') . ':</td>
                                </tr>
                                <tr>
                                    <td width="30%">' . ($order->order_type == 1 ? A::t('auctions', 'Auction') : A::t('auctions', 'Package')) . ': </td><td>' . ($order->order_type == 1 ? $auctionName : $packageName) . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('auctions', 'Order Number') . ': </td><td>' . $order->order_number . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('app', 'Status') . ': </td><td>' . $status . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('auctions', 'Created at') . ': </td><td>' . CLocale::date($this->_view->dateTimeFormat, $order->created_at) . '</td>
                                </tr>
                                <tr>
                                    <td><b>' . A::t('auctions', 'Grand Total') . ': </b></td><td><b>' . $beforePrice . CNumber::format($order->total_price, $this->_view->numberFormat, array('decimalPoints' => 2)) . $afterPrice . '</b></td>
                                </tr>
                            </table>
                        </div>
                        ' . $outputMember . '
                        <div class="invoice-box">
                            <table class="pb10">
                                <tr>
                                    <td class="title" colspan="2">' . A::t('auctions', 'Payment') . ':</td>
                                </tr>
                                <tr>
                                    <td width="30%">' . A::t('auctions', 'Payment Type') . ': </td><td>' . $paymentProvider . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('auctions', 'Payment Method') . ': </td><td>' . $paymentMethod . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('auctions', 'Payment Date') . ': </td><td>' . (!CTime::isEmptyDateTime($order->payment_date) ? CLocale::date($this->_view->dateTimeFormat, $order->payment_date) : $unknown) . '</td>
                                </tr>
                                <tr>
                                    <td>' . A::t('auctions', 'Transaction ID') . ': </td><td>' . ($order->transaction_number ? $order->transaction_number : '--') . '</td>
                                </tr>
                            </table>
                        </div>
                    <div style="text-align:left;">' . A::t('auctions', 'Date Created Invoice') . ': ' . CLocale::date($this->_view->dateTimeFormat) . '</div>
                </table>
            </body>
        </html>';

        return $output;
    }
}
