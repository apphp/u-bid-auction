<div class="item">
    <div id="<?= 'description-auction-'.(CHtml::encode($auctionId)); ?>" class="<?= !empty($auctionTypeName) ? $auctionTypeName.'-auction ' : ''; ?><?= 'auction-'.(CHtml::encode($auctionId)); ?> v-team-member-box">
        <div class="cover">
            <div class="member-info pv0 ph0 center-text">
                <div class="row ph20">
                    <div class="col-xs-3 col-sm-3 col-md-3 p5 m0">
                        <small class="center-text"><?= A::t('auctions', 'Winner'); ?><br/><span id="winner" class="v-menu-item-info bg-warning"><?= !empty($winner) ? CHtml::encode($winner) : A::t('auctions', 'Unknown'); ?></span></small>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 p5 m0">
                        <small class="center-text"><?= A::t('auctions', 'Start Price'); ?><br/><span id="start_price"><?= CCurrency::format(CHtml::encode(!empty($startPrice) ? CHtml::encode($startPrice) : 0)); ?></span></small>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 p5 m0">
                        <small class="center-text"><?= A::t('auctions', 'Bids'); ?><br/><span id="bids"><?= !empty($bids) ? CHtml::encode($bids) : '0'; ?></span></small>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 p5 m0">
                        <small class="center-text"><?= A::t('auctions', 'Bidders'); ?><br/><span id="bidders"><?= !empty($bidders) ? CHtml::encode($bidders) : '0'; ?></span></small>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 p5 m0">
                        <small class="center-text"><i class="fa fa-eye"></i><br/><span id="hits"><?= !empty($hits) ? CHtml::encode($hits) : '0'; ?></span></small>
                    </div>
                </div>
                <div class="bb1"></div>
                <?php if($auctionClosed): ?>
                    <div class="m20 center alert alert-danger"><?= A::t('auctions', 'This auction is already closed!'); ?></div>
                <?php elseif($auctionNotStart): ?>
                    <div class="m20 center alert alert-info"><?= A::t('auctions', 'This auction is not started yet!'); ?></div>
                    <small><?= A::t('auctions', 'Will Start In'); ?>:</small>
                    <div class="timer" id="timer-auction-<?= CHtml::encode($auctionId); ?>"></div>
                    <hr>
                <?php elseif(!$auctionClosed && !$auctionNotStart): ?>
                    <small id="timer-label"><?= A::t('auctions', 'Ends Within'); ?>:</small>
                    <div class="timer" id="timer-auction-<?= CHtml::encode($auctionId); ?>"></div>
                    <small id="current-bid-label"><?= A::t('auctions', 'Current Bid'); ?>:</small>
                    <p class="current-bid"  data-auction-id="<?= CHtml::encode($auctionId); ?>" data-next-bid="<?= CHtml::encode($nextStep); ?>"><?= CCurrency::format(CHtml::encode($currentBid)); ?></p>
                    <div class="bid-now-link bb1">
                        <div><small><?= A::t('auctions', 'Next Step').': <span id="next_step">'.(!empty($nextStep) ? CCurrency::format(CHtml::encode($nextStep)) : A::t('auctions', 'Unknown')); ?></span></small></div>
                        <a href="javascript:void(0);" class="place-bid btn btn-bids v-btn v-orange v-medium-button mb10"><?= A::t('auctions', 'Bid Now'); ?></a>
                    </div>
                <?php endif; ?>
                <div class="row ph20">
                    <?php if(!$auctionClosed && !$auctionNotStart): ?>
                        <div class="col-xs-5 col-sm-12 col-md-6 p5 m0">
                            <a id="watchlist" href="javascript:void(0);" class="full-width normal-text btn v-btn v-small-button special-icon p5 m0 <?= $checkAuctionInWatchlist ? 'v-emerald' : 'v-peter-river' ;?>" data-auction-id="<?= CHtml::encode($auctionId); ?>"><i class="<?= $checkAuctionInWatchlist ? 'fa fa-check' : 'fa fa-plus' ;?>"></i><span><?= $checkAuctionInWatchlist ? A::t('auctions', 'Watching') : A::t('auctions', 'Add to Watchlist'); ?></span></a>
                        </div>
                        <div class="col-xs-7 col-sm-12 col-md-6 p5 m0">
                            <a id="buy-now" href="checkout/auctionPaymentForm/id/<?= CHtml::encode($auctionId); ?>" class="full-width normal-text btn v-btn v-small-button special-icon v-concrete p5 m0"data-auction-id="<?= CHtml::encode($auctionId); ?>"><i class="fa fa-usd"></i><?= A::t('auctions', 'Buy Now').' '.CCurrency::format(CHtml::encode($buyNowPrice)); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>