<?php
/*
 Template Name: Accueil
*/
?>

<?php get_header(); ?>

   	<div id="content">

   		<div class="row_01">
   			<div id="inner-content" class="wrap cf">
				
				<div class="falsh-info js-flash-info">
					<div class="titre">
						<?php _e('Flash info'); ?>

						<span class="close js-flash-close">+</span>
						<span class="open js-flash-open">></span>
					</div>
					<?php 
						$flash = criGetLastestPost('flash');
						criWpPost($flash);
					 ?>
					<?php if ($flash != null): ?>
						<?php 
							$_flash_excerpt = get_the_excerpt();
							$_flash_url = get_permalink();
						 ?>
					<div class="content" id="sel-flash-present">
						<div class="texte"><?php echo $_flash_excerpt; ?></div>
						<a href="<?php echo $_flash_url; ?>"><?php _e('Lire'); ?></a>
					</div>
					<?php endif; ?>
					<?php wp_reset_query(); ?>
				</div>

				<div class="block_03">
					<div class="block consulter js-home-block-link">
						<div class="content">
							<h2>
								<?php _e('Consulter'); ?>
								<span><?php _e('un expert juridique'); ?></span>
							</h2>
							<a href="#">+</a>
						</div>						
					</div>

					<div class="block rechercher js-home-block-link">
						<div class="content">
							<h2>
								<?php _e('Rechercher'); ?>
								<span><?php _e('dans les bases de connaissances'); ?></span>
							</h2>
							<a href="">+</a>
						</div>						
					</div>

					<div class="block acceder js-home-block-link">
						<div class="content">
							<h2>
								<?php _e('Accéder'); ?>
								<span><?php _e('à ma veille juridique'); ?></span>
							</h2>
							<a href="#">+</a>
						</div>						
					</div>
				</div>
   			</div>
   		</div>

   		<div class="row_02">
   			<div id="inner-content" class="wrap cf">

   				<div id="onglets">
   					<h3 class="juridique open js-tab-veille-open"><?php _e('Veille juridique'); ?></h3>   				
   					<h3 class="formations js-tab-formation-open"><?php _e('Formations'); ?></h3>
   				</div>
   				<div class="details">
   					<div id="accordion-juridique" class="accordion js-tab-veille open">
						<?php 
							setlocale(LC_ALL, 'fr_FR');
							$veilles = criFilterByDate('veille',3,3,'veille', 'd/m/Y');
							// var_dump($veilles);
						 ?>
						<?php foreach ($veilles as $keyd => $date): ?>
						<?php 
							if( preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date['date'], $matches) ){
								$_date = date_create_from_format('d/m/Y', $date['date']);
							}
						?>
						<?php // var_dump($_date) ?>
   						<div class="panel js-accordion-content <?php if($keyd > 0): ?> closed <?php endif; ?>">
					      <div class="date js-accordion-button ">
					      	<span class="jour"><?php echo date_format($_date, 'd') ?></span>
					      	<span class="mois"><?php echo date_format($_date, 'M') ?></span>
					      	<span class="annee"><?php echo date_format($_date, 'Y') ?></span> 
					      </div>
					      <div class="content">
							<ul>
								<?php foreach ($date['veille'] as $keyv => $veille) : ?>
									<?php 
										criWpPost($veille);
										// $_matiere = $veille->getMatiere() != null ? $veille->getMatiere() : 'Expertise générale';
										$_matiere = false != false ? false : __('Expertise générale');
										$_chapo = get_the_excerpt();//$veille->excerpt;
										$_link = get_permalink(); //$veille->link;

										// var_dump($keyd)
									 ?>
								<li >
									<img src="" alt="" />
									<h4><?php echo $_matiere; ?></h4>
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

					    <a href="#" title=""><span><?php _e('Toute la veille juridique'); ?></span></a>
   						
   					</div>


   					<div id="accordion-formations" class="accordion js-tab-formation">

   						<?php 
							$formations = criFilterByDate('formation',3,3,'formation', 'd/m/Y');
							// var_dump($formations);
						 ?>
						<?php foreach ($formations as $keyd => $date): ?>
						<?php 
							if( preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date['date'], $matches) ){
								$_date = date_create_from_format('d/m/Y', $date['date']);
							}
						?>
						<?php // var_dump($_date) ?>
   						<div class="panel js-accordion-content">
					      <div class="date js-accordion-button">
					      	<span class="jour"><?php echo date_format($_date, 'd') ?></span>
					      	<span class="mois"><?php echo date_format($_date, 'M') ?></span>
					      	<span class="annee"><?php echo date_format($_date, 'YY') ?></span> 
					      </div>
					      <div class="content">
							<ul>
								<?php foreach ($date['formation'] as $keyv => $formation) : ?>
									<?php 
										// $_matiere = $formation->getMatiere() != null ? $formation->getMatiere() : 'Expertise générale';
										$_matiere = false != false ? false : 'Expertise générale';
										$_chapo = $formation->excerpt;
										$_link = $formation->link;

										// var_dump($formation)
									 ?>
								<li>
									<img src="" alt="" />
									<h4><?php echo $_matiere; ?></h4>
									<div class="chapeau-categorie"><?php echo $_chapo ?></div>
									<a href="<?php echo $_link ?>"><?php _e('Lire'); ?></a>
								</li>
								<?php endforeach; ?>

							</ul>
					       
					      </div>
					    </div>
					    <?php endforeach ?>
					    <div class="blockEnd"></div>

					    <a href="#" title=""><span><?php _e('Toute la veille juridique'); ?></span></a>
   						
   					</div>
   				</div>
   			</div>
		</div>


		<div class="row_03">
			<div id="inner-content" class="wrap cf">
				<div class="cridon-app js-home-block-link">
					<div class="content">
						<div src="" alt="" class="img-main"></div>
						<h2><?php _e('Le cridon dans ma poche'); ?> </h2>
						<a href="#" title=""><span><?php _e('Découvrir notre application !'); ?></span></a>
						<div src="" alt="" class="img-appli" ></div>
					</div>
				</div>
				<div class="veille-juridique js-home-block-link">
					<div class="content">
						<h2><?php _e('Veille juridique personnalisée'); ?> </h2>
						<a href="" title=""><span><?php _e('S\'abonner à votre veille !'); ?></span></a>
						<img src="" alt="" class="appli" />
					</div>
				</div>
				<div class="info-flash-exclu js-home-block-link">
					<div class="content">
						<h2><?php _e('Info flash en exclusivité'); ?> </h2>
						<a href="#" title=""><span><?php _e('S\'inscrire à votre newsletter'); ?></span> </a>						
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

				<div class="actualite">
					<img src="" alt="" />
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
						<a href="#" title=""><?php _e('Lire'); ?></a>
					</div>
				</div>

				<a href="#" title="Lorem" class="LienVieCridon" ><span> <?php _e('Toute la vie du CRIDON'); ?> </span></a>
			</div>
		</div>	

		


	
		

	</div>

<?php get_footer(); ?>
