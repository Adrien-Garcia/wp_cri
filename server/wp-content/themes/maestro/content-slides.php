<?php $options = get_option( 'theme_settings' ); ?>

<div id="slider" class="slider<?php if($options['slider'] == "full"): echo "-full"; else: echo "-normal"; endif;?>">
  
	<?php if( have_rows('slide') ): ?>

		<ul class="bxslider">

		<?php while( have_rows('slide') ): the_row(); ?>
		
            <?php $slider_img_src = get_sub_field("image"); ?>
		
            <?php if($options['slider'] == "full") : ?>
            
                <?php if( get_sub_field("image") ):
                
                    //Sizes
					$size_mobile_img = "slider-accueil-mobile";
					$size_tablette_img = "slider-accueil-tablette";
					$size_pc_img = "slider-accueil-pc";
					
					// urls
					$url_mobile_img = wp_get_attachment_image_src( $slider_img_src, $size_mobile_img, false);
					$url_tablette_img = wp_get_attachment_image_src( $slider_img_src, $size_tablette_img, false);
					$url_pc_img = wp_get_attachment_image_src( $slider_img_src, $size_pc_img, false);
					
					if ($url_mobile_img) (string)$url_mobile_img = $url_mobile_img[0];
					if ($url_tablette_img) (string)$url_tablette_img = $url_tablette_img[0];
					if ($url_pc_img) (string)$url_pc_img = $url_pc_img[0];
					
                    ?>			    
				
                    <li data-mob="<?php echo $url_mobile_img ?>" data-tab="<?php echo $url_tablette_img ?>" data-desk="<?php echo $url_pc_img ?>" style="background-image: url('<?php echo $url_pc_img ?>');">
                    
                        <a onclick="ga('send', 'event', 'Slider accueil', 'click', '<?php the_sub_field("titre"); ?>');" class="slide-full-link" href="<?php the_sub_field("lien_bouton"); ?>" title="<?php the_sub_field("libelle_bouton"); ?>"></a>
    
        				<span>
        					<?php the_sub_field("titre"); ?>
        				</span>
        				
                        <div>
        					<?php the_sub_field("description"); ?>
        				</div>
        				
                    </li>  
                
                <?php endif; ?>          
            
            <?php else: ?>
            
    			<li>
    
    				<a onclick="ga('send', 'event', 'Slider accueil', 'click', '<?php the_sub_field("titre"); ?>');" class="slide-full-link" href="<?php the_sub_field("lien_bouton"); ?>" title="<?php the_sub_field("libelle_bouton"); ?>"></a>
    
    				<span>
    					<?php the_sub_field("titre"); ?>
    				</span>
    
    				<?php if( get_sub_field("image") ): 
				        
        				//Sizes
        				$size_mobile_img = "slider-accueil-mobile";
        				$size_tablette_img = "slider-accueil-tablette";
        				$size_pc_img = "slider-accueil-pc";
        					
        				// urls
        				$url_mobile_img = wp_get_attachment_image_src( $slider_img_src, $size_mobile_img, false);
        				$url_tablette_img = wp_get_attachment_image_src( $slider_img_src, $size_tablette_img, false);
        				$url_pc_img = wp_get_attachment_image_src( $slider_img_src, $size_pc_img, false);
        					
        				if ($url_mobile_img) (string)$url_mobile_img = $url_mobile_img[0];
        				if ($url_tablette_img) (string)$url_tablette_img = $url_tablette_img[0];
        				if ($url_pc_img) (string)$url_pc_img = $url_pc_img[0];
        				    
        				?>
        				
    					<?php /* Activer picturefill si projet responsive */ ?>
    					<picture>
    						<!--[if IE 9]><video style="display: none;"><![endif]-->
    						<source srcset="<?php echo $url_mobile_img; ?>" media="(max-width: 760px)">
    						<source srcset="<?php echo $url_tablette_img; ?>" media="(max-width: 1030px)">
    						<source srcset="<?php echo $url_pc_img; ?>" media="(min-width: 1360px)">
    						<!--[if IE 9]></video><![endif]-->
    						<img srcset="<?php echo $url_mobile_img; ?>" alt="<?php the_sub_field('titre'); ?> : <?php the_sub_field('description'); ?>">
    					</picture>
    
    				<?php endif; ?>
    
    				<div>
    					<?php the_sub_field("description"); ?>
    				</div>
    
    				<a onclick="ga('send', 'event', 'Slider accueil', 'click', '<?php the_sub_field("titre"); ?>');" href="<?php the_sub_field("lien_bouton"); ?>" title="<?php the_sub_field("libelle_bouton"); ?>"><?php the_sub_field("libelle_bouton"); ?></a>
    
    			</li>
			
			<?php endif; ?>

		<?php endwhile; ?>

		</ul>
		<?php  /* Fin slider */  ?>

		<?php  /* Pagination (si besoin d'une pagination customisée, sinon utiliser la pagination générée par bxSlider */  ?>
		<!--div id="bx-pager">

			<div>

			<?php $cont = 0 ; ?>

			<?php while( have_rows('slide') ): the_row(); ?>

				<a data-slide-index="<?php echo $cont; ?>" title="<?php the_title(); ?>"></a>

			<?php $cont++; endwhile; ?>

			<?php wp_reset_query(); ?>

			</div>

		</div-->
		<?php  /* Fin pgination */  ?>

	<?php endif; ?>
            
  </div>