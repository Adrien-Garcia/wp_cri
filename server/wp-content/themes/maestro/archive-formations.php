<?php get_header(); ?>

	<div id="content" class="archive archive-formations">
				
		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Nos formations</h1>

				<div id="filtres_formations">
	   				<div class="futures js-tab-formations-futures-open open"><span>à venir</span></div>
	   				<div class="passees js-tab-formations-passees-open"><span>passées</span></div>
				</div>
				<div style="display:block; clear: both; width:100%">
					
				</div>

				<div class="listing formations tab js-tab-formations-futures open">
					<?php set_query_var( 'objects', $formationsFutures ); ?>
					
					<?php echo get_template_part("content","post-list"); ?>

                    <div class="pagination">
                    	<?php echo $this->pagination(); ?>
                    </div>
                    
                </div>

				<div class="listing formations tab js-tab-formations-passees">
					<?php set_query_var( 'objects', $formationsPassees ); ?>

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
