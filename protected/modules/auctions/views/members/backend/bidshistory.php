<?php
$this->_activeMenu = 'members/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Members Management'), 'url'=>'members/manage'),
    array('label'=>A::t('auctions', 'Bids History')),
);

use \Modules\Auctions\Models\BidsHistory;

$tableName = CConfig::get('db.prefix').BidsHistory::model()->getTableName();
?>

<h1><?= A::t('auctions', 'Bids History'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">

        <a class="sub-tab" href="members/edit/id/<?= $id; ?>"><?= A::t('auctions', 'Edit Member'); ?></a>
        &raquo;
        <a class="sub-tab active"><?= A::t('auctions', 'Bids History'); ?></a>
        <a class="sub-tab" href="members/ordersHistory/memberId/<?= $id; ?>"><?= A::t('auctions', 'Orders History'); ?></a>
    </div>
    <div class="content">
        <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\BidsHistory',
            'actionPath'=>'members/bidsHistory/memberId/'.$id,
            'condition'	=> $tableName.'.member_id = '.$id,
            'defaultOrder'=>array('created_at'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
            ),
            'fields'=>array(
				'auction_name'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction'), 'width'=>'150px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'auction_type'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction Type'), 'width'=>'140px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>null, 'width'=>'', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                'size_bid'      => array('type'=>'label', 'title'=>A::t('auctions', 'Value'), 'width'=>'100px', 'class'=>'right', 'headerClass'=>'right', 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'size_bid')), 'isSortable'=>true),
            ),
            'actions'=>array(
//                'edit'    => array(
//                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'edit'),
//                    'link'=>'bidsHistory/edit/auctionId/'.$auctionId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
//                ),
            ),
            'return'=>true,
        ));

        ?>
    </div>
</div>
