<?php
    $this->_activeMenu = 'taxes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Taxes'), 'url'=>'taxes/manage'),
        array('label'=>A::t('auctions', 'Edit Tax')),
    );
?>

<h1><?= A::t('auctions', 'Taxes Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
        <div class="sub-title">
        <?= A::t('auctions', 'Edit Tax'); ?>
        </div>

    <div class="content">
        <?php
            echo $actionMessage;

            CWidget::create('CDataForm', array(
                'model'             => 'Modules\Auctions\Models\Taxes',
                'operationType'     => 'edit',
                'primaryKey'        => $id,
                'action'            => 'taxes/edit/id/'.$id,
                'successUrl'        => 'taxes/manage',
                'cancelUrl'         => 'taxes/manage',
                'passParameters'    => false,
                'method'            => 'post',
                'htmlOptions'       => array(
                    'id'                => 'frmTaxesEdit',
                    'name'              => 'frmTaxesEdit',
                    'enctype'           => 'multipart/form-data',
                    'autoGenerateId'    => true
                ),
                'requiredFieldsAlert'   => true,
                'fields'                => array(
                    'name'        => array('type'=>'textbox', 'title'=>A::t('auctions', 'Name'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('class' => 'middle', 'maxLength'=>50)),
                    'description' => array('type'=>'textarea', 'title'=>A::t('auctions', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>2048)),
                    'percent'     => array('type'=>'textbox', 'title'=>A::t('auctions', 'Percent'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0', 'maxValue'=>'100.00', 'format'=>$numberFormat, 'maxLength'=>5), 'htmlOptions'=>array('maxLength'=>5, 'class'=>'small'), 'appendCode'=>'% '),
                    'sort_order'  => array('type'=>'textbox', 'title'=>A::t('auctions', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'minValue'=>'0', 'maxValue'=>'255', 'type'=>'range'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
                    'is_active'   => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                ),
                'buttons'           => array(
                    'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                    'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                    'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                ),
                'messagesSource'    => 'core',
                'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Tax')),
                'return'            => false,
            ));
        ?>

    </div>
</div>
