<?php get_header(); ?>

	<div id="content">

		<div id="inner-content" class="wrap cf">

			<div id="main" class="cf" role="main">
			
				<?php if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

					 	<header class="article-header">

					    	<h1 class="entry-title single-title"><?php the_title(); ?></h1>

					  	</header>

					  	<section class="entry-content cf">

					  		<?php if( in_category(6) ) : ?>

					  			<?php
								/* 
								Affichage de la date uniquement sur les liste d'actualités
								On utilise the_time sur les listes
								Voir http://codex.wordpress.org/Formatting_Date_and_Time pour les formats
								*/
								?>
								<time><?php the_date("d/m/Y"); ?></time>

							<?php endif; ?>

					    	<?php the_content(); ?>

					  	</section>

					</article>

				<?php endwhile; ?>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="article-header">

							<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>

						</header>

						<section class="entry-content">

							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>

						</section>

						<footer class="article-footer">

							<p><?php _e( 'This is the error message in the single.php template.', 'bonestheme' ); ?></p>

						</footer>

					</article>

				<?php endif; ?>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
