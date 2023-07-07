<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Modules'), 'url' => $backendPath . 'modules/'),
    array('label' => A::t('auctions', 'Auctions'), 'url' => $backendPath . 'modules/settings/code/auctions'),
    array('label' => A::t('auctions', 'Auctions Management'), 'url' => 'auctions/manage'),
    array('label' => A::t('auctions', 'Reviews Management'), 'url' => 'reviews/manage/auctionId/' . $auctionId),
);

use Modules\Auctions\Models\Reviews;

$tableNameReviews = CConfig::get('db.prefix') . Reviews::model()->getTableName();
$statusParam = ($status !== '' ? '/status/' . $status : '');
$condition = $status !== '' ? $tableNameReviews . '.status = ' . $statusCode . ' AND ' . $tableNameReviews . '.auction_id = ' . $auctionId : '';
?>

<h1><?= A::t('auctions', 'Reviews Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
    <div class="content">

        <?php
        echo $actionMessage;

        CWidget::create('CGridView', array(
            'model' => 'Modules\Auctions\Models\Reviews',
            'actionPath' => 'reviews/manage' . $statusParam,
            'condition' => $condition,
            'passParameters' => true,
            'defaultOrder' => array('created_at' => 'DESC', 'status' => 'ASC'),
            'pagination' => array('enable' => true, 'pageSize' => 20),
            'sorting' => true,
            'filters' => array(),
            'fields' => array(
                'member_name' => array('title' => A::t('auctions', 'Member Name'), 'type' => 'concat', 'align' => '', 'width' => '150px', 'class' => 'left', 'headerTooltip' => '', 'headerClass' => 'left', 'isSortable' => false, 'concatFields' => array('first_name', 'last_name'), 'concatSeparator' => ' '),
                'message' => array('title' => A::t('auctions', 'Message'), 'type' => 'label', 'align' => '', 'width' => '', 'class' => 'left', 'headerClass' => 'left', 'isSortable' => true, 'maxLength' => '70'),
                'created_at' => array('title' => A::t('auctions', 'Created at'), 'type' => 'datetime', 'align' => 'center', 'width' => '130px', 'class' => 'center', 'headerClass' => 'left', 'isSortable' => true, 'maxLength' => '100', 'definedValues' => array(null => '--'), 'format' => $dateTimeFormat),
                'rating' => array('title' => A::t('auctions', 'Rating'), 'type' => 'html', 'align' => '', 'width' => '100px', 'class' => 'center', 'headerClass' => 'center', 'isSortable' => true, 'prependCode' => '<label><img src="templates/default/images/small_star/smallstar-', 'appendCode' => '.png" /></label>'),
                'status' => array('title' => A::t('auctions', 'Status'), 'type' => 'label', 'width' => '80px', 'class' => 'center', 'headerClass' => 'center', 'isSortable' => true, 'definedValues' => $labelStatusReviews, 'htmlOptions' => array('class' => 'tooltip-link', 'title' => A::te('auctions', 'Status'))),
            ),
            'actions' => array(
                'edit' => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'edit'),
                    'link' => 'reviews/edit/auctionId/' . $auctionId . '/id/{id}' . $statusParam, 'imagePath' => 'templates/backend/images/edit.png', 'title' => A::t('app', 'Edit this record')
                ),
                'delete' => array(
                    'disabled' => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'delete'),
                    'link' => 'reviews/delete/auctionId/' . $auctionId . '/id/{id}' . $statusParam, 'imagePath' => 'templates/backend/images/delete.png', 'title' => A::t('app', 'Delete this record'), 'onDeleteAlert' => true
                )
            ),
            'messagesSource' => 'core',
            'alerts' => array('type' => 'flash', 'itemName' => A::t('appointments', 'Review')),
            'return' => false,
        ));

        ?>
    </div>
</div>
