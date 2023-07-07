<?php
    $this->_activeMenu = 'taxes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Taxes Management')),
    );
?>

<h1><?= A::t('auctions', 'Taxes Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('tax', 'add')):
            echo '<a href="taxes/add" class="add-new">'.A::t('auctions', 'Add Tax').'</a>';
        endif;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('taxes', 'edit')):
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'link', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'taxes/changeStatus/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'));
        else:
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'source'=>array(''=>'', '0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'));
        endif;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\Taxes',
            'actionPath'=>'taxes/manage',
            'condition'=>'',
            'defaultOrder'=>array('sort_order'=>'ASC'),
            'passParameters'=>true,
            //'customParameters'=>array('param_1'=>'integer', 'param_1'=>'string' [,...]),
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'name' => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50'),
                'description' => array('title'=>A::t('auctions', 'Description'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'150px', 'maxLength'=>'128'),
            ),
            'fields'=>array(
                'name'            => array('title'=>A::t('auctions', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'210px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                'description'     => array('title'=>A::t('auctions', 'Description'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'stripTags'=>true, 'maxLength'=>110),
                'percent'         => array('title'=>A::t('auctions', 'Percent'), 'type'=>'decimal', 'align'=>'right', 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'format'=>$typeFormat, 'decimalPoints'=>2, 'appendCode'=>'% '),
                'countries'       => array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'90px', 'class'=>'right', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'taxes/manageCountries/taxId/{id}', 'linkText'=>A::t('auctions', 'Set Prices'), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'countries_count' => array('sourceField'=>'id', 'title'=>'', 'type'=>'enum', 'align'=>'', 'width'=>'20px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'definedValues'=>array(''=>A::t('auctions', 'Worldwide')), 'default'=>'0', 'source'=>$arrContriesIds, 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'sort_order'      => array('title'=>A::t('auctions', 'Sort Order'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'changeOrder'=>true),
                'is_active'       => $isActive,
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('tax', 'edit'),
                    'link'=>'taxes/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('tax', 'delete'),
                    'link'=>'taxes/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));
    ?>
    </div>
</div>
