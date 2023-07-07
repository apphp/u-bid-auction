<?php header('content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>" />
    <meta name="generator" content="<?= CConfig::get('name').' v'.CConfig::get('version'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- don't move it -->
    <base href="<?= A::app()->getRequest()->getBaseUrl(); ?>" />
    <title><?= CHtml::encode($this->_pageTitle); ?></title>
	<link rel="shortcut icon" href="templates/default/images/apphp.ico" />

    <!-- Web Fonts  -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,700,800,900" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="templates/default/css/custom.css">

    <!-- Form Elements -->
    <link href="" rel="stylesheet" />

    <!-- Libs CSS -->
	<?php
		if(!TRUE):
			// Link all CSS files at the place
            $listCssFiles = array(
                'bootstrap.min.css',
                'font-awesome.min.css',
                'v-nav-menu.css',
                'v-portfolio.css',
                'v-blog.css',
                'v-bg-stylish.css',
                'v-shortcodes.css',
                'v-form-element.css',
                'theme-responsive.css',
                'theme.css',
            );
            if(A::app()->getLanguage('direction') == 'rtl'){
                $listCssFiles[] = 'custom.rtl.css';
            }
			echo CHtml::cssFiles(
                $listCssFiles,
				'templates/default/css/'
			);
		else:
			// Register all CSS files and generate one minified file
			///A::app()->getClientScript()->registerCss('aaaaaa', 'color:#fff;');
            $listCssFiles = array(
                'bootstrap.min.css',
                'font-awesome.min.css',
                'v-nav-menu.css',
                'v-portfolio.css',
                'v-blog.css',
                'v-bg-stylish.css',
                'v-shortcodes.css',
                'v-form-element.css',
                'theme-responsive.css',
                'theme.css',
            );
		    if(A::app()->getLanguage('direction') == 'rtl'){
                $listCssFiles[] = 'custom.rtl.css';
            }
			A::app()->getClientScript()->registerCssFiles(
				$listCssFiles,
				'templates/default/css/'
			);
            A::app()->getClientScript()->registerCssFiles(
                array(
                    'owl-carousel/owl.theme.css',
                    'owl-carousel/owl.carousel.css',
                    'rs-plugin/css/settings.css',
                    'rs-plugin/css/custom-captions.css',
                ),
                'templates/default/plugins/'
            );
		endif;
        echo CHtml::cssFile('assets/vendors/toastr/toastr.min.css');
	?>


    <!-- jquery files -->
	<?//= CHtml::scriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'); ?>
	<?//= CHtml::scriptFile('http://code.jquery.com/ui/1.10.2/jquery-ui.js'); ?>
    <?= CHtml::scriptFile('assets/vendors/jquery/jquery.js'); ?>
</head>
<body>

<?php include('templates/default/header.php'); ?>

    <div id="container">

        <!-- BANNERS-->
        <?php if(Website::isDefaultPage() && !empty($banners)):?>
            <div class="home-slider-wrap fullwidthbanner-container" id="home">
                <div class="v-rev-slider" data-slider-options='{ "startheight": 570 }'>
                    <ul>
                        <?php foreach($banners as $banner): ?>
                            <li data-transition="random" data-slotamount="7" data-masterspeed="300" >
                                <img src="<?= $baseUrl; ?>assets/modules/banners/images/items/<?= $banner['image_file']; ?>" alt="image-<?= $banner['id']; ?>"  data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">
                                <div class="tp-caption light_heavy_60 sfr str"
                                     data-x="600"
                                     data-y="150"
                                     data-speed="500"
                                     data-start="500"
                                     data-endspeed="300">
                                    <?= $banner['banner_title']; ?>
                                </div>
                                <div class="tp-caption v-lead white-color sfr str light_medium_20"
                                     data-x="600"
                                     data-y="250"
                                     data-speed="500"
                                     data-start="1000"
                                     data-endspeed="300">
                                    <?= $banner['banner_description']; ?>
                                </div>
                                <?php if(!empty($banner['link_url'])): ?>
                                    <div class="tp-caption lfb stb"
                                         data-x="600"
                                         data-y="400"
                                         data-speed="700"
                                         data-start="1700"
                                         data-easing="Circ.easeInOut"
                                         data-splitin="none"
                                         data-splitout="none"
                                         data-elementdelay="0"
                                         data-endelementdelay="0"
                                         data-endspeed="600">
                                        <a href='<?= $banner['link_url']; ?>' class="btn v-btn v-second-light"><?= !empty($banner['banner_button']) ? $banner['banner_button'] : A::t('app', 'View'); ?></a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="shadow-right"></div>
            </div>
        <?php endif; ?>

        <?php
        # Show breadcrumbs
        $breadCrumbs = A::app()->view->_breadCrumbs;
        if(!Website::isDefaultPage() && !empty($breadCrumbs)):
            ?>
            <div class="v-page-heading v-bg-stylish v-bg-stylish-v1">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="heading-text">
                                <h4 class="entry-title"><?= A::app()->view->_breadcrumbsTitle != '' ? A::app()->view->_breadcrumbsTitle : A::app()->view->_pageTitle; ?></h4>
                            </div>
                            <?php
                            CWidget::create('CBreadCrumbs', array(
                                'links' => A::app()->view->_breadCrumbs,
                                'separator' => '&nbsp;/&nbsp;',
                                'wrapperClass' => 'breadcrumb',
                                'return' => false
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?= A::app()->view->getLayoutContent(); ?>

        <?php include('templates/default/footer.php'); ?>
    </div>

    <!--// BACK TO TOP //-->
    <div id="back-to-top" class="animate-top"><i class="fa fa-angle-up"></i></div>

    <!-- Libs -->
    <?= CHtml::scriptFile('templates/default/js/bootstrap.min.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.flexslider-min.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.easing.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.fitvids.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.carouFredSel.min.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/theme-plugins.js', 2); ?>
    <?= CHtml::scriptFile('templates/default/js/digital-countdown-clock/countdown-timer.js', 2); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.isotope.min.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/jquery.validate.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/imagesloaded.js'); ?>
    <?= CHtml::scriptFile('templates/default/js/view.min.js?auto'); ?>

    <?= CHtml::scriptFile('templates/default/plugins/rs-plugin/js/jquery.themepunch.tools.min.js'); ?>
    <?= CHtml::scriptFile('templates/default/plugins/rs-plugin/js/jquery.themepunch.revolution.min.js'); ?>

    <?= CHtml::scriptFile('templates/default/js/theme-core.js'); ?>
    <?php
    if(Modules::model()->exists("code = 'auctions' AND is_installed = 1")):
        echo CHtml::scriptFile('assets/modules/auctions/js/auctions.js', 2);
    endif;
    ?>
    <?= CHtml::scriptFile('assets/vendors/toastr/toastr.min.js', 2); ?>
    <?= CHtml::scriptFile('assets/vendors/jquery/jquery-ui.min.js', 2); ?>

</body>
</html>