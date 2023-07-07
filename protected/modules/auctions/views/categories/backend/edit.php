<?php
$this->_activeMenu = 'categories/manage';
$this->_breadCrumbs = array(
    array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
    array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
    array('label'=>A::t('auctions', 'Categories Management'), 'url'=>'categories/manage'),
    array('label'=>A::t('auctions', 'Edit Category')),
);

$formName = 'frmCategoryEdit';
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
        'model'             => 'Modules\Auctions\Models\Categories',
        'primaryKey'        => $id,
        'operationType'     => 'edit',
        'action'            => 'categories/edit/id/'.$id,
        'successUrl'        => 'categories/manage'.($parentId > 0 ? '/parentId/'.$parentId : ''),
        'cancelUrl'         => 'categories/manage'.($parentId > 0 ? '/parentId/'.$parentId : ''),
        'passParameters'    => false,
        'method'            => 'post',
        'htmlOptions'       => array(
            'name'              => $formName,
            'autoGenerateId'    => true
        ),
        'requiredFieldsAlert' => true,
        'fields' => array(
            'separatorContact'=>array(
                'separatorInfo' => array('legend'=>A::t('auctions', 'General Information')),
                'icon' =>array(
                    'type'              => 'imageUpload',
                    'title'             => A::t('auctions', 'Image'),
                    'default'           => '',
                    'validation'        => array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/auctions/images/categories/', 'maxSize'=>'1M', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>CHash::getRandomString(15), 'fileNameCase'=>'lower', 'maxWidth'=>'512px', 'maxHeight'=>'512px'),
                    'imageOptions'      => array('showImage'=>true, 'showImageName'=>true, 'imageClass'=>'icon-big'),
                    'deleteOptions'     => array('showLink'=>true, 'linkUrl'=>'categories/edit/id/'.$id.'/delete/image', 'linkText'=>A::t('auctions', 'Delete')),
                    'thumbnailOptions'  => array('create'=>true, 'field'=>'icon_thumb', 'width'=>'150px', 'directory'=>'thumbs/'),
                    'fileOptions'       => array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'assets/modules/auctions/images/categories/')
                ),
                'sort_order' => array('type'=>'textbox', 'title'=>A::t('auctions', 'Sort Order'), 'default'=>0, 'tooltip'=>'', 'validation'=>array('required'=>false, 'maxLength'=>3, 'type'=>'numeric'), 'htmlOptions'=>array('maxLength'=>3, 'class'=>'small')),
            ),
            'parent_id' => array('type'=>'data', 'default'=>$parentId)
        ),
        'translationInfo' => array('relation'=>array('id', 'category_id'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'))),
        'translationFields' => array(
            'name'  => array('type'=>'textbox', 'title'=>A::t('auctions', 'Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125', 'class'=>'large')),
            'description'   => array('type'=>'textarea', 'title'=>A::t('auctions', 'Description'), 'validation'=>array('required'=>false, 'type'=>'text', 'maxLength'=>2048), 'htmlOptions'=>array('maxLength'=>'2048')),
        ),
        'buttons' => array(
            'submitUpdateClose' =>array('type'=>'submit', 'value'=>A::t('app', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
            'submitUpdate'      =>array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
            'cancel'            => array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
        ),
        'messagesSource'    => 'core',
        'alerts'            => array('type'=>'flash', 'itemName'=>A::t('auctions', 'Category')),
        'return'            => true,
    ));
    ?>
    </div>
</div>