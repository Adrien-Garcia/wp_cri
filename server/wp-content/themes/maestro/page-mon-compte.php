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

				<ul>
					<li>
						<div class="bt">Tableaux de bord</div>

						<div id="tableau-de-bord" class="pannel">

							<div class="mon-solde">
								<h2>Mon solde</h2>

								<div class="solde-pts">
									<svg version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="300px" height="300px" viewBox="0 0 300 300" enable-background="new 0 0 300 300" xml:space="preserve">
									<g id="XMLID_28_">

										<circle id="XMLID_33_" fill="#FFFFFF" cx="150" cy="150" r="150"/>
										<path id="XMLID_27_" d="M150,20c34.7,0,67.4,13.5,91.9,38.1C266.5,82.6,280,115.3,280,150s-13.5,67.4-38.1,91.9
											C217.4,266.5,184.7,280,150,280s-67.4-13.5-91.9-38.1C33.5,217.4,20,184.7,20,150s13.5-67.4,38.1-91.9C82.6,33.5,115.3,20,150,20
											 M150,0C67.2,0,0,67.2,0,150s67.2,150,150,150s150-67.2,150-150S232.8,0,150,0L150,0z"/>
									</g>
									</svg>

									<div class="point">
										<div class="pts">
											134 <span>pts</span>
										</div>
										<span>quota 150</span>
									</div>									
								</div>
								
								<div class="consomation">
									<div class="pts">
										<span class="pts">16</span>
										<span class="texte">points consommés</span>
										<span class="date">au 12.09.2015</span>
									</div>							
								</div>

								<div class="appel-courrier">
									<div class="appel">
										<span>6</span> appels								
									</div>
									<div class="courrier">
										<span>10</span> courriers								
									</div>
								</div>

								
							</div>
							<div class="mes-questions">
								<h2>Mes dernières questions</h2>
							</div>
							
						</div>


					</li>
					<li>
						<div class="bt">Mes Questions</div>
						<div id="mes-questions" class="pannel">					
						</div>
					</li>
					<li class="active">
						<div class="bt">Mon profil</div>

						<div id="mon-profil" class="pannel">					
							<div class="mes-informations">

								<h2>Mes informations</h2>

								<div class="img-profil">
									<img src="" alt="" />
								</div>
								<div class="coordonnees">
									<div class="nom">
										<span>Nom de l'étude</span>
										Nom Prénom
									</div>
									<div class="adresse">
										25 rue du Moulin
										<span>69000 leau</span>
									</div>
									<div class="contact">
										adresse@mail.fr
										<span>00 00 00 00 00</span>
									</div>
									<a href="#" title="">Modifier mes informations</a>
								</div>
							</div>

							<div class="mes-centres-dinterets">

								<h2>Mes centres d'intérêts</h2>

								<div class="description">
									Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.Nem se net faccum fugiant, tem estrum saniam nobissit, officia volut etum aut il mil et officid ut faccus seni aligent aut eosam ratquam nis.
								</div>
								
								<form>
									<ul>
										<li>
											<label>
												<input type="checkbox" id="" class="" name="" value="Droit des obligations, contrats et biens">
												Droit des obligations, contrats et biens
											</label>
										</li>
										<li>
											<label class="select">
												<input type="checkbox" id="" class="" name="" value="Droit de la construction et de l’urbanisme">
												Droit de la construction et de l’urbanisme
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="" class="" name="" value="Droit fiscal et international">
												Droit fiscal et international
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="" class="" name="" value="Droit civil  de la famille">
												Droit civil  de la famille
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="" class="" name="" value="Droit rural">
												Droit rural
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="" class="" name="" value="Management et productivité">
												Management et productivité
											</label>
										</li>
									</ul>
									<input type="submit" name="Valider" value="Valider">
								</form>
							</div>

							<div class="newsletter">

								<h2>Ma newsletter</h2>
								<div class="description">
									Vous êtes inscrit à notre newsletter selon vos centres d'interets.
								</div>
								<a href="#" title="">Me désinscrir</a>

							</div>

						</div>
					</li>
					<li>
						<div class="bt">Règles de facturation</div>
						<div id="regles-facturation" class="pannel">					
						</div>
					</li>
				</ul>

				

				

				

				


				
				

				

			</div>

			<?php /*get_sidebar();*/ ?>

		</div>

	</div>

<?php get_footer(); ?>
