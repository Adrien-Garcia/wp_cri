<?php
    $questions = criRestoreQuestions();
    $answered = $questions->getAnswered();
    $pending = $questions->getPending();
?>
<div id="questions-attentes">
	<h2><?php _e('Mes questions en attentes'); ?></h2>

	<?php if(count($pending) != 0): ?>
	
	<ul>
        <?php foreach ($pending as $index => $question) : ?>
		<li>
            <?php
                $date = date_create_from_format('Y-m-d', $question->question->real_date);
                $wdate = date_create_from_format('Y-m-d', $question->question->wish_date);
                $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "00.00.0000";
                $sWdate =  $wdate ? date('d.m.Y', $wdate->getTimestamp()) : "00.00.0000";
            ?>
			<span class="date">Question du <?php echo $sDate ; ?></span>
			<span class="reponse">Réponse souhaitées le <?php echo $sWdate ; ?></span>
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
					<span class="id-question">N ° <?php echo $question->question->srenum ; ?></span>
				</li>
				<li class="pdf"></li>
				<li class="plusdedetails ">
					<span class="js-account-questions-more-button">plus de détails</span>
					<div class="details js-account-questions-more">
						<ul>
							<li>
								<span><?php echo $matiere->label ; ?></span>
								<span><?php echo $question->competence->label ; ?></span>
								<span><?php echo $question->question->resume ; ?></span>
								<ul>
                                    <?php
                                        //$docs = $questions->getDocuments('question', $question->question->id);
                                    //var_dump($docs);
                                    ?>
									<li><a href="" target="_blank">nomdufichier.pdf</a></li>
									<li><a href="" target="_blank">nomdufichier.pdf</a></li>
									<li><a href="" target="_blank">nomdufichier.pdf</a></li>
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
	<h2><?php _e('Historiques de mes questions'); ?></h2>

	<div class="filtres">
		<ul>
			<li> <a href="">Toutes mes questions</a></li>
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
                    <?php foreach($matieres as $id => $label): ?>
                        <option <?php echo ($imatieres == 0) ? "selected" : "" ?> value="<?php echo $id ?>"><?php echo $label ?></option>
                        <?php $imatieres++; ?>
                    <?php endforeach; ?>
				</select>
			</li>
		</ul>

	</div>

	<?php if(count($answered) != 0): ?>

	<ul>
        <?php foreach ($answered as $index => $question) : ?>
            <?php
            $date = date_create_from_format('Y-m-d', $question->question->real_date);
            $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "00.00.0000";
            $adate = date_create_from_format('Y-m-d', $question->question->date_modif);
            $sAdate = $adate ? date('d.m.Y', $adate->getTimestamp()) : "00.00.0000";
            ?>
        <li>
			<span class="date">Question du <?php echo $sDate ; ?></span>
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
					<span class="status">répondu le <?php echo $sAdate ; ?></span>
					<span class="person">par <?php echo $question->question->juriste ; ?></span>
				</li>
				<li>
					<span class="delai"><?php echo $question->support->label; ?></span>
				</li>
				<li>
					<span class="pts"><?php echo $question->support->value; ?> pts</span>
				</li>
				<li>
					<span class="id-question">N ° <?php echo $question->question->srenum; ?></span>
				</li>
				<li class="pdf">
					<a href="" class="pdf"></a>
				</li>
			</ul>
			<ul class="suite-complement">
				<li class="pdf">
					<a href="" class="pdf"><b>Suite</b> S123456789</a>
				</li>
				<li class="pdf">
					<a href="" class="pdf"><b>Complément</b> C123456789</a>
				</li>
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
