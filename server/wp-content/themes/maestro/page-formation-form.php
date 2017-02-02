<?php get_header(); ?>

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
						<!-- Demande de cession de formation / Demande de formation Glabale -->
						<h1 class="page-title h1 cessionFormation" itemprop="headline"><?php _e('Demande de formation'); ?></h1>
						<!-- Fin -->

						<!-- Demande de Pré inscription -->
						<h1 class="page-title h1" itemprop="headline"><?php _e('Pré-inscription'); ?></h1>
						<!-- Fin -->

					</header> <?php // end article header ?>

					<section class="entry-content cf" itemprop="articleBody">

						<div class="col2 content-wrapper">
							<!-- Demande de cession de formation -->
							<div>
								<div class="h3">Nom complet formation loremp ipsum dolor sit amet consectur</div>
								<p>
									Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, ex essi blaccum fuga. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am.
								</p>
								<div class="important">
									<div class="titre">Important</div>
									Pour organiser une formation, la participation de sept personnes à minima est requise.
								</div>
							</div>
							<!-- Fin -->

							<!-- Demande de Pré inscription -->
							<div>
								<div class="h3">Nom complet formation loremp ipsum dolor sit amet consectur</div>
								<div class="organisme">Hôtel de Ville - Lyon</div>
								<div class="horaire">Toute la journée</div>
								<a href="#LIENVERSLAFORMATIONCHOISIE">En savoir plus</a>
							</div>
							<!-- Fin -->
						</div>

						<div class="col2 form-wrapper">
							<!-- Demande de cession de formation -->
							<div class="h3">Afin de nous communiquer plus de renseignements, veuillez remplir le formulaire suivant :</div>
							<!-- Fin -->

							<!-- Pré inscription -->
							<div class="h3">Afin de finaliser votre pré-inscription, veuillez remplir le formulaire suivant :</div>
							<!-- Fin -->

							<form action="" method="post">
								<div>
									<label for="formationParticipants">Nombre de participants <span class="required">*</span></label>
                                    <input type="number" value="-" min="1" name="formationParticipants" id="formationParticipants" class="large" required="required" tabindex="0" />
								</div>
								<div>
									<label for="formationTheme">Thématique <span class="required">*</span></label>
									<input type="text" name="formationTheme" id="formationTheme" required="required" class="large" tabindex="1">
								</div>
								<div>
                                    <label for="formationCommentaire">Commentaires</label>
									<!-- Pré inscription / Demande de cession de formation -->
									<p class="label">Merci de renseigner les informations personnelles des participants (nom / prénom) ainsi qu'une proposition de date et de lieux souhaités</p>
									<!-- Fin -->
									<textarea name="formationCommentaire" id=""formationCommentaire cols="30" rows="10" class="textarea" tabindex="2"></textarea>
								</div>
								<div class="required-info">*Champs obligatoires</div>
								<input type="submit" name="formationSubmit" id="formationSubmit" class="gform_button button" value="Envoyer la demande Valider la pré-inscription" tabindex="10" />
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
