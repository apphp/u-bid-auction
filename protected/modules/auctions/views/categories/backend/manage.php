<?php
$this->_activeMenu = 'categories/manage';
$this->_breadCrumbs = array(
	array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
	array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
	array('label'=>A::t('auctions', 'Categories Management'), 'url'=>'categories/manage'),
);
?>

<h1><?= A::t('auctions', 'Categories Management'); ?></h1>

<div class="bloc">
	<?= $tabs; ?>
    <?php if($parentId > 0): ?>
        <div class="sub-title">
            <?= $subTabs; ?>
        </div>
    <?php endif; ?>
	<div class="content">
		<?php
		echo $actionMessage;

		if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('category', 'add')){
			echo '<a href="categories/add'.($parentId > 0 ? '/parentId/'.$parentId : '').'" class="add-new">'.A::t('auctions', 'Add Category').'</a>';
		}

		echo CWidget::create('CGridView', array(
			'model'             =>'Modules\Auctions\Models\Categories',
			'actionPath'        =>'categories/manage',
            'condition'         =>'parent_id = '.$parentId,
			'defaultOrder'      =>array('sort_order'=>'ASC'),
			'passParameters'    =>true,
			'pagination'        =>array('enable'=>true, 'pageSize'=>20),
			'sorting'           =>true,
			'filters'           =>array(
                'name'         => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50'),
                'description'  => array('title'=>A::t('auctions', 'Description'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'150px', 'maxLength'=>'128'),
			),
			'fields'=>array(
                'icon_thumb'            => array('title'=>A::t('auctions', 'Image'), 'type'=>'image', 'align'=>'', 'width'=>'45px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'imagePath'=>'assets/modules/auctions/images/categories/thumbs/', 'defaultImage'=>'no_image_thumb.png', 'imageWidth'=>'28px', 'imageHeight'=>'22px', 'alt'=>'', 'showImageInfo'=>true),
                'name'                  => array('title'=>A::t('auctions', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'210px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                'description'           => array('title'=>A::t('auctions', 'Description'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'maxLength'=>100, 'htmlOptions'=>array()),
                'sub_categories'        => array('disabled'=>($parentId != 0 ? true : false), 'title'=>'', 'type'=>'link', 'align'=>'right', 'width'=>'110px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'categories/manage/parentId/{id}', 'linkText'=>A::t('auctions', 'Sub Categories'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'sub_categories_count'  => array('disabled'=>($parentId != 0 ? true : false), 'title'=>'', 'type'=>'enum', 'align'=>'', 'width'=>'20px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'definedValues'=>array(''=>'0'), 'default'=>'0', 'sourceField'=>'id', 'source'=>$arrSubCategoryIds, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'auctions_link'         => array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'90px', 'class'=>'right', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'auctions/manage?category_id={id}&but_filter=Filter', 'linkText'=>A::t('auctions', 'Auctions'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'auctions_count'        => array('title'=>'', 'type'=>'enum', 'align'=>'', 'width'=>'20px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'definedValues'=>array(''=>'0'), 'default'=>'0', 'sourceField'=>'id', 'source'=>$arrAuctionCountIds, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'sort_order'            => array('title'=>A::t('auctions', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'changeOrder'=>true),
			),
			'actions'=>array(
				'edit' => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('categories', 'edit'),
					'link'      => 'categories/edit/id/{id}'.($parentId > 0 ? '/parentId/'.$parentId : ''), 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
				),
				'delete' => array(
					'disabled'  => !Admins::hasPrivilege('modules', 'edit') || Admins::hasPrivilege('categories', 'delete'),
					'link'      => 'categories/delete/id/{id}'.($parentId > 0 ? '/parentId/'.$parentId : ''), 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
				)
			),
			'return'=>true,
		));

		?>
	</div>
</div>
