<?php get_header(); ?>

	<div id="content" class="page page-mon-compte">
				
		<div class="breadcrumbs">
			<div id="inner-content" class="wrap cf">
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				<a href="#" title="">Accueil</a> + <span>Mon compte</span>
			</div>
		</div>
		

		<div id="main" class="cf" role="main">

			<div id="inner-content" class="wrap cf">
			
				<?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
				
				<?php 
					$notaire = $object;
					//var_dump($notaire);
				 ?>

				<h1>Mon compte</h1>
				<ul>
					<li class="js-account-dashboard js-account-blocs active">
						<div class="bt js-account-dashboard-button">Tableaux de bord</div>
						<div id="tableau-de-bord" class="pannel">
							<?php echo get_template_part("content","mon-compte-dashboard"); ?>
						</div>

					</li>
					<li class="js-account-questions js-account-blocs">
						<div class="bt js-account-questions-button">Mes Questions</div>
						<div id="mes-questions" class="pannel">	
							<h2> PROCHAINEMENT </h2>
						</div>
					</li>
					<li class="js-account-profil js-account-blocs">
						<div class="bt js-account-profil-button">Mon profil</div>
						<div id="mon-profil" class="pannel">
							<?php echo get_template_part("content","mon-compte-profil"); ?>
						</div>
					</li>
					<li class="js-account-facturation js-account-blocs">
						<div class="bt js-account-facturation-button">Règles de facturation</div>
						<div id="regles-facturation" class="pannel">
							<h2> PROCHAINEMENT </h2>			
						</div>
					</li>
				</ul>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
