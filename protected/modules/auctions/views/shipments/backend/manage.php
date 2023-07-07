<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
	array('label'=>A::t('auctions', 'Shipments Management')),
);
use Modules\Auctions\Models\Shipments;
$tableNameShipments = CConfig::get('db.prefix').Shipments::model()->getTableName();
?>

<h1><?= A::t('auctions', 'Shipments Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
	<div class="content">

		<?php
		echo $actionMessage;

		// if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction', 'add')){
        //     if(empty($messageStatus)){
        //         echo '<a href="shipments/add/auctionId/'.$auctionId.'" class="add-new">'.A::t('auctions', 'Add Shipment').'</a>';
        //     }else{
        //         echo $messageStatus;
        //     }
        // }

		echo CWidget::create('CGridView', array(
			'model'=>'Modules\Auctions\Models\Shipments',
			'actionPath'=>'shipments/manage',
			'condition'	=> $tableNameShipments.'.auction_id = '.$auctionId,
			'defaultOrder'=>array('created_at'=>'ASC'),
			'passParameters'=>true,
			'pagination'=>array('enable'=>true, 'pageSize'=>20),
			'sorting'=>true,
			'filters'=>array(
                'carrier'           => array('title'=>A::t('auctions', 'Carrier'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'tracking_number'   => array('title'=>A::t('auctions', 'Tracking Number'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                //'auction_name'      => array('title'=>A::t('auctions', 'Auction'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'member_name'       => array('title'=>A::t('auctions', 'Member'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'created_at'        => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'100px', 'maxLength'=>'32'),
                'shipping_status'   => array('title'=>A::t('auctions', 'Shipping Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'source'=>$shippingStatus, 'emptyOption'=>true, 'emptyValue'=>''),
            ),
			'fields'=>array(
                'auction_name'      => array('type'=>'label', 'title'=>A::t('auctions', 'Auction'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'member_name'       => array('type'=>'label', 'title'=>A::t('auctions', 'Member'), 'width'=>'200px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'carrier'           => array('type'=>'label', 'title'=>A::t('auctions', 'Carrier'), 'width'=>'150px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'tracking_number'   => array('type'=>'label', 'title'=>A::t('auctions', 'Tracking Number'), 'width'=>'200px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'created_at'        => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>null, 'width'=>'100px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                'shipping_status'   => array('title'=>A::t('auctions', 'Shipping Status'), 'type'=>'label', 'align'=>'', 'width'=>'120px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$shippingStatusLabel, 'htmlOptions'=>array('class'=>'tooltip-link')),
			),
			'actions'=>array(
				'edit'    => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'edit'),
					'link'=>'shipments/edit/auctionId/'.$auctionId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				// 'delete'  => array(
				// 	'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'delete'),
				// 	'link'=>'shipments/delete/auctionId/'.$auctionId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				// )
			),
			'return'=>true,
		));

		?>
	</div>
</div>
