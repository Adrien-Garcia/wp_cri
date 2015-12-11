<?php get_header(); ?>

	<div id="content" class="archive archive-cahier">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Les cahiers du CRIDON</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Les Cahiers du CRIDON</h1>

				<div id="filtres_veilles">					
				</div>

				<div class="listing veille" id="sel-veilles">
					

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						
							<div class="date-veille sel-veilles-date">
								<div class="sep"></div>
								<span class="jour">21</span>
						      	<span class="mois">oct</span>
						      	<span class="annee">2016</span> 				
							</div>
						
						<div class="details">							
							<div class="block_right sel-veilles-content js-home-block-link" >								
								<h2><?php the_title() ?></h2>
								<a href="" title="télécharger le document pdf">Télécharger le sommaire</a>
								<ul>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
								</ul>
							</div>
						</div>
						
					</article>	

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						
							<div class="date-veille sel-veilles-date">
								<div class="sep"></div>
								<span class="jour">21</span>
						      	<span class="mois">oct</span>
						      	<span class="annee">2016</span> 				
							</div>
						
						<div class="details">							
							<div class="block_right sel-veilles-content js-home-block-link" >								
								<h2><?php the_title() ?></h2>
								<a href="" title="télécharger le document pdf">Télécharger le sommaire</a>
								<ul>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere">Matière</div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
								</ul>
							</div>
						</div>
						
					</article>

									
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
