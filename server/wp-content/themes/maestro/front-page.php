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
							$_flash_excerpt = get_the_excerpt();
							$_flash_url = get_permalink();
						 ?>
					<div class="content" id="sel-flash-present">
						<div class="texte"><?php echo $_flash_excerpt; ?></div>
						<a id="sel-flash-link-present" href="<?php echo $_flash_url; ?>"><?php _e('Lire'); ?></a>
					</div>
					<?php endif; ?>
					<?php wp_reset_query(); ?>
				</div>

				

				<?php echo get_template_part("content","3-block-home"); ?>

				
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
   						<div class="panel js-accordion-content <?php if($keyd > 0): ?> closed <?php endif; ?> sel-juridique-panel">
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
							$formations = criFilterByDate('formation',3,1,'formation', 'd/m/Y');
							// var_dump($formations);
						 ?>
						<?php foreach ($formations as $keyd => $date): ?>
						<?php 
							if( preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $date['date'], $matches) ){
								$_date = date_create_from_format('d/m/Y', $date['date']);
							}
						?>
						<?php // var_dump($_date) ?>
   						<div class="panel js-accordion-content <?php if($keyd > 0): ?> closed <?php endif; ?> sel-formation-panel">
					      <div class="date js-accordion-button">
					      	<span class="jour"><?php echo date_format($_date, 'd') ?></span>
					      	<span class="mois"><?php echo date_format($_date, 'M') ?></span>
					      	<span class="annee"><?php echo date_format($_date, 'Y') ?></span> 
					      </div>
					      <div class="content">
							<ul>
								<?php foreach ($date['formation'] as $keyv => $formation) : ?>
									<?php 
										criWpPost($formation);

										// $_matiere = $formation->getMatiere() != null ? $formation->getMatiere() : 'Expertise générale';
										$_matiere = false != false ? false : 'Expertise générale';
										$_title = get_the_title();
										$_chapo = get_the_excerpt();//$veille->excerpt;
										$_link = get_permalink(); //$veille->link;
										// var_dump($formation)
									 ?>
								<li>
									<img src="" alt="" />
									<h4><?php echo $_title; ?></h4>
									<div class="chapeau-categorie"><?php echo $_chapo ?></div>
									<a href="<?php echo $_link ?>"><?php _e('Lire'); ?></a>
								</li>
								<?php endforeach; ?>

							</ul>
					       
					      </div>
					    </div>
					    <?php endforeach ?>
					    <?php wp_reset_query(); ?>

					    <div class="blockEnd"></div>

					    <a href="#" title=""><span><?php _e('Toutes les formations'); ?></span></a>
   						
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

			
			<div id="owl-support" class="owl-carousel">
			
	            <div class="item">
	            	<input id="support_<?php echo $support->id ?>" type="radio" name="support" value="<?php echo $support->id ?>" class="hidden js-question-support-radio" onchange="alert(this.id);">
	              	<span class="label"><?php echo $support->label_front; ?></span>
	              	<p class="description">
	              		<?php echo $support->description; ?>
	              	</p>
	            </div>

	            <div class="item">
	            	22222222222222222222222222222
	            </div>
	        	
	        	<div class="item">
	            	3333333333333333333333333333
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
				<?php $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

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
						<div class="chapeau"><?php the_excerpt() ?></div>
						<div class="description">
							<?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 40, "..." ) ?>
						</div>
						<a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php _e('Lire'); ?></a>
					</div>
				</div>
		    	<?php wp_reset_query(); ?>
		    <?php endif; ?>

				<a href="#" title="Lorem" class="LienVieCridon" ><span> <?php _e('Toute la vie du CRIDON'); ?> </span></a>
			</div>
		</div>	


		

		


	
		

	</div>

<?php get_footer(); ?>
