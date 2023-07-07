<?php
$this->_activeMenu = 'auctionTypes/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Auction Types Management'), 'url'=>'auctionTypes/manage'),
);
?>

<h1><?= A::t('auctions', 'Auction Types Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>

	<div class="content">
		<?php
		echo $actionMessage;

//		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction_type', 'add')){
//			echo '<a href="auctionTypes/add" class="add-new">'.A::t('auctions', 'Add Auction Type').'</a>';
//		}

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction_type', 'edit')){
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'auctionTypes/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('auctions', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }

		echo CWidget::create('CGridView', array(
			'model'=>'Modules\Auctions\Models\AuctionTypes',
			'actionPath'=>'auctionTypes/manage',
			'condition'	=> '',
			'defaultOrder'=>array('sort_order'=>'ASC'),
			'passParameters'=>true,
			'pagination'=>array('enable'=>true, 'pageSize'=>20),
			'sorting'=>true,
			'filters'=>array(
				'name'          => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
				'description'   => array('title'=>A::t('auctions', 'Description'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
				'is_active'     => array('title'=>A::t('app', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'60px', 'source'=>array(''=>'', '0'=>A::t('app', 'No'), '1'=>A::t('app', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
			),
			'fields'=>array(
				'name'          => array('type'=>'label', 'title'=>A::t('auctions', 'Name'), 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
				'description'   => array('type'=>'label', 'title'=>A::t('auctions', 'Description'), 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'maxLength'=>'140'),
                'sort_order'    => array('type'=>'label', 'title'=>A::t('auctions', 'Sort Order'), 'class'=>'center', 'headerClass'=>'center', 'width'=>'90px', 'isSortable'=>true, 'changeOrder'=>true),
                'is_default'    => array('title'=>A::t('auctions', 'Default'), 'type'=>'html', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'is_active'     => $isActive,
			),
			'actions'=>array(
				'edit'    => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctionTypes', 'edit'),
					'link'=>'auctionTypes/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
//				'delete'  => array(
//					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('auctionTypes', 'delete'),
//					'link'=>'auctionTypes/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
//				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
