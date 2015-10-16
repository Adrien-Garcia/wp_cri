			<footer class="footer" role="contentinfo">

				<div class="footer-top">
					
					<div class="block-links">
						<div id="inner-top-footer" class="wrap cf">
							<ul id="">
								<li class="formations">
									<?php _e('Le catalogue formations'); ?>
									<a href="#" title=""><?php _e('Consulter'); ?></a>
								</li>
								<li class="cahier">
									<?php _e('Les cahiers du cridon'); ?>
									<a href="#" title=""><?php _e('Consulter'); ?></a>
								</li>
								<li class="services">
									<?php _e('Les services plus'); ?>
									<a href="#" title=""><?php _e('Consulter'); ?></a>
								</li>
							</ul>
						</div>
					</div>
					<div class="footer-cridon">
						<div id="inner-footer" class="wrap cf">


							<div class="block-left">
								<div class="chapeau">
									<span>Une équipe de spécialistes au service des notaires.</span>
									<p>Le CRIDON GRAND EST accompagne depuis un demi-siècle le notariat dans sa démarche de sécurité juridique dans l'intérêt des citoyens.
									</p>
								</div>
								<div class="descriptif">
									Ut dolupta tincta solor mosapidi omnis quiatur ehendesti aboribu sandebis voloreh eniscidunt et et re etur, enihil illaudae adigent.
									Ri ni is doluptatur? Faceatis sendictem volupit, im et poreris doles im quas eate eaquat qui ium qui delenectium invelis imeneceptas volenditis acescid modi santio totassi tiature pelendistior a derspidem sit autasi cor sum verum qui voluptaera vid quassunt estius, sapiciatem volut ut remodit ionsequis volentiande.
								</div>
							</div>
						</div>
						<div class="block-right">
							<ul>
								<li class="application">
									<h4>
										<?php echo get_field('Le cridon dans ma poche'); ?>
										<a href="#"><?php echo get_field('Découvrir notre application'); ?></a>
									</h4>
								</li>
								<li class="veille">
									<h4>
										<?php echo get_field('Veille juridique personnalisée'); ?>
										<a href="#"><?php echo get_field('S\'abonner à votre veille'); ?></a>
									</h4>
								</li>
								<li class="flash">
									<h4>
										<?php echo get_field('Flash info en exclusivité'); ?>
										<a href="#"><?php echo get_field('S\'inscrire à votre newsletter'); ?></a>
									</h4>
								</li>
							</ul>
						</div>
					</div>	
				</div>
				
				<div class="footer-bottom">
					<div id="inner-footer" class="wrap cf">

						<p class="source-org copyright"> <b>Cridon</b> <span>Grand Est</span> copyright &copy; <?php echo date('Y'); ?></p>

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
							<span class="class2 logoJP" title="Jetpulp"></span>
						<?php endif; ?>

					</div>
				</div>

			</footer>

		</div>
		
		<!--[if lte IE 9]>
			<?php echo get_template_part("content","oldbrowser"); ?>
		<![endif]-->
		
		<?php wp_footer(); ?>
		
	</body>

</html>
