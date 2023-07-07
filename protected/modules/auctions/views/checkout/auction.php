<?php
$this->_pageTitle = A::t('auctions', 'Checkout Auction');

$breadCrumbs = array();
$breadCrumbs[] = array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage());
$breadCrumbs[] = array('label' => A::t('auctions', 'Auctions'), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')));
if(!empty($parentCategories) && is_array($parentCategories)){
    if(!empty($parentCategories['parent_category']) && is_array($parentCategories['parent_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['parent_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['parent_category']['id']), CHtml::encode($parentCategories['parent_category']['name'])));
    }
    if(!empty($parentCategories['current_category']) && is_array($parentCategories['current_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['current_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['current_category']['id']), CHtml::encode($parentCategories['current_category']['name'])));
    }
}
$breadCrumbs[] = array('label' => $auction->auction_name, 'url'=>Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction->id, $auction->auction_name));
$breadCrumbs[] = array('label' => A::t('auctions', 'Checkout Auction'));
$this->_breadCrumbs = $breadCrumbs;
?>
<div class="row">
    <?= $actionMessage; ?>
    <div class="v-heading-v2">
        <h3><?= A::t('auctions', 'Auction Total Price'); ?></h3>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 v-gallery-widget">
            <img src='assets/modules/auctions/images/auctionImages/<?= !empty($auctionImage[$auction->id]['image_file']) ? CHtml::encode($auctionImage[$auction->id]['image_file']): 'no_image.png'; ?>' alt="<?= !empty($auctionImage[$auction->id]['title']) ? CHtml::encode($auctionImage[$auction->id]['title']): $auction->auction_name; ?>" />
        </div>
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <ul>
                <li><h5><?= A::t('auctions', 'Member Name'); ?>:</h5></li>
                <li><h5><?= A::t('auctions', 'Auction'); ?>:</h5></li>
                <li><h5><strong><?= A::t('auctions', 'Price'); ?>:</strong></h5></li>
                <?php if(!empty($taxes) && $totalTax > 0): ?>
                    <?php foreach($taxes as $tax): ?>
                        <li><h5><?= CHtml::encode($tax['name'].' '.round($tax['percent'], 2).'%'); ?>:</h5></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if(count($taxes) > 1): ?>
                    <li><h5><strong><?= A::t('auctions', 'Taxes Total'); ?>:</strong></h5></li>
                <?php endif; ?>
                <li><h5><strong><?= A::t('auctions', 'Grand Total'); ?>:</strong></h5></li>
            </ul>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <ul>
                <li><h5><?= CHtml::encode($member->full_name); ?></h5></li>
                <li><h5><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction->id, $auction->auction_name); ?>" class="link-to-post"><?= CHtml::encode($auction->auction_name); ?></a></h5></li>
                <li><h5><strong><?= CCurrency::format($auction->buy_now_price); ?></strong></h5></li>
                <?php if(!empty($taxes) && $totalTax > 0): ?>
                    <?php foreach($taxes as $tax): ?>
                        <li><h5><?= CCurrency::format($auction->buy_now_price * ($tax['percent'] * 0.01)); ?></h5></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if(count($taxes) > 1): ?>
                    <li><h5><strong><?= CCurrency::format($totalTax); ?>:</strong></h5></li>
                <?php endif; ?>
                <li><h5><strong><?= CCurrency::format($grandTotal); ?></strong></h5></li>
            </ul>
        </div>
    </div>
    <?php if(APPHP_MODE == 'demo'):
        echo CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')));
        echo CHtml::submitButton(A::t('auctions', 'Go To Payment'), array('class'=>'button'));
    else: ?>
        <div class="row">
            <div class="col-sm-5">
               <?= CWidget::create('CFormView', array(
                    'action'	        => 'checkout/auctionPaymentForm/id/'.$auction->id.'/type/buy_now',
                    'method'            => 'post',
                    'htmlOptions'       => array(
                        'name'              => 'frmCheckoutAuction',
                        'id'                => 'frmCheckoutAuction',
                        'class'             => '',
                        'autoGenerateId'    => true,
                    ),
                    'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
                    'fields'    => array(
                        'act'               => array('type'=>'hidden', 'value'=>'send'),
                        'payment_method'    => array('type'=>'select', 'title'=>A::t('auctions', 'Payment Method'), 'default'=>'', 'mandatoryStar'=>true, 'data'=>$providers, 'emptyOption'=>true, 'emptyValue'=>A::t('app', '-- select --'), 'htmlOptions'=>array('class'=>'form-control')),
                    ),
                    'buttons' => array(
                        'submit' => array('type'=>'submit', 'value'=>A::t('auctions', 'Go To Payment'), 'htmlOptions'=>array('class'=>'btn v-btn v-btn-default v-small-button')),
                    ),
                )); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
