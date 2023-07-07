<?php
$columnSide = (A::app()->getLanguage('direction') == 'rtl') ? 'right' : 'left';
?>
<div class="v-page-wrap has-<?= $columnSide; ?>-sidebar has-one-sidebar">
	<div class="container">
		<div class="row">
			<aside class="sidebar <?= $columnSide; ?>-sidebar col-sm-12 col-lg-3">
				<div class="v-search-widget mb10">
					<form role="search" method="get" id="searchAuction" class="searchform" action="auctions/categories" autocomplete="off">
						<div class="form-group">
							<input class="form-control" type="text" value="<?= !empty($search) ? $search : ''; ?>" name="search" id="search" placeholder="<?= A::t('auctions', 'Search Auctions') ?>">
						</div>
					</form>
				</div>
                <?= FrontendMenu::draw('left'); ?>
			</aside>
			<div class="col-sm-12 col-lg-9">
                <?= A::app()->view->getContent(); ?>
			</div>
		</div>
	</div>
</div>