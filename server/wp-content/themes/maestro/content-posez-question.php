<?php if (CriIsNotaire() && CriCanAccessSensitiveInfo(CONST_QUESTIONECRITES_ROLE)) : ?>
<div id="layer-posez-question" style="display:none;">

	<form action="" id="questionFormId" method="post" enctype="multipart/form-data" class="js-question-form">
	<div class="block_top">

		<div class="titre">
			<span class="close_layer layer-posez-question_close"></span>
			<span class="texte"><?php _e('Poser une question'); ?></span>
		</div>
		
		<div class="onglets">
			<div class="h2 open niveau-expertise js-question-button-expertise">1. <span><?php _e('Niveau d\'expertise'); ?></span></div>
			<div class="h2 consultation js-question-button-consultation">2. <span><?php _e('Delais / Support de réponse'); ?></span></div>
			<div class="h2 question js-question-button-ma-question">3. <span><?php _e('Ma question'); ?></span></div>

		</div>
		<div class="details">
			<div class="niveau-expertise js-question-tab-expertise open">
				<div class="">
					<?php
						$expertises = CriListExpertiseAll();
						// var_dump($supports)
					 ?>
				</div>
				<div id="owl-niveau-expertise" class="owl-carousel">
                    <?php foreach ($expertises as $data): ?>
		            <div class="item analytics_<?php echo $data->id ?>_question">
		            	<input title="niveau hidden" id="niveau-<?php echo $data->id ?>" type="radio" name="niveau-<?php echo $data->id ?>" value="niveau-<?php echo $data->id ?>" data-value="niveau-<?php echo $data->id ?>" class="hidden js-question-expertise-radio">

		              	<p class="description">
                            <?php echo $data->description; ?>
		              	</p>
		              	<a href="#" title="En savoir plus"><span><?php _e('En savoir plus'); ?></span></a>
		              	<span class="label"><?php echo $data->label_front; ?></span>
		            </div>
                    <?php endforeach; ?>

		        </div>

			</div>
			<div class="consultation js-question-tab-consultation">
				<div class="">

                    <div id="owl-support" class="owl-carousel">
                    <?php foreach ($expertises as $data) : ?>
                    <?php $supports = $data->supports; ?>
                        <?php foreach ($supports as $key => $support): ?>
                            <div class="item analytics_<?php echo $support->label_front; ?>_question">
                                <input title="support hidden" id="support_<?php echo $support->id ?>" type="radio" name="question_support" value="<?php echo $support->id ?>" data-value="<?php echo $support->value ; ?>" class="hidden js-question-support-radio">
                                <span class="label"><?php echo $support->label_front; ?></span>
                                <p class="description">
                                    <?php echo $support->description; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </div>
                </div>

            </div>
			<div class="question js-question-tab-ma-question">
				<div class="block_gauche">
					<div class="img"></div>
					<span class="titre">Votre consultation écrite</span>
					<p>Indiquez la matière principale, la compétence (facultative) en découlant, l'objet et le texte de votre question. Il vous est également possible d'insérer jusqu'à cinq documents annexes (copie de décision de tribunal, plan, etc.).</p>
				</div>
				<div class="block_droit">
					<div class="form">
						<?php 
							$matieres = CriListMatieres();
						 ?>
						<select name="question_matiere" id="question_matiere" class="js-question-select-matiere" >
                            <option selected value="">Choisir une matière</option>
                            <?php foreach($matieres as $id => $data): ?>
                                <?php
                                $label = $data['label'];
                                $code = $data['code'];
                                ?>
                                <option value="<?php echo $id ?>"><?php echo $label ?></option>
                            <?php endforeach; ?>
						</select>
						<?php $icomp = 0; ?>
						<?php foreach($matieres as $id => $data): ?>
							<?php
                                $label = $data['label'];
                                $code = $data['code'];
								$competences = CriCompetenceByMatiere($id);
							 ?>
							<select class="js-question-select-competence <?php echo ($icomp == 0) ? "" : "hidden" ?>" data-matiere-id="<?php echo $id ?>" data-name="question_competence"  name="<?php echo ($icomp == 0) ? "question_competence" : "" ?>" id="question_competence_<?php echo $id ?>" >
                                <option selected value="<?php echo $code; ?>">Choisir une compétence</option>
                                <?php foreach ($competences as $cid => $clabel): ?>
                                    <?php if ($cid != $code) : ?>
									<option value="<?php echo $cid ?>"><?php echo $clabel ?></option>
                                    <?php endif; ?>
                                <?php endforeach ?>
							</select>
							<?php $icomp++; ?>
						<?php endforeach; ?>
						<input class="js-question-object" type="text" name="question_objet" id="question_objet" value="" placeholder="Objet de la question / Références de dossier" maxlength="80">
						<textarea class="js-question-message" name="question_message" id="question_message" placeholder="Votre question"></textarea>

                        <?php for ($i = 0; $i < 5; $i++) : ?>
						<div class="fileUpload btn btn-primary js-file-hide <?php echo ($i == 0) ? "" : "hidden"; ?>">
                            <span class="fileName js-file-name">Vide</span>
						    <button class="btn btn-primary btn-reset js-file-reset">+</button>
						    <input type="file" class="upload js-question-file"  id="question_fichier_<?php echo $i; ?>" name="question_fichier[]"  placeholder="Télécharger vos documents"/>

						    <span class="fileButtonFront">Attacher une pièce jointe</span>
                        </div>
                        <?php endfor; ?>

						<div class="sep"></div>
						<div id="msgBlockQuestionId" class="js-question-error"></div>
						<input class="js-question-submit analytics_Envoyer_question" type="submit" name="Envoyer ma question" value="Envoyer ma question">
					    
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
				<div class="h2">
					<?php _e('Poser'); ?>
					<span><?php _e('une question par téléphone'); ?></span>
				</div>
				<a href="/poser-une-question-par-telephone/" target="_blank">+</a>
			</li>
			<li class="block demander js-question-documentation-button">
				<div class="h2">
					<?php _e('Demander'); ?>
					<span><?php _e('une documentation'); ?></span>
				</div>
				<a href="#">+</a>
			</li>
			<li class="js-home-block-link">
				<div class="h2">
					<?php _e('Prendre'); ?>
					<span><?php _e('un rendez-vous'); ?></span>
				</div>
				<a href="mailto:visite@cridon-lyon.fr">+</a>
			</li>
		</ul>

		<div class="block_03" id="effetLivreIE">
			<div class="block poser js-home-block-link">
				<div class="content">
					<div class="h2">
						<?php _e('Poser'); ?>
						<span><?php _e('une question par téléphone'); ?></span>
					</div>
					<a href="/poser-une-question-par-telephone/" target="_blank">+</a>
				</div>						
			</div>

			<div class="block demander js-question-documentation-button analytics_Demande_doc">
				<div class="content">
					<div class="h2">
						<?php _e('Demander'); ?>
						<span><?php _e('une documentation'); ?></span>
					</div>
					<a href="#">+</a>
				</div>						
			</div>

			<div class="block prendre js-home-block-link analytics_Demande_rdv">
				<div class="content">
					<div class="h2">
						<?php _e('Prendre'); ?>
						<span><?php _e('un rendez-vous'); ?></span>
					</div>
					<a href="mailto:visite@cridon-lyon.fr">+</a>
				</div>						
			</div>
		</div>
		
	</div>

</div>

<?php endif; ?>
