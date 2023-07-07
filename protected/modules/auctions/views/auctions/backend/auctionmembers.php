<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Auction Members')),
);

use \Modules\Auctions\Models\Members;
$tableNameMembers = CConfig::get('db.prefix').Members::model()->getTableName();
?>

<h1><?= A::t('auctions', 'Auction Members'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
	<div class="content">
		<?php
		echo $actionMessage;

		echo CWidget::create('CGridView', array(
			'model'             => 'Modules\Auctions\Models\Members',
			'actionPath'        => 'auctions/auctionMembers',
			'condition'	        => (!empty($memberIds) && is_array($memberIds)) ? $tableNameMembers.'.id IN ('.implode(',', $memberIds).')' : '1 = 0',
			'defaultOrder'      => array('created_at'=>'DESC'),
			'passParameters'    => true,
			'pagination'        => array('enable'=>true, 'pageSize'=>20),
			'sorting'           => true,
			'filters'           => array(
				'auction_number'    => array('title'=>A::t('auctions', 'Auction ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'80px', 'maxLength'=>'10'),
				'name'              => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'auction_translations', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'125'),
				'date_from'         => array('title'=>A::t('auctions', 'Start date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
				'date_to'           => array('title'=>A::t('auctions', 'End date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
                'auction_type_id'   => array('title'=>A::t('auctions', 'Auction Type'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'70px', 'source'=>$auctionTypesList),
                'category_id'       => array('title'=>A::t('auctions', 'Category'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'120px', 'source'=>$categoriesList),
                'status'         	=> array('title'=>A::t('app', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'85px', 'source'=>$status, 'emptyOption'=>true, 'emptyValue'=>''),
			),
			'fields'=>array(
                'first_name' 		=> array('title'=>A::t('auctions', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'last_name'  		=> array('title'=>A::t('auctions', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'username'          => array('title'=>A::t('auctions', 'Username'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'email'             => array('title'=>A::t('auctions', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'phone'             => array('title'=>A::t('auctions', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'bids_amount'       => array('title'=>A::t('auctions', 'Bids Amount'), 'type'=>'label', 'align'=>'right', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'is_active'         => array('title'=>A::t('app', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
			),
			'actions'=>array(),
			'return'=>true,
		));
		?>
	</div>
</div>
