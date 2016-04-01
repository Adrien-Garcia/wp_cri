<?php
/*
 Template Name: Accueil
*/
?>

<?php get_header(); ?>

   	<div id="content">

   		<div class="row_01" id="sel-front-page">
   			<div id="inner-content" class="wrap cf">
				
				<div class="falsh-info js-flash-info">
					<div class="titre">
						<?php _e('Flash info'); ?>

						<span class="close js-flash-close">+</span>
						<span class="open js-flash-open">></span>
					</div>
					<?php 
						$flash = criGetLatestPost('flash');
						criWpPost($flash);
					 ?>
					<?php if ($flash != null): ?>
						<?php 
							$_flash_title = get_the_title();
							$_flash_excerpt = get_the_excerpt();
							$_flash_url = get_permalink();
						 ?>
					<div class="content" id="sel-flash-present">
						<div class="texte">
							<?php //echo $_flash_title; ?>
							<?php echo truncate($_flash_title,110, '...'); ?>
							<a id="sel-flash-link-present" href="<?php echo $_flash_url; ?>"><?php _e('Lire'); ?></a>
						</div>
						
					</div>
					<?php endif; ?>
					<?php wp_reset_query(); ?>
				</div>

				

				<?php get_template_part("content","3-block-home"); ?>
				<div class="partenaires">
					<div class="">Partenaires exclusifs du <span>CRIDON</span> LYON</div>
					<ul>
						<li>
							<img src='/wp-content/themes/maestro/library/images/origin/logo-lexbase-2.png' alt='lexbase'>
						</li>
						<li>
							<img src='/wp-content/themes/maestro/library/images/origin/logo-woltersKluwer-2.png' alt='Wolters Kluwer'>
						</li>
					</ul>
				</div>
				
   			</div>
   		</div>



   		<div class="row_02">
   			<div id="inner-content" class="wrap cf">

   				<div id="onglets">
   					<h3 class="juridique open js-tab-veille-open"><span><?php _e('Veille juridique'); ?></span></h3>   				
   					<h3 class="formations js-tab-formation-open"><span><?php _e('Formations'); ?></span></h3>
   				</div>
   				<div class="details">
   					<div id="accordion-juridique" class="accordion js-tab-veille open">
						<?php
							$veilles = criFilterByDate('veille',3,3,'veille', 'Y-m-d');
							// var_dump($veilles);
						 ?>
						<?php foreach ($veilles as $keyd => $date): ?>
						<?php 
                            $current_date = $date['date'];
						?>
						<?php // var_dump($_date) ?>
   						<div class="panel js-accordion-content <?php if($keyd > 0): ?> closed <?php endif; ?> sel-juridique-panel">
					      <div class="date js-accordion-button ">
					      	<span class="jour"><?php echo strftime('%d',strtotime($current_date)) ?></span>
					      	<span class="mois"><?php echo mb_substr(strftime('%b',strtotime($current_date)),0,3) ?></span>
					      	<span class="annee"><?php echo strftime('%Y',strtotime($current_date)) ?></span>
					      </div>
					      <div class="content">
							<ul>
								<?php foreach ($date['veille'] as $keyv => $veille) : ?>
									<?php 
										criWpPost($veille);
										$_chapo = get_the_excerpt();//$veille->excerpt;
										$_link = get_permalink(); //$veille->link;

									 ?>
								<li class="js-home-block-link">
									<img src="<?php echo $veille->matiere->picto ?>" alt="<?php echo $veille->matiere->label ?>" />
									<h4><?php echo $veille->matiere->label ?></h4>
									<div class="chapeau-categorie"><?php echo $_chapo ?></div>
									<a href="<?php echo $_link; ?>"><?php _e('Lire'); ?></a>
								</li>
								<?php endforeach; ?>

							</ul>
					       
					      </div>
					    </div>
					    <?php endforeach; ?>
					    <?php wp_reset_query(); ?>
					    <div class="blockEnd"></div>

					    <a href="<?php echo MvcRouter::public_url(array('controller' => 'veilles', 'action'     => 'index')) ?>" id="sel-hp-veilles-link" title=""><span><?php _e('Toute la veille juridique'); ?></span></a>
   						
   					</div>


   					<div id="accordion-formations" class="accordion js-tab-formation">

   						<?php 
							$formations = criFilterByDate('formation',3,1,'formation', 'Y-m-d');
							// var_dump($formations);
						 ?>
						<?php foreach ($formations as $keyd => $date): ?>
						<?php 
                            $current_date = $date['date'];
						?>
						<?php // var_dump($_date) ?>
   						<div class="panel js-accordion-content <?php if($keyd > 0): ?> closed <?php endif; ?> sel-formation-panel">
					      <div class="date js-accordion-button">
                              <span class="jour"><?php echo strftime('%d',strtotime($current_date)) ?></span>
                              <span class="mois"><?php echo mb_substr(strftime('%b',strtotime($current_date)),0,3) ?></span>
                              <span class="annee"><?php echo strftime('%Y',strtotime($current_date)) ?></span>
					      </div>
					      <div class="content">
							<ul>
								<?php foreach ($date['formation'] as $keyv => $formation) : ?>
									<?php 
										criWpPost($formation);

										$_title = get_the_title();
										$_chapo = get_the_excerpt();//$veille->excerpt;
										$_link = get_permalink(); //$veille->link;
										// var_dump($formation)
									 ?>

								<li class="js-home-block-link">
									<img src="<?php echo $formation->matiere->picto ?>" alt="<?php echo $formation->matiere->label ?>" />
									<h4><?php echo $_title; ?></h4>
									<div class="chapeau-categorie"><?php echo $_chapo ?></div>
									<div class="adresse">
                                        <?php echo nl2br($formation->formation->address) ?><br />
                                        <?php echo $formation->formation->postal_code ?>
                                        <?php echo $formation->formation->town ?>
									</div>
									<a href="<?php echo $_link ?>"><?php _e('Lire'); ?></a>
								</li>
								<?php endforeach; ?>

							</ul>
					       
					      </div>
					    </div>
					    <?php endforeach ?>
					    <?php wp_reset_query(); ?>

					    <div class="blockEnd"></div>

					    <a href="<?php echo MvcRouter::public_url(array('controller' => 'formations', 'action'     => 'index')) ?>" title=""><span><?php _e('Toutes les formations'); ?></span></a>
   						
   					</div>
   				</div>
   			</div>
		</div>


		<div class="row_03">
			<div id="inner-content" class="wrap cf">

				<div class="cridonline">
					<ul>
						<li>Lorem ipsum dolor sit amet, consectetur</li>
						<li>adipisicing elit, sed do eiusmod</li>
						<li>didunt ut labore et dolore magna</li>
						<li>atem accusantium doloremque laudantium</li>
						<li>totam rem aperiam, eaque</li>
					</ul>
					<a href="#" title="Découvrir nos offres crid'online" class="link1"><span><?php _e('Découvrir nos offres !'); ?></span></a>

					<a title="Découvrir nos offres crid'online" class="link2 <?php if(!CriIsNotaire()) : ?>js-panel-connexion-open<?php endif; ?>" 
						<?php if(CriIsNotaire()) : ?> href="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'cridonline')); ?>" 
						<?php else : ?> href="#" data-login-message="PROTECTED_CONTENT" <?php endif; ?> 
						>
						<span><?php _e('Souscrire à nos offres !'); ?></span>
					</a>
				</div>


				
				<div class="veille-juridique <?php if(!CriIsNotaire()) : ?> js-panel-connexion-open <?php else: ?> js-home-block-link <?php endif; ?>" <?php if(!CriIsNotaire()) : ?> data-login-message="ERROR_NEWSLETTER_NOT_CONNECTED" <?php endif; ?> >
					<div class="content">
						<h2><?php _e('Veille juridique personnalisée'); ?> </h2>

						<a <?php if(CriIsNotaire()) : ?> href="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'profil')); ?>" <?php else : ?> href="#" data-login-message="ERROR_NEWSLETTER_NOT_CONNECTED" <?php endif; ?> >
							<span><?php _e('S\'abonner à votre veille !'); ?></span>
						</a>

						<img src="" alt="" class="appli" />
					</div>
				</div>
				<div class="info-flash-exclu <?php if(!CriIsNotaire()) : ?> js-panel-connexion-open <?php else: ?> js-home-block-link <?php endif; ?>" <?php if(!CriIsNotaire()) : ?> data-login-message="PROTECTED_CONTENT" <?php endif; ?>>
					<div class="content">
						<h2><?php _e('Info flash en exclusivité'); ?> </h2>
						<a <?php if(CriIsNotaire()) : ?> href="<?php echo MvcRouter::public_url(array('controller' => 'flashes', 'action'     => 'index')) ?>" <?php else : ?> href="#" data-login-message="ERROR_NEWSLETTER_NOT_CONNECTED" <?php endif; ?> >
							<span><?php _e('Consulter les flash infos'); ?></span>
						</a>						
					</div>
				</div>
				<div class="cridon-app js-home-block-link">
					<div class="content">						
						<h2><?php _e('Le cridon dans ma poche'); ?> </h2>
						<a href="/le-cridon-dans-ma-poche/" title=""><span><?php _e('Découvrir notre application !'); ?></span></a>
						<div class="img-appli" ></div>
					</div>
				</div>
			
			</div>

		</div>

		<div class="row_04">
			<div id="inner-content" class="wrap cf">
				
				<h2>
					<?php _e('La vie'); ?>
					<span><?php _e('du CRIDON'); ?> </span>
				</h2>
				<?php 
				$vie = criGetLatestPost('vie_cridon');
				//var_dump($vie);
			 ?>

			 <?php // $vars = get_defined_vars(); 
//$nomvar = array_keys($vars);
			 

			 	
			  ?>

			<?php if( $vie != null):?> 
				<?php criWpPost($vie); //var_dump($post); ?>
                <?php /** @var WP_Post $post */
                $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

				//var_dump($thumbnail_src);  ?>
				<div class="actualite" id="sel-actu-cridon-home">
					<?php // the_post_thumbnail(array(480, 225)); ?>
					<div class="img" style="background-image: url(<?php echo  $thumbnail_src[0] ?>);"></div>
					<div class="date">
						<span class="jour"><?php echo get_the_date( 'd') ?></span>
						<span class="mois"><?php echo get_the_date( 'M') ?></span>
						<span class="annee"><?php echo get_the_date( 'Y') ?></span> 
					</div>

					<div class="content">
						<h3><?php the_title() ?></h3>
						<?php if (!empty($post->post_excerpt)): ?>
						<div class="chapeau"><?php echo get_the_excerpt(); ?></div>
						<?php endif ?>
						<div class="description">
							<?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 40, "..." ) ?>
						</div>
						<a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php _e('Lire'); ?></a>
					</div>
				</div>
		    	<?php wp_reset_query(); ?>
		    <?php endif; ?>

				<a href="<?php echo MvcRouter::public_url(array('controller' => 'vie_cridons', 'action'     => 'index')) ?>" title="Lorem" class="LienVieCridon" ><span> <?php _e('Toute la vie du CRIDON'); ?> </span></a>
			</div>
		</div>	


		

		


	
		

	</div>

<?php get_footer(); ?>
