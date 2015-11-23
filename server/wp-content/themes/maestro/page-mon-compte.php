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
 
				<h1>Mon compte</h1>
				<ul id="sel-compte">
					<li class="js-account-dashboard js-account-blocs <?php echo (!isset($onglet) || $onglet == 1) ? " active " : ""?>">
						<div class="bt js-account-dashboard-button">Tableaux de bord</div>
						<div id="tableau-de-bord" class="pannel">
							<?php echo get_template_part("content","mon-compte-dashboard"); ?>
						</div>

					</li>
					<li class="js-account-questions js-account-blocs   <?php echo ($onglet == 2) ? " active " : ""?>" >
						<div class="bt js-account-questions-button">Mes Questions</div>
						<div id="mes-questions" class="pannel">	
							<?php echo get_template_part("content","mon-compte-questions"); ?>
						</div>
					</li>
					<li class="js-account-profil js-account-blocs <?php echo ($onglet == 3) ? " active " : ""?>">
						<div class="bt js-account-profil-button" id="sel-compte-profil-button">Mon profil</div>
						<div id="mon-profil" class="pannel">
							<?php echo get_template_part("content","mon-compte-profil"); ?>
						</div>
					</li>
					<?php if (CriCanAccessFinance()): ?>
					<li class="js-account-facturation js-account-blocs <?php echo ($onglet == 4) ? " active " : ""?>">
						<div class="bt js-account-facturation-button">Règles de facturation</div>
						<div id="regles-facturation" class="pannel">
							<h2> PROCHAINEMENT </h2>			
						</div>
					</li>				
					<?php endif ?>
				</ul>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
