<?php
$this->_pageTitle = A::t('auctions', 'My Shipments');
$this->_breadCrumbs = array(
    array('label' => A::t('app', 'Home'), 'url' => Website::getDefaultPage()),
    array('label' => A::t('app', 'Dashboard'), 'url' => 'members/dashboard'),
    array('label' => A::t('auctions', 'My Shipments')),
);

use Modules\Auctions\Models\Shipments;

$tableNameShipments = CConfig::get('db.prefix') . Shipments::model()->getTableName();
?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            echo $actionMessage;

            echo CWidget::create('CGridView', array(
                'model' => 'Modules\Auctions\Models\Shipments',
                'actionPath' => 'shipments/myShipments',
                'condition' => $tableNameShipments . '.member_id = ' . $memberId,
                'defaultOrder' => array('created_at' => 'ASC'),
                'passParameters' => true,
                'pagination' => array('enable' => true, 'pageSize' => 20),
                'sorting' => true,
                'options' => array(
                    'filterDiv' => array('class' => 'frmFilter'),
                    'filterType' => 'default',
                    'gridWrapper' => array('tag' => 'div', 'class' => 'table-responsive'),
                    'gridTable' => array('class' => 'table'),
                ),
                'filters' => array(
                    'auction_id' => array('title' => '', 'visible' => false, 'table' => $tableNameShipments, 'type' => 'textbox', 'operator' => '=', 'width' => '', 'maxLength' => '11'),
                    'carrier' => array('title' => A::t('auctions', 'Carrier'), 'type' => 'textbox', 'operator' => '%like%', 'width' => '100px', 'maxLength' => '32'),
                    'tracking_number' => array('title' => A::t('auctions', 'Tracking Number'), 'type' => 'textbox', 'operator' => '%like%', 'width' => '100px', 'maxLength' => '32'),
                    'created_at' => array('title' => A::t('auctions', 'Created at'), 'type' => 'datetime', 'operator' => 'like%', 'width' => '90px', 'maxLength' => '32'),
                    'shipping_status' => array('title' => A::t('auctions', 'Shipping Status'), 'type' => 'enum', 'operator' => '=', 'width' => '100px', 'source' => $shippingStatus, 'emptyOption' => true, 'emptyValue' => ''),

                ),
                'fields' => array(
                    'auction_link' => array('title' => A::t('auctions', 'Auction'), 'type' => 'link', 'width' => '', 'class' => 'right', 'headerClass' => 'right', 'isSortable' => true, 'linkUrl' => 'auctions/{auction_id}', 'linkText' => '{auction_name}'),
                    'carrier' => array('type' => 'label', 'title' => A::t('auctions', 'Carrier'), 'width' => '125px', 'class' => 'left', 'headerClass' => 'left', 'isSortable' => true),
                    'tracking_number' => array('type' => 'label', 'title' => A::t('auctions', 'Tracking Number'), 'width' => '150px', 'class' => 'left', 'headerClass' => 'left', 'isSortable' => true),
                    'created_at' => array('title' => A::t('auctions', 'Created at'), 'type' => 'datetime', 'table' => '', 'default' => null, 'width' => '120px', 'maxLength' => '', 'format' => $dateFormat, 'htmlOptions' => array()),
                    'steps_shipment' => array('title' => '', 'type' => 'link', 'width' => '75px', 'class' => 'right', 'align' => 'center', 'isSortable' => true, 'linkUrl' => 'shipments/stepsShipment/id/{id}/auctionId/{auction_id}', 'linkText' => A::t('auctions', 'More'), 'prependCode' => '[ ', 'appendCode' => ' ]'),
                    'confirm_received' => array('title' => '', 'type' => 'html', 'align' => '', 'width' => '170px', 'class' => 'left', 'headerTooltip' => '', 'headerClass' => 'left', 'isSortable' => false, 'callback' => array('class' => 'Modules\Auctions\Components\AuctionsComponent', 'function' => 'drawConfirmDeliveredLink', 'params' => array('field_name' => 'shipping_status')), 'definedValues' => array(), 'htmlOptions' => array()),
                    'shipping_status' => array('title' => A::t('auctions', 'Shipping Status'), 'type' => 'label', 'align' => 'center', 'width' => '100px', 'isSortable' => true, 'definedValues' => $shippingStatusLabel, 'htmlOptions' => array('class' => 'tooltip-link')),
                ),
                'return' => true,
            ));
            ?>
        </div>
    </div>
<?php
A::app()->getClientScript()->registerScript(
    'cancel-appointment',
    '$(document).ready(function() {
        $("#confirm_received").on("click", function(){
            return confirm("' . A::te('auctions', 'Are you sure you want to confirm the delivery of the auction? This action cannot be undone.') . '");
        });
    });',
    2
);;