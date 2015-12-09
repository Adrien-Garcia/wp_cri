<?php get_header(); ?>

	<div id="content" class="page page-contact">

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

						<h1 class="page-title h1" itemprop="headline">Contact</h1>

					</header> <?php // end article header ?>

					<section class="entry-content cf" itemprop="articleBody">

						<p>L'équipe du CRIDON LYON est à votre disposition pour répondre à vos questions</p>
						<img src="<?php echo get_template_directory_uri(); ?>/library/images/le-cridon-contact.jpg" alt="" />

						<?php // the_content(); ?>

					</section> <?php // end article section ?>

					<footer class="article-footer cf">

						<div class="form">
							<h2>Nous envoyer un message</h2>
							<?php echo do_shortcode('[gravityform id=1 name=ContactUs title=false description=false]'); ?>
						</div>
						<div class="coordonnees">
							<h2>Nous joindre</h2>
							<table>
								<tr>
									<td>
										<?php _e('Consultation téléphonique'); ?>
										<span><?php _e('de 14h00 à 17h30 de Lundi au Vendredi'); ?></span>
									</td>
									<td>04 37 24 79 24</td>
								</tr>
								<tr>
									<td>
										<?php _e('Question administrative'); ?>
										<span><?php _e('N° vert'); ?></span>
									</td>
									<td>0 800 008 206</td>
								</tr>
								<tr>
									<td>
										<?php _e('Autres demandes'); ?>
										<span><?php _e('tout pubic'); ?></span>
									</td>
									<td>04 37 24 79 00</td>
								</tr>
								<tr>
									<td>
										<?php _e('Fax'); ?>
									</td>
									<td>04 37 24 79 71</td>
								</tr>
								<tr>
									<td>
										CRIDON LYON
										<span>37 Bd des Brotteaux<br />
										69455 LYON CEDEX 06</span>
									</td>
									<td><a href=""><?php _e('Localisez vous !'); ?></a></td>
								</tr>
							</table>
							<?php echo do_shortcode('[mappress mapid="1" width="100%" height="322"]'); ?>
						</div>

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
