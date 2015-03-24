			<footer class="footer" role="contentinfo">

				<div id="inner-footer" class="wrap cf">

					<nav role="navigation">

						<?php nav_pied_de_page(); ?>
						
					</nav>

					<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.
					<?php
					/* Lien JETPULP à générer avec l'outil interne :
					 * http://wpplayground.jetpulp.fr/link_generator
					 * Le lien doit uniquement être actif sur la page d'accueil.
					 * Sur toutes les autres pages, c'est un span.
					 */ ?>
					<?php if( is_front_page() ) : ?> 
						
					<?php else : ?>
						
					<?php endif; ?>
					</p>

				</div>

			</footer>

		</div>
		
		<!--[if lte IE 9]>
			<?php echo get_template_part("content","oldbrowser"); ?>
		<![endif]-->
		
		<?php wp_footer(); ?>
		
	</body>

</html>
