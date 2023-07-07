<?php
    $this->_activeMenu = 'taxes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Taxes Management'), 'url'=>'taxes/manage'),
        array('label'=>A::t('auctions', 'Tax Countries Management'), 'url'=>'taxes/manageCountries/taxId/'.$taxId),
        array('label'=>A::t('auctions', 'Edit Tax')),
    );
?>

<h1><?= A::t('auctions', 'Tax Countries Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
        <?php
            echo $actionMessage;

            CWidget::create('CDataForm', array(
                'model'          => 'Modules\Auctions\Models\TaxCountries',
                'operationType'  => 'edit',
                'primaryKey'     => $id,
                'action'         => 'taxes/editCountry/taxId/'.$taxId.'/id/'.$id,
                'successUrl'     => 'taxes/manageCountries/taxId/'.$taxId,
                'cancelUrl'      => 'taxes/manageCountries/taxId/'.$taxId,
                'passParameters' => false,
                'method'         => 'post',
                'htmlOptions'    => array(
                    'id'             => 'frmTaxesEdit',
                    'name'           => 'frmTaxesEdit',
                    'enctype'        => 'multipart/form-data',
                    'autoGenerateId' => true
                ),
                'requiredFieldsAlert' => true,
                'fields'              => array(
                    'country_code' => array('type'=>'select', 'title'=>A::t('auctions', 'Country'), 'default'=>'', 'tooltip'=>'', 'validation'=>array('required'=>true, 'unique'=>true, 'uniqueCondition'=>'tax_id = '.(int)$taxId, 'type'=>'set', 'source'=>array_keys($arrCountryNames)), 'data'=>$arrCountryNames, 'emptyOption'=>true, 'emptyValue'=>'-- '.A::t('auctions', 'select').' --', 'viewType'=>'dropDownList', 'htmlOptions'=>array()),
                    'percent'      => array('type'=>'textbox', 'title'=>A::t('auctions', 'Percent'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0', 'maxValue'=>'100.00', 'format'=>$numberFormat, 'maxLength'=>6), 'htmlOptions'=>array('maxLength'=>5, 'class'=>'small'), 'appendCode'=>'% '),
                ),
                'translationInfo'   => array(),
                'translationFields' => array(),
                'buttons' => array(
                    'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                    'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                    'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
                ),
                'messagesSource'    => 'core',
                'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Country')),
                'return'            => false,
            ));
        ?>

    </div>
</div>
