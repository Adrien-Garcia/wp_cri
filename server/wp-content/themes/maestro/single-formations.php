<?php get_header(); ?>

	<div id="content" class="single single-formations">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<?php // $vars = get_defined_vars(); var_dump($object); ?>

			<div id="main" class="cf" role="main">
				<div id="inner-content" class="wrap cf">



				<div class="titre">
					<span class="h1"><?php _e('Formation'); ?> - <?php echo $object->matiere->label ?></span>
				</div>

				<div class="sep"></div>

				<?php // if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php set_query_var( 'object', $object ); ?>
					<?php set_query_var( 'sessions', $sessions ); ?>

					<?php echo get_template_part("content","post-details-formation"); ?>



					<a href="<?php echo MvcRouter::public_url(array('controller' => 'formations', 'action'     => 'index')) ?>" class="bt liste">
						<?php _e('Retour à la liste des formations'); ?>
					</a>
					<a href="<?php echo MvcRouter::public_url(array('controller' => 'formations', 'action'     => 'calendar')) ?>" class="bt agenda"><?php _e('Consulter l\'agenda des formations'); ?></a>

				<?php // endwhile; ?>



			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
