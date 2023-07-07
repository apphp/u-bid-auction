<?php
$this->_activeMenu = 'members/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Members Management'), 'url'=>'members/manage'),
);

use \Modules\Auctions\Models\Members;
?>

<h1><?= A::t('auctions', 'Members Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>

	<div class="content">
		<?php
		echo $actionMessage;

		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('member', 'add')){
			echo '<a href="members/add" class="add-new">'.A::t('auctions', 'Add Member').'</a>';
		}

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('member', 'edit')){
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'link', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'members/changeStatus/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('auctions', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'label', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link'));
        }

		echo CWidget::create('CGridView', array(
			'model'             => 'Modules\Auctions\Models\Members',
			'actionPath'        => 'members/manage',
			'condition'	        => '',
			'defaultOrder'      => array(),
			'passParameters'    => true,
			'pagination'        => array('enable'=>true, 'pageSize'=>20),
			'sorting'           => true,
			'filters'           => array(
                'id'                      => array('title'=>'', 'visible'=>false, 'table'=>CConfig::get('db.prefix').Members::model()->getTableName(), 'type'=>'textbox', 'operator'=>'=', 'width'=>'100px', 'maxLength'=>'32'),
                'first_name,last_name'    => array('title'=>A::t('auctions', 'Member Name'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'32'),
                'username'                => array('title'=>A::t('auctions', 'Username'), 'type'=>'textbox', 'operator'=>'%like%', 'default'=>'', 'width'=>'100px', 'maxLength'=>'32'),
                'email'                   => array('title'=>A::t('auctions', 'Email'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'100'),
                'is_active'               => array('title'=>A::t('app', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'60px', 'source'=>array(''=>'', '0'=>A::t('app', 'No'), '1'=>A::t('app', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
			),
			'fields'=>array(
                'first_name'            => array('title'=>A::t('auctions', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'last_name'             => array('title'=>A::t('auctions', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'username'              => array('title'=>A::t('auctions', 'Username'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'email'                 => array('title'=>A::t('auctions', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'phone'                 => array('title'=>A::t('auctions', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'bids_amount'           => array('title'=>A::t('auctions', 'Bids Amount'), 'type'=>'label', 'align'=>'right', 'width'=>'130px', 'class'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'addresses_link'        => array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'100px', 'class'=>'right', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'members/shipmentAddress/memberId/{id}', 'linkText'=>A::t('auctions', 'Addresses'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'addresses_count_total' => array('title'=>'', 'type'=>'enum', 'align'=>'', 'width'=>'30px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'definedValues'=>array(''=>'0'), 'default'=>'0', 'sourceField'=>'id', 'source'=>$arrAddresses, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'is_active'             => $isActive,
			),
			'actions'=>array(
				'edit' => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('members', 'edit'),
					'link'      => 'members/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				'delete'  => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('members', 'delete'),
					'link'      => 'members/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
