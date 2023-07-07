<?php
$this->_activeMenu = 'members/manage';
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Modules'), 'url' => $backendPath . 'modules/'),
    array('label' => A::t('auctions', 'Auctions'), 'url' => $backendPath . 'modules/settings/code/auctions'),
    array('label' => A::t('auctions', 'Members Management'), 'url' => 'members/manage'),
    array('label' => A::t('auctions', 'Edit Address')),
);

$formName = 'frmAddressEdit';
$onchange = "addChangeCountry(this.value,'')";
?>

    <h1><?= A::t('auctions', 'Addresses Management'); ?></h1>

    <div class="bloc">
        <?= $tabs; ?>
        <div class="sub-title">
            <a class="sub-tab previous"
               href="members/shipmentAddress/memberId/<?= $memberId; ?>"><?= A::t('auctions', 'Addresses'); ?></a>
            » <?= A::t('auctions', 'Edit Address'); ?>
        </div>
        <div class="content">
            <?php
            $fields = array();

            $fields['separatorPersonal'] = array();
            $fields['separatorPersonal']['separatorInfo'] = array('legend' => A::t('auctions', 'Personal Information'));
            $fields['separatorPersonal']['first_name'] = array('type' => 'textbox', 'title' => A::t('auctions', 'First Name'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32), 'htmlOptions' => array('maxlength' => '32'));
            $fields['separatorPersonal']['last_name'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Last Name'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32), 'htmlOptions' => array('maxlength' => '32'));
            $fields['separatorPersonal']['company'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Company'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 128, 'unique' => false), 'htmlOptions' => array('maxlength' => '128', 'autocomplete' => 'off'));

            $fields['separatorContact'] = array();
            $fields['separatorContact']['separatorInfo'] = array('legend' => A::t('auctions', 'Contact Information'));
            $fields['separatorContact']['phone'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Phone'), 'default' => '', 'validation' => array('required' => true, 'type' => 'phoneString', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off'));
            $fields['separatorContact']['fax'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Fax'), 'default' => '', 'validation' => array('required' => false, 'type' => 'phoneString', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off'));

            $fields['separatorAddress'] = array();
            $fields['separatorAddress']['separatorInfo'] = array('legend' => A::t('auctions', 'Address Information'));
            $fields['separatorAddress']['address'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Address'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off'));
            $fields['separatorAddress']['address_2'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Address (line 2)'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off'));
            $fields['separatorAddress']['city'] = array('type' => 'textbox', 'title' => A::t('auctions', 'City'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 64, 'unique' => false), 'htmlOptions' => array('maxlength' => '64', 'autocomplete' => 'off'));
            $fields['separatorAddress']['zip_code'] = array('type' => 'textbox', 'title' => A::t('auctions', 'Zip Code'), 'default' => '', 'validation' => array('required' => true, 'type' => 'text', 'maxLength' => 32, 'unique' => false), 'htmlOptions' => array('maxlength' => '32', 'autocomplete' => 'off', 'class' => 'small'));
            $fields['separatorAddress']['country_code'] = array('type' => 'select', 'title' => A::t('auctions', 'Country'), 'tooltip' => '', 'default' => '', 'validation' => array('required' => true, 'type' => 'set', 'source' => array_keys($countries)), 'data' => $countries, 'htmlOptions' => array('onchange' => $onchange));
            $fields['separatorAddress']['state'] = array('type' => 'textbox', 'title' => A::t('auctions', 'State/Province'), 'default' => '', 'validation' => array('required' => false, 'type' => 'text', 'maxLength' => 50, 'unique' => false), 'htmlOptions' => array('maxlength' => 50, 'autocomplete' => 'off'));

            $fields['separatorOtherInfo'] = array();
            $fields['separatorOtherInfo']['separatorInfo'] = array('legend' => A::t('auctions', 'Other'));
            $fields['separatorOtherInfo']['is_default'] = array('type' => 'checkbox', 'title' => A::t('auctions', 'Default'), 'validation' => array('type' => 'set', 'source' => array(0, 1)), 'htmlOptions' => ($address->is_default ? array('disabled' => 'disabled', 'uncheckValue' => $address->is_default) : array()), 'viewType' => 'custom');

            echo CWidget::create('CDataForm', array(
                'model' => 'Modules\Auctions\Models\ShipmentAddress',
                'primaryKey' => $address->id,
                'operationType' => 'edit',
                'action' => 'members/editShipmentAddress/id/' . $address->id . '/memberId/' . $memberId,
                'successUrl' => 'members/shipmentAddress/memberId/' . $memberId,
                'cancelUrl' => 'members/shipmentAddress/memberId/' . $memberId,
                'method' => 'post',
                'htmlOptions' => array(
                    'id' => $formName,
                    'name' => $formName,
                    'autoGenerateId' => true
                ),
                'requiredFieldsAlert' => false,
                'fields' => $fields,
                'buttons' => array(
                    'submitUpdateClose' => array('type' => 'submit', 'value' => A::t('app', 'Update & Close'), 'htmlOptions' => array('name' => 'btnUpdateClose', 'class' => 'button')),
                    'submitUpdate' => array('type' => 'submit', 'value' => A::t('app', 'Update'), 'htmlOptions' => array('name' => 'btnUpdate', 'class' => 'button')),
                    'cancel' => array('type' => 'button', 'value' => A::t('app', 'Cancel'), 'htmlOptions' => array('name' => '', 'class' => 'button white')),
                ),
                'buttonsPosition' => 'bottom',
                'messagesSource' => 'core',
                'return' => true,
            ));
            ?>
        </div>
    </div>
<?php
A::app()->getClientScript()->registerScript(
    'changeCountry',
    'addChangeCountry = function (country,stateCode){
            var ajax = null;
            jQuery("select#' . $formName . '_state").chosen("destroy");
            ajax = auctions_changeCountry("' . $formName . '",country,stateCode,"backend");
            if(ajax == null){
                jQuery("select#' . $formName . '_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#' . $formName . '_state").chosen();
                });
            }
        }

        jQuery(document).ready(function(){
            var country = "' . $countryCode . '";
            var stateCode = "' . $stateCode . '";

            ajax = auctions_changeCountry("' . $formName . '",country,stateCode,"backend");
            if(ajax == null){
                jQuery("select#' . $formName . '_state").chosen("destroy");
                jQuery("select#' . $formName . '_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#' . $formName . '_state").chosen("destroy");
                    jQuery("select#' . $formName . '_state").chosen();
                });
            }
        });',
    1
);
