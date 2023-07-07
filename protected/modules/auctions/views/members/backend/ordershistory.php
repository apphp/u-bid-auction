<?php
$this->_activeMenu = 'members/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Members Management'), 'url'=>'members/manage'),
    array('label'=>A::t('auctions', 'Orders History')),
);

A::app()->getClientScript()->registerCss('labelBlue', '
        span.label-blue, span.label-lightblue { width:auto; display:inline-block; padding:2px 9px; -webkit-border-radius:9px; -moz-border-radius:9px; border-radius:9px; font-size:11px; font-weight:normal; line-height:14px; color:#ffffff;   vertical-align:baseline; white-space:nowrap; text-shadow:0 -1px 0 rgba(0, 0, 0, 0.25); }
        span.label-blue { background-color:#385cad;}
        span.label-lightblue { background-color:#789ced;}
    ');

function getStatus($record, $params = array())
{
    $arrStatus = array('0'=>'<span class="label-lightgray">'.A::t('auctions', 'Preparing').'</span>', '1'=>'<span class="label-gray">'.A::t('auctions', 'Pending').'</span>', '2'=>'<span class="label-lightblue">'.A::t('auctions', 'Paid').'</span>', '3'=>'<span class="label-green">'.A::t('auctions', 'Completed').'</span>', '4'=>'<span class="label-red">'.A::t('auctions', 'Refunded').'</span>');

    $result = isset($arrStatus[$record['status']]) ? $arrStatus[$record['status']] : '<span class="label-lightgray">'.A::t('auctions', 'Unknown').'</span>';

    return $result;
}

use \Modules\Auctions\Models\Orders;

$tableName = CConfig::get('db.prefix').Orders::model()->getTableName();
?>

<h1><?= A::t('auctions', 'Orders History'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">

        <a class="sub-tab" href="members/edit/id/<?= $id; ?>"><?= A::t('auctions', 'Edit Member'); ?></a>
        &raquo;
        <a class="sub-tab" href="members/bidsHistory/memberId/<?= $id; ?>"><?= A::t('auctions', 'Bids History'); ?></a>
        <a class="sub-tab active"><?= A::t('auctions', 'Orders History'); ?></a>
    </div>
    <div class="content">
        <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\Orders',
            'actionPath'=>'orders/manage',
            'condition'	=> $tableName.'.member_id = '.$id,
            'defaultOrder'=>array('created_at'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'order_number'  => array('title'=>A::t('auctions', 'Order Number'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50'),
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'operator'=>'like%', 'default'=>'', 'width'=>'80px', 'maxLength'=>'', 'format'=>'', 'htmlOptions'=>array()),
                'status'        => array('title'=>A::t('app', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'source'=>$arrStatus, 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'order_number'      => array('title'=>A::t('auctions', 'Order Number'), 'type'=>'label', 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'htmlOptions'=>array()),
                'payment_id'        => array('title'=>A::t('auctions', 'Provider'), 'type'=>'enum', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrPaymentProviders, 'htmlOptions'=>array()),
                'payment_method'    => array('title'=>A::t('auctions', 'Method'), 'type'=>'enum', 'align'=>'', 'width'=>'200px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrPaymentMethods, 'htmlOptions'=>array()),
                'created_at'        => array('type'=>'datetime', 'title'=>A::t('auctions', 'Created at'), 'width'=>'140px', 'default'=>null, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date'), 'htmlOptions'=>array(), 'definedValues'=>array(), 'format'=>$dateTimeFormat, 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                'status'            => array('title'=>A::t('app', 'Status'), 'type'=>'html', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'callback'=>array('function'=>'getStatus', 'params'=>array())),
                'format_price'      => array('title'=>A::t('auctions', 'Price'), 'type'=>'label', 'align'=>'right', 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'prependCode'=>''),
            ),
            'actions'=>array(
//                'edit'    => array(
//                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('order', 'edit'),
//                    'link'=>'orders/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
//                ),
            ),
            'return'=>true,
        ));
        ?>
    </div>
</div>
