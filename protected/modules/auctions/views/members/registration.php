<?php
$this->_pageTitle = A::t('auctions', 'Member Registration');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Member Registration')),
);
$formName = 'frmMembersRegistration';
$onchange = "addChangeCountry(this.value,'')";
?>

<div class="col-sm-5 col-sm-offset-1">
    <p class="v-smash-text-large pull-top">
        <span><?= A::t('auctions', 'Registration'); ?></span>
    </p>
    <div class="horizontal-break left"></div>

    <ul class="v-list-v2">
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'Free Registration.'); ?></span></li>
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'Registration in two clicks.'); ?></span></li>
        <li class="v-animation" data-animation="fade-from-right" data-delay="900"><i class="fa fa-check"></i><span class="v-lead">Lorem ipsum dolor sit amet, consectetur.</span></li>
    </ul>
</div>
<div class="col-sm-5">

    <div id="message_success" class="alert alert-success" style="display: none;">
        <label><?= $messageSuccess; ?></label>
    </div>
    <div id="message_info" class="alert alert-info" style="display: none;">
        <label><?= $messageInfo; ?></label>
    </div>
    <div id="message_error" class="alert alert-error" style="display: none;">
        <label><?= $messageError; ?></label>
    </div>

    <?php
    $termsConditionTitle = A::t('auctions', 'By signing up, I agree to the {terms_and_conditions}',
        array(
            '{terms_and_conditions}'=>'<a target="_blank" rel="noopener noreferrer" id="linkTermCondition" href="members/termsConditions">'.A::t('auctions', 'Terms & Conditions').'</a>',
        ));
    echo CWidget::create('CFormView', array(
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'id'              => $formName,
            'class'             => 'signup',
            'autoGenerateId'    => true,
        ),
        'requiredFieldsAlert' => false,
        'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group mb5'),
        'fields'    => array(
            'separatorPersonal' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Personal Information'), 'disabled'=>$paramsFormRegistration['separatorPersonal']['disabled']),
                'first_name'        => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['first_name']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'autofocus'=>'autofocus', 'data-required'=>$paramsFormRegistration['first_name']['required'], 'placeholder'=>A::t('auctions', 'First Name'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['first_name']['disabled']),
                'first_name_alert'  => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'First Name'))), 'htmlOptions'=>array('id'=>'first_name_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['first_name']['disabled']),
                'last_name'         => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['last_name']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['last_name']['required'], 'placeholder'=>A::t('auctions', 'Last Name'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['last_name']['disabled']),
                'last_name_alert'   => array('type'=>'label', ')title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Last Name'))), 'htmlOptions'=>array('id'=>'last_name_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['last_name']['disabled']),
                'gender'            => array('type'=>'select', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['gender']['required'], 'data'=>$genders, 'emptyOption'=>true, 'emptyValue'=>A::t('auctions', 'Gender'), 'htmlOptions'=>array('maxlength'=>10, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['gender']['required']), 'disabled'=> $paramsFormRegistration['gender']['disabled']),
                'gender_alert'      => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Gender'))), 'htmlOptions'=>array('id'=>'gender_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['gender']['disabled']),
                'birth_date'        => array('type'=>'datetime', '_title'=>'', 'maxDate'=>'1', 'yearRange'=>'-100:+0', 'mandatoryStar'=>$paramsFormRegistration['birth_date']['required'], 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px', 'class'=>'form-control date_time_picker', 'data-required'=>$paramsFormRegistration['birth_date']['required'], 'placeholder'=>A::t('auctions', 'Birth Date'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['birth_date']['disabled']),
                'birth_date_alert'  => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Birth Date'))), 'htmlOptions'=>array('id'=>'birth_date_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['birth_date']['disabled']),
            ),
            'separatorContact' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Contact Information'), 'disabled'=>$paramsFormRegistration['separatorContact']['disabled']),
                'website'       => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['website']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['website']['required'], 'placeholder'=>A::t('auctions', 'Website'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['website']['disabled']),
                'website_alert' => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Website'))), 'htmlOptions'=>array('id'=>'website_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['website']['disabled']),
                'company'       => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['company']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['company']['required'], 'placeholder'=>A::t('auctions', 'Company'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['company']['disabled']),
                'company_alert' => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Company'))), 'htmlOptions'=>array('id'=>'company_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['company']['disabled']),
                'phone'         => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['phone']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['phone']['required'], 'placeholder'=>A::t('auctions', 'Phone'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['phone']['disabled']),
                'phone_alert'   => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Phone'))), 'htmlOptions'=>array('id'=>'phone_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['phone']['disabled']),
                'fax'           => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['fax']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['fax']['required'], 'placeholder'=>A::t('auctions', 'Fax'), 'autocomplete'=>'off'), 'disabled'=> $paramsFormRegistration['fax']['disabled']),
                'fax_alert'     => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Fax'))), 'htmlOptions'=>array('id'=>'fax_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['fax']['disabled']),
            ),
            'separatorAddress' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Address Information'), 'disabled'=>$paramsFormRegistration['separatorAddress']['disabled']),
                'address'               => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['address']['required'], 'htmlOptions'=>array('maxlength'=>125, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['address']['required'], 'autocomplete'=>'off', 'placeholder'=>A::t('auctions', 'Address')), 'disabled'=> $paramsFormRegistration['address']['disabled']),
                'address_alert'         => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Address'))), 'htmlOptions'=>array('id'=>'address_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['address']['disabled']),
                'address_2'             => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['address_2']['required'], 'htmlOptions'=>array('maxlength'=>125, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['address_2']['required'], 'autocomplete'=>'off', 'placeholder'=>A::t('auctions', 'Address (line 2)')), 'disabled'=> $paramsFormRegistration['address_2']['disabled']),
                'address_2_alert'       => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Address (line 2)'))), 'htmlOptions'=>array('id'=>'address_2_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['address_2']['disabled']),
                'city'                  => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['city']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['city']['required'], 'autocomplete'=>'off', 'placeholder'=>A::t('auctions', 'City')), 'disabled'=> $paramsFormRegistration['city']['disabled']),
                'city_alert'            => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'City'))), 'htmlOptions'=>array('id'=>'city_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['city']['disabled']),
                'zip_code'              => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['zip_code']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['zip_code']['required'], 'autocomplete'=>'off', 'placeholder'=>A::t('auctions', 'Zip Code')), 'disabled'=> $paramsFormRegistration['zip_code']['disabled']),
                'zip_code_alert'        => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Zip Code'))), 'htmlOptions'=>array('id'=>'zip_code_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['zip_code']['disabled']),
                'country_code'          => array('type'=>'select', '_title'=>'', 'default'=>$defaultCountryCode, 'mandatoryStar'=>$paramsFormRegistration['country_code']['required'], 'emptyOption'=>true, 'emptyValue'=>A::t('auctions', 'Country'), 'data'=>$countries, 'htmlOptions'=>array('maxlength'=>10, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['country_code']['required'], 'onchange'=>$onchange), 'disabled'=> $paramsFormRegistration['country_code']['disabled']),
                'country_code_alert'    => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Country'))), 'htmlOptions'=>array('id'=>'country_code_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['country_code']['disabled']),
                'state'                 => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['state']['required'], 'htmlOptions'=>array('maxlength'=>50, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['state']['required'], 'autocomplete'=>'off', 'placeholder'=>A::t('auctions', 'State/Province')), 'disabled'=> $paramsFormRegistration['state']['disabled']),
                'state_alert'           => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'State/Province'))), 'htmlOptions'=>array('id'=>'state_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['state']['disabled']),
            ),
            'separatorAccount' => array(
                'separatorInfo'                 => array('legend'=>A::t('auctions', 'Account Information'), 'disabled'=>$paramsFormRegistration['separatorAccount']['disabled']),
                'email'                         => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>100, 'autocomplete'=>'off', 'class'=>'middle form-control', 'data-required'=>'true', 'placeholder'=>A::t('auctions', 'Email')), 'disabled'=> $paramsFormRegistration['email']['disabled']),
                'email_alert'                   => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Email'))), 'htmlOptions'=>array('id'=>'email_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['email']['disabled']),
                'email_alert_valid'             => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'You must provide a valid email address!'), 'htmlOptions'=>array('id'=>'email_alert_valid', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['email']['disabled']),
                'username'                      => array('type'=>'textbox', '_title'=>'', 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>32, 'autocomplete'=>'off', 'class'=>'form-control', 'placeholder'=>A::t('auctions', 'Username'))),
                'username_alert'                => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Username'))), 'htmlOptions'=>array('id'=>'username_alert', 'style'=>'display:none', 'class'=>'alert alert-error')),
                'password'                      => array('type'=>'password', '__title'=>A::t('auctions', 'Password'), 'default'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>25, 'placeholder'=>A::t('auctions', 'Password'), 'class'=>'form-control')),
                'password_alert'                => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Password'))), 'htmlOptions'=>array('id'=>'password_alert', 'style'=>'display:none', 'class'=>'alert alert-error')),
                'confirm_password'              => array('type'=>'password', '__title'=>A::t('auctions', 'Confirm Password'), 'default'=>'', 'mandatoryStar'=>$paramsFormRegistration['confirm_password']['required'], 'htmlOptions'=>array('maxlength'=>25, 'data-required'=>$paramsFormRegistration['confirm_password']['required'], 'placeholder'=>A::t('auctions', 'Confirm Password'), 'class'=>'form-control'), 'disabled'=> $paramsFormRegistration['confirm_password']['disabled']),
                'confirm_password_alert'        => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Confirm Password'))), 'htmlOptions'=>array('id'=>'confirm_password_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['confirm_password']['disabled']),
                'confirm_password_alert_equal'  => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The password field must match the password confirmation field!'), 'htmlOptions'=>array('id'=>'confirm_password_alert_equal', 'style'=>'display:none', 'class'=>'alert alert-error')),
                'language_code'                 => array('type'=>'select', '_title'=>'', 'default'=>A::app()->getLanguage(), 'emptyOption'=>true, 'emptyValue'=>A::t('auctions', 'Preferred Language'),  'data'=>$langList, 'mandatoryStar'=>$paramsFormRegistration['language_code']['required'], 'htmlOptions'=>array('maxlength'=>10, 'class'=>'form-control', 'data-required'=>$paramsFormRegistration['language_code']['required']), 'disabled'=> $paramsFormRegistration['language_code']['disabled']),
                'language_code_alert'           => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Preferred Language'))), 'htmlOptions'=>array('id'=>'language_code_alert', 'style'=>'display:none', 'class'=>'alert alert-error'), 'disabled'=> $paramsFormRegistration['language_code']['disabled']),
                'notifications'                 => array('type'=>'checkbox', 'title'=>A::t('auctions', 'Send Notifications'), 'default'=>'0', 'mandatoryStar'=>false, 'htmlOptions'=>array('class'=>'ml5')),
            ),
            'i_agree'       => array('type'=>'checkbox', 'appendCode'=>$termsConditionTitle, 'default'=>'0', 'mandatoryStar'=>true,  'htmlOptions'=>array('class'=>'mr5')),
            'i_agree_alert' => array('type'=>'label', 'title'=>'', 'default'=>A::t('auctions', 'You must agree with the terms and conditions before you create an account.'), 'htmlOptions'=>array('id'=>'i_agree_alert', 'style'=>'display:none', 'class'=>'alert alert-error')),
            'captcha'       => array('type'=>'captcha',  '_title'=>'', 'tooltip'=>'', 'value'=>'', 'disabled'=> ($verificationCaptcha && !$paramsFormRegistration['captcha']['disabled']) ? false : true),
			'captcha_alert' => array('type'=>'label', '_title'=>'', 'default'=>A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Captcha'))), 'htmlOptions'=>array('id'=>$formName.'_captcha_alert', 'style'=>'display:none', 'class'=>'alert alert-error')),
        ),
        'buttons' => array(
            'custom' => array('type'=>'button', 'value'=>A::t('auctions', 'Register'), 'htmlOptions'=>array('id'=>'memberRegistration', 'name'=>'memberRegistration', 'data-sending'=>A::t('auctions', 'Sending...'), 'data-send'=>A::t('auctions', 'Register'), 'data-form-name'=>$formName, 'class'=>'btn v-btn v-btn-default v-small-button')),
        ),
    ));
    ?>
</div>
<div class="container">
    <div class="v-spacer col-sm-12 v-height-standard"></div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'addClassToCaptcha',
    '
    $(document).ready(function() {
       $(".captcha-result").addClass("form-control");
       $(".captcha-result").attr("placeholder","'.A::t('auctions', 'Captcha').'");
    });
    ',
    4
);

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
