<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
    array('label'=>A::t('auctions', 'Edit Auction')),
);

$formName = 'frmAuctionEdit';

A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/tiny_mce.js');
A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/config.js');
A::app()->getClientScript()->registerCssFile('templates/default/css/font-awesome.min.css');
?>

<h1><?= A::t('auctions', 'Auctions Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <?= $subTabs; ?>
    </div>
    <div class="content">

    <?= !empty($drawStepShipment) ? $drawStepShipment : ''; ?>
    <?= !empty($actionMessage) ? $actionMessage : ''; ?>

    <?php
    echo CWidget::create('CDataForm', array(
        'model'             => 'Modules\Auctions\Models\Auctions',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'auctions/edit/id/'.$id,
        'successUrl'        => 'auctions/manage',
        'cancelUrl'         => 'auctions/manage',
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'autoGenerateId'    => true
        ),
        'requiredFieldsAlert' => true,
        'fields' => array(
            'separatorGeneral' =>array(
                'separatorInfo'=>array('legend'=>A::t('auctions', 'General Information')),
                'auction_type_id'       => array('type'=>'label', 'title'=>A::t('auctions', 'Auction Type'), 'tooltip'=>'', 'default'=>'--', 'validation'=>array('required'=>false, 'type'=>'set'), 'htmlOptions'=>array(), 'definedValues'=>$auctionTypesList),
                'category_id'       => array('type'=>'select', 'title'=>A::t('auctions', 'Category'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($categoriesList)), 'data'=>$categoriesList, 'htmlOptions'=>array()),
                'status'            => array('type'=>'select', 'title'=>A::t('app', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($status)), 'data'=>$status, 'htmlOptions'=>array()),
                'paid_status' => array('type'=>'select', 'title'=>A::t('auctions', 'Paid Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($paidStatus)), 'data'=>$paidStatus, 'htmlOptions'=>array()),
                'shipping_status' => array('type'=>'select', 'title'=>A::t('auctions', 'Shipping Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($shippingStatus)), 'data'=>$shippingStatus, 'htmlOptions'=>array()),
            ),
            'separatorDate' =>array(
                'separatorInfo'=>array('legend'=>A::t('auctions', 'Auction Date Information')),
                'date_from' =>array('type'=>'datetime', 'title'=>A::t('auctions', 'Start date'), 'tooltip'=>'', 'default'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>19, 'minValue'=>(($auction->date_from < CLocale::date('Y-m-d H:i:s') && $auction->date_to > CLocale::date('Y-m-d H:i:s')) ? '' : date('Y-m-d')), 'maxValue'=>date('Y-m-d', strtotime("+1 month"))), 'maxDate'=>'+30', 'yearRange'=>'+0:+0', 'htmlOptions'=>array('maxlength'=>'19', 'style'=>'width:140px'), 'definedValues'=>array(), 'viewType'=>'datetime', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>''),
                'date_to'   =>array('type'=>'datetime', 'title'=>A::t('auctions', 'End date'), 'tooltip'=>'', 'default'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>19, 'minValue'=>date('Y-m-d'), 'maxValue'=>date('Y-m-d', strtotime("+2 years"))), 'maxDate'=>'', 'yearRange'=>'+0:+2', 'htmlOptions'=>array('maxlength'=>'19', 'style'=>'width:140px'), 'definedValues'=>array(), 'viewType'=>'datetime', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>''),
            ),
            'separatorPrice' =>array(
                'separatorInfo'=>array('legend'=>A::t('auctions', 'Auction Price Information')),
                'start_price'       => array('type'=>'textbox',  'title'=>A::t('auctions', 'Start Price'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                'buy_now_price'     => array('type'=>'textbox',  'title'=>A::t('auctions', 'Buy Now Price'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                'size_bid'          => array('type'=>'textbox',  'title'=>A::t('auctions', 'Step Price'), 'default'=>'0.01', 'tooltip'=>A::t('auctions', 'Bid amount. The default bid amount is 1 penny.'), 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                'step_size'         => array('type'=>'textbox',  'title'=>A::t('auctions', 'Step Bids Size'), 'default'=>0, 'disabled'=>($auction->auction_type_id == 1 ? true : false), 'tooltip'=>A::t('auctions', 'The number of bids that will be deducted from the member`s account'), 'validation'=>array('required'=>($auction->auction_type_id == 1 ? false : true), 'type'=>'int', 'minValue'=>'0', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>'', 'appendCode'=>$priceAppendCode),
                'current_bid'       => array('type'=>'label',  	 'title'=>A::t('auctions', 'Current Bid'), 'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>[], 'prependCode'=>'<span style="float:left;margin-top:7px;margin-right:5px">'.$pricePrependCode.'</span> '),
            ),
        ),
        'translationInfo' => array('relation'=>array('id', 'auction_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
        'translationFields' => array(
            'name'              => array('type'=>'textbox', 'title'=>A::t('auctions', 'Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'large')),
            'short_description' => array('type'=>'textarea', 'title'=>A::t('auctions', 'Short Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'id'=>'auction_short_description')),
            'description'       => array('type'=>'textarea', 'title'=>A::t('auctions', 'Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048', 'id'=>'auction_description')),
        ),
        'buttons' => array(
            'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
            'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
            'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Auction')),
        'return'            => true,
    ));
    ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript('setTinyMceEditorDescription', 'setEditor("auction_description",false);', 2);
A::app()->getClientScript()->registerScript('setTinyMceEditorShortDescription', 'setEditor("auction_short_description",false);', 2);