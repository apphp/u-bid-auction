<?php
$this->_pageTitle = A::t('auctions', 'My Shipment Address');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label'=>A::t('auctions', 'My Shipment Address')),
);
?>
<div class="row">
    <div class="col-sm-12">
        <a href="members/addMyShipmentAddress/" class="btn v-btn v-third-dark v-small-button"><?= A::t('auctions', 'Add Address'); ?></a>
        <?= $actionMessage; ?>
    </div>
    <div class="col-sm-12">
        <?php

        CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\ShipmentAddress',
            'actionPath'=>'members/myShipmentAddress',
            'condition'=>'member_id = '.(int)$memberId,
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'options'	=> array(
                'filterDiv' 	=> array('class'=>'frmFilter smallFilters'),
                'filterType' 	=> 'default',
                'gridTable' 	=> array('class'=>'table'),
            ),
            'filters'=>array(
                'address'   => array('title'=>A::t('auctions', 'Address'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'120px', 'maxLength'=>'50', 'htmlOptions'=>array()),
                'city'      => array('title'=>A::t('auctions', 'City'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50', 'htmlOptions'=>array()),
            ),
            'fields'=>array(
                'first_name'     => array('title'=>A::t('auctions', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                'last_name'      => array('title'=>A::t('auctions', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                //'country_code'   => array('title'=>A::t('auctions', 'Country'), 'type'=>'enum', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'source'=>$countries, 'isSortable'=>false, 'htmlOptions'=>array()),
                //'state'   		 => array('title'=>A::t('app', 'State'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                //'city'           => array('title'=>A::t('auctions', 'City'),  'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'address'        => array('title'=>A::t('auctions', 'Address'), 'type'=>'label', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'htmlOptions'=>array()),
                'phone'          => array('title'=>A::t('auctions', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'150px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false),
                'is_default'     => array('title'=>A::t('auctions', 'Default'), 'type'=>'enum', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>array('0'=>'<span class="label-red label-square">'.A::t('app', 'No').'</span>', '1'=>'<span class="label-green label-square">'.A::t('app', 'Yes').'</span>')),
            ),
            'actions'=>array(
                'edit'    => array(
                    'link'=>'members/editMyShipmentAddress/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
                'delete'  => array(
                    'link'=>'members/deleteMyShipmentAddress/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

        ?>
    </div>
</div>
