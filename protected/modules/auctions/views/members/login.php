<?php
$this->_pageTitle = A::t('auctions', 'Member Login');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Member Login')),
);
?>

<div class="col-sm-5 col-sm-offset-1">
    <p class="v-smash-text-large pull-top">
        <span><?= A::t('auctions', 'Login'); ?></span>
    </p>
    <div class="horizontal-break left"></div>

    <ul class="v-list-v2">
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'Welcome to your website.'); ?></span></li>
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'By logging in, you agree to the Terms & Conditions.'); ?></span></li>
        <li class="v-animation" data-animation="fade-from-right" data-delay="150"><i class="fa fa-check"></i><span class="v-lead"><?= A::t('auctions', 'If you disagree with any part of these terms and conditions, please do not use our website.'); ?></span></li>
    </ul>
</div>
<div class="col-sm-5">
    <?php if(A::app()->getCookie()->get('memberLoginAttemptsAuth') != ''):
        echo CWidget::create('CFormView', array(
            'action'=>'members/login',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmLogin',
                'id'=>'frmLogin',
                'class'=>'signup'
            ),
            'fieldWrapper'=>array('tag'=>'div', 'class'=>''),
            'fields'=>array(
                'message'   => array('type'=>'label', 'value'=>A::t('auctions', 'Please confirm you are a human by clicking the button below!'), 'htmlOptions'=>array('class'=>'alert alert-info')),
                'memberLoginAttemptsAuth' =>array('type'=>'hidden', 'value'=>A::app()->getCookie()->get('memberLoginAttemptsAuth')),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>A::t('auctions', 'Confirm'), 'htmlOptions'=>array('class'=>'btn v-btn v-btn-default v-small-button')),
            ),
            'return'=>true,
        ));
    else: ?>
    <?= $actionMessage; ?>

    <form class="signup" action="members/login" method="post" id="frmMemberLogin" autocomplete="off">
        <input type="hidden" value="send" name="act" id="act">
        <section class="form-group-vertical">
            <div class="input-group input-group-icon mb10">
                <span class="input-group-addon">
                    <span class="icon"><i class="fa fa-user"></i></span>
                </span>
                <input type="text" value="<?= $username; ?>" autofocus required placeholder="<?= A::t('auctions', 'Username'); ?>" maxlength="25" class="form-control" name="user_name" id="user_name">
            </div>
            <div class="input-group input-group-icon mb10">
                <span class="input-group-addon">
                    <span class="icon"><i class="fa fa-key"></i></span>
                </span>
                <input type="password" value="" required placeholder="<?= A::t('auctions', 'Password'); ?>" maxlength="20" class="form-control" name="password" id="password">
            </div>
        </section>
        <div class="row">
            <?php if($allowRememberMe): ?>
                <div class="col-sm-8">
                    <div class="checkbox-custom checkbox-default">
                        <input id="remember" name="remember" type="checkbox">
                        <label for="remember"><?= A::t('auctions', 'Remember Me'); ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-sm-4 pull-right">
                <button type="submit" class="btn v-btn v-btn-default v-small-button no-three-d pull-right no-margin-bottom no-margin-right"><?= A::t('auctions', 'Login'); ?></button>
            </div>
        </div>
        <p class="text-aling-left pull-top-small">
            <?php
            if($allowRegistration):
                echo '<a class="v-link" href="members/registration">'.A::t('auctions', 'Create account').'</a><br/>'.PHP_EOL;
            endif;
            if($allowResetPassword):
                echo '<a class="v-link" href="members/restorePassword">'.A::t('auctions', 'Forgot your password?').'</a>'.PHP_EOL;
            endif;
            ?>
        </p>
    </form>
    <?php endif; ?>
</div>

<div class="container">
    <div class="v-spacer col-sm-12 v-height-standard"></div>
</div>
