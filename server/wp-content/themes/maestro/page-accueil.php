<?php
/*
 Template Name: Accueil
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div id="inner-content" class="wrap cf">

			<div id="main" class="m-all t-2of3 d-5of7 cf" role="main">

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<div id="home_content">

						<?php /* Insertion contenu slider pour page d'accueil */ ?>
						<?php get_template_part("content","slides"); ?>

					</div>

				<?php endwhile; endif; ?>

			</div>

		</div>

	</div>

<?php get_footer(); ?>
