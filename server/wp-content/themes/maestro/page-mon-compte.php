<?php get_header(); ?>
	<div id="content" class="page page-mon-compte">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>
		

		<div id="main" class="cf" role="main">

			<div id="inner-content" class="wrap cf">
 
				<h1>Mon compte</h1>
				<ul id="sel-compte">
					<li class="js-account-dashboard js-account-blocs <?php echo (!isset($onglet) || $onglet == 1) ? " active " : ""?>" data-js-name="Dashboard" data-js-ajax-src="<?php get_home_url() ?>/notaires/contentdashboard">
						<a href="<?php get_home_url() ?>/notaires/" class="bt js-account-dashboard-button analytics_Dashboard_dashboard">Tableaux de bord</a>
						<div id="tableau-de-bord" class="pannel js-account-ajax">
                            <?php if (!isset($onglet) || $onglet == 1) : ?>
                                <?php CriRenderView('contentdashboard', array('controller' => $this, 'questions' => $questions, 'notaire' => $notaire), 'notaires') ?>
                            <?php endif; ?>
						</div>

					</li>
					<li class="js-account-questions js-account-blocs <?php echo ($onglet == 2) ? " active " : ""?>" data-js-name="Questions" data-js-ajax-src="<?php get_home_url() ?>/notaires/contentquestions">
						<a href="<?php get_home_url() ?>/notaires/questions" class="bt js-account-questions-button analytics_Dashboard_questions">Mes Questions</a>
						<div id="mes-questions" class="pannel js-account-ajax">
                            <?php if ($onglet == 2) : ?>
                                <?php CriRenderView('contentquestions', array('notaire' => $notaire, 'answered' => $answered,'pending'=> $pending,'juristesPending'=> $juristesPending,'juristesAnswered' => $juristesAnswered,'matieres' => $matieres,'controller' => $this), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<li class="js-account-profil js-account-blocs <?php echo ($onglet == 3) ? " active " : ""?>" data-js-name="Profil" data-js-ajax-src="<?php get_home_url() ?>/notaires/contentprofil">
						<a href="<?php get_home_url() ?>/notaires/profil" class="bt js-account-profil-button analytics_Dashboard_profil" id="sel-compte-profil-button">Mon profil</a>
						<div id="mon-profil" class="pannel js-account-ajax">
                            <?php if ($onglet == 3) : ?>
                                <?php CriRenderView('contentprofil', array('matieres' => $matieres,'notaire' => $notaire), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<?php if (CriCanAccessFinance()): ?>
					<li class="js-account-facturation js-account-blocs <?php echo ($onglet == 4) ? " active " : ""?>" data-js-name="Facturation" data-js-ajax-src="<?php get_home_url() ?>/notaires/contentfacturation">
						<a href="<?php get_home_url() ?>/notaires/facturation" class="bt js-account-facturation-button analytics_Dashboard_facturation">Règles de facturation</a>
						<div id="regles-facturation" class="pannel js-account-ajax">
                        <?php if ($onglet == 4) : ?>
                        	<?php CriRenderView('contentprofil', array('notaire' => $notaire, 'content' => $content), 'facturation') ?>
                            
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
