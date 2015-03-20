			<footer class="footer" role="contentinfo">

				<div id="inner-footer" class="wrap cf">

					<nav role="navigation">

						<?php nav_pied_de_page(); ?>
						
					</nav>

					<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. <a class="lienjp">Créations site web</a></p>

				</div>

			</footer>

		</div>
		
		<!--[if lt IE 9]>
			<?php echo get_template_part("content","oldbrowser"); ?>
		<![endif]-->
		
		<?php wp_footer(); ?>
		
	</body>

</html>
