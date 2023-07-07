<?php
    $this->_activeMenu = 'members/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Members Management'), 'url'=>'members/manage'),
        array('label'=>A::t('auctions', 'Edit Member')),
    );

    $formName = 'frmMembersEdit';
    $onchange = "addChangeCountry(this.value,'')";
?>

<h1><?= A::t('auctions', 'Members Management'); ?></h1>

<div class="bloc">
<?= $tabs; ?>
    <div class="sub-title">

        <a class="sub-tab active"><?= A::t('auctions', 'Edit Member'); ?></a>
        &raquo;
        <a class="sub-tab" href="members/bidsHistory/memberId/<?= $id; ?>"><?= A::t('auctions', 'Bids History'); ?></a>
        <a class="sub-tab" href="members/ordersHistory/memberId/<?= $id; ?>"><?= A::t('auctions', 'Orders History'); ?></a>
    </div>
    <div class="content">

        <?php
        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Auctions\Models\Members',
            'primaryKey'        => $id,
            'operationType'     => 'edit',
            'action'            => 'members/edit/id/'.$id,
            'successUrl'        => 'members/manage',
            'cancelUrl'         => 'members/manage',
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'name'              => $formName,
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert' => true,
            'fields'    => array(
                'separatorPersonal' => array(
                    'separatorInfo' => array('legend'=>A::t('auctions', 'Personal Information')),
                    'first_name' 	=> array('type'=>'textbox', 'title'=>A::t('auctions', 'First Name'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['first_name']['required'], 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>50)),
                    'last_name'  	=> array('type'=>'textbox', 'title'=>A::t('auctions', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['last_name']['required'], 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>50)),
                    'gender'        => array('type'=>'select', 'title'=>A::t('auctions', 'Gender'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['gender']['required'], 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>A::t('app', '-- select --'), 'htmlOptions'=>array('maxlength'=>10)),
                    'birth_date'    => array('type'=>'datetime', 'title'=>A::t('auctions', 'Birth Date'), 'validation'=>array('required'=>$paramsFormRegistration['birth_date']['required'], 'type'=>'date', 'maxLength'=>10, 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'maxDate'=>'1', 'yearRange'=>'-100:+0', 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px'), 'definedValues'=>array()),
                ),
                'separatorContact' => array(
                    'separatorInfo' => array('legend'=>A::t('auctions', 'Contact Information')),
                    'website'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Website'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['website']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off')),
                    'company'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Company'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['company']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off')),
                    'phone'         => array('type'=>'textbox', 'title'=>A::t('auctions', 'Phone'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['phone']['required'], 'type'=>'phoneString', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off')),
                    'fax'           => array('type'=>'textbox', 'title'=>A::t('auctions', 'Fax'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['fax']['required'], 'type'=>'phoneString', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off')),
                ),
                'separatorAddress' => array(
                    'separatorInfo' => array('legend'=>A::t('auctions', 'Address Information')),
                    'address'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Address'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['address']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off')),
                    'address_2'     => array('type'=>'textbox', 'title'=>A::t('auctions', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['address_2']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off')),
                    'city'          => array('type'=>'textbox', 'title'=>A::t('auctions', 'City'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['city']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off')),
                    'zip_code'      => array('type'=>'textbox', 'title'=>A::t('auctions', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['zip_code']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'medium')),
                    'country_code'  => array('type'=>'select', 'title'=>A::t('auctions', 'Country'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['country_code']['required'], 'type'=>'set', 'source'=>array_keys($countries)), 'data'=>$countries, 'htmlOptions'=>array('onchange'=>$onchange)),
                    'state'         => array('type'=>'textbox', 'title'=>A::t('auctions', 'State/Province'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['state']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off')),
                ),
                'separatorAccount' => array(
                    'separatorInfo'     => array('legend'=>A::t('auctions', 'Account Information')),
                    'email'             => array('type'=>'textbox', 'title'=>A::t('auctions', 'Email'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['email']['required'], 'type'=>'email', 'maxLength'=>100), 'htmlOptions'=>array('maxlength'=>100, 'autocomplete'=>'off', 'class'=>'middle')),
                    'username'          => array('type'=>'label', 'title'=>A::t('auctions', 'Username'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false),
                    'password'          => array('type'=>'password', 'title'=>A::t('auctions', 'Password'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxLength'=>25), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>$salt), 'htmlOptions'=>array('maxlength'=>25, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'), 'disabled'=>$changePassword ? false : true),
                    'passwordRetype'    => array('type'=>'password', 'title'=>A::t('auctions', 'Confirm Password'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxlength'=>25), 'htmlOptions'=>array('maxlength'=>25, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;'), 'disabled'=>$changePassword ? false : true),
                    'language_code'     => array('type'=>'select', 'title'=>A::t('auctions', 'Preferred Language'), 'tooltip'=>'', 'default'=>A::app()->getLanguage(), 'validation'=>array('required'=>$paramsFormRegistration['language_code']['required'], 'type'=>'set', 'source'=>array_keys($langList)), 'data'=>$langList),
                    'is_active'         => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                    'is_removed'        => array('type'=>'checkbox', 'title'=>A::t('app', 'Removed'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                ),
                'separatorOther' => array(
                    'separatorInfo' => array('legend'=>A::t('auctions', 'Other')),
                    'notifications' => array('type'=>'checkbox', 'title'=>A::t('auctions', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                    'comments'      => array('type'=>'textarea', 'title'=>A::t('auctions', 'Comments'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>2048)),
                ),
            ),
            'buttons' => array(
                'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'messagesSource'    => 'core',
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Member')),
            'return'            => true,
        ));
        ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'changeCountry',
    'addChangeCountry = function (country,stateCode){
            var ajax = null;
            jQuery("select#'.$formName.'_state").chosen("destroy");
            ajax = auctions_changeCountry("'.$formName.'",country,stateCode,"backend");
            if(ajax == null){
                jQuery("select#'.$formName.'_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#'.$formName.'_state").chosen();
                });
            }
        }

        jQuery(document).ready(function(){
            var country = "'.$countryCode.'";
            var stateCode = "'.$stateCode.'";

            ajax = auctions_changeCountry("'.$formName.'",country,stateCode,"backend");
            if(ajax == null){
                jQuery("select#'.$formName.'_state").chosen("destroy");
                jQuery("select#'.$formName.'_state").chosen();
            }else{
                ajax.done(function (){
                    jQuery("select#'.$formName.'_state").chosen("destroy");
                    jQuery("select#'.$formName.'_state").chosen();
                });
            }
        });',
    1
);