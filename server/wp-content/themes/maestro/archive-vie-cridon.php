<?php get_header(); ?>

	<div id="content" class="archive archive-vie-cridon">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a>  +  <span>Vie du CRIDON</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Vie du CRIDON</h1>

				<div class="listing vie-cridon" id="sel-vie-cridon">
					<?php set_query_var( 'objects', $objects ); ?>
					
					<?php echo get_template_part("content","post-list"); ?>

					<div class="pagination">
						<?php echo $this->pagination(); ?>
					</div>
                </div>

			</div>					

		</div>

		<?php // endwhile; ?>

		<?php // wp_pagenavi(); ?>

		

			

		<?php /*get_sidebar();*/ ?>

		
	</div>

<?php get_footer(); ?>
