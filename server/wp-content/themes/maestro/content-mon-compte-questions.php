<div id="questions-attentes">
	<h2><span><?php _e('Mes questions'); ?></span>
        <span><?php _e('en attente'); ?></span>
    </h2>

	<?php if($pending): ?>
	<ul>
        <?php foreach ($pending as $index => $question) : ?>
		<li class="<?php if ($question->id_affectation == 1) : echo 'distribution';
                     elseif ($question->id_affectation == 2) : echo 'traitement';
                     elseif ($question->id_affectation == 3) : echo 'suspendue';
                     endif; ?> ">
            <?php
                $date = date_create_from_format('Y-m-d', $question->creation_date);
                $wdate = date_create_from_format('Y-m-d', $question->wish_date);
                $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "";
                $sWdate =  $wdate ? date('d.m.Y', $wdate->getTimestamp()) : "";
            ?>
            <?php if (! empty($sDate)) : ?>
			<span class="date">Question du <?php echo $sDate ; ?></span>
            <?php endif; ?>
            <!-- <?php if (! empty($sWdate)) : ?>
            <span class="reponse">Réponse estimée le <?php echo $sWdate ; ?></span>
            <?php endif; ?> -->

            <ul>
                <?php
                    $matiere = $question->matiere;
                ?>
				<li>
                    <?php if (!empty($matiere->picto)): ?>
					    <img src="<?php echo $matiere->picto; ?>" alt="<?php echo $matiere->short_label ; ?>">
                    <?php endif; ?>
				</li>
				<li>
                    <?php $trimMatiereLabel = trim($matiere->label) ?>
                    <?php if (!empty($trimMatiereLabel)): ?>
					    <span class="matiere"><?php echo $matiere->label ; ?></span>
                    <?php endif; ?>
                    <?php
                        if ( !empty($question->content) ) {
                            $resume = wp_trim_words($question->content, 18 );
                        } else {
                            $resume = wp_trim_words($question->resume, 18 );
                        }
                    ?>
                    <?php if (! empty($resume)) : ?>
					<p><?php echo html_entity_decode(stripslashes( $resume )) ; ?></p>
                    <?php endif; ?>
				</li>
				<li>
                    <?php
                        $status = isset(Config::$labelAffection[$question->id_affectation]) ? Config::$labelAffection[$question->id_affectation] : "Status indisponible";
                    if ( ($question->id_affectation == 2 || $question->id_affectation == CONST_QUEST_ANSWERED)
                        && $juristesPending[$question->id]->juriste_code != null
                    ) {
                        $status .= '<span class="person">par ' . ($juristesPending[$question->id]->juriste_name != null ? $juristesPending[$question->id]->juriste_name : $juristesPending[$question->id]->juriste_code) . '</span>';
                    }
                    ?>


                    <span class="status"><span><?php echo $status ; ?></span></span>
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
                    <?php if (! empty($question->srenum)) : ?>
					<span class="id-question">N ° <?php echo $question->srenum ; ?></span>
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
								<span><?php echo html_entity_decode(stripslashes( $question->resume )) ; ?></span>
								<ul>
                                <?php
                                    $docs = array();
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
									<li><a href="<?php echo $publicUrl ?>" target="_blank"><?php echo html_entity_decode($document->name) ?></a></li>

                                <?php
                                        endif;
                                    endforeach;
                                ?>
								</ul>
							</li>

                            <?php if ( !empty($question->content) ) : ?>
                                <li>
                                    <span>Votre question</span>
                                    <?php echo html_entity_decode(stripslashes( $question->content )) ; ?>
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
	<h2>
        <span><?php _e('Historique de'); ?></span>
        <span><?php _e('mes questions'); ?></span>
    </h2>
    <form class="js-account-form-filter" action="<?php get_home_url() ?>/notaires/questions#historique-questions">

        <div class="filtres">
            <ul>
                <li> <a href="<?php get_home_url() ?>/notaires/questions/#historique-questions">Toutes mes questions</a></li>
                <li>
                    <span class="titre">Période :</span>
                    <div class="du">
                        <label>Du</label>
                        <input name="d1" type="text" id="datefrom" class="datepicker js-account-du-filter" value="<?php echo $_GET['d1'] ; ?>" />
                    </div>
                    <div class="au">
                        <label>Au</label>
                        <input name="d2" type="text" id="dateto" class="datepicker js-account-au-filter" value="<?php echo $_GET['d2'] ; ?>" />
                    </div>
                </li>
                <li>
                    <span class="titre">Matière :</span>
                    <select name="m" class="js-account-matiere-filter">
                        <option value="" <?php echo empty($_GET['m']) ? "selected" : ""; ?>>Selectionnez une matière</option>
                        <?php foreach($matieres as $id => $data): ?>
                            <?php
                            $label = $data['label'];
                            ?>
                            <option value="<?php echo $id ?>" <?php echo (!empty($_GET['m']) && $_GET['m'] == $id) ? "selected" : ""; ?>><?php echo $label ?></option>

                        <?php endforeach; ?>
                    </select>
                </li>
            </ul>
        </div>
    </form>

	<?php if($answered): ?>

	<ul>
        <?php foreach ($answered as $index => $question) : ?>

            <?php
            $date = date_create_from_format('Y-m-d', $question->creation_date);
            $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "";
            $adate = date_create_from_format('Y-m-d', $question->date_modif);
            $sAdate = $adate ? date('d.m.Y', $adate->getTimestamp()) : "";
            ?>
        <li class="repondue">
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
                    if ( !empty($question->content) ) {
                        $resume = wp_trim_words($question->content, 18 );
                    } else {
                        $resume = wp_trim_words($question->resume, 18 );
                    }
                    ?>
                    <p><?php echo html_entity_decode(stripslashes($resume)) ; ?></p>
                </li>
				<li>
					<!--span class="answer">répondu</span!-->
                    <?php if (! empty($sAdate)) : ?>
					<span class="status"><?php echo Config::$labelAffection[CONST_QUEST_ANSWERED] ?> le <?php echo $sAdate ; ?></span>
                    <?php endif; ?>
                    <span class="person">par <?php echo $juristesAnswered[$question->id]->juriste_name != null ? $juristesAnswered[$question->id]->juriste_name : $juristesAnswered[$question->id]->juriste_code ?></span>
				</li>
				<li>
					<span class="delai"><?php echo $question->support->label; ?></span>
				</li>
				<li>
					<span class="pts"><?php echo $question->support->value; ?> pts</span>
				</li>
				<li>
                    <?php if (! empty($question->srenum)) : ?>
                    <span class="id-question">N ° <?php echo $question->srenum; ?></span>
                    <?php endif; ?>

				</li>
                <li class="pdf">
                    <?php
                        $documents = $question->documents;
                    ?>
                    <?php foreach($documents as $document): ?>
                        <?php if( !($document->label == 'Suite') && !($document->label == 'Complément') && !($document->label == 'Archive')): ?>
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

            
                <?php
                usort($documents, function($a, $b)
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

                <!-- Si on a au moins 2 documents, on aura : le document du cridon de la réponse + au moins une suite ou complément. -->
                <?php if (count($documents) >= 2) : ?>
                <ul class="suite-complement">


                <?php foreach($documents as $document): ?>
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
                            <a href="<?php echo $publicUrl ?>" class="pdf" title="Télécharger le document de <?php echo html_entity_decode($document->label) ?>"><b><?php echo html_entity_decode($document->label) ?></b> <?php echo $code ?></a>

                        </li>
                    <?php endif ?>
                <?php endforeach ?>

                </ul>
            <?php endif; ?>


        </li>
		<?php endforeach; ?>
	</ul>
	<?php else:  ?>
	Votre recherche n'a pas produit de résultats ou vous n'avez pas encore posé de questions
	<?php endif; ?>
	<div style="clear:both;"></div>
    <div class="pagination <?php echo (isset($is_ajax) && $is_ajax == true) ? "js-account-ajax-pagination" : ""; ?>">
        <?php // echo $questions->getPagination()
        echo $controller->pagination();
        ?>
    </div>

    <div class="legende">
        <ul>
            <li class="distribution">En cours de distribution</li>
            <li class="traitement">En cours de traitement</li>
            <li class="repondue">Répondue</li>
            <li class="suspendue">Suspendue</li>
        </ul>        
    </div>

</div>
