<?php
if (!empty($categoriesList) && is_array($categoriesList)) :

$activeCategory = !empty($categoryId) ? (int)$categoryId : 0;
?>
<section class="widget v-nav-menu-widget clearfix mv20">
    <div class="widget-heading clearfix">
        <h4 class="v-heading"><span><?= A::t('auctions', 'Categories'); ?></span></h4>
    </div>
    <ul class="list-group">
        <li class="list-group-item<?= empty($activeCategory) ? ' active' : ''; ?>"><a href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', 0, A::t('auctions', 'All Auctions')); ?>"><?= A::t('auctions', 'All Auctions'); ?></a></li>

        <?php foreach($categoriesList as $id=>$category): ?>
            <li class="list-group-item<?= ($activeCategory == $id) ? ' active' : ''; ?>">
                <a href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($id), CHtml::encode($category['name'])); ?>" title="<?= CHtml::encode($category['name']); ?>"><?= CHtml::encode($category['name']); ?></a>
                <?php if (!empty($category['subCategories'] && is_array($category['subCategories']))): ?>
                    <span class="sub-category" data-toggle="collapse" href="#category-<?= $id; ?>"><i class="fa <?= in_array($activeCategory, array_keys($category['subCategories'])) ? 'fa-minus' : 'fa-plus'; ?>"></i></span>
                    <div id="category-<?= $id; ?>" class="collapse<?= in_array($activeCategory, array_keys($category['subCategories'])) ? ' in' : ''; ?>">
                        <ul>
                            <?php foreach($category['subCategories'] as $subId=>$subCategories): ?>
                                <li class="list-group-item<?= ($activeCategory == $subId) ? ' active' : ''; ?>">
                                    <a class="link-sub-category ml10" href="<?= Website::prepareLinkByFormat('auctions', 'auction_categories_format', CHtml::encode($subId), CHtml::encode($subCategories)); ?>" title="<?= CHtml::encode($category['name']); ?>"><?= CHtml::encode($subCategories); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>