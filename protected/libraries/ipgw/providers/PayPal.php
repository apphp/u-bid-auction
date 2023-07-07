<?php
/**
 * This file implements PayPalStandard payment provider functionality
 *
 * Usage:
 * SEE: https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
 * 
 * CLoader::library('ipgw/PaymentProvider.php');
 * $payPal = PaymentProvider::init('paypal_standard');
 * echo $payPal->drawPaymentForm(array(
 *		'merchant_id' 	=> 'sales@example.com',
 *		'form_type'		=> ''		// 'multiple' or ''
 *		'item_name' 	=> 'Item Name',
 *		'item_number' 	=> 'Item Number',
 *		'amount'		=> 9.90,
 *		'custom'		=> '', 		// order ID
 *		'lc'			=> '', 		// country's language  
 *		'cn'			=> '', 		// If this variable is omitted, the default label above the note field is "Add special instructions to merchant."
 *		'rm'			=> '', 		// Return method. 0 - all shopping cart payments use the GET method, 1 - the buyer's browser is redirected to the return URL by using the GET method, but no payment variables are included, 2 - the buyer's browser is redirected to the return URL by using the POST method, and all payment variables are included
 *							   		// The rm variable takes effect only if the return variable is set.
 *		'currency_code'	=> 'USD', 	// The currency of the payment. The default is USD.
 *		'no_shipping'	=> '', 		// Do not prompt buyers for a shipping address.
 *		'address1'		=> 'st. Big Street, 1',
 *		'address2'		=> '',	
 *		'city'			=> 'New York',	
 *		'zip'			=> '1001',
 *		'state'			=> 'New York',	
 *		'country'		=> 'us',
 *		'first_name'	=> 'John',
 *		'last_name'		=> 'Smith',
 *		'email'			=> 'j.smith@example.com',
 *		'phone'			=> '12345678',
 *
 *		'mode'			=> 1,		// 1- Real mode, 0 - Test mode
 *		'notify'		=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/handlePayment/paypal/model/module',	// IPN processing link
 *		'cancel'		=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/testCheckout',						// Cancel order link
 *		'cancel_return'	=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/testCheckout',						// Cancel & return to site link
 *		'back'			=> '',		// Back to Shopping Cart - defined by developer
 * )); 
 * 
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		        
 * ---------------         	---------------            	---------------
 * drawPaymentForm
 * handlePayment
 *
 */	  

class PayPalStandard extends PaymentGateway
{
	/** @str */
	const NL = "\n";
		
	/**
	 * Draws payment form
	 * @param array $params
	 */
	public function drawPaymentForm($params = array())
	{
		$output 			= '';		
		$mode 				= isset($params['mode']) ? $params['mode'] : 1;

		$formAction 		= $mode == 1 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$formType 			= isset($params['form_type']) ? $params['form_type'] : '';
		$business 			= isset($params['merchant_id']) ? $params['merchant_id'] : '';
		$itemName 			= isset($params['item_name']) ? $params['item_name'] : '';
		$itemNumber 		= isset($params['item_number']) ? $params['item_number'] : '001';
		$amount 			= isset($params['amount']) ? $params['amount'] : '0';
		$custom 			= isset($params['custom']) ? $params['custom'] : '';
		$lc					= isset($params['lc']) ? $params['lc'] : 'US';
		$cn					= isset($params['cn']) ? $params['cn'] : '';
		$rm					= isset($params['rm']) ? $params['rm'] : '1';
		$currencyCode		= isset($params['currency_code']) ? $params['currency_code'] : 'USD';
		$noShipping			= isset($params['no_shipping']) ? $params['no_shipping'] : '1';

		$address1			= isset($params['address1']) ? $params['address1'] : '';
		$address2			= isset($params['address2']) ? $params['address2'] : '';
		$city				= isset($params['city']) ? $params['city'] : '';
		$zip				= isset($params['zip']) ? $params['zip'] : '';
		$state				= isset($params['state']) ? $params['state'] : '';
		$country			= isset($params['country']) ? $params['country'] : '';
		
		$firstName			= isset($params['first_name']) ? $params['first_name'] : '';
		$lastName			= isset($params['last_name']) ? $params['last_name'] : '';
		$email				= isset($params['email']) ? $params['email'] : '';
		$phone				= isset($params['phone']) ? $params['phone'] : '';
		
		$notifyUrl			= isset($params['notify']) ? $params['notify'] : '';
		$returnUrl			= isset($params['return']) ? $params['return'] : '';
		$cancelReturnUrl	= isset($params['cancel_return']) ? $params['cancel_return'] : '';				
		$backUrl			= isset($params['back']) ? $params['back'] : '';
		
		$output .= CHtml::openForm($formAction, 'post', array('name'=>'payform')).self::NL;
		$output .= CHtml::hiddenField('business', $business).self::NL;
		if($formType == 'multiple'){
		//	$output .= $nl.draw_hidden_field('cmd', '_cart', false);
		//	$output .= $nl.draw_hidden_field('upload', '1', false);
		//	$output .= $pp_params['paypal_form_fields'];
		//
		//	if($pp_params['extras_sub_total'] > 0){
		//		$pp_params['paypal_form_fields_count']++;					
		//		$output .= $nl.draw_hidden_field('item_name_'.$pp_params['paypal_form_fields_count'], _EXTRAS, false);
		//		$output .= $nl.draw_hidden_field('quantity_'.$pp_params['paypal_form_fields_count'], '1', false);
		//		$output .= $nl.draw_hidden_field('amount_'.$pp_params['paypal_form_fields_count'], number_format($pp_params['extras_sub_total'], '2', '.', ','), false);
		//	}									
		//	if($pp_params['vat_cost'] > 0){
		//		$pp_params['paypal_form_fields_count']++;
		//		$output .= $nl.draw_hidden_field('item_name_'.$pp_params['paypal_form_fields_count'], _VAT, false);
		//		$output .= $nl.draw_hidden_field('quantity_'.$pp_params['paypal_form_fields_count'], '1', false);
		//		$output .= $nl.draw_hidden_field('amount_'.$pp_params['paypal_form_fields_count'], number_format($pp_params['vat_cost'], '2', '.', ','), false);
		//	}
		}else{
			$output .= CHtml::hiddenField('cmd', '_xclick').self::NL;
			$output .= CHtml::hiddenField('item_name', $itemName).self::NL;
			$output .= CHtml::hiddenField('item_number', $itemNumber).self::NL;
			$output .= CHtml::hiddenField('amount', round($amount, 2)).self::NL;			
		}
		$output .= CHtml::hiddenField('currency_code', $currencyCode).self::NL;
		$output .= CHtml::hiddenField('custom', $custom).self::NL;
		$output .= CHtml::hiddenField('lc', $lc).self::NL;
		$output .= CHtml::hiddenField('cn', $cn).self::NL;
		$output .= CHtml::hiddenField('rm', $rm).self::NL;
		$output .= CHtml::hiddenField('no_shipping', $noShipping).self::NL;
		$output .= CHtml::hiddenField('address_override', '0').self::NL;

		$output .= CHtml::hiddenField('address1', $address1).self::NL;
		$output .= CHtml::hiddenField('address2', $address2).self::NL;
		$output .= CHtml::hiddenField('city', $city).self::NL;
		$output .= CHtml::hiddenField('zip', $zip).self::NL;
		$output .= CHtml::hiddenField('state', $state).self::NL;
		$output .= CHtml::hiddenField('country', $country).self::NL;
		
		$output .= CHtml::hiddenField('first_name', $firstName).self::NL;
		$output .= CHtml::hiddenField('last_name', $lastName).self::NL;
		$output .= CHtml::hiddenField('email', $email).self::NL;
		
		$phoneParts = explode('-', $phone);
		if(isset($phoneParts[0])) $output .= CHtml::hiddenField('night_phone_a', $phoneParts[0]).self::NL;
		if(isset($phoneParts[1])) $output .= CHtml::hiddenField('night_phone_b', $phoneParts[1]).self::NL;
		if(isset($phoneParts[2])) $output .= CHtml::hiddenField('night_phone_c', $phoneParts[2]).self::NL;
		
		$output .= CHtml::hiddenField('notify', $notifyUrl).self::NL;
		$output .= CHtml::hiddenField('return', $returnUrl).self::NL;
		$output .= CHtml::hiddenField('cancel_return', $cancelReturnUrl).self::NL;
		
		//$output .= $nl.draw_hidden_field('bn', 'PP-BuyNowBF', false);

		//$output .= $nl.'<br />'._PAYPAL_NOTICE.'<br /><br />';
		$output .= '<input type="image" src="images/payment_providers/btn_pp_paynow.gif" title="" value="'.A::te('app', 'Go To Payment').'" name="btnSubmit" />'.self::NL;
		if((!empty($backUrl))){
			$output .= '&nbsp; - '.A::t('app', 'or').' - &nbsp;'.self::NL;
			$output .= (!empty($backUrl)) ? '<a href="'.$backUrl.'">'.A::t('app', 'Back').'</a>' : '';
		}
		
		$output .= CHtml::closeForm().self::NL;

		// If enabled test mode add test button in the payment form.
		if (APPHP_MODE == 'debug' && $mode == 0) {
            $output .= CHtml::openForm($notifyUrl, 'post', array('name'=>'testPayForm')).self::NL;
            $output .= '<div class="test-pay-form">'.self::NL;
            $output .= '<h3>'.A::t('core', 'Emulation IPN Response').'</h3>'.self::NL;
            $output .= CHtml::hiddenField('custom', $custom).self::NL;
            $output .= CHtml::hiddenField('txn_id', CHash::getRandomString(20)).self::NL;
            $output .= CHtml::hiddenField('payment_status', 'completed').self::NL;
            $output .= CHtml::submitButton(A::t('core', 'Test Payment'), array('name'=>'tesPayButton', 'class'=>'btn v-btn v-third-dark v-small-button')).self::NL;
            $output .= '</div>'.self::NL;
            $output .= CHtml::closeForm().self::NL;
        }
		
		return $output;
	}
	
	/**
	 * Handles payment
	 * @param array $params
	 * @return array
	 */
	public function handlePayment($params)
	{
		$return = array('error' => 0, 'message' => '', 'order' => array());

		$this->_logMode = !empty($params['log']) ? (bool)$params['log'] : false;
		
		// 'file' - default or 'screen'
		$this->_logTo = 'file';
		$this->_logData = '';
		
		$cRequest = A::app()->getRequest();
		
        $nl         = "\n";
        $msg        = '';
        $logData    = '';
        $alert      = '';
        $alertType  = '';

        // Get PayPalStandard response
        $status             = strtolower($cRequest->getPost('payment_status'));
        $orderNumber        = $cRequest->getPost('custom');
        $transactionNumber  = $cRequest->getPost('txn_id');
        $payerStatus        = $cRequest->getPost('payer_status');
        $paymentType        = $cRequest->getPost('payment_type');
        $paymentMethod      = $cRequest->getPost('payment_method');
        $total              = $cRequest->getPost('mc_gross');

		// Payment Methods: 0 - Payment Company Account, 1 - Credit Card, 2 - E-Check
		if($status == 'completed'){
			if($payerStatus == 'verified'){
				$paymentMethod = '0';
			}else{
				$paymentMethod = '1';
			}			
		}else{
			$paymentMethod = ($paymentType == 'echeck') ? '2' : '0'; 
		}
				
        $return['order'] = array('order_number' => $orderNumber);

		// Start log data
		// ---------------------------------------------------------------------
		if($this->_logMode){
			if($this->_logTo == 'file'){
				$myFile = 'protected/tmp/logs/payments.log';
				$fh = fopen($myFile, 'a') or die('can\'t open file');				
			}

			$logData .= $nl.$nl.'= ['.date('Y-m-d H:i:s').'] ============= PayPalStandard'.$nl;
			$logData .= '<br />---------------<br />'.$nl;
			$logData .= 'POST<br />'.$nl;
			foreach($_POST as $key=>$value) {
				$logData .= $key.'='.$value.'<br />'.$nl;        
			}
			$logData .= '<br />---------------<br />'.$nl;
			$logData .= 'GET<br />'.$nl;
			foreach($_GET as $key=>$value) {
				$logData .= $key.'='.$value.'<br />'.$nl;        
			}        
		}

        // ---------------------------------------------------------------------
        switch($status){
            // 1 order pending
            case 'pending':
                $alert = 'Pending Payment - '.$cRequest->getPost('pending_reason');
                break;

            case 'completed':
                $paymentDate = strtotime($cRequest->getPost('payment_date'));
                $paymentDate = !empty($paymentDate) ? $paymentDate : time();
                $return['order'] = array(
                    'order_number' => $orderNumber,
                    'transaction_number' => $transactionNumber,
                    'payment_type' => $paymentType,
                    'payment_method' => $paymentMethod,
                    'payment_date' => date('Y-m-d H:i:s', $paymentDate)
                );
                $alert = 'Completed';
                break;

            case 'updated':
                // 3 updated already
                $alert = 'Thank you for your order!<br /><br />';
                $alertType = 'success';
                break;

            case 'failed':
                // 4 this will only happen in case of echeck.
                $alert = 'Payment Failed';
                break;

            case 'denied':
                // 5 denied payment by u
                $alert = 'Payment Denied';
                break;

            case 'refunded':
                // 6 payment refunded by u
                $alert = 'Payment Refunded';
                break;

            case 'canceled':
                /* 7 reversal cancelled
                 mark the payment as dispute cancelled */
                $alert = 'Cancelled reversal';
                break;

            default:
                // 0 order is not good
                $alert = 'Unknown Payment Status - please try again.';
                // . $objPaymentIPN->GetPaymentStatus();
                break;
        }

        if($status != 'completed'){
            $return['error'] = 99;
        }
        $return['message'] = $alert;

		// Save log data
        // ---------------------------------------------------------------------
        if($this->_logMode){
            $logData .= '<br />'.$nl.$alert.'<br />'.$nl;
            if($this->_logTo == 'file'){
                fwrite($fh, strip_tags($logData));
                fclose($fh);
            }else{
                echo $logData;
            }
        }
        // ---------------------------------------------------------------------

        return $return;
    }
}


// =============================================================================
// =============================================================================


/**
 * This file implements PayPalRecurring payment provider functionality
 *
 * Usage:
 * SEE: https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
 * 
 * CLoader::library('ipgw/PaymentProvider.php');
 * $payPal = PaymentProvider::init('paypal_recurring');
 * echo $payPal->drawPaymentForm(array(
 *		'merchant_id' 	=> 'sales@example.com',
 *		'form_type'		=> ''		// 'multiple' or ''
 *		'item_name' 	=> 'Item Name',
 *		'item_number' 	=> 'Item Number',
 *		'amount'		=> 9.90,
 *		'custom'		=> '', 		// order ID
 *		'lc'			=> '', 		// country's language  
 *		'cn'			=> '', 		// If this variable is omitted, the default label above the note field is "Add special instructions to merchant."
 *		'rm'			=> '', 		// Return method. 0 - all shopping cart payments use the GET method, 1 - the buyer's browser is redirected to the return URL by using the GET method, but no payment variables are included, 2 - the buyer's browser is redirected to the return URL by using the POST method, and all payment variables are included
 *							   		// The rm variable takes effect only if the return variable is set.
 *		'currency_code'	=> 'USD', 	// The currency of the payment. The default is USD.
 *		'no_shipping'	=> '', 		// Do not prompt buyers for a shipping address.
 *		'address1'		=> 'st. Big Street, 1',
 *		'address2'		=> '',	
 *		'city'			=> 'New York',	
 *		'zip'			=> '1001',
 *		'state'			=> 'New York',	
 *		'country'		=> 'us',
 *		'first_name'	=> 'John',
 *		'last_name'		=> 'Smith',
 *		'email'			=> 'j.smith@example.com',
 *		'phone'			=> '12345678',
 *
 *		'mode'			=> 1,		// 1- Real mode, 0 - Test mode
 *		'notify'		=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/handlePayment/paypal/model/module',	// IPN processing link
 *		'cancel'		=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/testCheckout',						// Cancel order link
 *		'cancel_return'	=> A::app()->getRequest()->getBaseUrl().'/paymentProviders/testCheckout',						// Cancel & return to site link
 *		'back'			=> '',		// Back to Shopping Cart - defined by developer
 * )); 
 * 
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		        
 * ---------------         	---------------            	---------------
 * drawPaymentForm
 * handlePayment
 *
 */	  

class PayPalRecurring extends PaymentGateway
{
	/** @str */
	const NL = "\n";
	
	/**
	 * Draws payment form
	 * @param array $params
	 */
	public function drawPaymentForm($params = array())
	{
		$output 			= '';		
		$mode 				= isset($params['mode']) ? $params['mode'] : 1;
		$formAction 		= $mode == 1 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$formType 			= isset($params['form_type']) ? $params['form_type'] : '';
		$business 			= isset($params['merchant_id']) ? $params['merchant_id'] : '';
		$itemName 			= isset($params['item_name']) ? $params['item_name'] : '';
		$itemNumber 		= isset($params['item_number']) ? $params['item_number'] : '001';
		$amount 			= isset($params['amount']) ? $params['amount'] : '0';
		$custom 			= isset($params['custom']) ? $params['custom'] : '';
		$lc					= isset($params['lc']) ? $params['lc'] : 'US';
		$p3					= isset($params['p3']) ? $params['p3'] : '1';
		$t3					= isset($params['t3']) ? $params['t3'] : 'Y';
		$currencyCode		= isset($params['currency_code']) ? $params['currency_code'] : 'USD';
		$noShipping			= isset($params['no_shipping']) ? $params['no_shipping'] : '1';
		
		$address1			= isset($params['address1']) ? $params['address1'] : '';
		$address2			= isset($params['address2']) ? $params['address2'] : '';
		$city				= isset($params['city']) ? $params['city'] : '';
		$zip				= isset($params['zip']) ? $params['zip'] : '';
		$state				= isset($params['state']) ? $params['state'] : '';
		$country			= isset($params['country']) ? $params['country'] : '';
		
		$firstName			= isset($params['first_name']) ? $params['first_name'] : '';
		$lastName			= isset($params['last_name']) ? $params['last_name'] : '';
		$email				= isset($params['email']) ? $params['email'] : '';
		$phone				= isset($params['phone']) ? $params['phone'] : '';
		
		$notifyUrl			= isset($params['notify']) ? $params['notify'] : '';
		$returnUrl			= isset($params['return']) ? $params['return'] : '';
		$cancelReturnUrl	= isset($params['cancel_return']) ? $params['cancel_return'] : '';				
		$backUrl			= isset($params['back']) ? $params['back'] : '';
		
		$output .= CHtml::openForm($formAction, 'post', array('name'=>'payform')).self::NL;
		$output .= CHtml::hiddenField('business', $business).self::NL;

		$output .= CHtml::hiddenField('cmd', '_xclick-subscriptions').self::NL;
		$output .= CHtml::hiddenField('item_name', $itemName).self::NL;
		$output .= CHtml::hiddenField('item_number', $itemNumber).self::NL;
		
		$output .= CHtml::hiddenField('currency_code', $currencyCode).self::NL;
		$output .= CHtml::hiddenField('custom', $custom).self::NL;
		$output .= CHtml::hiddenField('lc', $lc).self::NL;
		$output .= CHtml::hiddenField('no_shipping', $noShipping).self::NL;
		$output .= CHtml::hiddenField('address_override', '0').self::NL;

		//$output .= CHtml::hiddenField('no_note', '1').self::NL;
		//$output .= CHtml::hiddenField('tax', '0').self::NL;

		$output .= CHtml::hiddenField('address1', $address1).self::NL;
		$output .= CHtml::hiddenField('address2', $address2).self::NL;
		$output .= CHtml::hiddenField('city', $city).self::NL;
		$output .= CHtml::hiddenField('zip', $zip).self::NL;
		$output .= CHtml::hiddenField('state', $state).self::NL;
		$output .= CHtml::hiddenField('country', $country).self::NL;
		
		$output .= CHtml::hiddenField('first_name', $firstName).self::NL;
		$output .= CHtml::hiddenField('last_name', $lastName).self::NL;
		$output .= CHtml::hiddenField('email', $email).self::NL;
		
		$phoneParts = explode('-', $phone);
		if(isset($phoneParts[0])) $output .= CHtml::hiddenField('night_phone_a', $phoneParts[0]).self::NL;
		if(isset($phoneParts[1])) $output .= CHtml::hiddenField('night_phone_b', $phoneParts[1]).self::NL;
		if(isset($phoneParts[2])) $output .= CHtml::hiddenField('night_phone_c', $phoneParts[2]).self::NL;
		
		$output .= CHtml::hiddenField('notify', $notifyUrl).self::NL;
		$output .= CHtml::hiddenField('return', $returnUrl).self::NL;
		$output .= CHtml::hiddenField('cancel_return', $cancelReturnUrl).self::NL;

        $output .= 'Pay $'.$amount.' every ';

        // Regular subscription price.
        $output .= CHtml::hiddenField('a3', $amount).self::NL;
		// Recurring payments. 0 - subscription payments do not recur. 1 - subscription payments recur. (Default is 0).
        $output .= CHtml::hiddenField('src', 1).self::NL;
		// Reattempt on failure. 0 - Do not reattempt failed recurring payments. 1 - Reattempt failed recurring payments before canceling. (Default is 1),
		$output .= CHtml::hiddenField('sra', 1).self::NL;
        // Subscription duration. Specify an integer value (character length: 2)
        $output .= CHtml::dropDownList('p3', $p3, array('1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6'), array('id'=>'p3')).self::NL;
        // Regular subscription units of duration (character length: 1)
        $output .= CHtml::dropDownList('t3', $t3, array('D'=>A::t('app', 'Day/s'), 'W'=>A::t('app', 'Week/s'), 'M'=>A::t('app', 'Month/s'), 'Y'=>A::t('app', 'Year/s')), array('id'=>'t3')).self::NL.'<br /><br />';

		//$output .= $nl.'<br />'._PAYPAL_NOTICE.'<br /><br />';
		$output .= '<input type="image" src="images/payment_providers/btn_pp_subscribe.png" title="" value="'.A::te('app', 'Go To Payment').'" name="btnSubmit" />'.self::NL;
		if((!empty($backUrl))){
			$output .= '&nbsp; - '.A::t('app', 'or').' - &nbsp;'.self::NL;
			$output .= (!empty($backUrl)) ? '<a href="'.$backUrl.'">'.A::t('app', 'Back').'</a>' : '';
		}
		
		$output .= CHtml::closeForm().self::NL;
		
		return $output;
	}
	
	/**
	 * Handles payment
	 * @param array $params
	 * @return array
	 */
	public function handlePayment($params)
	{
		$return = array('error' => 0, 'message' => '', 'order' => array());

		$this->_logMode = !empty($params['log']) ? (bool)$params['log'] : false;
		
		// 'file' - default or 'screen'
		$this->_logTo = 'file';
		$this->_logData = '';
		
		$cRequest = A::app()->getRequest();
		
        $nl         = "\n";
        $msg        = '';
        $logData    = '';
        $alert      = '';
        $alertType  = '';

        // Get PayPalRecurring response
        $status             = strtolower($cRequest->getPost('payment_status'));
        $orderNumber        = $cRequest->getPost('custom');
        $transactionNumber  = $cRequest->getPost('txn_id');
        $payerStatus        = $cRequest->getPost('payer_status');
        $paymentType        = $cRequest->getPost('payment_type');
        $paymentMethod      = $cRequest->getPost('payment_method');
        $total              = $cRequest->getPost('mc_gross');

		// Payment Methods: 0 - Payment Company Account, 1 - Credit Card, 2 - E-Check
		if($status == 'completed'){
			if($payerStatus == 'verified'){
				$paymentMethod = '0';
			}else{
				$paymentMethod = '1';
			}			
		}else{
			$paymentMethod = ($paymentType == 'echeck') ? '2' : '0'; 
		}
				
        $return['order'] = array('order_number' => $orderNumber);

		// Start log data
		// ---------------------------------------------------------------------
		if($this->_logMode){
			if($this->_logTo == 'file'){
				$myFile = 'protected/tmp/logs/payments.log';
				$fh = fopen($myFile, 'a') or die('can\'t open file');				
			}

			$logData .= $nl.$nl.'= ['.date('Y-m-d H:i:s').'] ============= PayPalRecurring'.$nl;
			$logData .= '<br />---------------<br />'.$nl;
			$logData .= 'POST<br />'.$nl;
			foreach($_POST as $key=>$value) {
				$logData .= $key.'='.$value.'<br />'.$nl;        
			}
			$logData .= '<br />---------------<br />'.$nl;
			$logData .= 'GET<br />'.$nl;
			foreach($_GET as $key=>$value) {
				$logData .= $key.'='.$value.'<br />'.$nl;        
			}        
		}

        // ---------------------------------------------------------------------
        switch($status){
            // 1 order pending
            case 'pending':
                $alert = 'Pending Payment - '.$cRequest->getPost('pending_reason');
                break;

            case 'completed':
                $paymentDate = strtotime($cRequest->getPost('payment_date'));
                $paymentDate = !empty($paymentDate) ? $paymentDate : time();
                $return['order'] = array(
                    'order_number' => $orderNumber,
                    'transaction_number' => $transactionNumber,
                    'payment_type' => $paymentType,
                    'payment_method' => $paymentMethod,
                    'payment_date' => date('Y-m-d H:i:s', $paymentDate)
                );
                $alert = 'Completed';
                break;

            case 'updated':
                // 3 updated already
                $alert = 'Thank you for your order!<br /><br />';
                $alertType = 'success';
                break;

            case 'failed':
                // 4 this will only happen in case of echeck.
                $alert = 'Payment Failed';
                break;

            case 'denied':
                // 5 denied payment by u
                $alert = 'Payment Denied';
                break;

            case 'refunded':
                // 6 payment refunded by u
                $alert = 'Payment Refunded';
                break;

            case 'canceled':
                /* 7 reversal cancelled
                 mark the payment as dispute cancelled */
                $alert = 'Cancelled reversal';
                break;

            default:
                // 0 order is not good
                $alert = 'Unknown Payment Status - please try again.';
                // . $objPaymentIPN->GetPaymentStatus();
                break;
        }

        if($status != 'completed'){
            $return['error'] = 99;
        }
        $return['message'] = $alert;

        // Save log data
        // ---------------------------------------------------------------------
        if($this->_logMode){
            $logData .= '<br />'.$nl.$alert.'<br />'.$nl;
            if($this->_logTo == 'file'){
                fwrite($fh, strip_tags($logData));
                fclose($fh);
            }else{
                echo $logData;
            }
        }
        // ---------------------------------------------------------------------

        return $return;
    }
}
