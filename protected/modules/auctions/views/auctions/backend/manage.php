<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
);

use Modules\Auctions\Models\Auctions;
?>

<h1><?= A::t('auctions', 'Auctions Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>

	<div class="content">
		<?php
		echo $actionMessage;

		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction', 'add')){
			echo '<a href="auctions/add" class="add-new">'.A::t('auctions', 'Add Auction').'</a>';
		}

		echo CWidget::create('CGridView', array(
			'model'             => 'Modules\Auctions\Models\Auctions',
			'actionPath'        => 'auctions/manage',
			'condition'	        => '',
			'defaultOrder'      => array('created_at'=>'DESC', 'status'=>'ASC'),
			'passParameters'    => true,
			'pagination'        => array('enable'=>true, 'pageSize'=>20),
			'sorting'           => true,
			'filters'           => array(
                'id'                => array('title'=>'', 'visible'=>false, 'table'=>CConfig::get('db.prefix').Auctions::model()->getTableName(), 'type'=>'textbox', 'operator'=>'=', 'width'=>'', 'maxLength'=>'32'),
				'auction_number'    => array('title'=>A::t('auctions', 'Auction ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'80px', 'maxLength'=>'10'),
				'name'              => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'auction_translations', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'125'),
				'date_from'         => array('title'=>A::t('auctions', 'Start date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
				'date_to'           => array('title'=>A::t('auctions', 'End date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
                'auction_type_id'   => array('title'=>A::t('auctions', 'Auction Type'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'70px', 'source'=>$auctionTypesList),
                'category_id'       => array('title'=>A::t('auctions', 'Category'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'120px', 'source'=>$categoriesList),
                'status'         	=> array('title'=>A::t('app', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'85px', 'source'=>$status, 'emptyOption'=>true, 'emptyValue'=>''),
			),
			'fields'=>array(
				'auction_name'      => array('type'=>'label', 'title'=>A::t('auctions', 'Name'), 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'auction_type_id'   => array('title'=>A::t('auctions', 'Type'), 'type'=>'enum', 'align'=>'left', 'width'=>'80px', 'class'=>'left', 'headerTooltip'=>'', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$auctionTypesList),
                'date_from'         => array('title'=>A::t('auctions', 'Start date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>null, 'width'=>'150px', 'maxLength'=>'', 'format'=>$dateTimeFormat, 'htmlOptions'=>array()),
				'date_to'           => array('title'=>A::t('auctions', 'End date'), 'type'=>'datetime', 'table'=>'', 'operator'=>'=', 'default'=>null, 'width'=>'150px', 'maxLength'=>'', 'format'=>$dateTimeFormat, 'htmlOptions'=>array()),
				'start_price'       => array('title'=>A::t('auctions', 'Start Price'), 'type'=>'html', 'align'=>'right', 'width'=>'65px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'start_price'))),
				'size_bid'          => array('title'=>A::t('auctions', 'Bid Amount'), 'type'=>'html', 'align'=>'right', 'width'=>'60px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'size_bid'))),
                'reviews_link'      => array('title'=>'', 'type'=>'link', 'width'=>'65px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'reviews/manage/auctionId/{id}', 'linkText'=>A::t('auctions', 'Reviews'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'orders_link'       => array('title'=>'', 'type'=>'link', 'width'=>'65px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'orders/manage/orderType/1?&auction_id={id}&but_filter=Filter', 'linkText'=>A::t('auctions', 'Orders'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'images_link'       => array('title'=>'', 'type'=>'link', 'width'=>'65px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'auctionImages/manage/auctionId/{id}', 'linkText'=>A::t('auctions', 'Images'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'bids_history_link' => array('title'=>'', 'type'=>'link', 'width'=>'80px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'bidsHistory/manage/auctionId/{id}', 'linkText'=>A::t('auctions', 'Bids History'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
				'status'            => array('title'=>A::t('app', 'Status'), 'type'=>'label', 'align'=>'', 'width'=>'70px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$statusLabel, 'htmlOptions'=>array('class'=>'tooltip-link')),
				'paid_status'       => array('title'=>A::t('auctions', 'Paid Status'), 'type'=>'html', 'align'=>'', 'width'=>'88px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'drawPaidStatus', 'params'=>array('field_name'=>'status')), 'htmlOptions'=>array('class'=>'tooltip-link')),
				'shipping_status'   => array('title'=>A::t('auctions', 'Shipping Status'), 'type'=>'html', 'align'=>'', 'width'=>'90px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'drawShippingStatus', 'params'=>array('field_name'=>'paid_status')), 'htmlOptions'=>array('class'=>'tooltip-link')),
				'id'    			=> array('type'=>'label', 'title'=>A::t('auctions', 'ID'), 'width'=>'20px', 'align'=>'center', 'isSortable'=>true),
			),
			'actions'=>array(
				'edit' => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'edit'),
					'link'      => 'auctions/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				'delete'  => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctions', 'delete'),
					'link'      => 'auctions/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
