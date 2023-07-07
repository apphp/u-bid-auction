<?php
    $this->_pageTitle = A::t('auctions', 'Member Registration');
    $this->_breadCrumbs = array(
        array('label'=> A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
        array('label'=> A::t('auctions', 'Member Login'), 'url'=>'members/login'),
        array('label' => A::t('auctions', 'Member Registration'))
    );
?>

<div class="col-sm-10  col-sm-offset-1">
    <?= $actionMessage; ?>
</div>
