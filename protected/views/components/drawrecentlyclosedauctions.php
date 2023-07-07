<?php if (!empty($recentlyClosedAuctions) && is_array($recentlyClosedAuctions)) : ?>
    <section class="widget widget_sf_recent_custom_comments clearfix">
        <div class="widget-heading clearfix">
            <h4 class="v-heading"><span><?= A::t('auctions', 'Recently Closed Auctions'); ?></span></h4>
        </div>
        <ul class="recent-comments-list">
            <?php foreach ($recentlyClosedAuctions as $auction) : ?>
                <li class="comment">
                    <div class="comment-wrap clearfix">
                        <div class="comment-avatar">
                            <img src="<?= !empty($auctionImages[$auction['id']]) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($auctionImages[$auction['id']]['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?>" class="avatar" height="100" width="100" alt="<?= !empty($auctionImages[$auction['id']]) ? CHtml::encode($auctionImages[$auction['id']]['title']) : CHtml::encode($auction['auction_name']); ?>">
                        </div>
                        <div class="comment-content">
                            <div class="comment-body">
                                <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>">
                                    <p><?= CHtml::encode($auction['auction_name']); ?></p>
                                </a>
                            </div>
                            <div class="comment-meta">
                                <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>"><?= A::t('app', 'Read More'); ?> →</a>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
