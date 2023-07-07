<?php
$this->_activeMenu = 'auctionTypes/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Auction Types Management'), 'url'=>'auctionTypes/manage'),
    array('label'=>A::t('auctions', 'Edit Auction Type')),
);

$formName = 'frmAuctionTypeEdit';
?>

<h1><?= A::t('auctions', 'Auction Types Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('auctions', 'Edit Auction Type'); ?></div>
    <div class="content">

    <?php
    echo $actionMessage;

    echo CWidget::create('CDataForm', array(
        'model'             => 'Modules\Auctions\Models\AuctionTypes',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'auctionTypes/edit/id/'.$id,
        'successUrl'        => 'auctionTypes/manage',
        'cancelUrl'         => 'auctionTypes/manage',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'autoGenerateId'    => true
        ),
        'requiredFieldsAlert' => true,
        'fields' => array(
            'separatorContact' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'General Information')),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('auctions', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>3, 'type'=>'numeric'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                'is_default' 	=> array('type'=>'checkbox', 'title'=>A::t('auctions', 'Default'), 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>($auctionType->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$auctionType->is_default) : array()), 'viewType'=>'custom'),
                'is_active'      => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'),         'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom', 'htmlOptions'=>($auctionType->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$auctionType->is_default) : array())),
            ),
        ),
        'translationInfo' => array('relation'=>array('id', 'auction_type_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
        'translationFields' => array(
            'name'          => array('type'=>'textbox', 'title'=>A::t('auctions', 'Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'large')),
            'description'   => array('type'=>'textarea', 'title'=>A::t('auctions', 'Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048')),
        ),
        'buttons' => array(
            'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
            'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
            'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Auction Type')),
        'return'            => true,
    ));
    ?>
    </div>
</div>