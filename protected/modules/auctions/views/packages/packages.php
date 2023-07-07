<?php
$this->_pageTitle = A::t('auctions', 'Bid Packages');

$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'Bid Packages')),
);

if($countPackages <= 3){
    $classPricingColumn = 'three-cols';
}elseif($countPackages == 5){
    $classPricingColumn = 'five-cols';
}else{
    $classPricingColumn = 'four-cols';
    $countBlockInLine = 4;
    $countLine = ceil($countPackages / $countBlockInLine);
}
?>
<div class="col-sm-12">
    <?= $actionMessage; ?>
    <div class="pricing-table <?= $classPricingColumn; ?>">
        <?php foreach($packages as $package): ?>
        <div class="pricing-column<?= $package['is_default'] ? ' highlight accent-color' : ''; ?>">
            <h3><?= CHtml::encode($package['name']); ?></h3>
            <div class="pricing-column-content">
                <h4><span class="dollar-sign"></span><?= CCurrency::format(CHtml::encode($package['price'])); ?></h4>
                <span class="interval"><?= !empty($package['description']) ? CHtml::encode($package['description']) : ''; ?></span>
                <ul class="features">
                    <li><i class="fa fa-star"></i><?= A::t('auctions', 'Bids Amount').': <strong>'.CHtml::encode($package['bids_amount']); ?></strong></li>
                    <li><i class="fa fa-dollar"></i><?= A::t('auctions', 'Price One Bid').': <strong>'.CHtml::encode($package['price_one_bid']); ?></strong></li>
                </ul>
                <a href="checkout/package/<?= CHtml::encode($package['id']); ?>" class="btn v-btn v-btn-default no-three-d"><?= $package['price'] > 0 ? A::t('auctions', 'Buy Now') : A::t('auctions', 'Get Free'); ?></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="pb70">
        <h3><?= A::t('auctions', 'package_item_1_title'); ?></h3>
        <p><?= A::t('auctions', 'package_item_1_desctiption'); ?></p>

        <h3><?= A::t('auctions', 'package_item_2_title'); ?></h3>
        <p><?= A::t('auctions', 'package_item_2_desctiption'); ?></p>

        <h3><?= A::t('auctions', 'package_item_3_title'); ?></h3>
        <p><?= A::t('auctions', 'package_item_3_desctiption'); ?></p>
    </div>
</div>







