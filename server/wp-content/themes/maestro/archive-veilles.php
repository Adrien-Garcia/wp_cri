<?php get_header(); ?>

	<div id="content" class="archive-veilles">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Veille juridique</span>
			</div>
		</div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1>Veille juridique</h1>

				<div id="filtres_veilles">					
				</div>

				<?php // if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="listing veille">						

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<div class="date-veille">
							<div class="sep"></div>
							<span class="jour">10</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 				
						</div>
						
						<div class="details">
							<div class="block_left">
								<div class="img-cat">
									<img src="" al="" />
								</div>
							</div>
							<div class="block_right">
								<div class="matiere">Droit social</div>
								<h2>Surendettement des particuliers 1</h2>
								<div class="chapeau">
									Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.
								</div>
								<div class="extrait">
									Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.
								</div>
								<ul class="mots_cles">
									<li>droit</li>
									<li>social</li>
									<li>loi</li>
								</ul>
								<a href="#" title="">Lire</a>
							</div>
						</div>
						
					</article>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<div class="date-veille">
							<div class="sep"></div>
							<span class="jour">10</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 				
						</div>
						
						<div class="details">
							<div class="block_left">
								<div class="img-cat">
									<img src="" al="" />
								</div>
							</div>
							<div class="block_right">
								<div class="matiere">Droit social</div>
								<h2>Surendettement des particuliers 2</h2>
								<div class="chapeau">
									Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.
								</div>
								<div class="extrait">
									Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.
								</div>
								<ul class="mots_cles">
									<li>droit</li>
									<li>social</li>
									<li>loi</li>
								</ul>
								<a href="#" title="">Lire</a>
							</div>
						</div>
						
					</article>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
						<div class="date-veille">
							<div class="sep"></div>
							<span class="jour">10</span>
					      	<span class="mois">sept</span>
					      	<span class="annee">2015</span> 				
						</div>
						
						<div class="details">
							<div class="block_left">
								<div class="img-cat">
									<img src="" al="" />
								</div>
							</div>
							<div class="block_right">
								<div class="matiere">Droit social</div>
								<h2>Surendettement des particuliers 3</h2>
								<div class="chapeau">
									Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.
								</div>
								<div class="extrait">
									Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.
								</div>
								<ul class="mots_cles">
									<li>droit</li>
									<li>social</li>
									<li>loi</li>
								</ul>
								<a href="#" title="">Lire</a>
							</div>
						</div>
						
					</article>

				</div>

			</div>					

		</div>

		<?php // endwhile; ?>

		<?php // wp_pagenavi(); ?>

		

			

		<?php /*get_sidebar();*/ ?>

		
	</div>

<?php get_footer(); ?>
