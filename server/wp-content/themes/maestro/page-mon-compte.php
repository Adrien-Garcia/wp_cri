<?php get_header(); ?>
	<div id="content" class="page page-mon-compte">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>
		

		<div id="main" class="cf" role="main">
			<div class="header-wrap">
				<h1 class="wrap">Mon compte</h1>
			</div>

			<div id="inner-content" class="wrap cf">
 

				<!-- <a href="/wp-login.php?action=logout" class="logout"> Se déconnecter</a> -->
				<div id="sidebar_prive">
					<nav>
						<ul id="sel-compte">
							<li
								class="dashboard js-account-dashboard js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_DASHBOARD) ? " active " : "" ?>"
								data-js-name="Dashboard"
								>

								<a
									data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentdashboard'));?>"
									data-js-target-id="tableau-de-bord"
									href="<?php echo mvc_public_url(array('controller' => 'notaires'));?>"
									class="bt js-account-dashboard-button analytics_Dashboard_dashboard">
									<span>Tableaux de bord</span>
								</a>
							</li>
							<li
								class="questions js-account-questions js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_QUESTION) ? " active " : "" ?>"
								data-js-name="Questions"
								>

								<a
									data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentquestions'));?>"
									data-js-target-id="mes-questions"
									href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'questions'));?>"
									class="bt js-account-questions-button analytics_Dashboard_questions">
									<span>Mes Questions</span>
								</a>
							</li>
							<li
								class="profil js-account-profil js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_PROFIL) ? " active " : "" ?>"
								data-js-name="Profil"
								>

								<a
									data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentprofil'));?>"
									data-js-target-id="mon-profil"
									href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'profil'));?>"
									class="bt js-account-profil-button analytics_Dashboard_profil" id="sel-compte-profil-button">
									<span>Mon profil</span>
								</a>
							</li>
							<?php if (CriCanAccessSensitiveInfo(CONST_FINANCE_ROLE)): ?>
							<li
								class="facturation js-account-facturation js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_FACTURATION) ? " active " : "" ?>"
								data-js-name="Facturation"
								>

								<a
									data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentfacturation'));?>"
									data-js-target-id="regles-facturation"
									href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'facturation'));?>"
									class="bt js-account-facturation-button analytics_Dashboard_facturation">
									<span>Règles de facturation</span>
								</a>
							</li>
							<?php endif; ?>
                            <?php if (!isPromoActive()) : ?>
                                <?php if (CriCanAccessSensitiveInfo(CONST_CRIDONLINESUBSCRIPTION_ROLE)): ?>
                                <li
                                    class="cridonline js-account-cridonline js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_CRIDONLINE) ? " active " : "" ?>"
                                    data-js-name="Cridonline"
                                    >

                                    <a
                                        data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonline')) ?>"
                                        data-js-target-id="cridonline"
                                        href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'cridonline'));?>"
                                        class="bt js-account-cridonline-button analytics_Dashboard_cridonline cridonline">
                                        <span><span>Crid</span>'Online</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if (CriCanAccessSensitiveInfo(CONST_CRIDONLINESUBSCRIPTION_ROLE)): ?>
                                    <li
                                        class="cridonline js-account-cridonline js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_CRIDONLINE) ? " active " : "" ?>"
                                        data-js-name="Cridonline"
                                        >

                                        <a
                                            data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlinepromo')) ?>"
                                            data-js-target-id="cridonline"
                                            href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'cridonline'));?>"
                                            class="bt js-account-cridonline-button analytics_Dashboard_cridonline cridonline">
                                            <span><span>Crid</span>'Online</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                            <?php endif; ?>
							<?php if (CriCanAccessSensitiveInfo(CONST_COLLABORATEUR_TAB_ROLE)): ?>
							<li
								class="collaborateurs js-account-collaborateur js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_COLLABORATEUR) ? " active " : "" ?>"
								data-js-name="Collaborateur"
								>

								<a
									data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'contentcollaborateur')) ?>"
									data-js-target-id="mes-collaborateurs"
									href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'collaborateur'));?>"
									class="bt js-account-collaborateur-button analytics_Dashboard_collaborateur">
									<span>Mes collaborateurs</span>
								</a>
							</li>
							<?php endif; ?>
							<?php if (CriCanAccessSensitiveInfo(CONST_FINANCE_ROLE)): ?>
								<li
									class="factures js-account-mes-factures js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_MES_FACTURES) ? " active " : "" ?>"
									data-js-name="MesFactures"
								>

									<a
										data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'contentmesfactures')) ?>"
										data-js-target-id="mes-factures"
										href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'mesfactures'));?>"
										class="bt js-account-mes-factures-button analytics_Dashboard_collaborateur">
										<span>Mes factures</span>
									</a>
								</li>
								<li
									class="mes-releves js-account-mes-releves js-account-blocs <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_MES_RELEVES) ? " active " : "" ?>"
									data-js-name="MesReleves"
								>

									<a
										data-js-ajax-src="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'contentmesreleves')) ?>"
										data-js-target-id="mes-releves"
										href="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'mesreleves'));?>"
										class="bt js-account-mes-releves-button analytics_Dashboard_collaborateur">
										<span>Mes relevés de consommation</span>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</nav>
				</div>
				<div class="content">
					<div id="tableau-de-bord" class="pannel js-account-ajax js-account-dashboard js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_DASHBOARD) ? " active " : "" ?>">
                        <?php if (!isset($onglet) || $onglet == CONST_ONGLET_DASHBOARD) : ?>
                            <?php CriRenderView('contentdashboard', array('controller' => $this, 'questions' => $questions, 'notaire' => $notaire, 'messageError' => $messageError), 'notaires') ?>
                        <?php endif; ?>
					</div>
					<div id="mes-questions" class="pannel js-account-ajax js-account-questions js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_QUESTION) ? " active " : "" ?>">
                        <?php if ($onglet == CONST_ONGLET_QUESTION) : ?>
                            <?php CriRenderView('contentquestions', array('notaire' => $notaire, 'answered' => $answered,'pending'=> $pending,'juristesPending'=> $juristesPending,'juristesAnswered' => $juristesAnswered,'matieres' => $matieres,'controller' => $this), 'notaires') ?>
                        <?php endif; ?>
					</div>
					<div id="mon-profil" class="pannel js-account-ajax js-account-profil js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_PROFIL) ? " active " : "" ?>">
	                    <?php if ($onglet == CONST_ONGLET_PROFIL) : ?>
	                        <?php CriRenderView('contentprofil', array('matieres' => $matieres,'notaire' => $notaire, 'priceVeilleLevel2' => $priceVeilleLevel2, 'priceVeilleLevel3' => $priceVeilleLevel3, 'message' => $message), 'notaires') ?>
	                    <?php endif; ?>
					</div>
					<div id="regles-facturation" class="pannel js-account-ajax js-account-facturation js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_FACTURATION) ? " active " : "" ?>">
                    	<?php if ($onglet == CONST_ONGLET_FACTURATION) : ?>
                    		<?php CriRenderView('contentfacturation', array('notaire' => $notaire, 'content' => $content), 'notaires') ?>
                        <?php endif; ?>
					</div>
                    <?php if (!isPromoActive()) : ?>
                         <div id="cridonline" class="pannel js-account-ajax js-account-cridonline js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_CRIDONLINE) ? " active " : "" ?>">
                            <?php if ($onglet == CONST_ONGLET_CRIDONLINE) : ?>
                                <?php CriRenderView('contentcridonline', array('notaire' => $notaire, 'priceVeilleLevel2' => $priceVeilleLevel2, 'priceVeilleLevel3' => $priceVeilleLevel3, 'messageError' => $messageError ), 'notaires') ?>

                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div id="cridonline" class="pannel js-account-ajax js-account-cridonline js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_CRIDONLINE) ? " active " : "" ?>">
                            <?php if ($onglet == CONST_ONGLET_CRIDONLINE) : ?>
                                <?php CriRenderView('contentcridonlinepromo', array('notaire' => $notaire, 'priceVeilleLevel2' => $priceVeilleLevel2, 'priceVeilleLevel3' => $priceVeilleLevel3, 'messageError' => $messageError ), 'notaires') ?>

                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div id="mes-collaborateurs" class="pannel js-account-ajax js-account-collaborateur js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_COLLABORATEUR) ? " active " : "" ?>">
                        <?php if ($onglet == CONST_ONGLET_COLLABORATEUR) : ?>
                            <?php CriRenderView('contentcollaborateur', array('collaborator_functions' => $collaborator_functions, 'liste' => $liste,'message' => $message,'controller' => $this), 'notaires') ?>
                        <?php endif; ?>
                    </div>
					<div id="mes-factures" class="pannel js-account-ajax js-account-mes-factures js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_MES_FACTURES) ? " active " : "" ?>">
						<?php if ($onglet == CONST_ONGLET_MES_FACTURES) : ?>
							<?php CriRenderView('contentmesfactures', array('notaire' => $notaire, 'content' => $content), 'notaires') ?>
						<?php endif; ?>
					</div>
					<div id="mes-releves" class="pannel js-account-ajax js-account-mes-releves js-account-content <?php echo (!isset($onglet) || $onglet == CONST_ONGLET_MES_RELEVES) ? " active " : "" ?>">
						<?php if ($onglet == CONST_ONGLET_MES_RELEVES) : ?>
							<?php CriRenderView('contentmesreleves', array('notaire' => $notaire, 'content' => $content), 'notaires') ?>
						<?php endif; ?>
					</div>
				</div>




			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
