<?php
    $this->_activeMenu = 'packages/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Packages Management'), 'url'=>'packages/manage'),
    );

    use Modules\Auctions\Models\Packages;
?>

<h1><?= A::t('auctions', 'Packages Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>

	<div class="content">
		<?php
		echo $actionMessage;

		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction', 'add')){
			echo '<a href="packages/add" class="add-new">'.A::t('auctions', 'Add Package').'</a>';
		}

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('package', 'edit')){
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'packages/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('auctions', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }

		echo CWidget::create('CGridView', array(
			'model'             => 'Modules\Auctions\Models\Packages',
			'actionPath'        => 'packages/manage',
			'condition'	        => '',
			'defaultOrder'      => array(),
			'passParameters'    => true,
			'pagination'        => array('enable'=>true, 'pageSize'=>20),
			'sorting'           => true,
            'filters'           => array(
                'id'            => array('title'=>'', 'visible'=>false, 'table'=>CConfig::get('db.prefix').Packages::model()->getTableName(), 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32'),
                'name'          => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'auction_package_translations', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'description'   => array('title'=>A::t('auctions', 'Description'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'auction_package_translations', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'is_active'     => array('title'=>A::t('app', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'60px', 'source'=>array(''=>'', '0'=>A::t('app', 'No'), '1'=>A::t('app', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),

            ),
			'fields'=>array(
                'name'          => array('type'=>'label', 'title'=>A::t('auctions', 'Name'), 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'description'   => array('type'=>'label', 'title'=>A::t('auctions', 'Description'), 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'maxLength'=>'140'),
                'bids_amount'   => array('type'=>'label', 'title'=>A::t('auctions', 'Bids'), 'align'=>'right', 'width'=>'60px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'price'         => array('title'=>A::t('auctions', 'Price'), 'type'=>'html', 'align'=>'right', 'width'=>'60px', 'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'price'))),
                'price_one_bid' => array('title'=>A::t('auctions', 'Price One Bid'), 'type'=>'html', 'align'=>'right', 'width'=>'60px', 'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'price_one_bid'))),
                'is_default'    => array('title'=>A::t('auctions', 'Default'), 'type'=>'html', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red badge-square">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link')),
                'is_active'     => $isActive,
			),
			'actions'=>array(
				'edit' => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('packages', 'edit'),
					'link'      => 'packages/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				'delete'  => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('packages', 'delete'),
					'link'      => 'packages/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
