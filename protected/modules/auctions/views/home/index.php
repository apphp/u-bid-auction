    <div class="container homepage">
        <div class="row">
            <div class="col-sm-12">
                <div class="v-heading-v2">
                    <h3><?= A::t('auctions', 'Auctions'); ?></h3>
                </div>
                <?php if(!empty($allAuctions) && is_array($allAuctions)):?>
                    <form class="form-horizontal form-bordered mb20" action="auctions/categories" method="get">
                        <div class="form-group">
                            <div class="col-md-5">
                                <?php if(!empty($categoriesList) && is_array($categoriesList)): ?>
                                    <div class="input-group mb-md">
                                        <div class="input-group-btn">
                                            <button tabindex="-1" class="btn btn-default" type="button"><?= A::t('auctions', 'Auction Categories'); ?></button>
                                            <button tabindex="-1" data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
                                                <span class="caret"></span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')); ?>"><?= A::t('auctions', 'All Auctions'); ?></a></li>
                                                <li class="divider"></li>
                                                <?php foreach($categoriesList as $id=>$category): ?>
                                                    <li><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($id), CHtml::encode($category)); ?>"><?= CHtml::encode($category); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group mb-md">
                                    <input type="text" name="search" class="form-control" autocomplete="off">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> <?= A::t('auctions', 'Search Auctions'); ?></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="all-auctions" class="v-team-member-wrap">

                        <?php foreach($allAuctions as $auction): ?>
                            <div id="all-auction-<?= CHtml::encode($auction['id']); ?>" class="<?= (!empty($auction['auction_type_id']) && isset($auctionTypesList[$auction['auction_type_id']])) ? mb_strtolower($auctionTypesList[$auction['auction_type_id']]).'-auction ' : ''; ?><?= !empty($auction['id']) ? 'auction-'.(CHtml::encode($auction['id'])).' ' : ''; ?>all-auctions v-team-member-box mb10">
                                <div class="cover">
                                    <div class="v-team-member-img">
                                        <div class="item">
                                            <figure class="animated-overlay overlay-alt">
                                                <img src="<?= !empty($auctionImages[$auction['id']]) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($auctionImages[$auction['id']]['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?>" alt="<?= !empty($auctionImages[$auction['id']]) ? CHtml::encode($auctionImages[$auction['id']]['title']) : CHtml::encode($auction['auction_name']); ?>">
                                                <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>" class="link-to-post"></a>
                                                <figcaption>
                                                    <div class="thumb-info thumb-info-v2"><i class="fa fa-angle-right" style="visibility: visible; opacity: 1; transition-duration: 0.3s; transform: scale(0.5) rotate(-90deg);"></i></div>
                                                </figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                    <div class="member-info pv5">
                                        <div class="heading">
                                            <div class="v-team-member-info">
                                                <h4 class="v-team-member-name"><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>"><?= !empty($auction['auction_name']) ? CHtml::encode($auction['auction_name']) : A::t('auctions', 'Unknown'); ?></a></h4>
                                                <div class="v-team-member-statu"><span class="current-bid" data-auction-id="<?= CHtml::encode($auction['id']); ?>" data-next-bid="<?= CHtml::encode($auction['current_bid'] + $auction['size_bid']); ?>"><?= CCurrency::format(CHtml::encode($auction['current_bid'])); ?></span></div>
                                            </div>
                                        </div>
                                        <div class="countdown-timer-wrapper">
                                            <div class="timer" id="timer-all-auction-<?= CHtml::encode($auction['id']); ?>"></div>
                                        </div>
                                        <div class="bid-now-link mb5">
                                            <a href="javascript:void(0);" class="place-bid btn btn-bids v-btn v-orange v-small-button mb5"><?= A::t('auctions', 'Bid Now'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="row ml5">
                        <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')); ?>" class="btn v-btn v-third-dark v-small-button"><?= A::t('app', 'View All'); ?></a>
                    </div>
                    <div class="container">
                        <div class="v-spacer col-sm-12 v-height-standard"></div>
                    </div>
                <?php else: ?>
                        <div class="alert alert-warning"><?= A::t('auctions', 'Active auctions not found!'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php //if(!empty($newAuctions)):?>
	<!-- <div class="container">-->
		<!-- <div class="row">-->
			<!-- <div class="col-sm-12">-->
				<!-- <div class="v-heading-v2">-->
					<!-- <h3>--><?//= A::t('auctions', 'New Arrivals'); ?><!--</h3>-->
				<!-- </div>-->
			<!-- </div>-->
			<!-- <div class="col-sm-12">-->
				<!-- <div class="carousel-wrap">-->
					<!-- <div class="owl-carousel" data-plugin-options='{"items": 4, "singleItem": false, "autoPlay": true}'>-->
						<!-- --><?php //foreach($newAuctions as $auction): ?>
							<!-- <div class="item">-->
								<!-- <div class="v-team-member-box">-->
									<!-- <div class="cover">-->
										<!-- <div class="v-team-member-img">-->
											<!-- <div class="item">-->
												<!-- <figure class="animated-overlay overlay-alt">-->
													<!-- <img src="--><?//= !empty($auctionImages[$auction['id']]) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($auctionImages[$auction['id']]['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?><!--" alt="--><?//= !empty($auctionImages[$auction['id']]) ? CHtml::encode($auctionImages[$auction['id']]['title']) : CHtml::encode($auction['auction_name']); ?><!--">-->
													<!-- <a href="--><?//= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?><!--" class="link-to-post"></a>-->
													<!-- <figcaption>-->
														<!-- <div class="thumb-info thumb-info-v2"><i class="fa fa-angle-right" style="visibility: visible; opacity: 1; transition-duration: 0.3s; transform: scale(0.5) rotate(-90deg);"></i></div>-->
													<!-- </figcaption>-->
												<!-- </figure>-->
											<!-- </div>-->
										<!-- </div>-->
										<!-- <div class="member-info pv5">-->
											<!-- <div class="heading">-->
												<!-- <div class="v-team-member-info">-->
													<!-- <h4 class="v-team-member-name"><a href="--><?//= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?><!--">--><?//= !empty($auction['auction_name']) ? CHtml::encode($auction['auction_name']) : A::t('auctions', 'Unknown'); ?><!--</a></h4>-->
													<!-- <div class="v-team-member-statu"><span class="current-bid">--><?//= CCurrency::format(CHtml::encode($auction['current_bid'])); ?><!--</span></div>-->
												<!-- </div>-->
											<!-- </div>-->
											<!-- <div class="countdown-timer-wrapper">-->
												<!-- <div class="timer" id="timer-auction-new---><?//= CHtml::encode($auction['id']); ?><!--"></div>-->
											<!-- </div>-->
											<!-- <div class="bid-now-link mb5">-->
												<!-- <a href="javascript:void(0);" class="btn btn-bids v-btn v-orange v-small-button mb5">--><?//= A::t('auctions', 'Bid Now'); ?><!--</a>-->
											<!-- </div>-->
										<!-- </div>-->
									<!-- </div>-->
								<!-- </div>-->
							<!-- </div>-->
						<!-- --><?php //endforeach; ?>
					<!-- </div>-->
					<!-- <div class="customNavigation">-->
						<!-- <a class="prev"><i class="fa fa-angle-left"></i></a>-->
						<!-- <a class="next"><i class="fa fa-angle-right"></i></a>-->
					<!-- </div>-->
				<!-- </div>-->
				<!-- <div class="container">-->
					<!-- <div class="v-spacer col-sm-12 v-height-standard"></div>-->
				<!-- </div>-->
			<!-- </div>-->
		<!-- </div>-->
	<!-- </div>-->
<?php //endif; ?>

<?php if(!empty($activeAuctionsTabs)):?>
	<!--Horizontal Tab - Bordered-->
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="tabs">
					<ul class="nav nav-tabs">
						<?php foreach($activeAuctionsTabs as $key=>$activeAuctionsTab): ?>
							<li class="<?= $key == 0 ? 'active' : ''; ?>"><a href="#<?= $activeAuctionsTab['id']; ?>" data-toggle="tab"><?= $activeAuctionsTab['tab_name']; ?></a></li>
						<?php endforeach; ?>
					</ul>
					<div class="tab-content">
						<?php foreach($activeAuctionsTabs as $key=>$activeAuctionsTab): ?>
							<div id="<?= $activeAuctionsTab['id']; ?>" class="tab-pane fade<?= $key == 0 ? ' active' : ''; ?> in">
								<div class="row">
									<div class="carousel-wrap">
										<div class="owl-carousel" data-plugin-options='{"items": 4, "singleItem": false, "autoPlay": false}'>
											<?php foreach($activeAuctionsTab['tab_content'] as $auction): ?>
												<div class="item">
													<div id="<?= $activeAuctionsTab['id'].'-auction-'.CHtml::encode($auction['id']); ?>" class="<?= (!empty($auction['auction_type_id']) && isset($auctionTypesList[$auction['auction_type_id']])) ? mb_strtolower($auctionTypesList[$auction['auction_type_id']]).'-auction ' : ''; ?><?= !empty($auction['id']) ? 'auction-'.$auction['id'].' ' : ''; ?>v-team-member-box">
														<div class="cover">
															<div class="v-team-member-img">
																<div class="item">
																	<figure class="animated-overlay overlay-alt">
																		<img src="<?= !empty($auctionImages[$auction['id']]) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($auctionImages[$auction['id']]['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?>" alt="<?= !empty($auctionImages[$auction['id']]) ? CHtml::encode($auctionImages[$auction['id']]['title']) : CHtml::encode($auction['auction_name']); ?>">
																		<a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>" class="link-to-post"></a>
																		<figcaption>
																			<div class="thumb-info thumb-info-v2"><i class="fa fa-angle-right" style="visibility: visible; opacity: 1; transition-duration: 0.3s; transform: scale(0.5) rotate(-90deg);"></i></div>
																		</figcaption>
																	</figure>
																</div>
															</div>
															<div class="member-info pv5">
																<div class="heading">
																	<div class="v-team-member-info">
																		<h4 class="v-team-member-name"><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>"><?= !empty($auction['auction_name']) ? CHtml::encode($auction['auction_name']) : A::t('auctions', 'Unknown'); ?></a></h4>
																		<div class="v-team-member-statu"><span class="current-bid" data-auction-id="<?= CHtml::encode($auction['id']); ?>" data-next-bid="<?= CHtml::encode($auction['current_bid'] + $auction['size_bid']); ?>"><?= CCurrency::format(CHtml::encode($auction['current_bid'])); ?></span></div>
																	</div>
																</div>
																<div class="countdown-timer-wrapper">
																	<div class="timer" id="timer-auction-<?= CHtml::encode($activeAuctionsTab['id']).'-'.CHtml::encode($auction['id']); ?>"></div>
																</div>
																<div class="bid-now-link mb5">
																	<a href="javascript:void(0);" class="place-bid btn btn-bids v-btn v-orange v-small-button mb5"><?= A::t('auctions', 'Bid Now'); ?></a>
																</div>
															</div>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
										<div class="customNavigation">
											<a class="prev"><i class="fa fa-angle-left"></i></a>
											<a class="next"><i class="fa fa-angle-right"></i></a>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--End Horizontal Tab - Bordered-->
<?php endif; ?>

<div class="v-bg-stylish v-bg-stylish-v4">
	<div class="container">
		<div class="col-sm-12">
			<div class="v-heading-v3">
				<h1><span><?= A::t('auctions', 'Some Features'); ?></span></h1>
				<div class="horizontal-break"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="0">
					<i class="fa fa-truck v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Fast Shipping'); ?></h3>

						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="200">
					<i class="fa fa-usd v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Auto Bidding'); ?></h3>
						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="400">
					<i class="fa fa-plus v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Bid Increments'); ?></h3>
						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="v-spacer col-sm-12 v-height-small"></div>

		<div class="row">
			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="1200">
					<i class="fa fa-clock-o v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Track when bids are made'); ?></h3>
						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet, constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="800">
					<i class="fa fa-star-o v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Featured Auctions'); ?></h3>
						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="feature-box left-icon-v2 v-animation v-animate" data-animation="flip-y" data-delay="1000">
					<i class="fa fa-navicon v-icon icn-holder medium"></i>
					<div class="feature-box-text">
						<h3><?= A::t('auctions', 'Categories'); ?></h3>
						<div class="feature-box-text-inner">
							<p>Lorem ipsum dolor sit amet constetur metus elit. Lorem ipsum dolor adipiscing sitelit aptent ametosan taciti sociosqu.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="v-spacer col-sm-12 v-height-small"></div>

	</div>
</div>


<?php
//Draw Timer
echo $drawTimerScript;
//Call Draw Timer
echo $callDrawTimerScript;