<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label' => A::t('auctions', 'Modules'), 'url' => 'modules/'),
    array('label' => A::t('auctions', 'Auctions'), 'url' => 'modules/settings/code/auctions'),
    array('label' => A::t('auctions', 'Auctions Management'), 'url' => 'auctions/manage'),
    array('label' => A::t('auctions', 'Reviews Management'), 'url' => 'reviews/manage/auctionId/' . $auctionId . '/status/' . $status),
    array('label' => A::t('auctions', 'Edit Review'))
);

$statusParam = ($status !== '' ? '/status/' . $status : '');
?>

<h1><?= A::t('auctions', 'Reviews Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('auctions', 'Edit Review'); ?></div>

    <div class="content">
        <?php CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\Reviews',
            'primaryKey' => $id,
            'operationType' => 'edit',
            'action' => 'reviews/edit/auctionId/' . $auctionId . '/id/' . $id . $statusParam,
            'successUrl' => 'reviews/manage/auctionId/' . $auctionId . $statusParam,
            'cancelUrl' => 'reviews/manage/auctionId/' . $auctionId . $statusParam,
            'method' => 'post',
            'htmlOptions' => array(
                'id' => 'frmReviewEdit',
                'name' => 'frmReviewEdit',
                'autoGenerateId' => true
            ),
            'requiredFieldsAlert' => true,
            'fields' => array(
                'member_name' => array('type' => 'label', 'title' => A::t('auctions', 'Member Name'), 'tooltip' => '', 'default' => $memberFullName, 'definedValues' => array(), 'htmlOptions' => array(), 'format' => '', 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'created_at'  => array('type' => 'label', 'title' => A::t('auctions', 'Created at'), 'tooltip' => '', 'default' => '', 'definedValues' => array(), 'htmlOptions' => array(), 'format' => $dateTimeFormat, 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'rating'      => array('type' => 'label', 'title' => A::t('auctions', 'Rating'), 'tooltip' => '', 'default' => '', 'definedValues' => $ratingStars, 'htmlOptions' => array(), 'format' => '', 'stripTags' => false, 'callback' => array('function' => '', 'params' => '')),
                'message'     => array('type' => 'textarea', 'title' => A::t('auctions', 'Message'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 500), 'htmlOptions' => array('maxLength' => '500')),
                'status'      => array('type' => 'select', 'title' => A::t('auctions', 'Status'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($editStatusReviews)), 'data' => $editStatusReviews, 'emptyOption' => true, 'emptyValue' => '', 'viewType' => 'dropdownlist', 'multiple' => false, 'storeType' => 'separatedValues', 'separator' => ';', 'htmlOptions' => array('class' => 'chosen-select-filter')),
            ),
            'buttons' => array(
                'submitUpdateClose' => array('type' => 'submit', 'value' => A::t('app', 'Update & Close'), 'htmlOptions' => array('name' => 'btnUpdateClose')),
                'submitUpdate' => array('type' => 'submit', 'value' => A::t('app', 'Update'), 'htmlOptions' => array('name' => 'btnUpdate')),
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'button white')),
            ),
            'buttonsPosition' => 'bottom',
            'messagesSource' => 'core',
            'showAllErrors' => false,
            'alerts' => array('type' => 'flash', 'itemName' => A::t('auctions', 'Review')),
            'return' => false,
        )); ?>
    </div>
</div>