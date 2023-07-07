<?php
$this->_activeMenu = 'auctions/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
    array('label'=>A::t('auctions', 'Add Auction')),
);

$formName = 'frmAuctionAdd';
$onchange = "addChangeCountry(this.value,'')";

A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/tiny_mce.js');
A::app()->getClientScript()->registerScriptFile('assets/vendors/tinymce/config.js');
?>


<h1><?= A::t('auctions', 'Auctions Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title"><?= A::t('auctions', 'Add Auction'); ?></div>
    <div class="content">

    <?php
        echo CWidget::create('CDataForm', array(
            'model'             => 'Modules\Auctions\Models\Auctions',
            'operationType'     => 'add',
            'action'            => 'auctions/add',
            'successUrl'        => 'auctions/manage',
            'cancelUrl'         => 'auctions/manage',
            'passParameters'    => false,
            'method'            => 'post',
            'htmlOptions'       => array(
                'name'              => $formName,
                'autoGenerateId'    => true,
            ),
            'requiredFieldsAlert' => true,
            'fields'    => array(
                'separatorGeneral' =>array(
                    'separatorInfo'=>array('legend'=>A::t('auctions', 'General Information')),
                    'auction_type_id'   => array('type'=>'select', 'title'=>A::t('auctions', 'Auction Type'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($auctionTypesList)), 'data'=>$auctionTypesList, 'htmlOptions'=>array()),
                    'category_id'       => array('type'=>'select', 'title'=>A::t('auctions', 'Category'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($categoriesList)), 'data'=>$categoriesList, 'htmlOptions'=>array()),
                    'status'            => array('type'=>'select', 'title'=>A::t('app', 'Status'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($status)), 'data'=>$status, 'htmlOptions'=>array()),
                ),
                'separatorDate' =>array(
                    'separatorInfo'=>array('legend'=>A::t('auctions', 'Auction Date Information')),
                    'date_from' =>array('type'=>'datetime', 'title'=>A::t('auctions', 'Start date'), 'tooltip'=>'', 'default'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>19, 'minValue'=>date('Y-m-d'), 'maxValue'=>date('Y-m-d', strtotime("+1 month"))), 'maxDate'=>'+30', 'yearRange'=>'+0:+0', 'htmlOptions'=>array('maxlength'=>'19', 'style'=>'width:140px'), 'definedValues'=>array(), 'viewType'=>'datetime', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>''),
                    'date_to'   =>array('type'=>'datetime', 'title'=>A::t('auctions', 'End date'), 'tooltip'=>'', 'default'=>null, 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>19, 'minValue'=>date('Y-m-d'), 'maxValue'=>date('Y-m-d', strtotime("+2 years"))), 'maxDate'=>'', 'yearRange'=>'+0:+2', 'htmlOptions'=>array('maxlength'=>'19', 'style'=>'width:140px'), 'definedValues'=>array(), 'viewType'=>'datetime', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>''),
                ),
                'separatorPrice' =>array(
                    'separatorInfo'=>array('legend'=>A::t('auctions', 'Auction Price Information')),
                    'start_price'       => array('type'=>'textbox',  'title'=>A::t('auctions', 'Start Price'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                    'buy_now_price'     => array('type'=>'textbox',  'title'=>A::t('auctions', 'Buy Now Price'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                    'size_bid'          => array('type'=>'textbox',  'title'=>A::t('auctions', 'Step Price'), 'default'=>'0.01', 'tooltip'=>A::t('auctions', 'Bid amount. The default bid amount is 1 penny.'), 'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'0.00', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>$pricePrependCode.' ', 'appendCode'=>$priceAppendCode),
                    'step_size'         => array('type'=>'textbox',  'title'=>A::t('auctions', 'Step Bids Size'), 'default'=>0, 'tooltip'=>A::t('auctions', 'The number of bids that will be deducted from the member`s account'), 'validation'=>array('required'=>true, 'type'=>'int', 'minValue'=>'0', 'maxValue'=>'9999999', 'format'=>$typeFormat), 'htmlOptions'=>array('maxLength'=>7, 'class'=>'small'), 'prependCode'=>'', 'appendCode'=>$priceAppendCode),
                ),
                'paid_status'         => array('type'=>'data', 'default'=>'0'),
                'shipping_status'     => array('type'=>'data', 'default'=>'0'),
                'current_bid'         => array('type'=>'data', 'default'=>'0'),
                'auction_number'      => array('type'=>'data', 'default'=>CHash::getRandomString(10, array('case'=>'upper'))),
                'created_at'          => array('type'=>'data', 'default'=>CLocale::date('Y-m-d H:i:s')),
                'status_changed'      => array('type'=>'data', 'default'=>CLocale::date('Y-m-d H:i:s')),
                'paid_status_changed' => array('type'=>'data', 'default'=>CLocale::date('Y-m-d H:i:s')),
            ),
            'translationInfo' => array('relation'=>array('id', 'auction_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
            'translationFields' => array(
                'name'               => array('type'=>'textbox', 'title'=>A::t('auctions', 'Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'large')),
                'short_description'  => array('type'=>'textarea', 'title'=>A::t('auctions', 'Short Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125','id'=>'auction_short_description')),
                'description'        => array('type'=>'textarea', 'title'=>A::t('auctions', 'Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048', 'id'=>'auction_description')),
            ),
            'buttons' => array(
               'submit' => array('type'=>'submit', 'value'=>A::t('app', 'Create'), 'htmlOptions'=>array('name'=>'')),
               'cancel' => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
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