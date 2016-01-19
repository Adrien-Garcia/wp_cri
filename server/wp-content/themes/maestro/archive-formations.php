<?php get_header(); ?>

	<div id="content" class="archive archive-formations">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Veille juridique</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Nos formations</h1>

				<div id="filtres_formations">
	   				<div class="futures open js-tab-formation-future-open"><span>à venir</span></div>
	   				<div class="passees js-tab-formation-passees-open"><span>passées</span></div>  
				</div>
				<div style="display:block; clear: both; width:100%">
					
				</div>

				<div class="listing formations" id="sel-formations">
					<?php set_query_var( 'objects', $objects ); ?>						
					
					<?php echo get_template_part("content","post-list"); ?>

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
