<?php get_header(); ?>
    <?php $id = CriNotaireData()->id; ?>
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
				<a href="/wp-login.php?action=logout" class="logout"> Se déconnecter</a>
				<ul id="sel-compte">
					<li class="js-account-dashboard js-account-blocs <?php echo (!isset($onglet) || $onglet == 1) ? " active " : ""?>" data-js-name="Dashboard" data-js-ajax-src="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/contentdashboard">
						<a href="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/" class="bt js-account-dashboard-button">Tableaux de bord</a>
						<div id="tableau-de-bord" class="pannel js-account-ajax">
                            <?php if (!isset($onglet) || $onglet == 1) : ?>
                                <?php CriRenderView('contentdashboard', array(), 'notaires') ?>
                            <?php endif; ?>
						</div>

					</li>
					<li class="js-account-questions js-account-blocs <?php echo ($onglet == 2) ? " active " : ""?>" data-js-name="Questions" data-js-ajax-src="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/contentquestions">
						<a href="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/questions" class="bt js-account-questions-button">Mes Questions</a>
						<div id="mes-questions" class="pannel js-account-ajax">
                            <?php if ($onglet == 2) : ?>
                                <?php CriRenderView('contentquestions', array(), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<li class="js-account-profil js-account-blocs <?php echo ($onglet == 3) ? " active " : ""?>" data-js-name="Profil" data-js-ajax-src="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/contentprofil">
						<a href="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/profil" class="bt js-account-profil-button" id="sel-compte-profil-button">Mon profil</a>
						<div id="mon-profil" class="pannel js-account-ajax">
                            <?php if ($onglet == 3) : ?>
                                <?php CriRenderView('contentprofil', array(), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<?php if (CriCanAccessFinance()): ?>
					<li class="js-account-facturation js-account-blocs <?php echo ($onglet == 4) ? " active " : ""?>" data-js-name="Facturation" data-js-ajax-src="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/contentfacturation">
						<a href="<?php get_home_url() ?>/notaires/<?php echo $id ; ?>/facturation" class="bt js-account-facturation-button">Règles de facturation</a>
						<div id="regles-facturation" class="pannel js-account-ajax">
                        <?php if ($onglet == 4) : ?>
                        	<?php CriRenderView('contentprofil', array(), 'facturation') ?>
                            
                            <?php endif; ?>
						</div>
					</li>				
					<?php endif ?>
				</ul>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
