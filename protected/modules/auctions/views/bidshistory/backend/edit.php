<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Bids History Management'), 'url'=>'bidsHistory/manage/auctionId/'.$auctionId),
    array('label'=>A::t('auctions', 'Edit Shipment')),
);

$formName = 'frmShipmentTypeEdit';
?>

<h1><?= A::t('auctions', 'Bids History Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
    <div class="content">

    <?php
    echo $actionMessage;

    echo CWidget::create('CDataForm', array(
        'model'             => 'Modules\Auctions\Models\BidsHistory',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'bidsHistory/edit/auctionId/'.$auctionId.'/id/'.$id,
        'successUrl'        => 'bidsHistory/manage/auctionId/'.$auctionId.'',
        'cancelUrl'         => 'bidsHistory/manage/auctionId/'.$auctionId.'',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'autoGenerateId'    => true
        ),
        'requiredFieldsAlert' => true,
        'fields' => array(
            'auction_type'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction Type'), 'validation'=>array(), 'htmlOptions'=>array()),
            'auction_name'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction'), 'validation'=>array(), 'htmlOptions'=>array()),
            'member_name'   => array('type'=>'label', 'title'=>A::t('auctions', 'Member'), 'validation'=>array(), 'htmlOptions'=>array()),
            'created_at'    => array('type'=>'label', 'title'=>A::t('auctions', 'Created at'), 'validation'=>array(), 'format'=>$dateTimeFormat, 'htmlOptions'=>array()),
            'size_bid'      => array('type'=>'label', 'title'=>A::t('auctions', 'Value Bid'), 'validation'=>array(), 'htmlOptions'=>array()),
        ),
        'buttons' => array(
            'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
            'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
            'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Shipment')),
        'return'            => true,
    ));
    ?>
    </div>
</div>