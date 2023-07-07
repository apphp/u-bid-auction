<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Packages Management'), 'url'=>'packages/manage'),
    array('label'=>A::t('auctions', 'Edit Package')),
);

$formName = 'frmPackageEdit';
?>

<h1><?= A::t('auctions', 'Packages Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('auctions', 'Edit Package'); ?></div>
    <div class="content">

    <?php
    echo $actionMessage;

    echo CWidget::create('CDataForm', array(
        'model'             => 'Modules\Auctions\Models\Packages',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'packages/edit/id/'.$id,
        'successUrl'        => 'packages/manage',
        'cancelUrl'         => 'packages/manage',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'autoGenerateId'    => true
        ),
        'requiredFieldsAlert' => true,
        'fields'    => array(
            'bids_amount'   => array('type'=>'textbox',  'title'=>A::t('auctions', 'Bids Amount'), 'default'=>0,     'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'int', 'maxLength'=>7), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small')),
            'price'         => array('type'=>'textbox',  'title'=>A::t('auctions', 'Price'), 'default'=>0,     'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'maxLength'=>7, 'minValue'=>'0.00', 'maxValue'=>'', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
            'price_one_bid' => array('type'=>'label',  'title'=>A::t('auctions', 'Price One Bid'), 'default'=>0,     'tooltip'=>'', 'htmlOptions'=>array('class'=>'small'), 'prependCode'=>'<label style="width:10px">'.$pricePrependCode.'</label> ', 'appendCode'=>$priceAppendCode),
            'is_default' 	=> array('type'=>'checkbox', 'title'=>A::t('auctions', 'Default'), 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>($package->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$package->is_default) : array()), 'viewType'=>'custom'),
            'is_active'     => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'),         'tooltip'=>'', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom', 'htmlOptions'=>($package->is_default ? array('disabled'=>'disabled', 'uncheckValue'=>$package->is_default) : array())),
        ),
        'translationInfo' => array('relation'=>array('id', 'package_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
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
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Package')),
        'return'            => true,
    ));
    ?>
    </div>
</div>