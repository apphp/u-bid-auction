<?php
$this->_pageTitle = A::t('auctions', 'My Auctions');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Auctions')),
);

use \Modules\Auctions\Models\Auctions;

$tableName = CConfig::get('db.prefix').Auctions::model()->getTableName();
?>

<div class="row">
    <div class="col-sm-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="<?= $statusTab == 'active' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'active' ? 'javascript:void(0);' : 'auctions/myAuctions/status/active'; ?>"><?= A::t('app', 'Active'); ?></a>
                </li>
                <li class="<?= $statusTab == 'won' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'won' ? 'javascript:void(0);' : 'auctions/myAuctions/status/won'; ?>"><?= A::t('auctions', 'Won Auctions'); ?></a>
                </li>
                <li class="<?= $statusTab == 'loose' ? 'active' : ''; ?>">
                    <a href="<?= $statusTab == 'loose' ? 'javascript:void(0);' : 'auctions/myAuctions/status/loose'; ?>"><?= A::t('auctions', 'Loose Auctions'); ?></a>
                </li>
            </ul>
        </div>
        <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'             => 'Modules\Auctions\Models\Auctions',
            'actionPath'        => 'auctions/myAuctions/status/'.$statusTab,
            'condition'	        => $condition,
            'defaultOrder'      => array('created_at'=>'DESC'),
            'passParameters'    => true,
            'pagination'        => array('enable'=>true, 'pageSize'=>20),
            'sorting'           => true,
            'options'	=> array(
                'filterDiv' 	=> array('class'=>'frmFilter'),
                'filterType' 	=> 'default',
                'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                'gridTable' 	=> array('class'=>'table'),
            ),
            'filters'           => array(
                'auction_number'    => array('title'=>A::t('auctions', 'Auction ID'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'80px', 'maxLength'=>'10'),
                'name'              => array('title'=>A::t('auctions', 'Name'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'auction_translations', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>'125'),
                'date_from'         => array('title'=>A::t('auctions', 'Start date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
                'date_to'           => array('title'=>A::t('auctions', 'End date'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'65px', 'maxLength'=>'32'),
                'auction_type_id'   => array('title'=>A::t('auctions', 'Auction Type'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'70px', 'source'=>$auctionTypesList),
                'category_id'       => array('title'=>A::t('auctions', 'Category'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'auctions', 'emptyOption'=>true, 'emptyValue'=>'', 'operator'=>'=', 'width'=>'120px', 'source'=>$categoriesList),
                'status'            => array('title'=>A::t('app', 'Status'), 'type'=>'enum', 'operator'=>'=', 'width'=>'85px', 'source'=>$status, 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'auction_link'      => array('title'=>A::t('auctions', 'Auction'), 'type'=>'link', 'width'=>'', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>false, 'linkUrl'=>'auctions/{id}', 'linkText'=>'{auction_name}'),
                'auction_type_id'   => array('title'=>A::t('auctions', 'Type'), 'type'=>'label', 'align'=>'', 'width'=>'70px',  'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>$auctionTypesList, 'htmlOptions'=>array()),
                // 'category_id'       => array('title'=>A::t('auctions', 'Category'), 'type'=>'label', 'align'=>'', 'width'=>'',  'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>$categoriesListManage, 'htmlOptions'=>array()),
                'date_from'         => array('title'=>A::t('auctions', 'Start date'), 'type'=>'datetime', 'table'=>'', 'default'=>null, 'width'=>'', 'maxLength'=>'', 'format'=>$dateTimeFormat, 'htmlOptions'=>array()),
                'date_to'           => array('title'=>A::t('auctions', 'End date'), 'type'=>'datetime', 'table'=>'', 'default'=>null, 'width'=>'', 'maxLength'=>'', 'format'=>$dateTimeFormat, 'htmlOptions'=>array()),
                'current_bid'       => array('title'=>A::t('auctions', 'Current Bid'), 'type'=>'html', 'align'=>'right', 'width'=>'125px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'start_price'))),
                // 'buy_now_price'     => array('title'=>A::t('auctions', 'Buy Now Price'), 'type'=>'html', 'align'=>'right', 'width'=>'60px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'buy_now_price'))),
                // 'size_bid'          => array('title'=>A::t('auctions', 'Step Price'), 'type'=>'html', 'align'=>'right', 'width'=>'60px',  'class'=>'right pr20',  'headerClass'=>'right pr20',  'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'size_bid'))),
                'status'            => array('title'=>A::t('app', 'Status'), 'type'=>'label', 'align'=>'center', 'width'=>'90px',  'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$statusLabel, 'htmlOptions'=>array('class'=>'tooltip-link')),
                'paid_status'       => array('title'=>A::t('auctions', 'Paid Status'), 'type'=>'label', 'align'=>'center', 'width'=>'70px', 'disabled'=>($statusTab == 'won' ? false : true), 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$paidStatusLabel, 'htmlOptions'=>array('class'=>'tooltip-link')),
                'shipping_status'   => array('title'=>A::t('auctions', 'Shipping Status'), 'type'=>'label', 'align'=>'center', 'width'=>'70px', 'disabled'=>($statusTab == 'won' ? false : true), 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$shippingStatusLabel, 'htmlOptions'=>array('class'=>'tooltip-link')),
                'paid_ship_link'    => array('title'=>'', 'type'=>'html', 'width'=>'110px', 'align'=>'center', 'disabled'=>($statusTab == 'won' ? false : true), 'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'drawPayOrShipmentLink', 'params'=>array('field_name'=>'paid_status'))),
                'review_link'       => array('title'=>'', 'type'=>'html', 'width'=>'110px', 'align'=>'center', 'disabled'=>($statusTab == 'won' ? false : true), 'isSortable'=>true, 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'drawReviewLink', 'params'=>array('field_name'=>'shipping_status'))),
            ),
            'return'=>true,
        ));

        ?>
    </div>
</div>
