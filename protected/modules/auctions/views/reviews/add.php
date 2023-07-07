<?php
$this->_pageTitle = A::t('auctions', 'Add Review');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url' => Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url' => 'members/dashboard'),
    array('label' => A::t('auctions', 'My Auctions'), 'url' => 'auctions/myAuctions'),
    array('label' => A::t('auctions', 'Add Review')),
);

A::app()->getClientScript()->registerCssFile('assets/vendors/bar-rating/css/css-stars.css');

$formName = 'frmReviewAdd';
?>
    <div class="col-sm-12">
        <?php CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\Reviews',
            'operationType' => 'add',
            'action' => 'reviews/add/auctionId/' . $auctionId,
            'successUrl' => 'auctions/myAuctions/status/won',
            'cancelUrl' => 'auctions/myAuctions/status/won',
            'method' => 'post',
            'htmlOptions' => array(
                'id' => 'frmReviewAdd',
                'name' => 'frmReviewAdd',
                'class' => 'signup',
                'autoGenerateId' => true
            ),
            'requiredFieldsAlert' => true,
            'fieldWrapper' => array('tag' => 'div', 'class' => 'form-group'),
            'fields' => array(
                'rating' => array('type' => 'select', 'title' => A::t('auctions', 'Rating'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($ratingValue)), 'data' => $ratingValue, 'emptyOption' => true, 'emptyValue' => '', 'viewType' => 'dropdownlist', 'multiple' => false, 'htmlOptions' => array('id' => 'rating', 'data-label' => A::t('auctions', 'Rating'))),
                'message' => array('type' => 'textarea', 'title' => A::t('auctions', 'Message'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 500), 'htmlOptions' => array('maxLength' => '500', 'id' => 'message')),
                'created_at' => array('type' => 'data', 'default' => CLocale::date('Y-m-d H:i:s')),
                'member_id' => array('type' => 'data', 'default' => $memberId),
                'auction_id' => array('type' => 'data', 'default' => $auctionId),
                'status' => array('type' => 'data', 'default' => $reviewModeration ? '0' : '1'),
            ),
            'buttons' => array(
                'submitUpdateClose' => array('type' => 'submit', 'value' => A::t('app', 'Create'), 'htmlOptions' => array('name' => 'btnUpdateClose', 'class' => 'btn v-btn v-btn-default v-small-button')),
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'btn v-btn v-third-dark v-small-button')),
            ),
            'buttonsPosition' => 'bottom',
            'messagesSource' => 'core',
            'showAllErrors' => false,
            'alerts' => array('type' => 'flash', 'itemName' => A::t('auctions', 'Review')),
            'return' => false,
        )); ?>
    </div>
<?php

A::app()->getClientScript()->registerScriptFile('assets/vendors/bar-rating/jquery.barrating.min.js', 2);

A::app()->getClientScript()->registerScript(
    'barRating',
    '$(function() {
        $("#rating").barrating({
            theme: "css-stars"
        });
    });',
    3
);