<?php
/*
 Template Name: Accueil
*/
?>

<?php get_header(); ?>

    <?php $options = get_option( 'theme_settings' );
        
    if ($options['slider'] != "none" && $options['slider'] == "full") :
        
        //Full Slider
    	get_template_part("content","slides");
    	
    endif; ?>

	<div id="content">
	
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
