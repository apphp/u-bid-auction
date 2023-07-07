<?php
/**
 * Checkout Controller
 * This controller intended to both Backend and Frontend modes
 *
 * PUBLIC:                              PRIVATE
 * -----------                          ------------------
 * __construct                          _getTaxesForCountry
 * indexAction                          _getPaymentProviders
 * packageAction                        _calculationTaxes
 * packagePaymentFormAction             _validationCreditCard
 * completeAction
 * auctionAction
 * auctionPaymentFormAction
 *
 *
 *
 *
 */

namespace Modules\Auctions\Controllers;

// Module
use \Modules\Auctions\Components\AuctionsComponent,
	\Modules\Auctions\Models\Orders,
	\Modules\Auctions\Models\ShipmentAddress,
	\Modules\Auctions\Models\Taxes,
	\Modules\Auctions\Models\TaxCountries;

// Framework
use \Modules,
	\ModulesSettings,
	\CArray,
	\CAuth,
	\CConfig,
	\CController,
	\CDatabase,
	\CHash,
	\CLoader,
	\CLocale,
	\Website,
	\CWidget;

// Application
use \A,
    \Bootstrap,
    \LocalTime,
    \PaymentProvider,
    \PaymentProviders;

class CheckoutController extends CController
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
		}

		$settings                    = Bootstrap::init()->getSettings();
		$this->_view->dateFormat     = $settings->date_format;
		$this->_view->timeFormat     = $settings->time_format;
		$this->_view->dateTimeFormat = $settings->datetime_format;
	}

    /**
     * Controller default action handler
     * @return void
     */
    public function indexAction()
    {
        // Set frontend mode
        $this->redirect('checkout/package');
    }

    /*   FRONTEND ACTIONS   */

    /**
     * Member Checkout Package action handler
     * @param int $packageId
     * @return void
     */
    public function packageAction($packageId = 0)
    {

        // block access to this controller for not-logged patients
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'Checkout Package')));
        // set frontend settings
        Website::setFrontend();


        $actionMessage  = '';
        $totalTax       = 0;
        $taxes          = array();
        $redirectPath   = 'packages/packages';
        $memberId       = CAuth::getLoggedRoleId();
        $member         = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, $redirectPath);
        $package        = AuctionsComponent::checkRecordAccess($packageId, 'Packages', true, $redirectPath);
        $alert          = A::app()->getSession()->getFlash('alert');
        $alertType      = A::app()->getSession()->getFlash('alertType');

        // block access if there is an order with a free package from a member
        if($package->price == 0){
            $checkFreePlanInOrders  = AuctionsComponent::checkFreePlanInOrders(true, 'members/dashboard');
        }

        if(!empty($alert)){
            $actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>false))
            );
        }

        if($member->country_code){
           $taxes       = $this->_getTaxesForCountry($member->country_code);
           $totalTax    = $this->_calculationTaxes($package->price, $member->country_code);
        }

        $this->_view->providers     = $this->_getPaymentProviders();
        $this->_view->grandTotal    = $package->price + $totalTax;
        $this->_view->totalTax      = $totalTax;
        $this->_view->taxes         = $taxes;
        $this->_view->actionMessage = $actionMessage;
        $this->_view->memberId      = $member->id;
        $this->_view->member        = $member;
        $this->_view->package       = $package;
        $this->_view->render('checkout/package');
    }

    /**
     * View Form for pay
     * @param int $packageId
     * @return void
     */
    public function packagePaymentFormAction($packageId = 0)
    {
        // block access to this controller for not-logged members
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'Checkout Package')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage      = '';
        $creditCardMessage  = '';
        $freePlan           = false;
        $saveOrder          = true;
        $arrVatInfo         = array();
        $taxes              = array();
        $grandTotal         = 0;
        $vatPercent         = 0;
        $totalTax           = 0;
        $providerSettingsId = 0;
        $memberId           = CAuth::getLoggedRoleId();
        $redirectPath       = 'packages/packages';
        $member             = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, $redirectPath);
        $package            = AuctionsComponent::checkRecordAccess($packageId, 'Packages', true, $redirectPath);
        $cRequest           = A::app()->getRequest();
        $act                = $cRequest->getPost('act', 'string');
        $paymentMethod      = $cRequest->getPost('payment_method', 'string');
        $back               = 'checkout/package/'.$package->id;
        $currencyCode       = CAuth::getLoggedParam('currency_code');

        //If the membership plan is free, then we add the doctor account
        if($package->price == 0 && $member){
            // block access if there is an order with a free package from a member
            $checkFreePlanInOrders  = AuctionsComponent::checkFreePlanInOrders(true, 'members/dashboard');
            $freePlan = true;
        }

        if(!$freePlan){
            if(empty($act)){
                $alert = '';
                $alertType = '';
            }elseif(empty($paymentMethod)){
                $alert = A::t('core', 'The field {title} cannot be empty! Please re-enter.', array('{title}'=>A::t('auctions', 'Payment Method')));
                $alertType = 'validation';
            }else{
                $providers = PaymentProviders::model()->findAll('is_active = 1');
                $providers = CArray::flipByField($providers, 'code');
                if(!in_array($paymentMethod, array_keys($providers))){
                    $alert = A::t('app', 'Input incorrect parameters');
                    $alertType = 'error';
                }
            }

            if(!empty($alertType) || $act != 'send'){
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect($back);
            }

            CLoader::library('ipgw/PaymentProvider.php');
            $provider = PaymentProvider::init($paymentMethod);
            $providerSettings = PaymentProviders::model()->find("code = :code", array(':code'=>$paymentMethod));
            $providerSettingsId = $providerSettings->id;
            if($member->country_code){
                $taxes       = $this->_getTaxesForCountry($member->country_code);
                $totalTax    = $this->_calculationTaxes($package->price, $member->country_code);
            }
            $grandTotal = $package->price + $totalTax;

            if(!empty($taxes)){
                foreach($taxes as $tax){
                    $vatPercent += $tax['percent'];
                    $name = $tax['name'];
                    $percent = $tax['percent'];
                    $price = $package->price * ($percent * 0.01);
                    $arrVatInfo[] = array('name'=>$name, 'percent'=>$percent, 'price'=>round($price, 2));
                }
            }
        }

        $lastPackageOrderNumber =  A::app()->getSession()->get('lastPackageOrderNumber');
        //Create or Update Order
        if(empty($lastPackageOrderNumber)){

            $order = new Orders();

            $order->order_number   	    = CHash::getRandomString(10, array('case' => 'upper'));
            $order->order_type   	    = 0;//Package
            $order->order_description   = A::t('auctions', 'Package Bids');
            $order->order_price 	    = $package->price;
            $order->vat_percent 	    = $vatPercent;
            $order->vat_fee 		    = $totalTax;
            $order->vat_fee_info 	    = !empty($arrVatInfo) ? serialize($arrVatInfo) : '';
            $order->total_price 	    = $grandTotal;
            $order->currency 		    = $currencyCode;
            $order->package_id          = $package->id;
            $order->member_id 		    = $member->id;
            $order->transaction_number  = '';
            $order->created_at 	        = date('Y-m-d H:i:s');
            $order->payment_id 		    = $providerSettingsId;
            $order->payment_method 	    = 0;
            $order->cc_type             = '';
            $order->cc_holder_name      = '';
            $order->cc_number           = '';
            $order->cc_expires_month    = '';
            $order->cc_expires_year     = '';
            $order->cc_cvv_code         = '';
            $order->status 			    = $freePlan ? 2 : 0;
            $order->status_changed 	    = CLocale::date('Y-m-d H:i:s');;
            $order->email_sent          = 0;

            A::app()->getSession()->set('lastPackageOrderNumber', $order->order_number);
        }else{
            $order = Orders::model()->find('order_number = :order_number AND order_type = 0', array(':order_number' => $lastPackageOrderNumber));
            //If not exists order in database redirect orders/checkout/
            if(!$order){
                A::app()->getSession()->remove('lastPackageOrderNumber');
                $alert = A::t('auctions', 'Order cannot be found in the database');
                $alertType = 'error';
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect($back);
            }elseif($order->package_id != $package->id || $order->payment_id != $providerSettingsId){
                $order->order_description   = A::t('auctions', 'Package Bids');
                $order->order_price         = $package->price;
                $order->vat_percent         = $vatPercent;
                $order->vat_fee             = $totalTax;
                $order->vat_fee_info        = !empty($arrVatInfo) ? serialize($arrVatInfo) : '';
                $order->total_price         = $grandTotal;
                $order->currency            = $currencyCode;
                $order->package_id          = $package->id;
                $order->member_id           = $member->id;
                $order->transaction_number  = '';
                $order->created_at          = date('Y-m-d H:i:s');
                $order->payment_id          = $providerSettingsId;
                $order->payment_method      = 0;
                $order->status              = $freePlan ? 2 : 0;
                $order->status_changed      = CLocale::date('Y-m-d H:i:s');;
            }
        }

        if(!$freePlan){
            $params = array(
                'item_name'     => $package->name,
                'item_number'   => $package->id,
                'amount'        => $order->total_price,
                'custom'        => $order->order_number,      // order ID
                // The rm variable takes effect only if the return variable is set.
                'currency_code' => $currencyCode,   // The currency of the payment. The default is USD.
                'no_shipping'   => '',      // Do not prompt buyers for a shipping address.
                'address1'      => $member->address,
                'address2'      => $member->address_2,
                'city'          => $member->city,
                'zip'           => $member->zip_code,
                'state'         => $member->state,
                'country'       => $member->country_code,
                'first_name'    => $member->first_name,
                'last_name'     => $member->last_name,
                'email'         => $member->email,
                'phone'         => $member->phone,
                'mode'          => $providerSettings->mode,
                'back'          => $back,       // Back to Auctions
                'notify'        => A::app()->getRequest()->getBaseUrl().'paymentProviders/handlePayment/provider/'.$paymentMethod.'/handler/orders/module/auctions', // IPN processing link
                'cancel'        => A::app()->getRequest()->getBaseUrl().'checkout/package/'.$package->id,                       // Cancel order link
                'cancel_return' => A::app()->getRequest()->getBaseUrl().'checkout/package/'.$package->id,                       // Cancel & return to site link
            );

            if($paymentMethod == 'paypal_standard'){
                $params = array_merge($params, array(
                    'merchant_id' => $providerSettings->merchant_id,
                ));
            }else if($paymentMethod == 'online_credit_card'){
                if(A::app()->getRequest()->isPostExists('cc_type')){
                    $saveOrder = false;
                    $arrCCType = array('Visa', 'MasterCard', 'American Express', 'Discover');
                    $arrCCExpiresMonth = array();
                    for($i = 1; $i <= 12; $i++){
                        $arrCCExpiresMonth[] = sprintf('%02s', $i);
                    }
                    $arrCCExpiresYear = range(LocalTime::currentDate('Y'), LocalTime::currentDate('Y') + 10);
                    $fields = array(
                        'fields' => array(
                            'cc_type'          => array('title'=>A::t('app', 'Credit Card Type'), 'validation'=>array('required'=>true, 'type'=>'text', 'source'=>'20')),
                            'cc_holder_name'   => array('title'=>A::t('app', 'Card Holder\'s Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50')),
                            'cc_number'        => array('title'=>A::t('app', 'Credit Card Number'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>'50')),
                            'cc_expires_month' => array('title'=>A::t('app', 'Expires Month'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>$arrCCExpiresMonth)),
                            'cc_expires_year'  => array('title'=>A::t('app', 'Expires Year'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>$arrCCExpiresYear)),
                            'cc_cvv_code'      => array('title'=>A::t('app', 'CVV Code'), 'validation'=>array('required'=>true, 'type'=>'number', 'maxLength'=>'4')),
                        ),
                        'messagesSource' => 'core',
                        'showAllErrors'  => false,
                    );
                    $result = CWidget::create('CFormValidation', $fields);
                    if($result['error']){
                        $alert     = $result['errorMessage'];
                        $alertType = 'validation';
                        $params['error_field'] = $result['errorField'];

                        $creditCardMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
                    }else{
                        $params['cc_type']          = A::app()->getRequest()->getPost('cc_type');
                        $params['cc_holder_name']   = A::app()->getRequest()->getPost('cc_holder_name');
                        $params['cc_number']        = A::app()->getRequest()->getPost('cc_number');
                        $params['cc_expires_month'] = A::app()->getRequest()->getPost('cc_expires_month');
                        $params['cc_expires_year']  = A::app()->getRequest()->getPost('cc_expires_year');
                        $params['cc_cvv_code']      = A::app()->getRequest()->getPost('cc_cvv_code');

                        $result = $this->_validationCreditCard($params);
                        if($result['error']){
                            $alert = $result['errorMessage'];
                            $alertType = 'validation';
                            $params['error_field'] = $result['errorField'];

                            $creditCardMessage = CWidget::create('CMessage', array($alertType, $alert, array()));
                        }elseif(!empty($order)){
                            $order->cc_type          = $params['cc_type'];
                            $order->cc_holder_name   = $params['cc_holder_name'];
                            $order->cc_number        = $params['cc_number'];
                            $order->cc_expires_month = $params['cc_expires_month'];
                            $order->cc_expires_year  = $params['cc_expires_year'];
                            $order->cc_cvv_code      = $params['cc_cvv_code'];
                            $order->payment_method 	 = 1;

                            if(!$order->save()){
                                if(APPHP_MODE == 'demo'){
                                    $alert = CDatabase::init()->getErrorMessage();
                                    $alertType = 'warning';
                                }else{
                                    $alert = A::t('auctions', 'The error occurred while adding new record!');
                                    $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
                                    $alertType = 'error';
                                }

                                A::app()->getSession()->setFlash('alert', $alert);
                                A::app()->getSession()->setFlash('alertType', $alertType);
                                $this->redirect($back);
                            }

                            $this->redirect($params['notify'], true);
                        }
                    }
                }
                $params['notify'] = A::app()->getRequest()->getBaseUrl().'checkout/packagePaymentForm/'.$package->id;
            }

            $form = $provider->drawPaymentForm($params);
        }

        if($saveOrder){
            if(!$order->save()){
                if(APPHP_MODE == 'demo'){
                    $alert = CDatabase::init()->getErrorMessage();
                    $alertType = 'warning';
                }else{
                    $alert = A::t('auctions', 'The error occurred while adding new record!');
                    $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
                    $alertType = 'error';
                }

                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect($back);
            }elseif($freePlan){
                $member->bids_amount += $package->bids_amount;
                if($member->save()){
                    $alert = A::t('auctions', 'The order has been placed in your system(For Free Package)');
                    $alertType = 'success';
                }else{
                    $alert = A::t('auctions', 'Cannot complete your order! Please try again later.');
                    $alertType = 'error';
                }

                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect('packages/packages');
            }
        }

        $this->_view->grandTotal        = $grandTotal;
        $this->_view->totalTax          = $totalTax;
        $this->_view->taxes             = $taxes;
        $this->_view->order             = $order;
        $this->_view->creditCardMessage = $creditCardMessage;
        $this->_view->actionMessage     = $actionMessage;
        $this->_view->form              = $form;
        $this->_view->member            = $member;
        $this->_view->package           = $package;
        $this->_view->providerSettings  = $providerSettings;
        $this->_view->render('checkout/packagePaymentForm');
    }

    /**
     * View Form for pay
     * @param int $auctionId
     * @param string $type
     * @return void
     */
    public function auctionPaymentFormAction($auctionId = 0, $type = 'buy_now')
    {
        // block access to this controller for not-logged members
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'Checkout Auction')));
        // set frontend settings
        Website::setFrontend();

        $actionMessage      = '';
        $shippingSelect     = '';
        $shippingAddress    = array();
        $arrVatInfo         = array();
        $taxes              = array();
        $vatPercent         = 0;
        $totalTax           = 0;
        $memberId           = CAuth::getLoggedRoleId();
        $member             = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, 'auctions/view/id/'.$auctionId);
        $auction            = AuctionsComponent::checkRecordAccess($auctionId, 'Auctions', true, 'auctions/view/id/'.$auctionId);
        $redirectPath       = Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction->id, $auction->auction_name);
        $currencyCode       = CAuth::getLoggedParam('currency_code');
        $paymentMethod      = 'paypal_standard';

        // Get Countries
        $countriesAndDefaultCountry = AuctionsComponent::getCountries();
        if (!empty($countriesAndDefaultCountry) && is_array($countriesAndDefaultCountry)) {
            $this->_view->countries = isset($countriesAndDefaultCountry['countries']) ? $countriesAndDefaultCountry['countries'] : array();
            $this->_view->defaultCountryCode = isset($countriesAndDefaultCountry['default_country_code']) ? $countriesAndDefaultCountry['default_country_code'] : '';
        }

        $cRequest = A::app()->getRequest();
        if($cRequest->isPostRequest()){
            $this->_view->countryCode = $cRequest->getPost('country_code');
            $this->_view->stateCode = $cRequest->getPost('state');
        }else{
            $this->_view->countryCode = $this->_view->defaultCountryCode;
            $this->_view->stateCode = '';
        }

        // Get shipping address for the member
        $arrAllAddress = ShipmentAddress::model()->findAll('member_id = :member_id', array(':member_id'=>$member->id));
        if(!empty($arrAllAddress) && is_array($arrAllAddress)){
            foreach($arrAllAddress as $address){
                if($address['is_default']){
                    $shippingSelect = $address['id'];
                }
                $addressToString  = '';
                if(empty($address['company'])){
                    $addressToString .= $address['first_name'];
                    $addressToString .= (!empty($address['last_name']) && !empty($address['first_name']) ? ' ' : '').$address['last_name'];
                    $addressToString .= !empty($addressToString) ? '; ' : '';
                }else{
                    $addressToString .= $address['company'].'; ';
                }
                $addressToString .= $address['address'];
                $addressToString .= ($addressToString && $address['city'] ? ', ' : '').$address['city'];
                $addressToString .= ($addressToString && $address['zip_code'] ? ', ' : '').$address['zip_code'];
                $addressToString .= ($addressToString && $address['state'] ? ', ' : '').$address['state'];

                $shippingAddress[$address['id']] = $addressToString;
            }
        }
        // Determine the type of payment
        if ($type == 'buy_now') {
            $price = $auction->buy_now_price;
        } elseif($type == 'won') {
            $price = $auction->current_bid;
        } else {
            $this->redirect($redirectPath);
        }

        $providers = PaymentProviders::model()->findAll('is_active = 1');
        $providers = CArray::flipByField($providers, 'code');
        if(!in_array($paymentMethod, array_keys($providers))){
            $alert = A::t('app', 'Input incorrect parameters');
            $alertType = 'error';
        }

        if(!empty($alertType)){
            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
            $this->redirect($redirectPath);
        }

        CLoader::library('ipgw/PaymentProvider.php');
        $provider = PaymentProvider::init($paymentMethod);
        $providerSettings = PaymentProviders::model()->find("code = :code", array(':code'=>$paymentMethod));
        $providerSettingsId = $providerSettings->id;
        if($member->country_code){
            $taxes       = $this->_getTaxesForCountry($member->country_code);
            $totalTax    = $this->_calculationTaxes($price, $member->country_code);
        }
        $grandTotal = $price + $totalTax;

        if(!empty($taxes)){
            foreach($taxes as $tax){
                $vatPercent += $tax['percent'];
                $name = $tax['name'];
                $percent = $tax['percent'];
                $taxesPrice = $price * ($percent * 0.01);
                $arrVatInfo[] = array('name'=>$name, 'percent'=>$percent, 'price'=>round($taxesPrice, 2));
            }
        }

        $lastAuctionOrderNumber =  A::app()->getSession()->get('lastAuctionOrderNumber');
        //Create or Update Order
        if(empty($lastAuctionOrderNumber) || ($type == 'won' && empty($lastAuctionOrderNumber))){
            $order = new Orders();

            $order->order_number   	    = CHash::getRandomString(10, array('case' => 'upper'));
            $order->order_type   	    = 1;//Auction
            $order->order_description   = A::t('auctions', 'Buying An Auction');
            $order->order_price 	    = $price;
            $order->vat_percent 	    = $vatPercent;
            $order->vat_fee 		    = $totalTax;
            $order->vat_fee_info 	    = !empty($arrVatInfo) ? serialize($arrVatInfo) : '';
            $order->total_price 	    = $grandTotal;
            $order->currency 		    = $currencyCode;
            $order->auction_id          = $auction->id;
            $order->member_id 		    = $member->id;
            $order->transaction_number  = '';
            $order->created_at 	        = CLocale::date('Y-m-d H:i:s');
            $order->payment_id 		    = $providerSettingsId;
            $order->payment_method 	    = 0;
            $order->cc_type             = '';
            $order->cc_holder_name      = '';
            $order->cc_number           = '';
            $order->cc_expires_month    = '';
            $order->cc_expires_year     = '';
            $order->cc_cvv_code         = '';
            $order->status 			    = 0;
            $order->status_changed 	    = CLocale::date('Y-m-d H:i:s');;
            $order->email_sent          = 0;

            A::app()->getSession()->set('lastAuctionOrderNumber', $order->order_number);
        }else{
            $order = Orders::model()->find('order_number = :order_number AND order_type = 1', array(':order_number' => $lastAuctionOrderNumber));
            //If not exists order in database redirect orders/checkout/
            if(!$order){
                A::app()->getSession()->remove('lastAuctionOrderNumber');
                $alert = A::t('auctions', 'Order cannot be found in the database');
                $alertType = 'error';
                A::app()->getSession()->setFlash('alert', $alert);
                A::app()->getSession()->setFlash('alertType', $alertType);
                $this->redirect($redirectPath);
            }elseif($order->auction_id != $auction->id || $order->payment_id != $providerSettingsId){
                $order->order_description   = A::t('auctions', 'Buying An Auction');
                $order->order_price 	    = $price;
                $order->vat_percent 	    = $vatPercent;
                $order->vat_fee 		    = $totalTax;
                $order->vat_fee_info 	    = !empty($arrVatInfo) ? serialize($arrVatInfo) : '';
                $order->total_price 	    = $grandTotal;
                $order->currency 		    = $currencyCode;
                $order->auction_id          = $auction->id;
                $order->member_id 		    = $member->id;
                $order->transaction_number  = '';
                $order->created_at 	        = date('Y-m-d H:i:s');
                $order->payment_id 		    = $providerSettingsId;
                $order->payment_method 	    = 0;
                $order->status 			    = 0;
                $order->status_changed 	    = CLocale::date('Y-m-d H:i:s');
                $order->email_sent          = 0;
            }
        }

        $params = array(
            'merchant_id' => $providerSettings->merchant_id,
            'item_name'     => $auction->auction_name,
            'item_number'   => $auction->id,
            'amount'        => $order->total_price,
            'custom'        => $order->order_number,      // order ID
            // The rm variable takes effect only if the return variable is set.
            'currency_code' => $currencyCode,   // The currency of the payment. The default is USD.
            'no_shipping'   => '',      // Do not prompt buyers for a shipping address.
            'address1'      => $member->address,
            'address2'      => $member->address_2,
            'city'          => $member->city,
            'zip'           => $member->zip_code,
            'state'         => $member->state,
            'country'       => $member->country_code,
            'first_name'    => $member->first_name,
            'last_name'     => $member->last_name,
            'email'         => $member->email,
            'phone'         => $member->phone,
            'mode'          => $providerSettings->mode,
            'back'          => $redirectPath,       // Back to Auctions
            'notify'        => A::app()->getRequest()->getBaseUrl().'paymentProviders/handlePayment/provider/'.$paymentMethod.'/handler/orders/module/auctions', // IPN processing link
            'cancel'        => A::app()->getRequest()->getBaseUrl().$redirectPath, // Cancel order link
            'cancel_return' => A::app()->getRequest()->getBaseUrl().$redirectPath, // Cancel & return to site link
        );

        $form = $provider->drawPaymentForm($params);

        if(!$order->save()){
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('auctions', 'The error occurred while adding new record!');
                $alert .= (APPHP_MODE == 'debug') ? '<br>'.CDatabase::init()->getErrorMessage() : '';
                $alertType = 'error';
            }

            A::app()->getSession()->setFlash('alert', $alert);
            A::app()->getSession()->setFlash('alertType', $alertType);
            $this->redirect($redirectPath);
        }

        $this->_view->price             = $price;
        $this->_view->grandTotal        = $grandTotal;
        $this->_view->totalTax          = $totalTax;
        $this->_view->taxes             = $taxes;
        $this->_view->order             = $order;
        $this->_view->actionMessage     = $actionMessage;
        $this->_view->form              = $form;
        $this->_view->member            = $member;
        $this->_view->auction           = $auction;
        $this->_view->providerSettings  = $providerSettings;
        $this->_view->shippingSelect    = $shippingSelect;
        $this->_view->shippingAddress   = $shippingAddress;
        $this->_view->parentCategories  = AuctionsComponent::getParentCategories($auction->category_id);
        $this->_view->render('checkout/auctionPaymentForm');
    }

    /**
     * Edit patient order action handler
     * @param int $provider
     */
    public function completeAction($provider = '')
    {
        // block access to this controller for not-logged members
        CAuth::handleLogin('members/login', 'member');
        // set meta tags according to active language
        Website::setMetaTags(array('title'=>A::t('auctions', 'Orders Complete')));
        // set frontend settings
        Website::setFrontend();

        $memberId = CAuth::getLoggedRoleId();
        $redirectPath   = 'packages/packages';
        $member         = AuctionsComponent::checkRecordAccess($memberId, 'Members', true, $redirectPath);
        $emailAlert      = '';
        $emailAlertType  = '';
        $alert           = A::app()->getSession()->getFlash('alert');
        $alertType       = A::app()->getSession()->getFlash('alertType');
        $lastPackageOrderNumber =  A::app()->getSession()->get('lastPackageOrderNumber');
        $allPaymentTypes = array();
        $allPayments     = PaymentProviders::model()->findAll('is_active = 1');

        if(!empty($allPayments) && is_array($allPayments)){
            foreach($allPayments as $payment){
                $allPaymentTypes[$payment['code']] = $payment['name'];
            }
        }

        if(!empty($lastPackageOrderNumber)){
            $order = Orders::model()->find('order_number = :order_number', array(':order_number'=>$lastPackageOrderNumber));
            if(!empty($order)){
                if($order->status == 1 || $order->status == 2){
                    $alert = A::t('auctions', 'The order has been placed in your system', array('{ORDER_NUMBER}'=>$order->order_number));
                    $alertType = 'success';
                    if($order->email_sent == 1){
                        $emailAlert = A::t('auctions', 'Email has been successfully sent!');
                        $emailAlertType = 'success';
                    }else{
                        $emailAlert = A::t('auctions', 'Email not sent!');
                        $emailAlertType = 'error';
                    }
                }else{
                    if(APPHP_MODE == 'debug'){
                        $alert = A::t('auctions', 'Order number {ORDER_NUMBER} is not found', array('{ORDER_NUMBER}'=>$lastPackageOrderNumber));
                    }else{
                        $alert = A::t('auctions', 'Cannot complete your order! Please try again later.');
                    }
                    $alertType = 'error';
                }
            }
            A::app()->getSession()->remove('lastPackageOrderNumber');
        }

        if(empty($alert)){
            $this->redirect('members/dashboard');
        }

        $this->_view->actionMessage = CWidget::create('CMessage', array($alertType, $alert));
        if(!empty($emailAlert)){
            $this->_view->emailMessage = CWidget::create('CMessage', array($emailAlertType, $emailAlert));
        }else{
            $this->_view->emailMessage = '';
        }
        $this->_view->namePayment   = isset($allPaymentTypes[$provider]) ? $allPaymentTypes[$provider] : A::t('auctions', 'Orders');
        $this->_view->render('checkout/complete');
    }

    /**
     * Get all taxes for member
     * @param string $countryCode
     * @return array
     */
    private function _getTaxesForCountry($countryCode = '')
    {
        $arrTaxPercent = array();

        // Search all global discounts
        $allTaxes = Taxes::model()->findAll('is_active = 1');
        if(!empty($allTaxes)){
            foreach($allTaxes as $oneTax){
                $arrTaxPercent[$oneTax['id']] = $oneTax;
            }
        }

        // Search for discounts on specific country
        $tableTaxCountries = CConfig::get('db.prefix').TaxCountries::model()->getTableName();
        $taxesForCountry = TaxCountries::model()->findAll($tableTaxCountries.'.country_code = :country_code', array(':country_code'=>$countryCode));
        if(!empty($taxesForCountry)){
            foreach($taxesForCountry as $oneTaxCountry){
                $taxId = $oneTaxCountry['tax_id'];
                if(isset($arrTaxPercent[$taxId])){
                    $arrTaxPercent[$taxId]['percent'] = $oneTaxCountry['percent'];
                }
            }
        }

        return $arrTaxPercent;
    }

    /**
     * Calculation Taxes
     * @return array $providers
     */
    private function _getPaymentProviders()
    {
        $providers = array();
        $paymentProviders = PaymentProviders::model()->findAll('is_active = 1');

        if(is_array($paymentProviders)){
            foreach($paymentProviders as $key => $paymentProvider){
                $providers[$paymentProvider['code']] = $paymentProvider['name'];
            }
        }

        return $providers;
    }

    /**
     * Calculation Taxes
     * @param float $price
     * @param string $countryCode
     * @return float
     */
    private function _calculationTaxes($price = 0.00, $countryCode = 'US')
    {
        $taxValue = 0.00;
        $arrTaxPercent = array();

        $arrTaxPercent = $this->_getTaxesForCountry($countryCode);

        // The calculation of the tax for the country
        if(!empty($arrTaxPercent)){
            foreach($arrTaxPercent as $taxPercent){
                $taxValue += ($price * $taxPercent['percent']) / 100;
            }
        }

        return round($taxValue, 2);
    }

    /*
	 * @param array $ccParams
	 * @return array
	 * */
    private function _validationCreditCard($ccParams = array())
    {
        $arrError = array(
            'error' => 1,
            'errorMessage' => '',
            'errorField' => ''
        );
        $cards = array(
            array('name' => 'Visa', 'length' => '13,16', 'prefixes' => '4', 'checkdigit' => true, 'test' => '4111111111111111'),
            array('name' => 'MasterCard', 'length' => '16', 'prefixes' => '51,52,53,54,55', 'checkdigit' => true, 'test' => '5555555555554444'),
            array('name' => 'American Express', 'length' => '15', 'prefixes' => '34,37', 'checkdigit' => true, 'test' => '371449635398431'),
            array('name' => 'Discover', 'length' => '16', 'prefixes' => '6011,622,64,65', 'checkdigit' => true, 'test' => '6011111111111117')
        );

        // check card holder's name
        if(trim($ccParams['cc_holder_name']) == ''){
            $arrError['errorMessage'] = A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('app', 'Card Holder\'s Name'))) ;
            $arrError['errorField'] = 'cc_holder_name';

            return $arrError;
        }

        // define card type
        $ccType = -1;
        for($i = 0; $i < count($cards); $i++){
            if(strtolower($ccParams['cc_type']) == strtolower($cards[$i]['name'])){
                $ccType = $i;
                break;
            }
        }
        if($ccType == -1){
            $arrError['errorMessage'] = A::t('auctions', 'Unknown Card Type');
            $arrError['errorField'] = 'cc_type';
            return $arrError;
        }
        if(strlen($ccParams['cc_number']) == 0){
            $arrError['errorMessage'] = A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('app', 'Credit Card Number'))) ;
            $arrError['errorField'] = 'cc_number';

            return $arrError;
        };
        $ccNumber = str_replace(array(' ', '-'), '', $ccParams['cc_number']);

        // Check that the number is numeric and of the right sort of length.
        if(!preg_match('/^[0-9]{13,19}$/i',$ccNumber)){
            $arrError['errorMessage'] = A::t('auctions', 'Card Invalid Format');
            $arrError['errorField'] = 'cc_number';

            return $arrError;
        }

        // Check that the number is not a test number
        if(($ccParams['mode'] == 'real') && ($cards[$ccType]['test'] == $ccNumber)){
            $arrError['errorMessage'] = A::t('auctions', 'Card Invalid Number');
            $arrError['errorField'] = 'cc_number';

            return $arrError;
        }

        // check the modulus 10 check digit - if required
        if($cards[$ccType]['checkdigit']){
            $checksum = 0;     // checksum total
            $j = 1;

            // handle each digit starting from the right
            for($i = strlen($ccNumber) - 1; $i >= 0; $i--){
                $calc = $ccNumber[$i] * $j;
                // if the result is in two digits add 1 to the checksum total
                if($calc > 9){
                    $checksum = $checksum + 1;
                    $calc = $calc - 10;
                }
                $checksum = $checksum + $calc;
                // switch j
                $j = ($j == 1 ? 2 : 1);
            }

            // if checksum is divisible by 10, it is a valid modulus 10 oe error occured
            if($checksum % 10 != 0){
                $arrError['errorMessage'] = A::t('auctions', 'Card Invalid Number');
                $arrError['errorField'] = 'cc_number';

                return $arrError;
            }
        }

        // prepare array with the valid prefixes for this card
        $prefix = explode(',', $cards[$ccType]['prefixes']);

        // check if any of them match what we have in the card number
        $isPrefixValid = false;
        for ($i = 0; $i < count($prefix); $i++) {
            $exp = '^'.$prefix[$i];
            if(preg_match('/'.$exp.'/i',$ccNumber)) {
                $isPrefixValid = true;
                break;
            }
        }

        // if there is no valid prefix the length is wrong
        if(!$isPrefixValid){
            $arrError['errorMessage'] = A::t('auctions', 'Card Wrong Length');
            $arrError['errorField'] = 'cc_number';

            return $arrError;
        }

        // check if the length is valid
        $isLengthValid = false;
        $lengths = explode(',',$cards[$ccType]['length']);
        for($j = 0; $j < count($lengths); $j++){
            if(strlen($ccNumber) == $lengths[$j]){
                $isLengthValid = true;
                break;
            }
        }

        if(!$isLengthValid){
            $arrError['errorMessage'] = A::t('auctions', 'Card Invalid Number');
            $arrError['errorField'] = 'cc_number';

            return $arrError;
        }

        // check expire date
        if($ccParams['cc_expires_year'].$ccParams['cc_expires_month'] < LocalTime::currentDate('Ym')){
            $arrError['errorMessage'] = A::t('auctions', 'Card Wrong Expires Date');
            $arrError['errorField'] = 'cc_expires_month';

            return $arrError;
        }

        // check cvv number
        if($ccParams['cc_cvv_code'] == ''){
            $arrError['errorMessage'] = A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('app', 'CVV Code')));
            $arrError['errorField'] = 'cc_cvv_code';

            return $arrError;
        }

        // The credit card is in the required format.
        return array(
            'error' => 0,
            'errorMessage' => '',
            'errorField' => ''
        );
    }
}
