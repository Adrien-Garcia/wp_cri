<?php get_header(); ?>

	<div id="content" class="single single-veilles">

		<div class="breadcrumbs">
			<div id="" class="wrap cf">				
				<a href="#" title="">Accueil</a> + <a href="#" title=""> Accéder aux connaissances juridiques </a>  +  <span>Veille juridique</span>
			</div>
		</div>

		<?php // $vars = get_defined_vars(); var_dump($object); ?>
		<?php criWpPost($object); ?>

			<div id="main" class="cf" role="main">
				<div id="inner-content" class="wrap cf">
			
				

				<div class="titre">
					<span class="h1"><?php _e('Veille Juridique'); ?></span>
				</div>

				<div class="sep"></div>

				<?php // if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

						<div class="date-veille sel-veilles-date">
							<span class="jour"><?php echo get_the_date( 'd') ?></span>
					      	<span class="mois"><?php echo get_the_date( 'M') ?></span>
					      	<span class="annee"><?php echo get_the_date( 'Y') ?></span> 				
						</div>

						<div class="details">
							<div class="block_left">
								<div class="img-cat">
									<img class="sel-veilles-picto" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
								</div>
							</div>

							

							<div class="block_right sel-veilles-content">							
								<div class="matiere"><?php echo $object->matiere->label ?></div>
								<h1 class="entry-title single-title"><?php the_title() ?></h1>
								<div class="chapeau">
									<?php echo get_the_excerpt() ?>
								</div>
								
							</div>
							<div class="block_full">
								
								<div class="content">									

									<?php the_content(); ?>

									<!--h2>sous titre 2</h2>

									<p>Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.</p>
									
									<ul>
										<li>doluptatur apedigenet eligen</li>
										<li>qui officit voluptatis doluptatur</li>
									    <li>ut modi re, cus dolest porio volorempore sandame</li>
									    <li><a href="">doluptatur apedigenet eligen</a></li>
									</ul>

									<p>Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, <s>ex essi blaccum fuga</s>. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am velignis aceat et escilig endicto ipsandi tatiis nonsectenda dit as aut labo. Ut voluptatur? Maximoles doluptatur apedigenet eligent ea vit pliquo totatur sunt ea cum di dipsus iuscient ad es dolupicid ut aut mi, imusanditat.</p>

									<h2>sous titre 2</h2>

									<p>Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.</p>

										<h3>Sous titre 3</h3>
										<p>Texte couleur 2 Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse 
										nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius 
										ditatendam eiusam quaeceatus.</p>

									<p>Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, ex essi blaccum fuga. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am velignis aceat et escilig endicto ipsandi tatiis nonsectenda dit as aut labo. Ut voluptatur? Maximoles doluptatur apedigenet eligent ea vit pliquo totatur sunt ea cum di dipsus iuscient ad es dolupicid ut aut mi, imusanditat.</p>

										<h3>Sous titre 3</h3>
										<p>Texte couleur 2 Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse 
										nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius 
										ditatendam eiusam quaeceatus.</p!-->
								</div>
								<ul class="mots_cles">
								<?php 
									$tags = get_the_tags();
									if( $tags ) : foreach ($tags as $tag) :
								 ?>
									<li><?php echo $tag->name; ?></li>
								<?php endforeach; endif; ?>
								</ul>

								<div class="documents-liees">
									<ul>
										<li><a href="#" target="_blank">NomDuDocument.pdf</a></li>
										<li><a href="#" target="_blank">NomDuDocument.pdf</a></li>
										<li><a href="#" target="_blank">NomDuDocument.pdf</a></li>
										<li><a href="#" target="_blank">NomDuDocument.pdf</a></li>
									</ul>
								</div>
								
								
							</div>

							
						</div>

					 	

					    	
					  	

					</article>



					<a href="<?php echo MvcRouter::public_url(array('controller' => 'veilles', 'action'     => 'index')) ?>"><?php _e('Retour'); ?></a>

				<?php // endwhile; ?>

				

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
