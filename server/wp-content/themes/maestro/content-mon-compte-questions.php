<?php
    $questions = criRestoreQuestions();
    $answered = $questions->getAnswered();
    $pending = $questions->getPending();
?>
<?php $notaire = CriNotaireData(); ?>
<div id="questions-attentes">
	<h2><?php _e('Mes questions en attente'); ?></h2>

	<?php if(count($pending) != 0): ?>
	
	<ul>
        <?php foreach ($pending as $index => $question) : ?>
		<li>
            <?php
                $date = date_create_from_format('Y-m-d', $question->question->creation_date);
                $wdate = date_create_from_format('Y-m-d', $question->question->wish_date);
                $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "";
                $sWdate =  $wdate ? date('d.m.Y', $wdate->getTimestamp()) : "";
            ?>
            <?php if (! empty($sDate)) : ?>
			<span class="date">Question du <?php echo $sDate ; ?></span>
            <?php endif; ?>
            <?php if (! empty($sWdate)) : ?>
            <span class="reponse">Réponse souhaitées le <?php echo $sWdate ; ?></span>
            <?php endif; ?>

            <ul>
                <?php
                    $matiere = $question->matiere;
                ?>
				<li>
					<img src="<?php echo $matiere->picto; ?>" alt="<?php echo $matiere->short_label ; ?>">
				</li>
				<li>
					<span class="matiere"><?php echo $matiere->label ; ?></span>
                    <?php
                        if ( !empty($question->question->content) ) {
                            $resume = wp_trim_words($question->question->content, 18 );
                        } else {
                            $resume = wp_trim_words($question->question->resume, 18 );
                        }
                    ?>
					<p><?php echo $resume ; ?></p>
				</li>
				<li>
                    <?php
                        $status = "En cours de traitement";
                    ?>
					<span class="status"><?php echo $status ; ?></span>
				</li>
				<li>
					<span class="delai"><?php echo $question->support->label; ?></span>
				</li>
				<li>
                    <?php if (! empty($question->support->value) ) : ?>
					<span class="pts"><?php echo $question->support->value; ?> pts</span>
                    <?php endif; ?>
                </li>
				<li>
                    <?php if (! empty($question->question->srenum)) : ?>
					<span class="id-question">N ° <?php echo $question->question->srenum ; ?></span>
                    <?php endif; ?>
                </li>
				<li class="pdf"></li>
				<li class="plusdedetails ">
					<span class="js-account-questions-more-button"><span>plus de détails</span></span>
					<div class="details js-account-questions-more">
						<ul>
							<li>
								<span><?php echo $matiere->label ; ?></span>
								<span><?php echo $question->competence->label ; ?></span>
								<span><?php echo $question->question->resume ; ?></span>
								<ul>
                                <?php
                                    foreach($question->documents as $document):
                                        if( ($document->label != 'Suite') &&  ($document->label != 'Complément') ):
                                ?>
                                    <?php
                                    $options = array(
                                        'controller' => 'documents',
                                        'action'     => 'download',
                                        'id'         => $document->id
                                    );
                                    $publicUrl  = MvcRouter::public_url($options);
                                    ?>
									<li><a href="<?php echo $publicUrl ?>" target="_blank"><?php echo $document->name ?></a></li>
                                                                    
                                <?php
                                        endif;
                                    endforeach;
                                ?>
								</ul>
							</li>

                            <?php if ( !empty($question->question->content) ) : ?>
                                <li>
                                    <span>Votre question</span>
                                    <?php echo $question->question->content ; ?>
                                </li>
                            <?php endif; ?>
						</ul>

					</div>

				</li>
			</ul>
		</li>
        <?php endforeach; ?>

	</ul>
<?php else:  ?>
Vous n'avez actuellement aucune question en attente de réponse. 
<?php endif; ?>
</div>

<div id="historique-questions">
	<h2><?php _e('Historique de mes questions'); ?></h2>

	<div class="filtres">
		<ul>
			<li> <a href="<?php get_home_url() ?>/notaires/<?php echo $notaire->id ; ?>/questions">Toutes mes questions</a></li>
			<li>
				<span class="titre">Période :</span>
				<p class="du">Du <input type="date" id="datefrom" class="datepicker"></p>
				<p class="au">Au <input type="date" id="dateto" class="datepicker"></p>
			</li>
			<li>
				<span class="titre">Matière :</span>
                <?php
                $matieres = CriListMatieres();
                ?>
				<select name="">
					<option value="">Selectionnez une matière</option>
                    <?php foreach($matieres as $id => $data): ?>
                        <?php
                        $label = $data['label'];
                        ?>
                        <option <?php echo ($imatieres == 0) ? "selected" : "" ?> value="<?php echo $id ?>"><?php echo $label ?></option>
                        <?php $imatieres++; ?>
                    <?php endforeach; ?>
				</select>
			</li>
		</ul>

	</div>

	<?php if(count($answered) != 0): ?>

	<ul>
        <?php $juristes = QuestionEntity::getJuristeAndAssistantFromQuestions($answered) ?>
        <?php foreach ($answered as $index => $question) : ?>

            <?php
            $date = date_create_from_format('Y-m-d', $question->question->creation_date);
            $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "";
            $adate = date_create_from_format('Y-m-d', $question->question->date_modif);
            $sAdate = $adate ? date('d.m.Y', $adate->getTimestamp()) : "";
            ?>
        <li>
            <?php if (! empty($sDate)) : ?>
			<span class="date">Question du <?php echo $sDate ; ?></span>
            <?php endif; ?>

            <ul>
                <?php
                $matiere = $question->matiere;
                ?>
				<li>
                    <img src="<?php echo $matiere->picto; ?>" alt="<?php echo $matiere->short_label ; ?>">
                </li>
				<li>
                    <span class="matiere"><?php echo $matiere->label ; ?></span>
                    <?php
                    if ( !empty($question->question->content) ) {
                        $resume = wp_trim_words($question->question->content, 18 );
                    } else {
                        $resume = wp_trim_words($question->question->resume, 18 );
                    }
                    ?>
                    <p><?php echo $resume ; ?></p>
                </li>
				<li>
					<!--span class="answer">répondu</span!-->
                    <?php if (! empty($sAdate)) : ?>
					<span class="status">répondu le <?php echo $sAdate ; ?></span>
                    <?php endif; ?>
                    <span class="person">par <?php echo $juristes[$question->question->id]->juriste_name != null ? $juristes[$question->question->id]->juriste_name : $juristes[$question->question->id]->juriste_code ?></span>
				</li>
				<li>
					<span class="delai"><?php echo $question->support->label; ?></span>
				</li>
				<li>
					<span class="pts"><?php echo $question->support->value; ?> pts</span>
				</li>
				<li>
                    <?php if (! empty($question->question->srenum)) : ?>
                    <span class="id-question">N ° <?php echo $question->question->srenum; ?></span>
                    <?php endif; ?>

				</li>
                <li class="pdf">
                    <?php
                        $documents = $question->documents;
                        $ds = array() ;
                        foreach($documents as $d) {
                            if (!array_key_exists($d->id, $ds)) {
                                $ds[$d->id] = $d;
                            }
                        }
                    ?>
                    <?php foreach($ds as $document): ?>
                        <?php if( !($document->label == 'Suite') && !($document->label == 'Complément') ): ?>
                            <?php
                            $options = array(
                                'controller' => 'documents',
                                'action'     => 'download',
                                'id'         => $document->id
                            );
                            $publicUrl  = MvcRouter::public_url($options);
                            ?>
                            <a href="<?php echo $publicUrl ?>" class="pdf" title="Télécharger le document de Question/Réponse"></a>
                        <?php endif ?>
                    <?php endforeach ?>
                </li>
            </ul>
            <ul class="suite-complement">
                <?php
                usort($ds, function($a, $b)
                {
                    if ($a->label == 'Suite' && $b->label == 'Complément') {
                        return -1;
                    } else if ($a->label == 'Complément' && $b->label == 'Suite') {
                        return 1;
                    } else {
                        return 0;
                    }
                });
                ?>
                <?php foreach($ds as $document): ?>
                    <?php if( ($document->label == 'Suite')|| ($document->label == 'Complément') ): ?>
                        <li class="pdf">
                            <?php
                            $options = array(
                                'controller' => 'documents',
                                'action'     => 'download',
                                'id'         => $document->id
                            );
                            $publicUrl  = MvcRouter::public_url($options);
                            $code = $document->name;
                            $code = preg_replace("/[\d]+_([^\.]+)\.pdf/i", "$1", $code);
                            ?>
                            <a href="<?php echo $publicUrl ?>" class="pdf" title="Télécharger le document de <?php echo $document->label ?>"><b><?php echo $document->label ?></b> <?php echo $code ?></a>

                        </li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
        </li>
		<?php endforeach; ?>
	</ul>
	<?php else:  ?>
	Vous n'avez pas encore posé de questions 
	<?php endif; ?>
	<div style="clear:both;"></div>
    <div class="pagination <?php echo (isset($is_ajax) && is_ajax == true) ? "js-account-ajax-pagination" : ""; ?>">
        <?php echo $questions->getPagination() ?>
    </div>

</div>
