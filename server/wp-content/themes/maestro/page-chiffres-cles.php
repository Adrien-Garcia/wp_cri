<?php
/*
 Template Name: Template chiffre clés
*/
?>

<?php get_header(); ?>

	<div id="content" class="page">

		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <span>Mon compte</span>
			</div>
		</div>

		<div id="inner-content" class="wrap cf">

			<div id="main" class="cf" role="main">	

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

					<header class="article-header">

						<h1 class="page-title h1" itemprop="headline"><?php the_title(); ?></h1>

					</header> <?php // end article header ?>

					<section class="entry-content cf" itemprop="articleBody">

						<?php // the_content(); ?>

						<h2>Le nombre des consultations traitées depuis l'origine</h2>
						&nbsp;
						<h2>Répartition par support pour l'année 2015</h2>
						&nbsp;
						<h2>Les matières pour l'année 2015</h2>

					</section> <?php // end article section ?>

					<footer class="article-footer cf">

					</footer>

				</article>

				<?php endwhile; else : ?>

					<article id="post-not-found" class="hentry cf">
						<header class="article-header">
							<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
						</header>
						<section class="entry-content">
							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
						</section>
						<footer class="article-footer">
								<p><?php _e( 'This is the error message in the page.php template.', 'bonestheme' ); ?></p>
						</footer>
					</article>

				<?php endif; ?>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
