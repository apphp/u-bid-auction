<?php
$this->_pageTitle = !empty($parentCategories['current_category']) ? $parentCategories['current_category']['name'] : A::t('auctions', 'All Auctions');

$breadCrumbs = array();
$breadCrumbs[] = array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage());
$breadCrumbs[] = array('label' => A::t('auctions', 'Auctions'), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')));
if(!empty($parentCategories) && is_array($parentCategories)){
    if(!empty($parentCategories['parent_category']) && is_array($parentCategories['parent_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['parent_category']['name']), 'url'=>Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($parentCategories['parent_category']['id']), CHtml::encode($parentCategories['parent_category']['name'])));
    }
    if(!empty($parentCategories['current_category']) && is_array($parentCategories['current_category'])){
        $breadCrumbs[] = array('label' => CHtml::encode($parentCategories['current_category']['name']), 'url'=>'');
    }
}
// $breadCrumbs[] = array('label' => $auction->auction_name);
$this->_breadCrumbs = $breadCrumbs;

if(!empty($auctions) && is_array($auctions)): ?>
    <div id="all-auctions" class="categories v-team-member-wrap">

        <?php foreach($auctions as $auction): ?>
            <div id="<?= 'categories-auction-'.CHtml::encode($auction['id']); ?>" class="<?= (!empty($auction['auction_type_id']) && isset($auctionTypesList[$auction['auction_type_id']])) ? mb_strtolower($auctionTypesList[$auction['auction_type_id']]).'-auction ' : ''; ?><?= !empty($auction['id']) ? 'auction-'.$auction['id'].' ' : ''; ?>v-team-member-box col-xs-12 col-sm-6 col-md-3 mb10 ph5">
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
                        <div class="bid-now-link mb5">
                            <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_link_format', $auction['id'], $auction['auction_name']); ?>" class="btn v-btn v-btn-default v-small-button mb5"><?= A::t('auctions', 'View'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    if(!empty($auctions)):
        if($totalRecords > 1):
            echo CWidget::create('CPagination', array(
                'actionPath'   => 'auctions/categories',
                'currentPage'  => $currentPage,
                'pageSize'     => $auctionsPerPage,
                'totalRecords' => $totalRecords,
                'showResultsOfTotal' => false,
                'linkType' => 0,
                'paginationType' 	=> 'prevNext|justNumbers',
                'linkNames' 		=> array('previous' => '', 'next'=>''),
                'showEmptyLinks' 	=> true,
                'htmlOptions' 		=> array('linksWrapperTag' => 'div', 'linksWrapperClass' => 'links-part'),
            ));
        endif;
    endif;
    ?>
<?php else: ?>
    <div class="alert alert-info"><?= A::t('auctions', 'The auction not found! Please try again later.'); ?></div>
<?php endif; ?>

<?php
//Draw Timer
//echo $drawTimerScript;
//Call Draw Timer
//echo $callDrawTimerScript;
