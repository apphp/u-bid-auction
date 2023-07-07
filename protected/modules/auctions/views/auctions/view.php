<?php
$this->_pageTitle = CHtml::encode($auction->auction_name);

$breadCrumbs = array();
$breadCrumbs[] = array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage());
$breadCrumbs[] = array('label' => A::t('auctions', 'Auctions'), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')));
if(!empty($parentCategories) && is_array($parentCategories)){
    if(!empty($parentCategories['parent_category']) && is_array($parentCategories['parent_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['parent_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['parent_category']['id']), CHtml::encode($parentCategories['parent_category']['name'])));
    }
    if(!empty($parentCategories['current_category']) && is_array($parentCategories['current_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['current_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['current_category']['id']), CHtml::encode($parentCategories['current_category']['name'])));
    }
}
$breadCrumbs[] = array('label' => CHtml::encode($auction->auction_name));
$this->_breadCrumbs = $breadCrumbs;

?>
<div class="v-portfolio-item-content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 mb10">
                <h1 class="m0"><?= CHtml::encode($auction->auction_name); ?></h1>
                <?= $auction->short_description ? '<p class="m0">'.(CHtml::encode($auction->short_description)).'</p>' : ''; ?>
                <p class="m0"><strong><?= A::t('auctions', 'Auction Type'); ?>: </strong><?= isset($auctionTypesList[$auction->auction_type_id]) ? CHtml::encode($auctionTypesList[$auction->auction_type_id]) : A::t('auctions', 'Unknown'); ?></p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6"><!-- After Create AutoBid -> col-sm-5 -->
                <div class="v-gallery-widget">
                    <div class="gallery-wrap">
                        <div class="flexslider gallery-slider" data-transition="slide">
                            <ul class="slides">
                                <?php if(!empty($auctionImages)): ?>
                                    <?php foreach($auctionImages as $auctionImage): ?>
                                        <li>
                                            <a href='assets/modules/auctions/images/auctionimages/<?= CHtml::encode($auctionImage['image_file']); ?>' rel="image-galleri" class='view'>
                                                <img src='assets/modules/auctions/images/auctionimages/<?= CHtml::encode($auctionImage['image_file']); ?>' alt="<?= CHtml::encode($auctionImage['title']); ?>" />
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <img class="auction-image" src='assets/modules/auctions/images/auctionimages/no_image.png' alt="<?= CHtml::encode($auction->auction_name); ?>" />
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="auction-form" class="col-sm-5"><!-- After Create AutoBid -> col-sm-4 -->
                <?= $auctionForm; ?>
            </div>
            <!-- --><?php //if($isLoggedIn): ?>
                <!-- <div class="col-sm-3">-->
                    <!-- <div class="item">-->
                        <!-- <div class="v-team-member-box">-->
                            <!-- <div class="cover">-->
                                <!-- <div class="member-info pv5 center-text">-->
                                    <!-- <h3>--><?//= A::t('auctions', 'Auto Bidding'); ?><!--</h3>-->
                                    <!-- <form class="" action="" method="post" id="frmMemberLogin">-->
                                        <!-- <input type="hidden" value="send" name="act" id="act">-->
                                        <!-- <section class="form-group-vertical">-->
                                            <!-- <div class="input-group input-group-icon mb10">-->
                                            <!-- <span class="input-group-addon">-->
                                                <!-- <span class="icon"><i class="fa fa-usd"></i></span>-->
                                            <!-- </span>-->
                                                <!-- <input id="bid_from" class="form-control" placeholder="Bid From (Ex. $100.00)" maxlength="10" type="text" value="" name="bid_from">-->
                                            <!-- </div>-->
                                            <!-- <div class="input-group input-group-icon mb10">-->
                                            <!-- <span class="input-group-addon">-->
                                                <!-- <span class="icon">#</span>-->
                                            <!-- </span>-->
                                                <!-- <input id="number_bids" class="form-control" placeholder="Number Bids" maxlength="3" type="text" value="" name="number_bids">-->
                                            <!-- </div>-->
                                            <!-- <input class="btn v-btn v-btn-default v-small-button" id="activate_auto_bid" value="Activate" type="button" name="ap0">-->
                                        <!-- </section>-->
                                    <!-- </form>-->
                                    <!-- <small><a href="javascript:void(0);">--><?//= A::t('auctions', 'Learn More Auto Bid'); ?><!--</a></small>-->
                                <!-- </div>-->
                            <!-- </div>-->
                        <!-- </div>-->
                    <!-- </div>-->
                <!-- </div>-->
            <!-- --><?php //endif; ?>
        </div>
    </div>
    <div class="container mt20">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#general" data-toggle="tab"><?= A::t('auctions', 'General'); ?></a></li>
                        <li class=""><a href="#bids_history" data-toggle="tab"><?= A::t('auctions', 'Bids History'); ?></a></li>
                        <?php if(!empty($reviews) && is_array($reviews)): ?>
                            <li class=""><a href="#reviews" data-toggle="tab"><?= A::t('auctions', 'Reviews'); ?></a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="tab-content">
                        <div id="general" class="tab-pane fade active in">
                            <div class="row">
                                <div class="col-md-8 col-sm-12">
                                    <?= '<h3>'.A::t('auctions', 'Description').'</h3>'; ?>
                                    <strong><?= A::t('auctions', 'Auction Name'); ?>: </strong><?= CHtml::encode($auction->auction_name); ?>
                                    <br>
                                    <strong><?= A::t('auctions', 'Short Description'); ?>: </strong><?= CHtml::encode($auction->short_description); ?>
                                    <br>
                                    <?= ($auction->description) ? $auction->description : ''; ?>
                                    <br>
									<strong><i class="fa fa-eye"></i> <?= ($auction->hits) ? CHtml::encode($auction->hits) : '0';?></strong>
								</div>
								<div class="col-md-4 col-sm-12 mt20">
                                    <ul id="item_location">
                                        <li><strong><?= A::t('auctions', 'Auction ID'); ?>: </strong><?= $auction->auction_number ? CHtml::encode($auction->auction_number) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Auction Type'); ?>: </strong><?= isset($auctionTypesList[$auction->auction_type_id]) ? CHtml::encode($auctionTypesList[$auction->auction_type_id]) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Category'); ?>: </strong><?= isset($categoriesListManage[$auction->category_id]) ? CHtml::encode($categoriesListManage[$auction->category_id]) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Start date'); ?>: </strong><?= $auction->date_from ? CLocale::date($dateTimeFormat, CHtml::encode($auction->date_from)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'End date'); ?>: </strong><?= $auction->date_to ? CLocale::date($dateTimeFormat, CHtml::encode($auction->date_to)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Start Price'); ?>: </strong><?= $auction->start_price ? CCurrency::format(CHtml::encode($auction->start_price)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Buy Now Price'); ?>: </strong><?= $auction->buy_now_price ? CCurrency::format(CHtml::encode($auction->buy_now_price)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Bids Amount'); ?>: </strong><?= $auction->size_bid ? CCurrency::format(CHtml::encode($auction->size_bid)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><?= A::t('auctions', 'Current Bid'); ?>: </strong><?= $auction->current_bid ? CCurrency::format(CHtml::encode($auction->current_bid)) : A::t('auctions', 'Unknown'); ?></li>
                                        <li><strong><i class="fa fa-calendar"></i> <?= A::t('auctions', 'Published'); ?>: </strong><?= $auction->created_at ? CLocale::date($dateTimeFormat, CHtml::encode($auction->created_at)) : A::t('auctions', 'Unknown'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="bids_history" class="tab-pane fade in">
                            <div class="row">
                                <div class="col-sm-12">
                                        <div id="table-wrapper">
                                            <div id="table-scroll">
                                                <table id="bids_history_table">
                                                    <thead>
                                                    <tr>
                                                        <th><span class="created_at text"><?= A::t('auctions', 'Bid Date'); ?></span></th>
                                                        <th><span class="member_name text"><?= A::t('auctions', 'Member'); ?></span></th>
                                                        <th><span class="size_bid text"><?= A::t('auctions', 'Value'); ?></span></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if(!empty($bidsHistory) && is_array($bidsHistory)): ?>
                                                        <?php foreach($bidsHistory as $bidHistory): ?>
                                                            <?php
                                                               $memberName = CHtml::encode($bidHistory['first_name'].' '.(CString::substr($bidHistory['last_name'], 1, '', false).'.'));
                                                            ?>
                                                            <tr>
                                                                <td class="created_at"><?= CLocale::date($dateTimeFormat, $bidHistory['created_at']); ?></td>
                                                                <td class="member_name"><?= $memberName.($bidHistory['member_id'] == CAuth::getLoggedRoleId() ? '<span class="v-menu-item-info bg-success">'.A::t('auctions', 'It\'s You').'</span>' : ''); ?></td>
                                                                <td class="size_bid"><?= CCurrency::format(CHtml::encode($bidHistory['size_bid'])); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php if(empty($bidsHistory)): ?>
                                        <div id="bid_history_info" class="alert alert-info"><?= A::t('auctions', 'No member has yet made a bid'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div id="reviews" class="tab-pane fade in">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if(!empty($reviews) && is_array($reviews)): ?>
                                        <div class="comments-wrap">
                                            <ul class="media-list">
                                                <?php foreach ($reviews as $review):
                                                    $reviewMemberName = '';
                                                    if (!empty($review['first_name'] && !empty($review['last_name']))):
                                                        $reviewMemberName = CHtml::encode($review['first_name'].' '.(CString::substr($review['last_name'], 1, '', false).'.'));
                                                    endif;
                                                ?>
                                                    <li class="media">
                                                        <div class="media-body">
                                                            <h4 class="media-heading">
                                                                <?= !empty($reviewMemberName) ? CHtml::encode($reviewMemberName) : A::t('auctions', 'Unknown'); ?>
                                                                <span class="date"><?= A::t('auctions', 'Published'); ?>: <?= !empty($review['created_at']) ? CLocale::date($dateTimeFormat, CHtml::encode($review['created_at'])) : A::t('auctions', 'Unknown'); ?></span>
                                                                <br><span class=""> <?= A::t('auctions', 'Rating'); ?>: <img src="templates/default/images/small_star/smallstar-<?= CHtml::encode($review['rating']); ?>.png" /></span>
                                                            </h4>
                                                            <?= !empty($review['message']) ? CHtml::encode($review['message']) : '--'; ?>
                                                        </div>
                                                    </li>
                                                    <hr>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">

        </div>
    </div>

    <?php if(!empty($similarAuctions) && is_array($similarAuctions)): ?>
    <div class="container similar-auctions">
        <div class="row">
            <div class="col-sm-12">
                <div class="v-heading-v2">
                    <h3><?= A::t('auctions', 'Similar Auctions'); ?></h3>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="carousel-wrap">
                    <div class="owl-carousel" data-plugin-options='{"items": 4, "singleItem": false, "autoPlay": true}'>

                        <?php foreach($similarAuctions as $similarAuction): ?>
                            <div class="item">
                                <div id="<?= 'similar-auction-'.(CHtml::encode($similarAuction['id'])); ?>" class="<?= (!empty($similarAuction['auction_type_id']) && isset($auctionTypesList[$similarAuction['auction_type_id']])) ? mb_strtolower($auctionTypesList[$similarAuction['auction_type_id']]).'-auction ' : ''; ?><?= !empty($similarAuction['id']) ? 'auction-'.$similarAuction['id'].' ' : ''; ?>v-team-member-box">
                                    <div class="cover">
                                        <div class="v-team-member-img">
                                            <div class="item">
                                                <figure class="animated-overlay overlay-alt">
                                                    <img src="<?= !empty($similarAuction['image_file']) ? 'assets/modules/auctions/images/auctionimages/'.CHtml::encode($similarAuction['image_file']) : 'assets/modules/auctions/images/auctionimages/no_image.png'; ?>" alt="<?= !empty($similarAuction['image_title']) ? CHtml::encode($similarAuction['image_title']) : CHtml::encode($similarAuction['auction_name']); ?>">
                                                    <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $similarAuction['id'], $similarAuction['auction_name']); ?>" class="link-to-post"></a>
                                                    <figcaption>
                                                        <div class="thumb-info thumb-info-v2"><i class="fa fa-angle-right" style="visibility: visible; opacity: 1; transition-duration: 0.3s; transform: scale(0.5) rotate(-90deg);"></i></div>
                                                    </figcaption>
                                                </figure>
                                            </div>
                                        </div>
                                        <div class="member-info">
                                            <div class="heading">
                                                <div class="v-team-member-info">
                                                    <h4 class="v-team-member-name"><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $similarAuction['id'], $similarAuction['auction_name']); ?>"><?= !empty($similarAuction['auction_name']) ? CHtml::encode($similarAuction['auction_name']) : A::t('auctions', 'Unknown'); ?></a></h4>
                                                    <div class="v-team-member-statu"><span class="current-bid" data-auction-id="<?= CHtml::encode($similarAuction['id']); ?>" data-next-bid="<?= CHtml::encode($similarAuction['current_bid'] + $similarAuction['size_bid']); ?>"><?= CCurrency::format(CHtml::encode($similarAuction['current_bid'])); ?></span></div>
                                                </div>
                                            </div>
                                            <div class="v-team-member-desc">
                                                <div class="bid-now-link m0">
                                                    <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $similarAuction['id'], $similarAuction['auction_name']); ?>" class="btn v-btn v-btn-default v-small-button mb5"><?= A::t('auctions', 'View'); ?></a>
                                                </div>
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
    </div>
    <?php endif; ?>
</div>
<?php
//Draw Timer
echo $drawTimerScript;
//Call Draw Timer
echo $callDrawTimerScript;

A::app()->getClientScript()->registerScript(
    'formSearch',
    'jQuery(".form-search").each(function(){
				var self = jQuery(this);
				self.find(".btn").click(function(){
					var keywords = self.find(\'input[name="keywords"]\').val();
					if(keywords == ""){						
						return false;	
					}
				});
			});',
    3
);