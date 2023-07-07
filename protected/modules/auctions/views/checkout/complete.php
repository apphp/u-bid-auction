<?php
$this->_pageTitle = A::t('auctions', 'Checkout');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'Orders'), 'url'=>'orders/myOrders'),
    array('label' => $namePayment),
);
?>
<div class="row">
    <?= $actionMessage; ?>
    <?= $emailMessage; ?>
</div>

