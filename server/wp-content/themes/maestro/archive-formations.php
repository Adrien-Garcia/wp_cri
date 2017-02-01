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
	   				<a href="<?= MvcRouter::public_url(array('controller' => 'formations')) ?>"
					   class="futures js-tab-formations-futures-open<?= isset($formationsFutures) ? ' open' : '' ?>">
						<span>à venir</span>
					</a>
	   				<a href="<?= MvcRouter::public_url(array('controller' => 'formations', 'action' => 'past')) ?>"
					   class="passees js-tab-formations-passees-open<?= isset($formationsPassees) ? ' open' : '' ?>">
						<span>passées</span>
					</a>
				</div>
				<div style="display:block; clear: both; width:100%">
					
				</div>

				<div class="listing formations tab js-tab-formations-futures<?= isset($formationsFutures) ? ' open' : '' ?>">
					<?php if (isset($formationsFutures)): ?>
					<?php set_query_var( 'objects', $formationsFutures ); ?>
					
					<?php echo get_template_part("content","post-list"); ?>

                    <div class="pagination">
                    	<?php echo $this->pagination(); ?>
                    </div>
                    <?php endif; ?>
                </div>

				<div class="listing formations tab js-tab-formations-passees<?= isset($formationsPassees) ? ' open' : '' ?>">
					<?php if (isset($formationsPassees)): ?>
					<?php set_query_var( 'objects', $formationsPassees ); ?>

					<?php echo get_template_part("content","post-list"); ?>

					<div class="pagination">
						<?php echo $this->pagination(); ?>
					</div>
					<?php endif; ?>
				</div>
				<a href="/catalogue-formations/" class="bt-formation">Consulter la liste complète des formations</a>
			</div>					



		</div>
		
	</div>

<?php get_footer(); ?>
