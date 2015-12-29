<?php if (CriIsNotaire()) : ?>
<div id="layer-posez-question" style="display:none;">

	<form action="" id="questionFormId" method="post" enctype="multipart/form-data" class="js-question-form">
	<div class="block_top">

		<div class="titre">
			<span class="close_layer layer-posez-question_close"></span>
			<span class="texte"><?php _e('Poser une question'); ?></span>
		</div>
		
		<div class="onglets">
			<h2 class="consultation open js-question-button-consultation"><span><?php _e('1. Type de consultation'); ?></span></h2>   				
			<h2 class="question js-question-button-ma-question"><span><?php _e('2. Ma question'); ?></span></h2>
		</div>
		<div class="details">
			<div class="consultation js-question-tab-consultation open">
				<div class="">
					<?php 
						$supports = CriListSupport();
						// var_dump($supports)
					 ?>
				</div>
				<div id="owl-support" class="owl-carousel">
				<?php foreach ($supports as $key => $support): ?>
		            <div class="item">
		            	<input title="support hidden" id="support_<?php echo $support->id ?>" type="radio" name="question_support" value="<?php echo $support->id ?>" data-value="<?php echo $support->value ; ?>" class="hidden js-question-support-radio">
		              	<span class="label"><?php echo $support->label_front; ?></span>
		              	<p class="description">
		              		<?php echo $support->description; ?>
		              	</p>
		            </div>
		        <?php endforeach; ?>
		        </div>
				
			</div>
			<div class="question js-question-tab-ma-question">
				<div class="block_gauche">
					<div class="img"></div>
					<span class="titre">Ulleniamet mod quaeribus</span>
					<p>Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.Nem se net faccum fugiant, tem estrum saniam nobissit, officia volut etum aut il mil et officid ut faccus seni aligent aut eosam ratquam nis</p>
				</div>
				<div class="block_droit">
					<div class="form">
						<?php 
							$matieres = CriListMatieres();
						 ?>
						<select name="question_matiere" id="question_matiere" class="js-question-select-matiere" >
                            <option selected value="">Choisir une matière</option>
                            <?php foreach($matieres as $id => $label): ?>
                                <option value="<?php echo $id ?>"><?php echo $label ?></option>
                            <?php endforeach; ?>
						</select>
						<?php $icomp = 0; ?>
						<?php foreach($matieres as $id => $label): ?>
							<?php
								$competences = CriCompetenceByMatiere($id);
							 ?>
							<select class="js-question-select-competence <?php echo ($icomp == 0) ? "" : "hidden" ?>" data-matiere-id="<?php echo $id ?>" data-name="question_competence"  name="<?php echo ($icomp == 0) ? "question_competence" : "" ?>" id="question_competence_<?php echo $id ?>" >
                                <option selected value="">Choisir une compétence</option>
                                <?php foreach ($competences as $cid => $clabel): ?>
									<option value="<?php echo $cid ?>"><?php echo $clabel ?></option>							
								<?php endforeach ?>
							</select>
							<?php $icomp++; ?>
						<?php endforeach; ?>
						<input class="js-question-object" type="text" name="question_objet" id="question_objet" value="" placeholder="Objet de la question">
						<textarea class="js-question-message" name="question_message" id="question_message" placeholder="Votre question"></textarea>

                        <?php for ($i = 0; $i < 5; $i++) : ?>
						<div class="fileUpload btn btn-primary <?php echo ($i == 0) ? "" : "hidden"; ?>">
                            <span class="fileName js-file-name">Vide</span>
						    <button class="btn btn-primary btn-reset js-file-reset">+</button>
						    <input type="file" class="upload js-question-file"  id="question_fichier_<?php echo $i; ?>" name="question_fichier[]"  placeholder="Télécharger vos documents"/>

						    <span class="fileButtonFront">Attacher une pièce jointe</span>
                        </div>
                        <?php endfor; ?>

						<div class="sep"></div>
						<div id="msgBlockQuestionId" class="js-question-error"></div>
						<input class="js-question-submit" type="submit" name="Envoyer ma question" value="Envoyer ma question">
					    
                    </div>
					
				</div>
                <div style=" clear:both; overflow:hidden; visibility:hidden; height:1px; display:block; margin:0;">&nbsp;</div>
			</div>
		</div>
		
	</div>
	</form>

	<div class="block_bottom">

		<ul class="block_3_mobile">
			<li class="js-home-block-link">
				<h2>
					<?php _e('Poser'); ?>
					<span><?php _e('une question par téléphone'); ?></span>
				</h2>
				<a href="/poser-une-question-par-telephone/" target="_blank">+</a>
			</li>
			<li class="block demander js-question-documentation-button">
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
				<a href="mailto:visite@cridon-lyon.fr">+</a>
			</li>
		</ul>

		<div class="block_03">
			<div class="block poser js-home-block-link">
				<div class="content">
					<h2>
						<?php _e('Poser'); ?>
						<span><?php _e('une question par téléphone'); ?></span>
					</h2>
					<a href="/poser-une-question-par-telephone/" target="_blank">+</a>
				</div>						
			</div>

			<div class="block demander js-question-documentation-button">
				<div class="content">
					<h2>
						<?php _e('Demander'); ?>
						<span><?php _e('une documentation'); ?></span>
					</h2>
					<a href="#">+</a>
				</div>						
			</div>

			<div class="block prendre js-home-block-link">
				<div class="content">
					<h2>
						<?php _e('Prendre'); ?>
						<span><?php _e('un rendez-vous'); ?></span>
					</h2>
					<a href="mailto:visite@cridon-lyon.fr">+</a>
				</div>						
			</div>
		</div>
		
	</div>

</div>

<?php endif; ?>