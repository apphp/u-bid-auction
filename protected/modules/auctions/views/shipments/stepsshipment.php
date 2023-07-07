<?php
$this->_pageTitle = A::t('auctions', 'Steps Shipment');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Shipments'), 'url'=>'shipments/myShipments'),
    array('label' => A::t('auctions', 'Steps Shipment')),
);
$formName = 'frmSteps';
?>
<div class="row">
    <div class="col-sm-12">
        <?php
        CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\Shipments',
            'primaryKey' => $shipmentId,
            'operationType' => 'edit',
            'cancelUrl' => 'shipments/myShipments',
            'passParameters' => false,
            'method' => 'post',
            'htmlOptions' => array(
                'name'              => $formName,
                'id'                => $formName,
                'class'             => 'signup',
                'autoGenerateId'    => true,
            ),
            'requiredFieldsAlert' => true,
            'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
            'fields' => array(
                'steps'            => array('type' => 'label', 'title' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 50), 'default'=>$drawStepShipment, 'htmlOptions' => array()),
                'carrier'          => array('type' => 'label', 'title' => A::t('auctions', 'Carrier'), 'validation' => array(), 'default' => '--', 'htmlOptions' => array('maxLength' => '50', 'class' => 'large')),
                'tracking_number'  => array('type' => 'label', 'title' => A::t('auctions', 'Tracking Number'), 'validation' => array(), 'default' => '--', 'htmlOptions' => array('maxLength' => '50', 'class' => 'large')),
                'shipping_status'  => array('type' => 'label', 'title' => A::t('auctions', 'Shipping Status'), 'tooltip' => '', 'default' => '--', 'validation' => array(), 'definedValues' => $shippingStatusLabel, 'htmlOptions' => array()),
                'shipped_date'     => array('type' => 'label', 'title' => A::t('auctions', 'Shipped Date'), 'tooltip' => '', 'default' => '--', 'validation' => array(), 'maxDate' => '+30', 'yearRange' => '+0:+0', 'htmlOptions' => array('maxlength' => '10', 'style' => 'width:140px'), 'definedValues' => array(), 'viewType' => 'date', 'dateFormat' => 'yy-mm-dd', 'buttonTrigger' => true, 'minDate' => ''),
                'received_date'    => array('type' => 'label', 'title' => A::t('auctions', 'Received Date'), 'tooltip' => '', 'default' => '--', 'validation' => array(), 'maxDate' => '+30', 'yearRange' => '+0:+0', 'htmlOptions' => array('maxlength' => '10', 'style' => 'width:140px'), 'definedValues' => array(), 'viewType' => 'date', 'dateFormat' => 'yy-mm-dd', 'buttonTrigger' => true, 'minDate' => ''),
                'shipping_comment' => array('type' => 'label', 'title' => A::t('auctions', 'Description'), 'validation' => array(), 'default' => '--', 'htmlOptions' => array('maxLength' => '2048')),
            ),
            'buttons' => array(
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'btn v-btn v-third-dark v-small-button')),
            ),
            'messagesSource' => 'core',
            'alerts' => array('type' => 'flash', 'itemName' => A::t('auctions', 'Shipment')),
            'return' => false,
        ));
        ?>
    </div>
</div>