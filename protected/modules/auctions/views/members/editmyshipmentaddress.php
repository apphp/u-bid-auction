<?php
$this->_pageTitle = A::t('auctions', 'Edit Address');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label'=>A::t('auctions', 'My Shipment Address'), 'url'=>'members/myShipmentAddress'),
    array('label' => A::t('auctions', 'Edit Address')),
);

$formName = 'frmAddressEdit';
$onchange = "addChangeCountry(this.value,'')";
?>
    <div class="col-sm-12">
        <?php
        $fields = array();

        $fields['separatorPersonal'] = array();
        $fields['separatorPersonal']['separatorInfo'] = array('legend' => A::t('auctions', 'Personal Information'));
        $fields['separatorPersonal']['first_name'] = array('type' => 'textbox', 'title' => A::t('auctions', 'First Name'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32), 'htmlOptions' => array('maxlength' => '32', 'class' => 'form-control'));
        $fields['separatorPersonal']['last_name'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Last Name'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32), 'htmlOptions' => array('maxlength' => '32', 'class' => 'form-control'));
        $fields['separatorPersonal']['company'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Company'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 128, 'unique' => false), 'htmlOptions' => array('maxlength' => '128', 'autocomplete' => 'off', 'class' => 'form-control'));

        $fields['separatorContact'] = array();
        $fields['separatorContact']['separatorInfo'] = array('legend' => A::t('auctions', 'Contact Information'));
        $fields['separatorContact']['phone'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Phone'), 'default' => '', 'validation' => array('required' => true, 'type' => 'phoneString', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off', 'class' => 'form-control'));
        $fields['separatorContact']['fax'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Fax'), 'default' => '', 'validation' => array('required' => false, 'type' => 'phoneString', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off', 'class' => 'form-control'));

        $fields['separatorAddress'] = array();
        $fields['separatorAddress']['separatorInfo'] = array('legend' => A::t('auctions', 'Address Information'));
        $fields['separatorAddress']['address'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Address'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off', 'class' => 'form-control'));
        $fields['separatorAddress']['address_2'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Address (line 2)'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off', 'class' => 'form-control'));
        $fields['separatorAddress']['city'] = array('type' => 'textbox', 'title' => A::t('auctions', 'City'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off', 'class' => 'form-control'));
        $fields['separatorAddress']['zip_code'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Zip Code'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off', 'class' => 'form-control'));
        $fields['separatorAddress']['country_code'] = array('type' => 'select', 'title' => A::t('auctions', 'Country'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($countries)), 'data' => $countries, 'htmlOptions' => array('onchange' => $onchange, 'class' => 'form-control'));
        $fields['separatorAddress']['state'] = array('type' => 'textbox', 'title' => A::t('auctions', 'State/Province'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 50, 'unique' => false), 'htmlOptions' => array('maxlength' => 50, 'autocomplete' => 'off', 'class' => 'form-control'));

        $fields['separatorOtherInfo'] = array();
        $fields['separatorOtherInfo']['separatorInfo'] = array('legend' => A::t('auctions', 'Other'));
        $fields['separatorOtherInfo']['is_default'] = array('type' => 'checkbox', 'title' => A::t('auctions', 'Default'), 'validation' => array('type' => 'set', 'source' => array(0, 1)), 'htmlOptions' => ($address->is_default ? array('disabled' => 'disabled', 'uncheckValue' => $address->is_default) : array()));

        echo CWidget::create('CDataForm', array(
            'model' => 'Modules\Auctions\Models\ShipmentAddress',
            'primaryKey' => $address->id,
            'operationType' => 'edit',
            'action' => 'members/editMyShipmentAddress/id/' . $address->id . '/memberId/' . $memberId,
            'successUrl' => 'members/myShipmentAddress/memberId/' . $memberId,
            'cancelUrl' => 'members/myShipmentAddress/memberId/' . $memberId,
            'method' => 'post',
            'htmlOptions'       => array(
                'name'              => $formName,
                'id'                => $formName,
                'class'             => 'signup',
                'autoGenerateId'    => true,
            ),
            'requiredFieldsAlert' => true,
            'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
            'fields' => $fields,
            'buttons' => array(
                'submitUpdateClose' => array('type' => 'submit', 'value' => A::t('app', 'Update & Close'), 'htmlOptions' => array('name' => 'btnUpdateClose', 'class' => 'btn v-btn v-btn-default v-small-button')),
                'submitUpdate' => array('type' => 'submit', 'value' => A::t('app', 'Update'), 'htmlOptions' => array('name' => 'btnUpdate', 'class' => 'btn v-btn v-btn-default v-small-button')),
                'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'btn v-btn v-third-dark v-small-button')),
            ),
            'buttonsPosition' => 'bottom',
            'messagesSource' => 'core',
            'alerts' => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Address')),
            'return' => true,
        ));
        ?>
    </div>
<?php
A::app()->getClientScript()->registerScript(
    'changeCountry',
    'addChangeCountry = function (country,stateCode){
        auctions_changeCountry("'.$formName.'",country,stateCode,"frontend");
    }

    jQuery(document).ready(function(){
        var country = "'.$countryCode.'";
        var stateCode = "'.$stateCode.'";

        auctions_changeCountry("'.$formName.'",country,stateCode,"frontend");
    });
    ',
    1
);