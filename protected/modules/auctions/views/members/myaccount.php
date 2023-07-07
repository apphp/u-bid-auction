<?php
$this->_pageTitle = A::t('auctions', 'My Account');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Account')),
);
$formName = 'frmMyAccount';
?>

<div class="col-sm-12">
<?php
    echo $actionMessage;
    echo CWidget::create('CFormView', array(
        'action'            => 'members/myAccount',
        'cancelUrl'         => 'members/dashboard',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'id'                => $formName,
            'class'             => 'signup',
            'autoGenerateId'    => true,
        ),
        'requiredFieldsAlert' => false,
        'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
        'fields' => array(
            'separatorPersonal' => array(
                'separatorInfo'     => array('legend'=>A::t('auctions', 'Personal Information')),
                'first_name' 		=> array('type'=>'label', 'title'=>A::t('auctions', 'First Name'), 'default'=>'--', 'value'=>$member->first_name),
                'last_name'  		=> array('type'=>'label', 'title'=>A::t('auctions', 'Last Name'),  'default'=>'--', 'value'=>$member->last_name),
                'gender'            => array('type'=>'label', 'title'=>A::t('auctions', 'Gender'),     'default'=>'--', 'value'=>$genders[$member->gender]),
                'birth_date'        => array('type'=>'label', 'title'=>A::t('auctions', 'Birth Date'), 'default'=>'--', 'value'=>($member->birth_date ? $member->birth_date : '--'), 'format'=>$dateFormat),
            ),
            'separatorContact' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Contact Information')),
                'website'         => array('type'=>'label', 'title'=>A::t('auctions', 'Website'), 'default'=>'--', 'value'=>$member->website),
                'company'         => array('type'=>'label', 'title'=>A::t('auctions', 'Company'), 'default'=>'--', 'value'=>$member->company),
                'phone'         => array('type'=>'label', 'title'=>A::t('auctions', 'Phone'), 'default'=>'--', 'value'=>$member->phone),
                'fax'           => array('type'=>'label', 'title'=>A::t('auctions', 'Fax'),   'default'=>'--', 'value'=>$member->fax),
            ),
            'separatorAddress' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Address Information')),
                'address'       => array('type'=>'label', 'title'=>A::t('auctions', 'Address'),          'default'=>'--', 'value'=>$member->address),
                'address_2'     => array('type'=>'label', 'title'=>A::t('auctions', 'Address (line 2)'), 'default'=>'--', 'value'=>$member->address_2),
                'city'          => array('type'=>'label', 'title'=>A::t('auctions', 'City'),             'default'=>'--', 'value'=>$member->city),
                'zip_code'      => array('type'=>'label', 'title'=>A::t('auctions', 'Zip Code'),         'default'=>'--', 'value'=>$member->zip_code),
                'country_code'  => array('type'=>'label', 'title'=>A::t('auctions', 'Country'),          'default'=>'--', 'value'=>(isset($countries[$member->country_code]) && $member->country_code) ? $countries[$member->country_code] : '--'),
                'state'         => array('type'=>'label', 'title'=>A::t('auctions', 'State/Province'),   'default'=>'--', 'value'=>isset($states[$member->state]) ? $states[$member->state] : $member->state),
            ),
            'separatorAccount' => array(
                'separatorInfo'     => array('legend'=>A::t('auctions', 'Account Information')),
                'email'             => array('type'=>'label', 'title'=>A::t('auctions', 'Email'),              'default'=>'--', 'value'=>$member->email),
                'username'          => array('type'=>'label', 'title'=>A::t('auctions', 'Username'),           'default'=>'--', 'value'=>$member->username),
                //'password'          => array('type'=>'label', 'title'=>A::t('auctions', 'Password'),           'default'=>'--', 'value'=>$member->password),
                'language_code'     => array('type'=>'label', 'title'=>A::t('auctions', 'Preferred Language'), 'default'=>'--', 'value'=>$langList[$member->language_code]),
            ),
            'separatorOther' => array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'Other')),
                'notifications'   => array('type'=>'label', 'title'=>A::t('auctions', 'Notifications'),     'default'=>'--', 'value'=>$member->notifications, 'definedValues'=>array('0'=>A::t('app', 'No'), '1'=>A::t('app', 'Yes'))),
                'created_at'      => array('type'=>'label', 'title'=>A::t('auctions', 'Created at'),        'default'=>'--', 'value'=>$member->created_at, 'format'=>$dateTimeFormat, 'definedValues'=>array('0000-00-00 00:00:00'=>'--')),
                'last_visited_at' => array('type'=>'label', 'title'=>A::t('auctions', 'Last visit at'),   'default'=>'--', 'value'=>$member->last_visited_at, 'format'=>$dateTimeFormat, 'definedValues'=>array('0000-00-00 00:00:00'=>'--')),
            ),
            'remove_account' => array('type'=>'html', 'title'=>A::t('auctions', 'Remove Account'), 'value'=>'<a href="members/removeAccount" class="btn v-btn v-third-dark v-small-button">'.A::t('auctions', 'Remove').'</a>',),
        ),
        'buttons' => array(
            'custom_1' => array('type'=>'button', 'value'=>A::t('auctions', 'Edit Account'), 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','members/editMyAccount');", 'class'=>'btn v-btn v-third-dark v-small-button')),
            'custom_2' => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','members/dashboard');", 'class'=>'btn v-btn v-third-dark v-small-button')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Member')),
        'return'            => true,
    ));
?>
</div>