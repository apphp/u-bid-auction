<?php
$this->_activeMenu = 'members/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Members Management'), 'url'=>'members/manage'),
    array('label'=>A::t('auctions', 'Addresses Management')),
);
?>

<h1><?= A::t('auctions', 'Addresses Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <a class="sub-tab" href="members/manage/"><?= A::t('auctions', 'Members'); ?></a>
        &raquo;
        <a class="sub-tab active" href="members/shipmentAddress/memberId/<?= $memberId; ?>"><?= A::t('auctions', 'Addresses'); ?></a>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('member', 'add')):
            echo '<a href="members/addShipmentAddress/memberId/'.$memberId.'" class="add-new">'.A::t('auctions', 'Add Address').'</a>';
        endif;

        CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\ShipmentAddress',
            'actionPath'=>'members/shipmentAddress/memberId/'.$memberId,
            'condition'=>'member_id = '.(int)$memberId,
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'address'   => array('title'=>A::t('auctions', 'Address'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'120px', 'maxLength'=>'50', 'htmlOptions'=>array('class'=>'form-control')),
                'city'      => array('title'=>A::t('auctions', 'City'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50', 'htmlOptions'=>array('class'=>'form-control')),
            ),
            'fields'=>array(
                'first_name'     => array('title'=>A::t('auctions', 'First Name'), 'type'=>'label', 'align'=>'', 'width'=>'110px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                'last_name'      => array('title'=>A::t('auctions', 'Last Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'htmlOptions'=>array()),
                'country_code'   => array('title'=>A::t('auctions', 'Country'), 'type'=>'enum', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'source'=>$countries, 'isSortable'=>false, 'htmlOptions'=>array()),
                'state'   		 => array('title'=>A::t('app', 'State'), 'type'=>'label', 'align'=>'', 'width'=>'80px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'city'           => array('title'=>A::t('auctions', 'City'),  'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'address'        => array('title'=>A::t('auctions', 'Address'), 'type'=>'label', 'align'=>'', 'width'=>'170px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'htmlOptions'=>array()),
                'phone'          => array('title'=>A::t('auctions', 'Phone'), 'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false),
                'is_default'     => array('title'=>A::t('auctions', 'Default'), 'type'=>'enum', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'source'=>array('0'=>'<span class="badge-red badge-square">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green badge-square">'.A::t('app', 'Yes').'</span>')),
            ),
            'actions'=>array(
                'edit'    => array(
                    'link'=>'members/editShipmentAddress/id/{id}/memberId/'.$memberId, 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
                'delete'  => array(
                    'link'=>'members/deleteShipmentAddress/id/{id}/memberId/'.$memberId, 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>false,
        ));

    ?>
    </div>
</div>
