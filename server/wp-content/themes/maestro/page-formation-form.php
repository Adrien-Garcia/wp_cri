<?php get_header(); ?>
<?php
    $demande = $preinscription ;
    $demande = empty($demande) ? $demandeFormation : $demande;
    $demande = empty($demande) ? $demandeGenerique : $demande;
?>
	<div id="content" class="page page-formation-form">

		<div class="breadcrumbs">
			<div class="wrap cf">
				<?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
			</div>
		</div>

		<div id="inner-content" class="wrap cf">

			<div id="main" class="cf" role="main">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

					<header class="article-header">
                        <?php if (!empty($demandeFormation) || !empty($demandeGenerique)) : ?>
                        <!-- Demande de cession de formation / Demande de formation Glabale -->
						<h1 class="page-title h1 cessionFormation" itemprop="headline"><?php _e('Demande de formation'); ?></h1>
						<!-- Fin -->
                        <?php endif; ?>

                        <?php if (!empty($preinscription)) : ?>
                        <!-- Demande de Pré inscription -->
						<h1 class="page-title h1" itemprop="headline"><?php _e('Pré-inscription'); ?></h1>
						<!-- Fin -->
                        <?php endif; ?>

					</header> <?php // end article header ?>

					<section class="entry-content cf" itemprop="articleBody">

						<div class="col2 content-wrapper">
                            <?php if (!empty($demandeFormation)) : ?>
                            <!-- Demande de cession de formation -->
							<div>
								<div class="h3"><?php echo $demande['formation']['title']; ?></div>
								<p>
                                    <?php echo apply_filters('the_content', $demande['formation']['content']); ?>
                                </p>
                                <p>
                                   <a href="<?php echo $demande['formation']['url'] ; ?>">En savoir plus</a>
                                </p>
                                <div class="important">
									<div class="titre">Important</div>
									Pour organiser une formation, la participation de sept personnes à minima est requise.
								</div>
							</div>
							<!-- Fin -->
                            <?php endif; ?>

                            <?php if (!empty($preinscription)) : ?>
							<!-- Demande de Pré inscription -->
							<div>
								<div class="h3"><?php echo $demande['formation']['title']; ?></div>
								<div class="organisme"><?php echo strtoupper($demande['formation']['organisme']) ; ?> - <?php echo $demande['formation']['city'] ; ?></div>
								<div class="horaire"><?php echo $demande['formation']['time'] ; ?></div>
								<a href="<?php echo $demande['formation']['url'] ; ?>">En savoir plus</a>
							</div>
							<!-- Fin -->
                            <?php endif; ?>

                        </div>

						<div class="col2 form-wrapper">
                            <?php if (!empty($demandeFormation) || !empty($demandeGenerique)) : ?>
                            <!-- Demande de cession de formation -->
							<div class="h3">Afin de nous communiquer plus de renseignements, veuillez remplir le formulaire suivant :</div>
							<!-- Fin -->
                            <?php endif; ?>
                            <?php if (!empty($preinscription)) : ?>
                            <!-- Pré inscription -->
							<div class="h3">Afin de finaliser votre pré-inscription, veuillez remplir le formulaire suivant :</div>
							<!-- Fin -->
                            <?php endif; ?>

							<form action="" method="post">
                                <?php if (!empty($preinscription) || !empty($demandeFormation)) : ?>
                                <div>
									<label for="formationParticipants">Nombre de participants <span class="required">*</span></label>
                                    <input type="number" value="-" min="1" name="formationParticipants" id="formationParticipants" class="large" required="required" tabindex="0" />
								</div>
                                <?php endif; ?>
                                <?php if (!empty($demandeGenerique)) : ?>
                                <div>
									<label for="formationTheme">Thématique <span class="required">*</span></label>
									<input type="text" name="formationTheme" id="formationTheme" required="required" class="large" tabindex="1">
								</div>
                                <?php endif; ?>
                                <div>
                                    <label for="formationCommentaire">Commentaires</label>
                                    <?php if (!empty($preinscription) || !empty($demandeFormation)) : ?>
                                    <!-- Pré inscription / Demande de cession de formation -->
									<p class="label">
                                        Merci de renseigner les informations personnelles des participants (nom / prénom)
                                        <?php if (!empty($demandeFormation)) : ?>
                                        ainsi qu'une proposition de date et de lieux souhaités
                                        <?php endif; ?>
                                    </p>
									<!-- Fin -->
                                    <?php endif; ?>
                                    <textarea name="formationCommentaire" id="formationCommentaire" cols="30" rows="10" class="textarea" tabindex="2"></textarea>
								</div>
								<div class="required-info">*Champs obligatoires</div>
								<input
                                    type="submit" name="formationSubmit" id="formationSubmit" class="gform_button button"
                                    value="<?php if (!empty($preinscription)) : ?>Valider la pré-inscription<?php else : ?>Envoyer la demande<?php endif; ?>"
                                    tabindex="10" />
							</form>
                            <?php if (!empty($error)) : ?>
							<div class="message error show">
								<?php echo $error ; ?>
							</div>
                            <?php endif; ?>
                            <?php if (!empty($valid)) : ?>
                            <div class="message valide show">
                                <?php echo $valid ; ?>
                            </div>
                            <?php endif; ?>
                        </div>

						

						<!-- <img src="<?php // echo get_template_directory_uri(); ?>/library/images/le-cridon-contact.jpg" alt="" /> -->

					</section>

				</article>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>