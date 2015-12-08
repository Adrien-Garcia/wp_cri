<?php get_header(); ?>

	<div id="content" class="archive archive-veilles">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Veille juridique</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Veille juridique</h1>

				<div id="filtres_veilles">					
				</div>

				<div class="listing veille" id="sel-veilles">						
				<?php $current_date = null; ?>

				<?php
                foreach ($objects as $key => $veille) :
				 ?>

                    <?php criWpPost($veille); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<?php 
							if( $current_date != get_the_date('d-M-Y')) :
								$current_date = get_the_date('d-M-Y');
						 ?>
							<div class="date-veille sel-veilles-date">
								<div class="sep"></div>
								<span class="jour"><?php echo get_the_date( 'd') ?></span>
						      	<span class="mois"><?php echo get_the_date( 'M') ?></span>
						      	<span class="annee"><?php echo get_the_date( 'Y') ?></span> 				
							</div>
						<?php endif; ?>
						<div class="details">
							<div class="block_left">
								<div class="img-cat">
								<?php 
									$matiere = get_the_matiere();
								 ?>
									<img class="sel-veilles-picto" src="<?php echo $veille->matiere->picto ?>" alt="<?php echo $veille->matiere->label ?>" />
								</div>
							</div>
							<div class="block_right sel-veilles-content js-home-block-link" >
							<?php //var_dump($this) ?>
								<div class="matiere"><?php echo $veille->matiere->label ?></div>
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

                    <?php endforeach; ?>
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
