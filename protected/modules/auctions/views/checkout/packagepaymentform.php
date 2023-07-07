<?php
$this->_pageTitle = A::t('auctions', 'Checkout Package');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'Bid Packages'), 'url'=>'packages/packages'),
    array('label' => A::t('auctions', 'Checkout Package')),
);
?>
<div class="row">
    <?= $actionMessage; ?>

    <div class="v-heading-v2">
        <h3><?= A::t('auctions', 'Payment Info'); ?></h3>
    </div>
    <ul>
        <li><h5><strong><?= A::t('auctions', 'Name'); ?>: </strong><?= $providerSettings->name; ?></h5></li>
        <?php if($providerSettings->instructions != ''): ?>
            <li><h5><strong><?= A::t('app', 'Instructions'); ?>:</strong></h5><p><?= $providerSettings->instructions; ?>:</p></li>
        <?php endif; ?>
    </ul>

    <div class="v-heading-v2">
        <h3><?= A::t('auctions', 'Member Info'); ?></h3>
    </div>
    <ul>
        <?= $member->full_name ? '<li><h5><strong>'.A::t('app', 'Full Name').': </strong>'.$member->full_name.'</h5></li>' : ''; ?>
        <?= $member->email ? '<li><h5><strong>'.A::t('app', 'Email').': </strong>'.$member->email.'</h5></li>' : ''; ?>
        <?= $member->phone ? '<li><h5><strong>'.A::t('app', 'Phone').': </strong>'.$member->phone.'</h5></li>' : ''; ?>
    </ul>

    <div class="v-heading-v2">
        <h3><?= A::t('auctions', 'Package Info'); ?></h3>
    </div>
    <ul>
        <?= $package->name ? '<li><h5><strong>'.A::t('auctions', 'Name').': </strong>'.$package->name.'</h5></li>' : ''; ?>
        <?= $package->bids_amount ? '<li><h5><strong>'.A::t('auctions', 'Bids Amount').': </strong>'.$package->bids_amount.'</h5></li>' : ''; ?>
    </ul>

    <div class="v-heading-v2">
        <h3><?= A::t('auctions', 'Package Total Price'); ?></h3>
    </div>
    <ul>
        <li><h5><strong><?= A::t('auctions', 'Price'); ?>: </strong><?= CCurrency::format($package->price); ?></h5></li>
        <?php if(!empty($taxes) && $totalTax > 0): ?>
            <?php foreach($taxes as $tax): ?>
                <li><h5><strong><?= CHtml::encode($tax['name'].' '.round($tax['percent'], 2).'%'); ?>: </strong><?= CCurrency::format($package->price * ($tax['percent'] * 0.01)); ?></h5></li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if(count($taxes) > 1): ?>
            <li><h5><strong><?= A::t('auctions', 'Taxes Total'); ?>: </strong><?= CCurrency::format($totalTax); ?></h5></li>
        <?php endif; ?>
        <li><h5><strong><?= A::t('auctions', 'Grand Total'); ?>: </strong><?= CCurrency::format($grandTotal); ?></h5></li>
    </ul>
    <?= $creditCardMessage; ?>
    <?= $form; ?>
</div>