<?php
$this->_pageTitle = A::t('auctions', 'My Orders');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Orders')),
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

?>
<div class="row">
    <div class="col-sm-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="<?= $orderType == 0 ? 'active' : ''; ?>">
                    <a href="<?= $orderType == 0 ? 'javascript:void(0);' : 'orders/myOrders/orderType/0'; ?>"><?= A::t('auctions', 'Packages'); ?></a>
                </li>
                <li class="<?= $orderType == 1 ? 'active' : ''; ?>">
                    <a href="<?= $orderType == 1 ? 'javascript:void(0);' : 'orders/myOrders/orderType/1'; ?>"><?= A::t('auctions', 'Auctions'); ?></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-sm-12">
        <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\Orders',
            'actionPath'=>'orders/myOrders',
            'condition'=>'member_id = '.$memberId.' AND order_type = '.$orderType,
            'defaultOrder'=>array('id'=>'DESC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'options'	=> array(
                'filterDiv' 	=> array('class'=>'frmFilter smallFilters'),
                'filterType' 	=> 'default',
                'gridTable' 	=> array('class'=>'table'),
            ),
            'filters'=>array(
                'order_number'  => array('title'=>A::t('auctions', 'Order Number'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'50'),
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'operator'=>'like%', 'default'=>'', 'width'=>'90px', 'maxLength'=>'', 'format'=>'', 'htmlOptions'=>array()),
                'status'        => array('title'=>A::t('app', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'100px', 'source'=>$arrStatus, 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'index'             => array('title'=>'#', 'type'=>'index', 'align'=>'', 'width'=>'30px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false, 'htmlOptions'=>array()),
                'auction_id'        => array('title'=>A::t('auctions', 'Auction'), 'type'=>'enum', 'disabled'=>$orderType == 1 ? false : true, 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrAuctions, 'htmlOptions'=>array()),
                'package_id'        => array('title'=>A::t('auctions', 'Package'), 'type'=>'enum', 'disabled'=>$orderType == 0 ? false : true, 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrPackages, 'htmlOptions'=>array()),
                'bids_amount_id'    => array('title'=>A::t('auctions', 'Bids Amount'), 'type'=>'enum', 'disabled'=>$orderType == 0 ? false : true, 'align'=>'', 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrBidsAmount, 'htmlOptions'=>array()),
                'payment_id'        => array('title'=>A::t('auctions', 'Provider'), 'type'=>'enum', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'source'=>$arrPaymentProviders, 'htmlOptions'=>array()),
                'created_at'        => array('type'=>'datetime', 'title'=>A::t('auctions', 'Created at'), 'width'=>'150px', 'default'=>null, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'date'), 'htmlOptions'=>array(), 'definedValues'=>array(), 'format'=>$dateTimeFormat, 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>''),
                'status'            => array('title'=>A::t('app', 'Status'), 'type'=>'html', 'align'=>'', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'callback'=>array('function'=>'getStatus', 'params'=>array())),
                'format_price'      => array('title'=>A::t('auctions', 'Price'), 'type'=>'label', 'align'=>'right', 'width'=>'70px', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'prependCode'=>''),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>false,
                    'link'=>'orders/invoiceMyOrder/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
            ),
            'return'=>true,
        ));
        ?>
    </div>
</div>
