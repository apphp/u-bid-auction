<?php
use Modules\Auctions\Models\Members;
?>
<!--Header Top-->
<header class="header-top">
	<div class="container">
		<div class="header-top-info">
			<ul>
				<?php if($this->sitePhone != ''): ?>
					<li><i class="fa fa-phone"></i><span><?= A::t('app', 'Call us'); ?>:</span> <a href="tel:<?= preg_replace('/[^0-9]/i', '', $this->sitePhone); ?>"><?= $this->sitePhone; ?></a> </li>
				<?php endif; ?>
				<?php if($this->siteEmail != ''): ?>
					<li><a href="mailto:<?= $this->siteEmail; ?>"><i class="fa fa-envelope-o"></i><?= $this->siteEmail; ?></a> </li>
				<?php endif; ?>
			</ul>
		</div>

		<?php
			$socialNetworks = SocialNetworks::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'), array(), 'social-networks');
			if(!empty($socialNetworks)):
				echo '<ul class="social-icons standard">';
				foreach($socialNetworks as $key => $socialNetwork):
					echo '<li class="'.str_replace('-', '', $socialNetwork['code']).'"><a href="'.$socialNetwork['link'].'" target="_blank" rel="noopener noreferrer"><i class="fa fa-'.$socialNetwork['code'].'"></i></a></li>';
				endforeach;
				echo '</ul>';
			endif;
		?>

		<nav class="header-top-menu std-menu">
			<ul class="menu nav-pills nav-main">
				<?php
					// Show links if module is installed
					if(Modules::model()->isInstalled('auctions') && CAuth::getLoggedRole() != 'member'):
						echo '<li class="m-item"><a href="members/login"><i class="fa fa-user"></i> &nbsp;'.A::t('auctions', 'Member Login').' </a></li>';
                        if(ModulesSettings::model()->param('auctions', 'member_allow_registration')):
                            echo '<li class="m-item"><a href="members/registration">'.A::t('auctions', 'Registration').'</a></li>';
                        endif;
					elseif(Modules::model()->isInstalled('auctions') && CAuth::getLoggedRole() == 'member'):
                        echo '<li class="m-item"><a href="members/dashboard">'.A::t('auctions', 'My Account').'</a></li>';
						echo '<li class="m-item"><a>'.A::t('auctions', 'Bids Amount').': <span id="bids_amount" class="v-menu-item-info bg-primary">'.Members::getBidsAmount().'</span></a></li>';
                    endif;
				?>
				<li class="dropdown m-item">
					<a class="dropdown-toggle" href="javascript:void(0);">
						<?= CFile::fileExists('images/flags/'.A::app()->getLanguage('icon')) ? '<img src="images/flags/'.A::app()->getLanguage('icon').'" alt="'.CHtml::encode(A::app()->getLanguage()).'" /> &nbsp;' : '' ; ?>
						<?= A::app()->getLanguage('name_native'); ?>
						<?//= A::t('app', 'Language'); ?>
						<?php
							$countLanguages = Languages::model()->count(array('condition'=>"is_active = 1 AND used_on IN ('front-end', 'global')", 'orderBy'=>'sort_order ASC'));
							if($countLanguages > 1):
								echo '<i class="fa fa-caret-down"></i>';
							endif;
						?>
					</a>
					<?= Languages::drawSelector(array('display'=>'list', 'class'=>'dropdown-menu language-selector')); ?>
				</li>

				<?php if(FALSE): ?>
				<!--<li class="dropdown m-item">
					<a class="dropdown-toggle" href="javascript:void(0);">
						<?//= A::app()->getCurrency('symbol'); ?>
						<?//= A::app()->getCurrency('name'); ?>
						<?//= A::t('app', 'Currency'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
					<?//= Currencies::drawSelector(array('display'=>'list', 'class'=>'dropdown-menu currency-selector')); ?>
				</li>-->
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</header>

