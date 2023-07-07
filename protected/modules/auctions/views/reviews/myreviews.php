<?php
$this->_pageTitle = A::t('auctions', 'My Reviews');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url' => Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url' => 'members/dashboard'),
    array('label' => A::t('auctions', 'My Reviews')),
);

use Modules\Auctions\Models\Reviews;

$tableNameReviews = CConfig::get('db.prefix') . Reviews::model()->getTableName();
$statusParam = ($status !== '' ? '/status/' . $status : '');
$condition = $status !== '' ? $tableNameReviews . '.status = ' . $statusCode . ' AND ' . $tableNameReviews . '.member_id = ' . $memberId : '';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="<?= $status == 'approved' ? 'active' : ''; ?>">
                    <a href="<?= $status == 'approved' ? 'javascript:void(0);' : 'reviews/myReviews/status/approved'; ?>"><?= A::t('auctions', 'Approved'); ?></a>
                </li>
                <li class="<?= $status == 'pending' ? 'active' : ''; ?>">
                    <a href="<?= $status == 'pending' ? 'javascript:void(0);' : 'reviews/myReviews/status/pending'; ?>"><?= A::t('auctions', 'Pending'); ?></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-sm-12">
        <?php

        CWidget::create('CGridView', array(
            'model' => 'Modules\Auctions\Models\Reviews',
            'actionPath' => 'reviews/myReviews' . $statusParam,
            'condition' => $condition,
            'passParameters' => true,
            'defaultOrder' => array('created_at' => 'DESC', 'status' => 'ASC'),
            'pagination' => array('enable' => true, 'pageSize' => 20),
            'options' => array(
                'filterDiv' => array('class' => 'frmFilter smallFilters'),
                'filterType' => 'default',
                'gridTable' => array('class' => 'table'),
            ),
            'sorting' => true,
            'filters' => array(),
            'fields' => array(
                'auction_id' => array('title' => A::t('auctions', 'Auction'), 'type'=>'enum', 'align'=>'', 'width'=>'150px', 'class'=>'center', 'headerTooltip'=>'', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>$arrAuctions),
                'message' => array('title' => A::t('auctions', 'Message'), 'type' => 'label', 'align' => '', 'width' => '', 'class' => 'left', 'headerClass' => 'left', 'isSortable' => true, 'maxLength' => '50'),
                'created_at' => array('title' => A::t('auctions', 'Created at'), 'type' => 'datetime', 'align' => 'center', 'width' => '130px', 'class' => 'center', 'headerClass' => 'left', 'isSortable' => true, 'maxLength' => '100', 'definedValues' => array(null => '--'), 'format' => $dateTimeFormat),
                'rating' => array('title' => A::t('auctions', 'Rating'), 'type' => 'html', 'align' => '', 'width' => '100px', 'class' => 'center', 'headerClass' => 'center', 'isSortable' => true, 'prependCode' => '<label><img src="templates/default/images/small_star/smallstar-', 'appendCode' => '.png" /></label>'),
                'status' => array('title' => A::t('auctions', 'Status'), 'type' => 'label', 'width' => '80px', 'class' => 'center', 'headerClass' => 'center', 'isSortable' => true, 'definedValues' => $labelStatusReviews, 'htmlOptions' => array('class' => 'tooltip-link', 'title' => A::te('auctions', 'Status'))),
            ),
            'actions' => array(
                'edit' => array(
                    'disabled' => false,
                    'link' => 'reviews/editMyReview/id/{id}' . $statusParam, 'imagePath' => 'templates/backend/images/edit.png', 'title' => A::t('app', 'Edit this record')
                ),
            ),
            'messagesSource' => 'core',
            'alerts' => array('type' => 'flash', 'itemName' => A::t('appointments', 'Review')),
            'return' => false,
        ));

        ?>
    </div>
</div>
