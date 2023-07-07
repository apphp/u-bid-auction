<?php
use Modules\Auctions\Components\AuctionsComponent;
?>
<!--Footer-Wrap-->
<div class="footer-wrap">
    <footer>
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<section class="widget">
						<img alt="bootsbox" src="templates/default/images/logo-dark.png" style="height: 40px; margin-bottom: 20px;">
						<p class="pull-bottom-small">
							Donec quam felis, ultricies nec, pellen tesqueeu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel aliquet nec, vulputate eget aliquet nec, arcu.
						</p>
						<p>
							<a href="<?= Website::getDefaultPage(); ?>"><?= A::t('app', 'Read More'); ?> →</a>
						</p>
					</section>
				</div>

                <div class="col-sm-3">
                    <section class="widget">
                        <div class="widget-heading">
                            <h4><?= A::t('app', 'Contact Information'); ?></h4>
                        </div>
                        <div class="footer-contact-info">
                            <ul>
                                <?php if($this->siteAddress): ?>
                                    <li>
                                        <p><i class="fa fa-map-marker"></i> <?= $this->siteAddress; ?></p>
                                    </li>
                                <?php endif; ?>
                                <?php if($this->siteEmail): ?>
                                    <li>
                                        <p><i class="fa fa-envelope"></i><strong>Email:</strong> <a href="<?= $this->siteEmail; ?>"><?= $this->siteEmail; ?></a></p>
                                    </li>
                                <?php endif; ?>
                                <?php if($this->sitePhone): ?>
                                    <li>
                                        <p><i class="fa fa-phone"></i><strong><?= A::t('auctions', 'Phone'); ?>:</strong> <a href="tel:<?= preg_replace('/[^0-9]/i', '', $this->sitePhone); ?>"><?= $this->sitePhone; ?></a></p>
                                    </li>
                                <?php endif; ?>
                                <?php if($this->siteFax): ?>
                                    <li>
                                        <p><i class="fa fa-fax"></i><strong><?= A::t('auctions', 'Phone'); ?>:</strong> <a href="tel:<?= preg_replace('/[^0-9]/i', '', $this->siteFax); ?>"><?= $this->siteFax; ?></a></p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                </div>
                <?php if(Modules::model()->isInstalled('blog')): ?>
                    <div class="col-sm-3">
                        <section class="widget v-recent-entry-widget">
                            <div class="widget-heading">
                                <h4><?= A::t('blog', 'Recent Posts'); ?></h4>
                            </div>
                            <?= \Modules\Blog\Components\BlogComponent::drawRecentPostsBlock(); ?>
                        </section>
                    </div>
                <?php endif; ?>
                <div class="col-sm-3">
                    <?= (Modules::model()->isInstalled('news')) ? \Modules\News\Components\NewsComponent::drawSubscriptionBlock() : ''; ?>
                </div>
			</div>
		</div>
	</footer>

	<div class="copyright">
		<div class="container">
            <div>
                <p><?= $this->siteFooter; ?></p>
                &nbsp;&#124;&nbsp;<?= AuctionsComponent::drawFooterLinks(); ?>
            </div>
			<?php
				$socialNetworks = SocialNetworks::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC'), array(), 'social-networks');
				if(!empty($socialNetworks)):
					echo '<ul class="social-icons std-menu pull-right">';
					foreach($socialNetworks as $key => $socialNetwork):
						echo '<li><a href="'.$socialNetwork['link'].'" target="_blank" data-placement="top" rel="tooltip noopener noreferrer" title="" data-original-title="'.$socialNetwork['name'].'"><i class="fa fa-'.$socialNetwork['code'].'"></i></a></li>';
					endforeach;
					echo '</ul>';
				endif;
			?>			

		</div>
	</div>
</div>
<!--End Footer-Wrap-->

