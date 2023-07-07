<?php
$this->_pageTitle = A::t('auctions', 'Remove Account');
$this->_breadCrumbs = array(
    array('label'=> A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label'=> A::t('auctions', 'My Account'), 'url'=>'members/myAccount'),
    array('label' => A::t('auctions', 'Remove Account'))
);
?>
<div class="col-sm-12">
    <?= $actionMessage; ?>
    <?php if($accountRemoved): ?>
        <script type="text/javascript">
            setTimeout(function(){window.location.href = "members/logout";}, 5000);
        </script>
    <?php else: ?>
        <?php
        echo CWidget::create('CFormView', array(
            'action'	        => 'members/removeAccount',
            'cancelUrl'	        => 'members/myAccount',
            'method'            => 'post',
            'htmlOptions'       => array(
                'name'              => 'frmRestorePassword',
                'id'                => 'frmRestorePassword',
                'class'             => 'signup',
                'autoGenerateId'    => true,
            ),
            'fieldWrapper'=>array('tag'=>'div', 'class'=>''),
            'fields'    => array(
                'act'       => array('type'=>'hidden', 'value'=>'send'),
                'message'   => array('type'=>'label', 'value'=>A::t('auctions', 'Account removal notice'), 'htmlOptions'=>array('class'=>'alert alert-info')),
            ),
            'buttons' => array(
                'submit' => array('type'=>'submit', 'value'=>A::t('auctions', 'Remove'), 'htmlOptions'=>array('class'=>'btn v-btn v-btn-default v-small-button')),
                'cancel' =>array('type'=>'button', 'value'=>A::t('core', 'Cancel'), 'htmlOptions'=>array('class'=>'btn v-btn v-third-dark v-small-button', 'onclick'=>"jQuery(location).attr('href','members/myAccount');")),
            ),
        ));
        ?>
   <?php endif; ?>
</div>

