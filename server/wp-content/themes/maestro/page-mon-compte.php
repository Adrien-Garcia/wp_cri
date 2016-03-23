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
				<a href="/wp-login.php?action=logout" class="logout"> Se déconnecter</a>
				<ul id="sel-compte">
					<li class="js-account-dashboard js-account-blocs <?php echo (!isset($onglet) || $onglet == 1) ? " active " : ""?>" data-js-name="Dashboard" data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentdashboard'));?> ">
						<a href="<?php echo mvc_public_url(array('controller' => 'notaires'));?>" class="bt js-account-dashboard-button analytics_Dashboard_dashboard">Tableaux de bord</a>
						<div id="tableau-de-bord" class="pannel js-account-ajax">
                            <?php if (!isset($onglet) || $onglet == 1) : ?>
                                <?php CriRenderView('contentdashboard', array('controller' => $this, 'questions' => $questions, 'notaire' => $notaire), 'notaires') ?>
                            <?php endif; ?>
						</div>

					</li>
					<li class="js-account-questions js-account-blocs <?php echo ($onglet == 2) ? " active " : ""?>" data-js-name="Questions" data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentquestions'));?>">
						<a href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'questions'));?>" class="bt js-account-questions-button analytics_Dashboard_questions">Mes Questions</a>
						<div id="mes-questions" class="pannel js-account-ajax">
                            <?php if ($onglet == 2) : ?>
                                <?php CriRenderView('contentquestions', array('notaire' => $notaire, 'answered' => $answered,'pending'=> $pending,'juristesPending'=> $juristesPending,'juristesAnswered' => $juristesAnswered,'matieres' => $matieres,'controller' => $this), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<li class="js-account-profil js-account-blocs <?php echo ($onglet == 3) ? " active " : ""?>" data-js-name="Profil" data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentprofil'));?>">
						<a href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'profil'));?>" class="bt js-account-profil-button analytics_Dashboard_profil" id="sel-compte-profil-button">Mon profil</a>
						<div id="mon-profil" class="pannel js-account-ajax">
                            <?php if ($onglet == 3) : ?>
                                <?php CriRenderView('contentprofil', array('matieres' => $matieres,'notaire' => $notaire), 'notaires') ?>
                            <?php endif; ?>
						</div>
					</li>
					<?php if (CriCanAccessSensitiveInfo()): ?>
					<li class="js-account-facturation js-account-blocs <?php echo ($onglet == 4) ? " active " : ""?>" data-js-name="Facturation" data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentfacturation'));?>">
						<a href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'facturation'));?>" class="bt js-account-facturation-button analytics_Dashboard_facturation">Règles de facturation</a>
						<div id="regles-facturation" class="pannel js-account-ajax">
                        <?php if ($onglet == 4) : ?>
                        	<?php CriRenderView('contentfacturation', array('notaire' => $notaire, 'content' => $content), 'notaires') ?>
                            
                            <?php endif; ?>
						</div>
					</li>
                    <li class="js-account-cridonline js-account-blocs <?php echo ($onglet == 5) ? " active " : ""?>" data-js-name="Cridonline" data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonline'));?>">
                        <a href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'cridonline'));?>" class="bt js-account-cridonline-button">Crid'Online</a>
                        <div id="cridonline" class="pannel js-account-ajax">
                        <?php if ($onglet == 5) : ?>
                            <?php CriRenderView('contentcridonline', array('notaire' => $notaire, 'priceVeilleLevel2' => $priceVeilleLevel2, 'priceVeilleLevel3' => $priceVeilleLevel3 ), 'notaires') ?>

                        <?php endif; ?>
                        </div>
                    </li>
					<?php endif ?>
                    <?php
                    // utile pour pouvoir afficher le formulaire de creation collaborateur
                    // suivant les modeles ci-dessus, pas de traitement en ajax, pas de popin affichée
                    ?>
                    <li class="js-account-collaborateur js-account-blocs <?php echo ($onglet == 6) ? " active " : ""?>" data-js-name="Collaborateur" data-js-ajax-src="<?php mvc_public_url(array('controller' => 'notaires', 'action' => 'contentcollaborateur')) ?>">
                        <a href="<?php mvc_public_url(array('controller' => 'notaires', 'action' => 'collaborateur')) ?>" class="bt js-account-collaborateur-button" id="sel-compte-collaborateur-button">Mes collaborateurs</a>
                        <div id="mes-collaborateurs" class="pannel js-account-ajax">
                            <?php if ($onglet == 6) : ?>
                                <?php CriRenderView('contentcollaborateur', array('collaborator_functions' => $collaborator_functions), 'notaires') ?>
                            <?php endif; ?>
                        </div>
                    </li>
				</ul>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
