<?php get_header(); ?>

	<div id="content" class="archive archive-veilles">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1"><?php echo $h1 ?></h1>

				<div id="filtres_veilles">

					<div id="tri_matiere">
						<label>Filtrer par matières</label>
						<span class="close">+</span>
						<span class="open active">></span>
					</div>

					<div class="panel">
						<a href="<?php echo MvcRouter::public_url(['controller' => 'veilles', 'action' => 'index']); ?>">Toute la veille juridique</a>

						<form action="<?php echo MvcRouter::public_url(['controller' => 'veilles']); ?>" method="get">
							<ul>
								<?php foreach($matieres as $key => $matiere): ?>
								<li>
									<!-- <label class="select"> -->
									<label class="<?php if($matiere->filtered){echo 'select';} ?> js-utils-checkbox-style">
										<input type="checkbox" id="" class="js-utils-checkbox" name="matieres[]" value="<?php echo $matiere->virtual_name ?>" <?php if($matiere->filtered){echo 'checked';} ?> >
										<?php echo $matiere->label ?>
									</label>
								</li>
								<?php endforeach; ?>
							</ul>
							<input type="submit" value="Valider" id="bt_valider"/>
						</form>
					</div>	

				</div>

				<div class="listing veille" id="sel-veilles">
					<?php set_query_var( 'objects', $objects ); ?>						
					
					<?php echo get_template_part("content","post-list-veilles"); ?>

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
