<?php
    $this->_activeMenu = 'orders/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Orders Management'), 'url'=>'orders/manage'),
        array('label'=>A::t('auctions', 'Edit Order')),
    );
?>

<h1><?= A::t('auctions', 'Orders Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <?= $subTabs; ?>

    <div class="content">
        <?php
        echo $actionMessage;

        $fields = array();
        $showDataForm = true;
        $showInvoice = false;

        switch($subTabName):
            case 'invoice':
                $showDataForm = false;
                $showInvoice = true;

                break;

            case 'general':
            default:

                $fields = array();

                if ($order->order_type == 1):
                    $fields['auction_id']        = array('type'=>'label',  'title'=>A::t('auctions', 'Auction'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$arrAuctions, 'htmlOptions'=>array());
                else:
                    $fields['package_id']        = array('type'=>'label',  'title'=>A::t('auctions', 'Package'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$arrPackages, 'htmlOptions'=>array());
                endif;
                $fields['member_name']       = array('type'=>'label',  'title'=>A::t('auctions', 'Member'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'htmlOptions'=>array());
                $fields['order_number']      = array('type'=>'label',  'title'=>A::t('auctions', 'Order Number'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'htmlOptions'=>array());
                $fields['payment_id']        = array('type'=>'label',  'title'=>A::t('auctions', 'Payment Type'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$arrPaymentProviders, 'htmlOptions'=>array());
                $fields['payment_method']    = array('type'=>'label',  'title'=>A::t('auctions', 'Payment Method'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>$arrPaymentMethods, 'htmlOptions'=>array());
                $fields['description']       = array('type'=>'label',  'title'=>A::t('auctions', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'htmlOptions'=>array());
                $fields['status']            = array('type'=>'select', 'title'=>A::t('app', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($allStatus)), 'data'=>$allStatus, 'htmlOptions'=>array());
                $fields['created_at']        = array('type'=>'label',  'title'=>A::t('auctions', 'Created at'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>$unknown, null=>$unknown), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat);
                $fields['payment_date']      = array('type'=>'label',  'title'=>A::t('auctions', 'Payment Date'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>$unknown, null=>$unknown), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat);
                $fields['status_changed']    = array('type'=>'label',  'title'=>A::t('auctions', 'Status Changed'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(''=>$unknown, null=>$unknown), 'htmlOptions'=>array(), 'format'=>$dateTimeFormat);
                $fields['format_price']       = array('type'=>'label',   'title'=>A::t('auctions', 'Price'), 'tooltip'=>'', 'default'=>'', 'validation'=>array(), 'definedValues'=>array(), 'htmlOptions'=>array());

                break;
        endswitch;

        if($showDataForm):
            echo CWidget::create('CDataForm', array(
                'model'=>'Modules\Auctions\Models\Orders',
                'primaryKey'=>$id,
                'operationType'=>'edit',
                'action'=>'orders/edit/id/'.$id,
                'successUrl'=>'orders/manage/orderType/'.$order->order_type,
                'cancelUrl'=>'orders/manage/orderType/'.$order->order_type,
                'passParameters'=>false,
                'method'=>'post',
                'htmlOptions'=>array(
                    'id'                => 'frmOrderPreview',
                    'name'              => 'frmOrderPreview',
                    'enctype'           => 'multipart/form-data',
                    'autoGenerateId'    => true
                ),
                'requiredFieldsAlert'=>true,
                'fields'    => $fields,
                'buttons'=>array(
                    'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                    'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                    'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                ),
                'messagesSource' 	=> 'core',
                'showAllErrors'     => false,
                'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Order')),
                'return'            => true,
            ));
        endif;
        ?>

        <?php if($showInvoice): ?>
            <div class="invoice-box">
                <div class="buttons-wrapper bw-bottom">
                    <a href="orders/downloadInvoice/orderId/<?= $id; ?>" class="export-data align-right"><b class="icon-export">&nbsp;</b> <?= A::t('auctions', 'Download Invoice'); ?></a>
                </div>

                <table class="pb10">
                    <tr>
                        <td class="title" colspan="2"><?= A::t('auctions', 'General'); ?>:</td>
                    </tr>
                    <tr>
                        <td><?= $order->order_type == 1 ? A::t('auctions', 'Auction') : A::t('auctions', 'Package'); ?>: </td><td><a href="<?= $order->order_type == 1 ? 'auctions/manage?id='.$order->auction_id.'&but_filter=Filter' : 'packages/manage?id='.$order->package_id.'&but_filter=Filter'; ?>" rel="noopener noreferrer"><?= $orderName; ?></a></td>
                    </tr>
                    <tr>
                        <td width="30%"><?= A::t('auctions', 'Order Number'); ?>: </td><td><?= $order->order_number; ?></td>
                    </tr>
                    <tr>
                        <td><?= A::t('app', 'Status'); ?>: </td><td><?= isset($allStatus[$order->status]) ? $allStatus[$order->status] : $unknown; ?></td>
                    </tr>
                    <tr>
                        <td><?= A::t('auctions', 'Created at'); ?>: </td><td><?= CLocale::date($dateTimeFormat, $order->created_at); ?></td>
                    </tr>
                    <tr>
                        <td><b><?= A::t('auctions', 'Grand Total'); ?>: </b></td><td><b><?= $beforePrice.CNumber::format($order->total_price, $numberFormat, array('decimalPoints'=>2)).$afterPrice; ?></b></td>
                    </tr>
                </table>

                <?php if(!empty($member)): ?>
                    <table class="pb10">
                        <tr>
                            <td class="title" colspan="2"><?= A::t('auctions', 'Member'); ?>:</td>
                        </tr>
                        <tr>
                            <td width="30%"><?= A::t('auctions', 'First Name'); ?>: </td><td><?= $member->first_name; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Last Name'); ?>: </td><td><?= $member->last_name; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Email'); ?>: </td><td><?= $member->email; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Phone'); ?>: </td><td><?= ($member->phone ? $member->phone : $unknown); ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Address'); ?>: </td><td><?= $member->address; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'City'); ?>: </td><td><?= ($member->city ? $member->city : ''); ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Zip Code'); ?>: </td><td><?= ($member->zip_code ? $member->zip_code : ''); ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'State/Province'); ?>: </td><td><?= (isset($arrStateNames[$member->state]) ? $member->state.' ('.$arrStateNames[$member->state].')' : $member->state); ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('auctions', 'Country'); ?>: </td><td><?= (isset($arrCountryNames[$member->country_code]) ? $arrCountryNames[$member->country_code] : $unknown); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>

                <table class="pb10">
                    <tr>
                        <td class="title" colspan="2"><?= A::t('auctions', 'Payment'); ?>:</td>
                    </tr>
                    <tr>
                        <td width="30%"><?= A::t('auctions', 'Payment Type'); ?>: </td><td><?= isset($arrPaymentProviders[$order->payment_id]) ? $arrPaymentProviders[$order->payment_id] : $unknown; ?></td>
                    </tr>
                    <tr>
                        <td><?= A::t('auctions', 'Payment Method'); ?>: </td><td><?= isset($arrPaymentMethods[$order->payment_method]) ? $arrPaymentMethods[$order->payment_method] : $unknown; ?></td>
                    </tr>
                    <tr>
                        <td><?= A::t('auctions', 'Payment Date'); ?>: </td><td><?= ! CTime::isEmptyDateTime($order->payment_date) ? CLocale::date($dateTimeFormat, $order->payment_date) : $unknown; ?></td>
                    </tr>
                    <tr>
                        <td><?= A::t('auctions', 'Transaction ID'); ?>: </td><td><?= $order->transaction_number ? $order->transaction_number : '--'; ?></td>
                    </tr>
                </table>
                <?php if($order->payment_method == 1): ?>
                    <table class="pb10">
                        <tr>
                            <td class="title" colspan="2"><?= A::t('auctions', 'Credit Card'); ?>:</td>
                        </tr>
                        <tr>
                            <td width="30%"><?= A::t('app', 'Credit Card Type'); ?>: </td><td><?= isset($arrCCType[$order->cc_type]) ? $arrCCType[$order->cc_type] : $unknown; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('app', 'Card Holder\'s Name'); ?>: </td><td><?= $order->cc_holder_name ? $order->cc_holder_name : $unknown; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('app', 'Credit Card Number'); ?>: </td><td><?= $order->cc_number ? $order->cc_number : $unknown; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('app', 'Expires Month'); ?>: </td><td><?= $order->cc_expires_month ? $order->cc_expires_month : $unknown; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('app', 'Expires Year'); ?>: </td><td><?= $order->cc_expires_year ? $order->cc_expires_year : $unknown; ?></td>
                        </tr>
                        <tr>
                            <td><?= A::t('app', 'CVV Code'); ?>: </td><td><?= $order->cc_cvv_code ? $order->cc_cvv_code : $unknown; ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>






        <?php endif; ?>
    </div>
</div>