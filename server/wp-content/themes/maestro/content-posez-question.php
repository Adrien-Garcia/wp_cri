<div id="layer-posez-question">

	<div class="block_top">

		<div class="titre">
			<span><?php _e('Posez une question'); ?></span>
		</div>
		
		<div class="onglets">
			<h2 class="consultation open js-tab-consultation-open"><?php _e('1. Type de consultation'); ?></h2>   				
			<h2 class="question js-tab-question-open"><?php _e('2. Ma question'); ?></h2>
		</div>
		<div class="details">
			<div class="consultation">

				<div id="owl-support" class="owl-carousel">
		            <div class="item">
		              <a href="">urgente</a>
		              <p class="description">
		              	Gia is est ent aut quatur sequam, volore simolest il inus, omnis ea quiaes etur, quae solenisquid magnis sitaeriberae pos dollorit velitas.
		              </p>
		            </div>
		            <div class="item">
		              <a href="">semaine</a>
		              <p class="description">
		              	Gia is est ent aut quatur sequam, volore simolest il inus, omnis ea quiaes etur, quae solenisquid magnis sitaeriberae pos dollorit velitas.
		              </p>
		            </div>
		             <div class="item">
		              <a href="">normal</a>
		              <p class="description">
		              	Gia is est ent aut quatur sequam, volore simolest il inus, omnis ea quiaes etur, quae solenisquid magnis sitaeriberae pos dollorit velitas.
		              </p>
		            </div>
		        </div>
				
			</div>
			<div class="question open">

				<div class="block_gauche">
					<div class="img"></div>
					<span class="titre">Ulleniamet mod quaeribus</span>
					<p>Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.Nem se net faccum fugiant, tem estrum saniam nobissit, officia volut etum aut il mil et officid ut faccus seni aligent aut eosam ratquam nis</p>
				</div>
				<div class="block_droit">
					<form action="" method="get" accept-charset="utf-8">
						<?php 
							$matieres = CriListMatieres();
							$imatieres = 0;
						 ?>
						<select name="question_matiere" id="question_matiere" placeholder="Domaine d'activité principal">
						<?php foreach($matieres as $id => $label): ?>
							<option <?php echo ($imatieres == 0) ? "selected" : "" ?> value="<?php echo $id ?>"><?php echo $label ?></option>
							<?php $imatieres++; ?>
						<?php endforeach; ?>
						</select>
						<?php $imatieres = 0; ?>
						<?php foreach($matieres as $id => $label): ?>
							<?php 
								$competences = CriCompetenceByMatiere($id);
							 ?>
							<select style="<?php echo ($imatieres == 0) ? "" : "display:none" ?>"  name="question_competence" id="question_competence_<?php echo $id ?>" placeholder="Sous domaine d'activité">
								<?php foreach ($competences as $cid => $clabel): ?>
									<option value="<?php echo $cid ?>"><?php echo $clabel ?></option>							
								<?php endforeach ?>
							</select>
							<?php $imatieres++; ?>
						<?php endforeach; ?>
						<input type="text" name="" value="" placeholder="Référence dossier dans mon étude">
						<textarea name="" placeholder="Votre question"></textarea>

						<input type="file" id="uploadFile" name="" value="" placeholder="Télécharger vos ducuments">
						<div class="fileUpload btn btn-primary">
						    <span>Parcourir</span>
						    <input type="file" class="upload" />
						</div>

						<div class="sep"></div>

						<input type="submit" name="Envoyer ma question" value="Envoyer ma question">
					</form>
				</div>
						
			</div>
		</div>
		
	</div>

	<div class="block_bottom">

		<ul class="block_3_mobile">
			<li class="js-home-block-link">
				<h2>
					<?php _e('Poser'); ?>
					<span><?php _e('une question par téléphone'); ?></span>
				</h2>
				<a href="#">+</a>
			</li>
			<li class="js-home-block-link">
				<h2>
					<?php _e('Demander'); ?>
					<span><?php _e('une documentation'); ?></span>
				</h2>
				<a href="#">+</a>
			</li>
			<li class="js-home-block-link">
				<h2>
					<?php _e('Prendre'); ?>
					<span><?php _e('un rendez-vous'); ?></span>
				</h2>
				<a href="#">+</a>
			</li>
		</ul>

		<div class="block_03">
			<div class="block consulter js-home-block-link">
				<div class="content">
					<h2>
						<?php _e('Poser'); ?>
						<span><?php _e('une question par téléphone'); ?></span>
					</h2>
					<a href="#">+</a>
				</div>						
			</div>

			<div class="block rechercher js-home-block-link">
				<div class="content">
					<h2>
						<?php _e('Demander'); ?>
						<span><?php _e('une documentation'); ?></span>
					</h2>
					<a href="#">+</a>
				</div>						
			</div>

			<div class="block acceder js-home-block-link">
				<div class="content">
					<h2>
						<?php _e('Prendre'); ?>
						<span><?php _e('un rendez-vous'); ?></span>
					</h2>
					<a href="#">+</a>
				</div>						
			</div>
		</div>
		
	</div>

</div>