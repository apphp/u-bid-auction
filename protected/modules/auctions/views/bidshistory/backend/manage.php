<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
	array('label'=>A::t('auctions', 'Bids History Management'), 'url'=>'bidsHistory/manage/auctionId.'.$auctionId),
);

use \Modules\Auctions\Models\BidsHistory;

$tableName = CConfig::get('db.prefix').BidsHistory::model()->getTableName();

?>

<h1><?= A::t('auctions', 'Bids History Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
	<div class="content">
		<?php
		echo $actionMessage;

		echo CWidget::create('CGridView', array(
			'model'=>'Modules\Auctions\Models\BidsHistory',
			'actionPath'=>'bidsHistory/manage/auctionId/'.$auctionId,
			'condition'	=> $tableName.'.auction_id = '.$auctionId,
			'defaultOrder'=>array('created_at'=>'ASC'),
			'passParameters'=>true,
			'pagination'=>array('enable'=>true, 'pageSize'=>20),
			'sorting'=>true,
			'filters'=>array(
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
            ),
			'fields'=>array(
				'auction_type'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction Type'), 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
				'auction_name'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
				'member_name'   => array('type'=>'label', 'title'=>A::t('auctions', 'Member'), 'width'=>'250px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'default'=>null, 'width'=>'100px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                'size_bid'      => array('type'=>'label', 'title'=>A::t('auctions', 'Value'), 'width'=>'50px', 'class'=>'right', 'headerClass'=>'right', 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'size_bid')), 'isSortable'=>true),
            ),
			'actions'=>array(
				'edit'    => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'edit'),
					'link'=>'bidsHistory/edit/auctionId/'.$auctionId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				'delete'  => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'delete'),
					'link'=>'bidsHistory/delete/auctionId/'.$auctionId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
