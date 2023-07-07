<?php
$this->_pageTitle = A::t('auctions', 'My Watchlist');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Watchlist')),
);
?>

<div class="row">
    <div id="all-auctions" class="col-sm-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="<?= $statusTab == 'active' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'active' ? 'javascript:void(0);' : 'auctions/myWatchlist/status/active'; ?>"><?= A::t('auctions', 'Active Auctions'); ?></a>
                </li>
                <li class="<?= $statusTab == 'not_started' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'not_started' ? 'javascript:void(0);' : 'auctions/myWatchlist/status/not_started'; ?>"><?= A::t('auctions', 'Not Started Auctions'); ?></a>
                </li>
                <li class="<?= $statusTab == 'closed' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'closed' ? 'javascript:void(0);' : 'auctions/myWatchlist/status/closed'; ?>"><?= A::t('auctions', 'Closed Auctions'); ?></a>
                </li>
            </ul>
        </div>
        <?= $actionMessage; ?>
        <?php if(!empty($watchlistAuctions) && is_array($watchlistAuctions)): ?>
            <?php foreach($watchlistAuctions as $auction): ?>
                <div id="auction-<?= CHtml::encode($auction['id']); ?>" class="<?= (!empty($auction['auction_type_id']) && isset($auctionTypesList[$auction['auction_type_id']])) ? mb_strtolower($auctionTypesList[$auction['auction_type_id']]).'-auction ' : ''; ?><?= !empty($auction['id']) ? 'auction-'.(CHtml::encode($auction['id'])).' ' : ''; ?>watchlist v-team-member-box col-xs- col-sm-6 col-md-4 mb10 ph5">
                    <div class="cover">
                        <div class="v-team-member-img">
                            <div class="item">
                                <figure class="animated-overlay overlay-alt">
                                    <img src="<?= !empty($auction['image_file']) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($auction['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?>" alt="<?= !empty($auction['image_title']) ? CHtml::encode($auction['image_title']) : CHtml::encode($auction['auction_name']); ?>">
                                    <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>" class="link-to-post"></a>
                                    <div class="delete-watchlist">
                                        <a href="auctions/removeWatchlist/id/<?= $auction['id']; ?>/status/<?= $statusTab; ?>">
                                            <i class="fa fa-times-circle"></i>
                                        </a>
                                    </div>
                                    <figcaption>
                                        <div class="thumb-info thumb-info-v2">
                                            <i class="fa fa-angle-right" style="visibility: visible; opacity: 1; transition-duration: 0.3s; transform: scale(0.5) rotate(-90deg);"></i>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="member-info pv5">
                            <div class="heading">
                                <div class="v-team-member-info">
                                    <h4 class="v-team-member-name"><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>"><?= !empty($auction['auction_name']) ? CHtml::encode($auction['auction_name']) : A::t('auctions', 'Unknown'); ?></a></h4>
                                    <div class="v-team-member-statu"><span class="current-bid" <?= ($statusTab == 'active' ? 'data-auction-id="'.CHtml::encode($auction['id']).'" data-next-bid="'.CHtml::encode($auction['current_bid'] + $auction['size_bid']).'"' : ''); ?>><?= CCurrency::format(CHtml::encode($auction['current_bid'])); ?></span></div>
                                </div>
                            </div>
                            <?php if($statusTab == 'active'): ?>
                                <div class="countdown-timer-wrapper">
                                    <div class="timer" id="timer-watchlist-auction-<?= CHtml::encode($auction['id']); ?>"></div>
                                </div>
                            <?php elseif($statusTab == 'not_started'): ?>
                                <div class="text-center">
                                    <small><?= A::t('auctions', 'Will Start In'); ?>:</small>
                                </div>
                                <div class="timer" id="timer-watchlist-auction-<?= CHtml::encode($auction['id']); ?>"></div>
                            <?php endif; ?>
                            <?php if($statusTab == 'active'): ?>
                                <div class="bid-now-link mb5">
                                    <a href="javascript:void(0);" class="place-bid btn btn-bids v-btn v-orange v-small-button mb5"><?= A::t('auctions', 'Bid Now'); ?></a>
                                </div>
                            <?php elseif($statusTab == 'closed'): ?>
                                <div class="bid-now-link">
                                    <div class="center alert alert-danger"><?= A::t('auctions', 'The auction is closed!'); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
//Draw Timer
echo $drawTimerScript;
//Call Draw Timer
echo $callDrawTimerScript;