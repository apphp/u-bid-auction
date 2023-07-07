<?php
$this->_activeMenu = 'shipments/manage';
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Modules'), 'url' => $backendPath . 'modules/'),
    array('label' => A::t('auctions', 'Auctions'), 'url' => $backendPath . 'modules/settings/code/auctions'),
    array('label' => A::t('auctions', 'Shipments Management'), 'url' => 'shipments/manage/auctionId/' . $auctionId),
    array('label' => A::t('auctions', 'Edit Shipment')),
);

$formName = 'frmShipmentTypeEdit';
?>

<h1><?= A::t('auctions', 'Shipments Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
    <div class="content">

        <?php
        echo $actionMessage;

        CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\Shipments',
            'primaryKey' => $id,
            'operationType' => 'edit',
            'action' => 'shipments/edit/auctionId/' . $auctionId . '/id/' . $id,
            'successUrl' => 'shipments/manage/auctionId/' . $auctionId . '',
            'cancelUrl' => 'shipments/manage/auctionId/' . $auctionId . '',
            'passParameters' => false,
            'method' => 'post',
            'htmlOptions' => array(
                'name' => $formName,
                'autoGenerateId' => true
            ),
            'requiredFieldsAlert' => true,
            'fields' => array(
                'carrier'          => array('type' => 'textbox', 'title' => A::t('auctions', 'Carrier'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 50), 'htmlOptions' => array('maxLength' => '50', 'class' => 'large')),
                'tracking_number'  => array('type' => 'textbox', 'title' => A::t('auctions', 'Tracking Number'), 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 50), 'htmlOptions' => array('maxLength' => '50', 'class' => 'large')),
                'shipping_status'  => array('type' => 'select', 'title' => A::t('auctions', 'Shipping Status'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($shippingStatus)), 'data' => $shippingStatus, 'htmlOptions' => array()),
                'shipped_date'     => array('type' => 'datetime', 'title' => A::t('auctions', 'Shipped Date'), 'tooltip' => '', 'default' => null, 'validation' => array('required' => false, 'type' => 'date', 'maxLength' => 10, 'minValue' => date('Y-m-d', strtotime("-1 month")), 'maxValue' => date('Y-m-d', strtotime("+1 month"))), 'maxDate' => '+30', 'yearRange' => '+0:+0', 'htmlOptions' => array('maxlength' => '10', 'style' => 'width:140px'), 'definedValues' => array(), 'viewType' => 'date', 'dateFormat' => 'yy-mm-dd', 'buttonTrigger' => true, 'minDate' => ''),
                'received_date'    => array('type' => 'datetime', 'title' => A::t('auctions', 'Received Date'), 'tooltip' => '', 'default' => null, 'validation' => array('required' => false, 'type' => 'date', 'maxLength' => 10, 'minValue' => date('Y-m-d', strtotime("-1 month")), 'maxValue' => date('Y-m-d', strtotime("+1 month"))), 'maxDate' => '+30', 'yearRange' => '+0:+0', 'htmlOptions' => array('maxlength' => '10', 'style' => 'width:140px'), 'definedValues' => array(), 'viewType' => 'date', 'dateFormat' => 'yy-mm-dd', 'buttonTrigger' => true, 'minDate' => ''),
                'shipping_comment' => array('type' => 'textarea', 'title' => A::t('auctions', 'Description'), 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 2048), 'htmlOptions' => array('maxLength' => '2048')),
            ),
            'buttons' => array(
                'submitUpdateClose' => array('type' => 'submit', 'value' => A::t('app', 'Update & Close'), 'htmlOptions' => array('name' => 'btnUpdateClose')),
                'submitUpdate' => array('type' => 'submit', 'value' => A::t('app', 'Update'), 'htmlOptions' => array('name' => 'btnUpdate')),
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'button white')),
            ),
            'messagesSource' => 'core',
            'alerts' => array('type' => 'flash', 'itemName' => A::t('auctions', 'Shipment')),
            'return' => false,
        ));
        ?>
    </div>
</div>