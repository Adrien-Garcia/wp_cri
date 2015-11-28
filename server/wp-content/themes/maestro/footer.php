			<footer class="footer" role="contentinfo" id="sel-footer">
				<?php

				//get theme options
				$options = get_option( 'theme_settings' );?>

				<div class="footer-top">
					
					<div class="block-links">
						<div id="inner-top-footer" class="wrap cf">
							<ul id="">
								<li class="formations js-home-block-link">
									<?php _e('Le catalogue formations'); ?>
									<a href="#" title=""><span><?php _e('Consulter'); ?></span></a>
								</li>
								<li class="cahier js-home-block-link">
									<?php _e('Les cahiers du cridon'); ?>
									<a href="#" title=""><span><?php _e('Consulter'); ?></span></a>
								</li>
								<li class="services js-home-block-link"> 
									<?php _e('Les services plus'); ?>
									<a href="#" title=""><span><?php _e('Consulter'); ?></span></a>
								</li>
							</ul>
						</div>
					</div>

					<div class="footer-cridon">
						<div id="inner-footer" class="wrap cf">
							<div class="block-left">
								<div class="chapeau">
								<?php if (isset($options['footer_block_content_top']) ) : ?>

									<?php echo $options['footer_block_content_top']; ?>

								<?php endif; ?>

								</div>
								<div class="descriptif">
								<?php if (isset($options['footer_block_content_bottom']) ) : ?>

									<?php echo $options['footer_block_content_bottom']; ?>

								<?php endif; ?>
								</div>
							</div>
							<div class="block-right">
								<ul>
									<li class="application">
										<h4><?php  _e('Le cridon dans ma poche'); ?></h4>
										<a href="#"><span><?php _e('Découvrir notre application'); ?></span></a>
									</li>
									<li class="veille">
										<h4><?php  _e('Veille juridique personnalisée'); ?></h4>
										<a href="#"><span><?php _e('S\'abonner à votre veille'); ?></span></a>	
									</li>
									<li class="flash">
										<h4><?php _e('Flash info en exclusivité'); ?></h4>
										<a href="#"><span><?php _e('S\'inscrire à votre newsletter'); ?></span></a>
									</li>
								</ul>
							</div>
						</div>	
					</div>
				</div>
				
				<div class="footer-bottom">
					<div id="inner-footer" class="wrap cf">

						<p class="source-org copyright"> <b>Cridon</b> <span>Lyon</span> copyright &copy; <?php echo date('Y'); ?></p>

						<nav role="navigation">

							<?php nav_pied_de_page(); ?>
							
						</nav>

						
						<?php
						/* Lien JETPULP à générer avec l'outil interne :
						 * http://wpplayground.jetpulp.fr/link_generator
						 * Le lien doit uniquement être actif sur la page d'accueil.
						 * Sur toutes les autres pages, c'est un span.
						 */ ?>
						<?php if( is_front_page() ) : ?> 
							<a href="http://www.jetpulp.fr" class="class2 logoJP" title="Jetpulp" target="_blank">Jetpulp</a>
						<?php else : ?>
							<span class="class2 logoJP" title="Jetpulp">Jetpulp</span>
						<?php endif; ?>
						<a class="poser-question layer-posez-question_open" href="#">
						<?php _e('Posez une question'); ?>
					</a>

					</div>
				</div>
				

			</footer>

		</div>
            <div class="ajax-loader-wrapper js-utils-animation-ajax">
                <div class="ajax-loader"></div>
            </div>
		
		<!--[if lte IE 9]>
			<?php echo get_template_part("content","oldbrowser"); ?>
		<![endif]-->
		
		<?php wp_footer(); ?>
		
	</body>

</html>
