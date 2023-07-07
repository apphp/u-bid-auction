<?php
$this->_pageTitle = A::t('auctions', 'My Bids History');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url'=>Website::getDefaultPage()),
    array('label' => A::t('auctions', 'Dashboard'), 'url'=>'members/dashboard'),
    array('label' => A::t('auctions', 'My Bids History')),
);

use \Modules\Auctions\Models\BidsHistory;

$tableName = CConfig::get('db.prefix').BidsHistory::model()->getTableName();
?>

<div class="row">
    <div class="col-sm-12">
        <?php
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'Modules\Auctions\Models\BidsHistory',
            'actionPath'=>'bidsHistory/MyBidsHistory',
            'condition'	=> $tableName.'.member_id = '.$memberId,
            'defaultOrder'=>array('created_at'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'options'	=> array(
                'filterDiv' 	=> array('class'=>'frmFilter smallFilters'),
                'filterType' 	=> 'default',
                'gridWrapper'   => array('tag'=>'div', 'class'=>'table-responsive'),
                'gridTable' 	=> array('class'=>'table'),
            ),
            'filters'=>array(
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'90px', 'maxLength'=>'32'),
            ),
            'fields'=>array(
                'auction_type'  => array('type'=>'label', 'title'=>A::t('auctions', 'Auction Type'), 'width'=>'120px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true),
                'auction_link'       => array('title'=>A::t('auctions', 'Auction'), 'type'=>'link', 'width'=>'', 'class'=>'right', 'headerClass'=>'right', 'isSortable'=>true, 'linkUrl'=>'auctions/{id}', 'linkText'=>'{auction_name}'),
                'created_at'    => array('title'=>A::t('auctions', 'Created at'), 'type'=>'datetime', 'table'=>'', 'default'=>null, 'width'=>'150px', 'maxLength'=>'', 'format'=>$dateFormat, 'htmlOptions'=>array()),
                'size_bid'      => array('type'=>'label', 'title'=>A::t('auctions', 'Value'), 'width'=>'75px', 'class'=>'right', 'headerClass'=>'right', 'callback'=>array('class'=>'Modules\Auctions\Components\AuctionsComponent', 'function'=>'priceFormating', 'params'=>array('field_name'=>'size_bid')), 'isSortable'=>true),
            ),
            'actions'=>array(),
            'return'=>true,
        ));

        ?>
    </div>
</div>