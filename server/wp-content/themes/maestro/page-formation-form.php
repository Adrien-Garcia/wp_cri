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

						<h1 class="page-title h1" itemprop="headline"><?php _e('Demande de formation'); ?></h1>

					</header> <?php // end article header ?>

					<section class="entry-content cf" itemprop="articleBody">

						<div class="col2 content-wrapper">
							<div class="h3">Nom complet formation loremp ipsum dolor sit amet consectur</div>
							<p>
								Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, ex essi blaccum fuga. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am.
							</p>

							<div class="important">
								<div class="titre">Important</div>
								Pour organiser une formation, la participation de sept personnes à minima est requise.
							</div>
						</div>

						<div class="col2 form-wrapper">
							<div class="h3">Afin de nous communiquer plus de renseignements, veuillez remplir le formulaire suivant :</div>

							<form action="">
								<div>
									<label>Nombre de participants <span class="required">*</span></label>
									<select name="" id="" class="gfield_select" tabindex="">
										<option value="" selected="selected">1</option>
										<option value="">2</option>
										<option value="">3</option>
									</select>
								</div>
								<div>
									<label>Commentaires</label>
									<textarea name="" id="" cols="30" rows="10" class="textarea" tabindex=""></textarea>
								</div>
								<div class="required-info">*Champs obligatoires</div>
								<input type="submit" id="" class="gform_button button" value="Envoyer la demande" tabindex="" />
							</form>
							<div class="message valide">
								Votre demande a bien été envoyée.
							</div>
							<div class="message error">
								Veuillez remplir tous les champs
							</div>
						</div>

						

						<!-- <img src="<?php // echo get_template_directory_uri(); ?>/library/images/le-cridon-contact.jpg" alt="" /> -->

					</section>

				</article>

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
