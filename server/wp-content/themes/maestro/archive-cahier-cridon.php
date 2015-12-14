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
                <?php
                $current_date = null;
                foreach ($objects as $key => $object) :
                ?>
                <?php criWpPost($object); ?>
                <?php var_dump($object) ?>
                <?php //var_dump($object->secondaires) ?>

				<div class="listing object" id="sel-object">
					

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
                        <?php
                        if( $current_date != get_the_date('d-M-Y')) :
                        $current_date = get_the_date('d-M-Y');
                        ?>
                        <div class="date-object sel-object-date">
                            <div class="sep"></div>
                            <span class="jour"><?php echo get_the_date( 'd') ?></span>
                            <span class="mois"><?php echo get_the_date( 'M') ?></span>
                            <span class="annee"><?php echo get_the_date( 'Y') ?></span>
                        </div>
                        <?php endif; ?>

						
						<div class="details">							
							<div class="block_right sel-object-content js-home-block-link" >
								<h2><?php the_title() ?></h2>
								<a href="" title="télécharger le document pdf">Télécharger le sommaire</a>
								<ul>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere"><?php echo $object->matiere->label ?></div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere"><?php echo $object->matiere->label ?></div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
									<li>
										<div class="img-cat">
											<img class="" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
										</div>
										<div class="matiere"><?php echo $object->matiere->label ?></div>
										<h3>Titre</h3>
										<a href="" title="Télécharger le document pdf"></a>
									</li>
								</ul>
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

		
	</div>

<?php get_footer(); ?>
