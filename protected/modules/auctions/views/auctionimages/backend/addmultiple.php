<?php
    $this->_activeMenu = 'auctions/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('app', 'Modules'), 'url'=>$backendPath.'modules/'),
        array('label'=>A::t('auctions', 'Auctions'), 'url'=>$backendPath.'modules/settings/code/auctions'),
        array('label'=>A::t('auctions', 'Auctions Management'), 'url'=>'auctions/manage'),
        array('label'=>A::t('auctions', 'Auction Images'), 'url'=>'auctionImages/manage/auctionId/'.$auctionId),
        array('label'=>A::t('auctions', 'Add Images')),
    );
?>

<h1><?= A::t('auctions', 'Auctions Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>
    <div class="sub-title">
        <?= $subTabs; ?>
    </div>

    <div class="content">
        <?= $actionMessage; ?>

        <?php
            echo CWidget::create('CFormView', array(
                'action'        =>  'auctionImages/addMultiple/auctionId/'.$auctionId,
                'cancelUrl'     =>  'auctionImages/manage/auctionId/'.$auctionId,
                'method'        =>  'post',
                'htmlOptions'   =>  array(
                    'name'              =>  'form-contact',
                    'enctype'           =>  'multipart/form-data',
                    'autoGenerateId'    =>  false
                ),
                'requiredFieldsAlert'=>true,
                'fields'=>array(
                    'act'               =>  array('type'=>'hidden', 'value'=>'send', 'htmlOptions'=>array()),
                    'auction_image[]'   => array('type'=>'file', 'title'=>'', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 'htmlOptions'=>array('multiple'=>'multiple', 'id'=>'auction_image')),
                ),
                'buttons'=>array(
                   'submit' =>array('type'=>'submit', 'value'=>A::te('auctions', 'Start Upload'), 'htmlOptions'=>array('name'=>'')),
                   'reset'  =>array('type'=>'reset', 'value'=>A::te('auctions', 'Reset'), 'htmlOptions'=>array('class'=>'button white')),
                   'cancel' =>array('type'=>'button', 'value'=>A::te('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','auctionImages/manage/auctionId/".$auctionId."')")),
                ),
                'buttonsPosition' => 'bottom',
                'return' => true
            ));
        ?>
        <br>
    </div>
</div>

<?php
A::app()->getClientScript()->registerScript(
    'autoportalMultiUpload',
    '$(document).ready(function(){
        $("input:submit").click(function(){
            if(parseInt($("#auction_image").get(0).files.length) > '.(int)$maxImages.'){
                alert("'.A::te('auctions', 'You can only upload a maximum of {count} files!', array('{count}'=>(int)$maxImages)).'");
                return false;
            }
            $(this).val("'.A::te("auctions", "Uploading...").'");
            $(this).closest("form").submit();
            $(this).attr("disabled", true);
        });
    });
    ',
    4
);
