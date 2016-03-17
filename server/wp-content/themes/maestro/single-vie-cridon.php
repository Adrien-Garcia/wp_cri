<?php get_header(); ?>

	<div id="content" class="single single-vie-cridon">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<?php // $vars = get_defined_vars(); var_dump($object); ?>

			<div id="main" class="cf" role="main">
				<div id="inner-content" class="wrap cf">
			
				

				<div class="titre">
					<span class="h1"><?php _e('Vie du CRIDON'); ?></span>
				</div>

				<div class="sep"></div>

				<?php // if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php set_query_var( 'object', $object ); ?>
					
					<?php echo get_template_part("content","post-details"); ?>



					<a href="<?php echo MvcRouter::public_url(array('controller' => 'vie_cridons', 'action'     => 'index')) ?>"><?php _e('Retour'); ?></a>

				<?php // endwhile; ?>

				

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
