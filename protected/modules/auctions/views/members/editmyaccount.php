<?php
$this->_pageTitle = A::t('auctions', 'Edit Account');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label'=> A::t('auctions', 'My Account'), 'url'=>'members/myAccount'),
    array('label' => A::t('auctions', 'Edit Account')),
);
$formName = 'frmMembersRegistration';
$onchange = "addChangeCountry(this.value,'')";
?>

<div class="col-sm-12">
<?php
    echo $actionMessage;
    echo CWidget::create('CDataForm', array(
        'model'             => 'Modules\Auctions\Models\Members',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'members/editMyAccount',
        'successUrl'        => 'members/myAccount',
        'cancelUrl'         => 'members/myAccount',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'id'                => $formName,
            'class'             => 'signup',
            'autoGenerateId'    => true,
        ),
        'requiredFieldsAlert' => true,
        'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
        'fields' => array(
            'separatorPersonal' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Personal Information')),
                'first_name' 	=> array('type'=>'textbox', 'title'=>A::t('auctions', 'First Name'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['first_name']['required'], 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control'), 'autocomplete'=>'off'),
                'last_name'  	=> array('type'=>'textbox', 'title'=>A::t('auctions', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['last_name']['required'], 'type'=>'text', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control'), 'autocomplete'=>'off'),
                'gender'        => array('type'=>'select', 'title'=>A::t('auctions', 'Gender'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['gender']['required'], 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>A::t('app', '-- select --'), 'htmlOptions'=>array('maxlength'=>10, 'class'=>'form-control')),
                'birth_date'    => array('type'=>'datetime', 'title'=>A::t('auctions', 'Birth Date'), 'validation'=>array('required'=>$paramsFormRegistration['birth_date']['required'], 'type'=>'date', 'maxLength'=>10, 'minValue'=>(date('Y')-110).'-00-00', 'maxValue'=>date('Y-m-d')), 'maxDate'=>'1', 'yearRange'=>'-100:+0', 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px', 'class'=>'form-control date_time_picker'), 'definedValues'=>array(), 'autocomplete'=>'off'),
            ),
            'separatorContact' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Contact Information')),
                'website'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Website'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['website']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off', 'class'=>'form-control')),
                'company'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Company'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['company']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off', 'class'=>'form-control')),
                'phone'         => array('type'=>'textbox', 'title'=>A::t('auctions', 'Phone'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['phone']['required'], 'type'=>'phoneString', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'form-control')),
                'fax'           => array('type'=>'textbox', 'title'=>A::t('auctions', 'Fax'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['fax']['required'], 'type'=>'phoneString', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'form-control')),
            ),
            'separatorAddress' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Address Information')),
                'address'       => array('type'=>'textbox', 'title'=>A::t('auctions', 'Address'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['address']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off', 'class'=>'form-control')),
                'address_2'     => array('type'=>'textbox', 'title'=>A::t('auctions', 'Address (line 2)'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['address_2']['required'], 'type'=>'text', 'maxLength'=>125, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>125, 'autocomplete'=>'off', 'class'=>'form-control')),
                'city'          => array('type'=>'textbox', 'title'=>A::t('auctions', 'City'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['city']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'form-control')),
                'zip_code'      => array('type'=>'textbox', 'title'=>A::t('auctions', 'Zip Code'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['zip_code']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'medium', 'class'=>'form-control')),
                'country_code'  => array('type'=>'select', 'title'=>A::t('auctions', 'Country'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['country_code']['required'], 'type'=>'set', 'source'=>array_keys($countries)), 'data'=>$countries, 'htmlOptions'=>array('onchange'=>$onchange, 'class'=>'form-control')),
                'state'         => array('type'=>'textbox', 'title'=>A::t('auctions', 'State/Province'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['state']['required'], 'type'=>'text', 'maxLength'=>50, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>50, 'autocomplete'=>'off', 'class'=>'form-control')),
            ),
            'separatorAccount' => array(
                'separatorInfo'     => array('legend'=>A::t('auctions', 'Account Information')),
                'email'             => array('type'=>'textbox', 'title'=>A::t('auctions', 'Email'), 'default'=>'', 'validation'=>array('required'=>$paramsFormRegistration['email']['required'], 'type'=>'email', 'maxLength'=>100), 'htmlOptions'=>array('maxlength'=>100, 'autocomplete'=>'off', 'class'=>'middle', 'class'=>'form-control')),
                'username'          => array('type'=>'label', 'title'=>A::t('auctions', 'Username'), 'default'=>'', 'tooltip'=>'', 'definedValues'=>array(), 'htmlOptions'=>array('class'=>'form-control'), 'format'=>'', 'stripTags'=>false),
                'password'          => array('type'=>'password', 'title'=>A::t('auctions', 'Password'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxLength'=>25), 'htmlOptions'=>array('maxlength'=>25, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;', 'class'=>'form-control'), 'autocomplete'=>'off'),
                'passwordRetype'    => array('type'=>'password', 'title'=>A::t('auctions', 'Confirm Password'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxlength'=>25), 'htmlOptions'=>array('maxlength'=>25, 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;', 'class'=>'form-control'), 'autocomplete'=>'off'),
                'language_code'     => array('type'=>'select', 'title'=>A::t('auctions', 'Preferred Language'), 'tooltip'=>'', 'default'=>A::app()->getLanguage(), 'validation'=>array('required'=>$paramsFormRegistration['language_code']['required'], 'type'=>'set', 'source'=>array_keys($langList)), 'data'=>$langList, 'htmlOptions'=>array('class'=>'form-control')),
            ),
            'separatorOther' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Other')),
                'notifications' => array('type'=>'checkbox', 'title'=>A::t('auctions', 'Notifications'), 'default'=>'0', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array('class'=>'ml5')),
            ),
        ),
        'buttons' => array(
            'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose', 'class'=>'btn v-btn v-btn-default v-small-button')),
            'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate', 'class'=>'btn v-btn v-btn-default v-small-button')),
            'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'btn v-btn v-third-dark v-small-button')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Member')),
        'return'            => true,
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