<?php
    $this->_pageTitle = A::t('auctions', 'Restore Password');
    $this->_breadCrumbs = array(
        array('label'=> A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('auctions', 'Member Login'), 'url'=>'members/login'),
        array('label' => A::t('auctions', 'Restore Password'))
    );
?>
<div class="col-sm-5 col-sm-offset-1">
    <p class="v-smash-text-large pull-top">
        <span><?= A::t('auctions', 'How It Works'); ?>:</span>
    </p>
    <ul class="v-list-v2">
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'Password recovery instructions'); ?></span></li>
    </ul>
</div>
<div class="col-sm-5">
    <div id="email_empty" class="alert alert-error" style="display:none">
        <label><?= A::t('auctions', 'The field {field_name} cannot be empty!', array('{field_name}'=>A::t('auctions', 'Email'))); ?></label>
    </div>
    <div id="email_valid" class="alert alert-error" style="display:none">
        <label><?= A::t('auctions', 'You must provide a valid email address!'); ?></label>
    </div>
    <?= $actionMessage; ?>
    <?php

    echo CWidget::create('CFormView', array(
        'action'	        => 'members/restorePassword',
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => 'frmRestorePassword',
            'id'                => 'frmRestorePassword',
            'class'             => 'signup',
            'autoGenerateId'    => true,
        ),
        'requiredFieldsAlert' => true,
        'fieldWrapper'=>array('tag'=>'div', 'class'=>'form-group'),
        'fields'    => array(
            'act'                   => array('type'=>'hidden', 'value'=>'send'),
            'email'                 => array('type'=>'textbox', 'title'=>A::t('auctions', 'Email'), 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>100, 'id'=>'email', 'class'=>'form-control', 'autofocus'=>'autofocus', 'placeholder'=>A::t('auctions', 'Enter Email'), 'autocomplete'=>'off')),
        ),
        'buttons' => array(
            'custom' => array('type'=>'button', 'value'=>A::t('auctions', 'Get New Password'), 'htmlOptions'=>array('id'=>'memberRestorePassword', 'data-sending'=>A::t('auctions', 'Sending...'), 'class'=>'btn v-btn v-btn-default v-small-button')),
        ),
    ));
    ?>
</div>

<div class="container">
    <div class="v-spacer col-sm-12 v-height-standard"></div>
</div>

<div class="col-sm-5 col-sm-offset-3">

</div>
