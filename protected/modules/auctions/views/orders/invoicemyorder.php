<?php
$this->_pageTitle = A::t('auctions', 'Edit My Orders');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Orders'), 'url'=>'orders/myOrders/orderType/'.$order->order_type),
    array('label' => A::t('auctions', 'Edit My Orders')),
);
?>
<div class="row">
    <div class="col-sm-12">
        <div class="invoice-box">
            <a href="orders/downloadInvoice/orderId/<?= $id; ?>" class="btn v-btn v-third-dark v-small-button"><?= A::t('auctions', 'Download Invoice'); ?></a>
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="v-heading v-text-heading"><span><?= A::t('auctions', 'General'); ?></span></h3>
                    <ul>
                        <?php if ($order->order_type == 1): ?>
                            <li><?= A::t('auctions', 'Auction'); ?>: <?= $orderName; ?></li>
                        <?php else: ?>
                            <li><?= A::t('auctions', 'Package'); ?>: <?= $orderName; ?></li>
                        <?php endif; ?>
                        <li><?= A::t('auctions', 'Order Number'); ?>: <?= $order->order_number; ?></li>
                        <li><?= A::t('app', 'Status'); ?>: <?= isset($allStatus[$order->status]) ? $allStatus[$order->status] : $unknown; ?></li>
                        <li><?= A::t('auctions', 'Created at'); ?>: <?= CLocale::date($dateTimeFormat, $order->created_at); ?></li>
                        <li><b><?= A::t('auctions', 'Grand Total'); ?>: </b><b><?= $beforePrice.CNumber::format($order->total_price, $numberFormat, array('decimalPoints'=>2)).$afterPrice; ?></b></li>
                    </ul>
                </div>
            </div>
            <?php if(!empty($member)): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="v-heading v-text-heading"><span><?= A::t('auctions', 'Member'); ?></span></h3>
                        <ul>
                            <li><?= A::t('auctions', 'First Name'); ?> <?= ($member->first_name ? $member->first_name : $unknown); ?></li>
                            <li><?= A::t('auctions', 'Last Name'); ?>: <?= ($member->last_name ? $member->last_name : $unknown); ?></li>
                            <li><?= A::t('auctions', 'Email'); ?>: <?= ($member->email ? $member->email : $unknown); ?></li>
                            <li><?= A::t('auctions', 'Phone'); ?>: <?= ($member->phone ? $member->phone : $unknown); ?></li>
                            <li><?= A::t('auctions', 'Address'); ?>:   <?= ($member->address ? $member->address : $unknown); ?></li>
                            <li><?= A::t('auctions', 'City'); ?>:  <?= ($member->city ? $member->city : $unknown); ?></li>
                            <li><?= A::t('auctions', 'Zip Code'); ?>:  <?= ($member->zip_code ? $member->zip_code : $unknown); ?></li>
                            <li><?= A::t('auctions', 'State/Province'); ?> <?= (isset($arrStateNames[$member->state]) ? $member->state.' ('.$arrStateNames[$member->state].')' : $member->state); ?></li>
                            <li><?= A::t('auctions', 'Country'); ?>:   <?= (isset($arrCountryNames[$member->country_code]) ? $arrCountryNames[$member->country_code] : $unknown); ?></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="v-heading v-text-heading"><span><?= A::t('auctions', 'Payment'); ?></span></h3>
                    <ul>
                        <li><?= A::t('auctions', 'Payment Type'); ?>: <?= isset($arrPaymentProviders[$order->payment_id]) ? $arrPaymentProviders[$order->payment_id] : $unknown; ?></li>
                        <li><?= A::t('auctions', 'Payment Method'); ?>: <?= isset($arrPaymentMethods[$order->payment_method]) ? $arrPaymentMethods[$order->payment_method] : $unknown; ?></li>
                        <li><?= A::t('auctions', 'Payment Date'); ?>: <?= !CTime::isEmptyDateTime($order->payment_date) ? CLocale::date($dateTimeFormat, $order->payment_date) : $unknown; ?></li>
                        <li><?= A::t('auctions', 'Transaction ID'); ?>: <?= $order->transaction_number ? $order->transaction_number : '--'; ?></li>
                    </ul>
                </div>
            </div>
            <?php if($order->payment_method == 1): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="v-heading v-text-heading"><span><?= A::t('auctions', 'Credit Card'); ?></span></h3>
                        <ul>
                            <li><?= A::t('app', 'Credit Card Type'); ?>: <?= isset($arrCCType[$order->cc_type]) ? $arrCCType[$order->cc_type] : $unknown; ?></li>
                            <li><?= A::t('app', 'Card Holder\'s Name'); ?>: <?= $order->cc_holder_name ? $order->cc_holder_name : $unknown; ?></li>
                            <li><?= A::t('app', 'Credit Card Number'); ?>: <?= $order->cc_number ? $order->cc_number : $unknown; ?></li>
                            <li><?= A::t('app', 'Expires Month'); ?>: <?= $order->cc_expires_month ? $order->cc_expires_month : $unknown; ?></li>
                            <li><?= A::t('app', 'Expires Year'); ?>: <?= $order->cc_expires_year ? $order->cc_expires_year : $unknown; ?></li>
                            <li><?= A::t('app', 'CVV Code'); ?>: <?= $order->cc_cvv_code ? $order->cc_cvv_code : $unknown; ?></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>