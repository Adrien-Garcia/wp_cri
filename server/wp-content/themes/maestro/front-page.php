<?php
/*
 Template Name: Accueil
*/
?>

<?php get_header(); ?>

   	<div id="content">

   		<div class="row_01">
   			<div id="inner-content" class="wrap cf">
				
				<div class="falsh-info">
					<div class="titre">
						<?php echo get_field('Flash info'); ?>
					</div>
					<div class="content">
						<div class="texte">Surendettement des particuliers – Protection des consommateurs – Effacement de la créance...</div>
						<a href=""><?php echo get_field('Lire'); ?></a>						
					</div>
				</div>

				<div class="3_block">
					<div class="block consulter">
						<div class="content">
							<h2>
								<?php echo get_field('Consulter'); ?>
								<span><?php echo get_field('un expert juridique'); ?></span>
							</h2>
						</div>						
					</div>

					<div class="block rechercher">
						<div class="content">
							<h2>
								<?php echo get_field('Rechercher'); ?>
								<span><?php echo get_field('dans les bases de connaissances'); ?></span>
							</h2>
						</div>						
					</div>

					<div class="block acceder">
						<div class="content">
							<h2>
								<?php echo get_field('Accéder'); ?>
								<span><?php echo get_field('à ma veille juridique'); ?></span>
							</h2>
						</div>						
					</div>
				</div>
   			</div>
   		</div>

   		<div class="row_02">
   			<div id="inner-content" class="wrap cf">

   				<div id="onglets">
   					<h3 class="juridique"><?php echo get_field('Veille juridique'); ?></h3>   				
   					<h3 class="formations"><?php echo get_field('Formations'); ?></h3>
   				</div>
   				<div class="details">
   					<div id="accordion-juridique">

   						<div class="panel">
					      <div class="date">
					      	<span class="jour">10</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 
					      </div>
					      <div class="content">
							<ul>
								<li>
									<img src="" alt="" />
									<h4>Droit Social</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<img src="" alt="" />
									<h4>Droit Social</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<img src="" alt="" />
									<h4>Droit Social</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
							</ul>
					       
					      </div>
					    </div>
					    <div class="panel">
					      <div class="date">
					      	<span class="jour">4</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 
					      </div>
					      <div class="content">
							<ul>
								<li>
									<h4>Droit Social 2</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<h4>Droit Social 2</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<h4>Droit Social 2</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
							</ul>
					       
					      </div>
					    </div>
					    <div class="panel">
					      <div class="date">
					      	<span class="jour">31</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 
					      </div>
					      <div class="content">
							<ul>
								<li>
									<h4>Droit Social 3</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<h4>Droit Social 3</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
								<li>
									<h4>Droit Social 3</h4>
									<div class="chapeau-categorie"></div>
									<a href=""><?php echo get_field('Lire'); ?></a>
								</li>
							</ul>					       
					      </div>
					    </div>

					    <a href="#" title=""><?php echo get_field('Toute la veille juridique'); ?></a>
   						
   					</div>


   					<div id="accordion-formations">

   						Lorem ipsum
   						
   					</div>
   				</div>
   			</div>
		</div>


		<div class="row_03">
			<div id="inner-content" class="wrap cf">
				<div class="cridon-app">
					<div class="content">
						<img src="" alt="" class="main">
						<h2><?php echo get_field('Le cridon dans ma poche'); ?> </h2>
						<a href="#" title=""><?php echo get_field('Découvrir notre application !'); ?> </a>
						<img src="" alt="" class="appli">
					</div>
				</div>
				<div class="veille-juridique">
					<div class="content">
						<h2><?php echo get_field('Veille juridique personnalisée'); ?> </h2>
						<a href="" title=""><?php echo get_field('S\'abonner à votre veille !'); ?> </a>
						<img src="" alt="" class="appli">
					</div>
				</div>
				<div class="info-flash-exclu">
					<div class="content">
						<h2><?php echo get_field('Info flash en exclusivité'); ?> </h2>
						<a href="#" title=""><?php echo get_field('S\'inscrire à votre newsletter'); ?> </a>						
					</div>
				</div>
			
			</div>
		</div>

		<div class="row_03">
			<div id="inner-content" class="wrap cf">
				<h2>
					<?php echo get_field('La vie'); ?>
					<span><?php echo get_field('du cridon'); ?> </span>
				</h2>
				<div class="actualite">
					<img src="" alt="">
					<div class="date">
						<span class="jour">1</span>
						<span class="mois">sept</span>
						<span class="annee">2015</span>
					</div>
					<div class="content">
						<h3>LE CRIDON</h3>
						<div class="chapeau">accueil la Chambre interdépartementale des Notaires de Savoie et de Haute-Savoie.</div>
						<div class="description">
							Untur am esto tem. Dolorit ipicien isitata in por aliquisqui sitatem porio. Os qui officitent vidit apiderrores venim sim quae <s>pa nus restrumenis</s> eataspe nihitati aceaquiatio eum eate dia secto exeriberit ut volora il idit alit, andae rat....
						</div>
						<a href="#" title=""><?php echo get_field('Lire'); ?></a>
					</div>
				</div>
				<a href="" title=""><?php echo get_field('Toute la vie du CRIDON'); ?></a>
			</div>
		</div>	

		<div class="row_03">
			<div id="inner-content" class="wrap cf">
				<ul id="">
					<li class="formations">
						<?php echo get_field('Le catalogue formations'); ?>
						<a href="#" title=""><?php echo get_field('Consulter'); ?></a>
					</li>
					<li class="cahier">
						<?php echo get_field('Les cahiers du cridon'); ?>
						<a href="#" title=""><?php echo get_field('Consulter'); ?></a>
					</li>
					<li class="services">
						<?php echo get_field('Les services plus'); ?>
						<a href="#" title=""><?php echo get_field('Consulter'); ?></a>
					</li>
				</ul>
			</div>
		</div>


	
		<div id="inner-content" class="wrap cf">

			<div id="main" class="cf" role="main">

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<div id="home_content">

						<?php if ($options['slider'] != "none" && $options['slider'] == "normal") :
                        
                            //Normal Slider
                        	get_template_part("content","slides");
                        	
                        endif; ?>

					</div>

				<?php endwhile; endif; ?>

			</div>

		</div>

	</div>

<?php get_footer(); ?>
