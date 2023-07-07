<?php
$this->_pageTitle = A::t('auctions', 'Dashboard');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard')),
);
use Modules\Auctions\Components\AuctionsComponent;
?>

<div class="col-sm-12">
    <div class="alert alert-info">
        <strong><?= A::t('auctions', 'Hi').', '.CAuth::getLoggedName(); ?></strong> <?= A::t('auctions', 'Welcome on the Dashboard!'); ?>
    </div>
    <?= AuctionsComponent::drawDashboardBlock(); ?>

	<br><br>
</div>