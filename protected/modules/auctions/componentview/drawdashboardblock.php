<div class="v-heading-v2">
    <h3><?= A::t('auctions', 'General'); ?></h3><div class="dashboard-logout"><a href="members/logout"><i class="fa fa-sign-out"></i> <?= A::t('auctions', 'Logout'); ?></a></div>
</div>
<ul>
    <li><a href="members/dashboard"><?= A::t('auctions', 'Dashboard'); ?></a><br></li>
    <li><a href="orders/myOrders"><?= A::t('auctions', 'Orders'); ?></a></li>
    <li><a href="packages/packages"><?= A::t('auctions', 'Bid Packages'); ?></a></li>
</ul>
<div class="v-heading-v2">
    <h3><?= A::t('auctions', 'Profile Details'); ?></h3>
</div>
<ul>
    <li><a href="members/myAccount"><?= A::t('auctions', 'My Account'); ?></a></li>
</ul>
<div class="v-heading-v2">
    <h3><?= A::t('auctions', 'Auctions Management'); ?></h3>
</div>
<ul>
    <li><a href="auctions/myAuctions"><?= A::t('auctions', 'My Auctions'); ?></a></li>
    <li><a href="reviews/myReviews"><?= A::t('auctions', 'My Reviews'); ?></a></li>
    <li><a href="auctions/myWatchlist"><?= A::t('auctions', 'My Watchlist'); ?></a></li>
    <li><a href="bidsHistory/myBidsHistory"><?= A::t('auctions', 'My Bids History'); ?></a></li>
    <li><a href="shipments/myShipments"><?= A::t('auctions', 'My Shipments'); ?></a></li>
    <li><a href="members/myShipmentAddress"><?= A::t('auctions', 'My Shipment Address'); ?></a></li>
</ul>