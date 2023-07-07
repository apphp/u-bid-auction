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
        <h3><?= A::t('auctions', 'Package Total Price'); ?></h3>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <ul class="">
                <li><h5><?= A::t('auctions', 'Member Name'); ?>:</h5></li>
                <li><h5><?= A::t('auctions', 'Package'); ?>:</h5></li>
                <li><h5><?= A::t('auctions', 'Bids Amount'); ?>:</h5></li>
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
        <div class="col-sm-2">
            <ul class="">
                <li><h5><?= CHtml::encode($member->full_name); ?></h5></li>
                <li><h5><?= CHtml::encode($package->name); ?></h5></li>
                <li><h5><?= CHtml::encode($package->bids_amount); ?></h5></li>
                <li><h5><strong><?= CCurrency::format($package->price); ?></strong></h5></li>
                <?php if(!empty($taxes) && $totalTax > 0): ?>
                    <?php foreach($taxes as $tax): ?>
                        <li><h5><?= CCurrency::format($package->price * ($tax['percent'] * 0.01)); ?></h5></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if(count($taxes) > 1): ?>
                    <li><h5><strong><?= CCurrency::format($totalTax); ?>:</strong></h5></li>
                <?php endif; ?>
                <li><h5><strong><?= CCurrency::format($grandTotal); ?></strong></h5></li>
            </ul>
        </div>
    </div>
    <?php
    if(APPHP_MODE == 'demo'):
        echo CWidget::create('CMessage', array('warning', A::t('core', 'This operation is blocked in Demo Mode!')));
        echo CHtml::submitButton(A::t('auctions', 'Go To Payment'), array('class'=>'button'));

    elseif($package->price == 0):
        echo '<a class="btn v-btn v-btn-default v-small-button" href="checkout/packagePaymentForm/'.$package->id.'">'.A::t('auctions', 'Get Free').'</a>';
    else:
        echo '<div class="row">';
            echo '<div class="col-sm-5">';
                echo CWidget::create('CFormView', array(
                    'action'	        => 'checkout/packagePaymentForm/'.$package->id,
                    'method'            => 'post',
                    'htmlOptions'       => array(
                        'name'              => 'frmCheckoutPackage',
                        'id'                => 'frmCheckoutPackage',
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
                ));
            echo ' </div>';
        echo ' </div>';
    endif;
    ?>
</div>
