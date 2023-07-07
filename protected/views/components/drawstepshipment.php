<div class="row">
    <div class="v-process-steps">
        <ul class="v-process">
            <li>
                <div class="feature-box feature-box-st">
                    <div class="feature-box-icon small icn-holder active"><i class="fa fa-exclamation v-icon"></i></div>
                    <div class="feature-box-text">
                        <h3><?= A::t('auctions', 'Pending'); ?></h3>
                    </div>
                </div>
            </li>
            <li>
                <div class="feature-box feature-box-st">
                    <div class="feature-box-icon small icn-holder<?= (in_array($shipmentShippingStatus, array(1,2))) ? ' active' : ''; ?>"><i class="fa fa-truck v-icon"></i></div>
                    <div class="feature-box-text">
                        <h3><?= A::t('auctions', 'Shipped'); ?></h3>
                    </div>
                </div>
            </li>
            <li>
                <div class="feature-box feature-box-st">
                    <div class="feature-box-icon small icn-holder<?= (in_array($shipmentShippingStatus, array(2))) ? ' active' : ''; ?>"><i class="fa fa-check-circle-o v-icon"></i></div>
                    <div class="feature-box-text">
                        <h3><?= A::t('auctions', 'Received'); ?></h3>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>