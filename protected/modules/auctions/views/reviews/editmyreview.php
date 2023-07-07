<?php
$statusParam = ($status !== '' ? '/status/' . $status : '');

$this->_pageTitle = A::t('auctions', 'My Reviews');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url' => Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url' => 'members/dashboard'),
    array('label' => A::t('auctions', 'My Reviews'), 'url' => 'reviews/myReviews'.$statusParam),
);
?>
<div class="row">
    <div class="col-sm-12">
        <?php CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\Reviews',
            'primaryKey' => $id,
            'operationType' => 'edit',
            'action' => 'reviews/editMyReview/id/' . $id . $statusParam,
            'successUrl' => 'reviews/myReviews'.$statusParam,
            'cancelUrl' => 'reviews/myReviews'.$statusParam,
            'method' => 'post',
            'htmlOptions'       => array(
                'name'              => 'frmEditReview',
                'id'                => 'frmEditReview',
                'class'             => 'signup',
                'autoGenerateId'    => true,
            ),
            'requiredFieldsAlert' => true,
            'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
            'fields' => array(
                'auction_id'  => array('type' => 'label', 'title' => A::t('auctions', 'Auction Name'), 'tooltip' => '', 'default' => '', 'definedValues' => $arrAuctions, 'htmlOptions' => array('class' => 'ml5'), 'format' => '', 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'created_at'  => array('type' => 'label', 'title' => A::t('auctions', 'Created at'), 'tooltip' => '', 'default' => '', 'definedValues' => array(), 'htmlOptions' => array('class' => 'ml5'), 'format' => $dateTimeFormat, 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'rating'      => array('type' => 'label', 'title' => A::t('auctions', 'Rating'), 'tooltip' => '', 'default' => '', 'definedValues' => $ratingStars, 'htmlOptions' => array('class' => 'ml5'), 'format' => '', 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'message'     => array('type' => 'label', 'title' => A::t('auctions', 'Message'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => false, 'type' => 'any'), 'htmlOptions' => array('class' => 'ml5')),
                'status'      => array('type' => 'label', 'title' => A::t('auctions', 'Status'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => false, 'type' => 'set'), 'definedValues' => $labelStatusReviews, 'emptyOption' => true, 'emptyValue' => '', 'viewType' => 'dropdownlist', 'multiple' => false, 'storeType' => 'separatedValues', 'separator' => ';', 'htmlOptions' => array('class' => 'ml5')),
            ),
            'buttons' => array(
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'btn v-btn v-third-dark v-small-button')),
            ),
            'buttonsPosition' => 'bottom',
            'messagesSource' => 'core',
            'showAllErrors' => false,
            'alerts' => array('type' => 'flash', 'itemName' => A::t('auctions', 'Review')),
            'return' => false,
        )); ?>
    </div>
</div>
