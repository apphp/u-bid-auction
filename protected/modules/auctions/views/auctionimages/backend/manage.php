<?php
    $this->_activeMenu = 'auctions/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
        array('label'=>A::t('auctions', 'Images')),
    );

//Register fancybox files
A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.mousewheel.pack.js', 2);
A::app()->getClientScript()->registerScriptFile('assets/vendors/fancybox/jquery.fancybox.pack'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.js', 2);
A::app()->getClientScript()->registerCssFile('assets/vendors/fancybox/jquery.fancybox'.(A::app()->getLanguage('direction') == 'rtl' ? '.rtl' : '').'.css');
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

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('auction', 'add')){
            echo '<a href="auctionImages/add/auctionId/'.$auctionId.'" class="add-new">'.A::t('auctions', 'Add Single Image').'</a>';
            if($allowMultiImageUpload){
                echo '&nbsp;&nbsp;&nbsp;';
                echo '<a href="auctionImages/addMultiple/auctionId/'.$auctionId.'" class="add-new">'.A::t('auctions', 'Add Multiple Images').'</a>';
            }
        }

        if(Admins::hasPrivilege('auction', 'edit')){
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'link', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px', 'linkUrl'=>'auctionImages/changeStatus/auctionId/'.$auctionId.'/id/{id}/page/{page}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('auctions', 'Click to change status')));
        }else{
            $isActive = array('title'=>A::t('app', 'Active'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px', 'linkUrl'=>'', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('app', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('app', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('app', 'Active')));
        }

        echo CWidget::create('CGridView', array(
            'model'             => 'Modules\Auctions\Models\AuctionImages',
            'actionPath'        => 'auctionImages/manage/auctionId/'.$auctionId,
            'condition'         => 'auction_id = '.$auctionId,
            'defaultOrder'      => array('sort_order'=>'ASC'),
            'passParameters'    => true,
            'pagination'        => array('enable'=>true, 'pageSize'=>20),
            'sorting'           => true,
            'filters'           => array(),
            'fields'            => array(
                'index'             => array('title'=>'', 'type'=>'index', 'align'=>'', 'width'=>'17px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>false),
                'image_file_thumb'  => array('title'=>A::t('auctions', 'Image'), 'type'=>'image', 'width'=>'60px',  'align'=>'', 'imagePath'=>'assets/modules/auctions/images/auctionimages/thumbs/', 'defaultImage'=>'no_image_thumb.png', 'imageWidth'=>'50px', 'imageHeight'=>'35px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'htmlOptions'=>array(), 'showImageInfo'=>true, 'prependCode'=>'<a class="fancybox" rel="reference_picture" href="#">', 'appendCode'=>'</a>'),
                'title'             => array('title'=>A::t('auctions', 'Title'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'stripTags'=>true),
                'sort_order'        => array('title'=>A::t('auctions', 'Sort Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'90px', 'changeOrder'=>true),
                'is_active'         => $isActive
            ),
            'actions'   => array(
                'edit'    => array(
                    'disabled'  => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('auction', 'edit'),
                    'link'      => 'auctionImages/edit/auctionId/'.$auctionId.'/id/{id}/', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('app', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'  => !Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('auction', 'delete'),
                    'link'      => 'auctionImages/delete/auctionId/'.$auctionId.'/id/{id}/', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('app', 'Delete this record'), 'onDeleteAlert'=>true
                )
            ),
            'return'=>true,
        ));
    ?>
    </div>
</div>
<?php
A::app()->getClientScript()->registerScript(
    'autoportalFancyboxHandler',
    "$('.fancybox').each(function() {
        var src = $(this).find('img').attr('src').replace('thumbs/', '').replace('_thumb', ''),
            row_id = $(this).closest('tr').attr('id'),
            title = $(this).closest('tr').find('td').eq(2).text();
        $(this).attr('href', src);
        $(this).attr('title', title);
    });
    $('.fancybox').fancybox({
        'opacity'       : true,
        'overlayShow'   : false,
        'overlayColor'  : '#000',
        'overlayOpacity': 0.5,
        'titlePosition' : 'inside',
        'cyclic'        : true,
        'transitionIn'  : 'elastic',
        'transitionOut' : 'fade'
    });
    ",
    5
);
