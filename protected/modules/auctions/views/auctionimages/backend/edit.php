<?php
    $this->_activeMenu = 'auctions/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
        array('label'=>A::t('auctions', 'Auction Images'), 'url'=>'auctionImages/manage/auctionId/'.$auctionId),
        array('label'=>A::t('auctions', 'Edit Image')),
    );
?>

<h1><?= A::t('auctions', 'Auctions Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CDataForm', array(
            'model'         => 'Modules\Auctions\Models\AuctionImages',
            'primaryKey'    => $id,
            'operationType' => 'edit',
            'action'        => 'auctionImages/edit/auctionId/'.$auctionId.'/id/'.$id,
            'successUrl'    => 'auctionImages/manage/auctionId/'.$auctionId,
            'cancelUrl'     => 'auctionImages/manage/auctionId/'.$auctionId,
            'method'        => 'post',
            'htmlOptions'   => array(
                'id'                => 'frmAuctionImageEdit',
                'name'              => 'frmAuctionImageEdit',
                'enctype'           => 'multipart/form-data',
                'autoGenerateId'    => true
            ),
            'requiredFieldsAlert'   => true,
            'fields'                => array(
                'auction_id'    => array('type'=>'data', 'default'=>$auctionId),
                'image_file'    => array(
                    'type'              => 'imageupload',
                    'title'             => A::t('auctions', 'Image'),
                    'validation'        => array('required'=>true, 'type'=>'image', 'targetPath'=>'assets/modules/auctions/images/auctionimages/', 'maxSize'=>$imageMaxSize, 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'a'.$auctionId.'_'.CHash::getRandomString(10)),
					//'watermarkOptions'	=> array('enable'=>$auctionsWatermark, 'text'=>$watermarkText),
					'imageOptions'      => array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'image-edit-mode icon-xbig'),
					'thumbnailOptions'  => array('create'=>true, 'directory'=>'thumbs/', 'field'=>'image_file_thumb', 'postfix'=>'_thumb', 'width'=>'170', 'height'=>'114'),
                    'deleteOptions'     => array('showLink'=>true, 'linkUrl'=>'auctionImages/edit/auctionId/'.$auctionId.'/id/'.$id.'/delete/image', 'linkText'=>A::t('auctions', 'Delete')),
                    'fileOptions'       => array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/auctions/images/auctionimages/')
                ),
                'title'         => array('type'=>'textbox', 'title'=>A::t('auctions', 'Image Title'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxlength'=>125, 'class'=>'middle')),
                'sort_order'    => array('type'=>'textbox', 'title'=>A::t('auctions', 'Sort Order'), 'tooltip'=>'', 'default'=>'0', 'validation'=>array('required'=>true, 'type'=>'numeric', 'maxLength'=>3), 'htmlOptions'=>array('maxlength'=>3, 'class'=>'small')),
                'is_active'     => array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'tooltip'=>'', 'default'=>'1', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array(), 'viewType'=>'custom'),
            ),
            'buttons'   => array(
                'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate'      => array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
            ),
            'buttonsPosition'   => 'bottom',
            'messagesSource'    => 'core',
            'showAllErrors'     => false,
            'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Image')),
            'return'            => true,
        ));
    ?>
    </div>
</div>
