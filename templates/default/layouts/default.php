<?php
$columnSide = (A::app()->getLanguage('direction') == 'rtl') ? 'left' : 'right';
?>
<div class="v-page-wrap has-<?= $columnSide; ?>-sidebar has-one-sidebar">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-9">
                <?= A::app()->view->getContent(); ?>
			</div>
			<aside class="sidebar <?= $columnSide; ?>-sidebar col-sm-12 col-md-3">
                <?= FrontendMenu::draw('right', $this->_activeMenu); ?>
			</aside>
		</div>
	</div>
</div>