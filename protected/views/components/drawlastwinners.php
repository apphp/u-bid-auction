<?php if (!empty($lastWinners) && is_array($lastWinners)) : ?>
    <section class="widget widget_sf_recent_custom_comments clearfix">
        <div class="widget-heading clearfix">
            <h4 class="v-heading"><span><?= A::t('auctions', 'Last Winners'); ?></span></h4>
        </div>
        <ul class="recent-comments-list">
            <?php foreach ($lastWinners as $memberName) : ?>
                <li class="comment">
                    <div class="comment-wrap clearfix">
                        <div class="comment-body">
                            <p><?= CHtml::encode($memberName); ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
