<?php get_header(); ?>

	<div id="content">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<div id="inner-content" class="wrap cf">

			<div id="main" class="cf" role="main">
				
				<h1 class="archive-title">
					<?php single_cat_title(); ?>
				</h1>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

						<header class="article-header">

							<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<?php
							/* 
							Affichage de la date uniquement sur les liste d'actualités
							On utilise the_time sur les listes
							Voir http://codex.wordpress.org/Formatting_Date_and_Time pour les formats
							*/
							?>
							<time><?php the_time("d/m/Y"); ?></time>

						</header>

						<section class="entry-content cf">

							<?php the_post_thumbnail( 'bones-thumb-300' ); ?>

							<?php the_excerpt(); ?>

						</section>

						<footer class="article-footer">

						</footer>

					</article>

				<?php endwhile; ?>

				<?php wp_pagenavi(); ?>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="article-header">
							<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
						</header>

						<section class="entry-content">
							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
						</section>

						<footer class="article-footer">
								<p><?php _e( 'This is the error message in the archive.php template.', 'bonestheme' ); ?></p>
						</footer>

					</article>

				<?php endif; ?>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
