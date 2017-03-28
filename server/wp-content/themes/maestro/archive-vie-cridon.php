<?php get_header(); ?>

	<div id="content" class="archive archive-vie-cridon">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Vie du CRIDON LYON</h1>

				<div class="listing vie-cridon" id="sel-vie-cridon">
					<?php set_query_var( 'objects', $objects ); ?>
					
					<?php echo get_template_part("content","post-list-veilles"); ?>

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
