<?php
    $this->_activeMenu = 'taxes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Taxes Management'), 'url'=>'taxes/manage'),
        array('label'=>A::t('auctions', 'Tax Countries Management')),
    );
?>

<h1><?= A::t('auctions', 'Tax Countries Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('tax', 'add')):
            echo '<a href="taxes/addCountry/taxId/'.$taxId.'" class="add-new">'.A::t('auctions', 'Add Country').'</a>';
        endif;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\TaxCountries',
            'actionPath'=>'taxes/manageCountries/taxId/'.$taxId,
            'condition'=>'tax_id = '.(int)$taxId,
            'defaultOrder'=>array('id'=>'DESC'),
            'passParameters'=>true,
            //'customParameters'=>array('param_1'=>'integer', 'param_1'=>'string' [,...]),
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(),
            'fields'=>array(
                'country_name' 	=> array('title'=>A::t('auctions', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
				'percent'      	=> array('title'=>A::t('auctions', 'Percent'), 'type'=>'decimal', 'align'=>'right', 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'format'=>$numberFormat, 'decimalPoints'=>2, 'appendCode'=>'% '),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('tax', 'edit'),
                    'link'=>'taxes/editCountry/taxId/'.$taxId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('tax', 'delete'),
                    'link'=>'taxes/deleteCountry/taxId/'.$taxId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));
    ?>
    </div>
</div>
