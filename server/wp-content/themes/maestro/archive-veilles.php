<?php get_header(); ?>

	<div id="content" class="archive-veilles">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Veille juridique</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1>Veille juridique</h1>

				<div id="filtres_veilles">					
				</div>

				<div class="listing veille">						
				<?php $current_date = null; ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<?php 
							if( $current_date != get_the_date('d-M-Y')) :
								$current_date = get_the_date('d-M-Y');
						 ?>
							<div class="date-veille">
								<div class="sep"></div>
								<span class="jour"><?php echo get_the_date( 'd') ?></span>
						      	<span class="mois"><?php echo get_the_date( 'M') ?></span>
						      	<span class="annee"><?php echo get_the_date( 'Y') ?></span> 				
							</div>
						<?php endif; ?>
						<div class="details">
							<div class="block_left">
								<div class="img-cat">
								<?php // @TODO Matiere picto ?>
									<img src="" alt="" />
								</div>
							</div>
							<div class="block_right">
							<?php //var_dump($this) ?>
								<div class="matiere">Droit social</div>
								<h2><?php the_title() ?></h2>
								<div class="chapeau">
									<?php echo get_the_excerpt() ?>
								</div>
								<div class="extrait">
									<?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 35, "..." ) ?>
								</div>
								<ul class="mots_cles">
								<?php 
									$tags = get_the_tags();
									if( $tags ) : foreach ($tags as $tag) :
								 ?>
									<li><?php echo $tag->name; ?></li>
								<?php endforeach; endif; ?>
								</ul>
								<a href="<?php the_permalink(); ?>" title="<?php the_title() ?>">Lire</a>
							</div>
						</div>
						
					</article>

				<?php endwhile; ?>

				<?php endif; ?>

				</div>

			</div>					

		</div>

		<?php // endwhile; ?>

		<?php // wp_pagenavi(); ?>

		

			

		<?php /*get_sidebar();*/ ?>

		
	</div>

<?php get_footer(); ?>
