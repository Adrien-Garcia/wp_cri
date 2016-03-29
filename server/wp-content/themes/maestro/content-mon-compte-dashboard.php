<?php if (isset($messageError)) : ?>
    <div class="error"><?php echo $messageError ?></div>
<?php endif; ?>
<div class="mon-solde">
	<h2><?php _e('Mon solde'); ?></h2>
	<div class="solde-pts <?php echo ($notaire->solde >= 0) ? "" : "inactive"; ?>">
        <svg version="1.1" id="solde-circle" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="326px" height="326px" viewBox="0 0 326 326" enable-background="new 0 0 326 326" xml:space="preserve">
			<defs id="defs3337">
			<linearGradient y2="1" x2="0.5" y1="0" x1="0.5" id="gradient">
				<stop id="stop-gradient1" offset="0%" ></stop>
				<stop id="stop-gradient2" offset="100%" ></stop>
			</linearGradient>
			</defs>
			<g id="layer1" >
			    <path style="stroke:url(#gradient);"
			       	d="M 176,23 a163, 163,0,1,1,-1,0 l 1,0"
                      die="M 163,10 a140, 140,0,1,1,-1,0 l 1,0"
			       	id="solde-circle-path" ></path>
			</g>
		</svg>
		<div class="point sel-solde-data" id="js-solde-data" data-solde="<?php echo ($notaire->solde >= 0) ? $notaire->solde : "0"; ?>" data-solde-max="<?php echo $notaire->quota ?>">
			<div class="pts" >
                <?php echo $notaire->solde >= 0 ? $notaire->solde : 0; ?> <span>pt<?php echo ($notaire->solde < 2 && $notaire->solde > -2) ? "" : "s" ?></span>
			</div>
			<span>quota <?php echo $notaire->quota ?></span>
		</div>									
	</div>
	
	<div class="consomation">
		<div class="pts">
			<span class="pts"><?php echo $notaire->pointConsomme ?></span>
			<span class="texte"><?php _e('points consommés'); ?></span>
			<span class="date">au <?php echo $notaire->date ?></span>
		</div>							
	</div>

	<div class="appel-courrier">
		<div class="appel">
			<span><?php echo $notaire->nbAppel ?></span> <?php _e('appels'); ?>
		</div>
		<div class="courrier">
			<span><?php echo $notaire->nbCourrier ?></span> <?php _e('courriers'); ?>
		</div>
	</div>

	
</div>
<div class="mes-questions">
	<h2><?php _e('Mes dernières questions'); ?></h2>

    <?php $i = 0; ?>
    <ul>
        <?php foreach($questions as $question) : ?>
            <?php if ( $i >= 3 ) { break; } ?>
        <?php $pending = ($question->id_affectation < CONST_QUEST_ANSWERED); ?>
            <li class="js-home-block-link js-account-questions-button">
                <?php
                $date = date_create_from_format('Y-m-d', $question->creation_date);
                $sDate = $date ? date('d.m.Y', $date->getTimestamp()) : "";
                ?>
                <a href="<?php get_home_url() ?>/notaires/<?php echo $notaire->id ; ?>/questions">
                    <?php if (! empty($sDate)) : ?>
                        <span class="date">Question du <?php echo $sDate ; ?></span>
                    <?php endif; ?>
                </a> <!-- Lien vers la page liste des question avec une ancre sur la question cliqué !-->
                <ul>
                    <li>
                        <?php
                        $matiere = $question->matiere;
                        ?>
                        <img width="30" height="30" src="<?php echo $matiere->picto ; ?>" alt="<?php echo $matiere->short_label ; ?>">
                    </li>
                    <li>
                        <span class="matiere"><?php echo $matiere->label ; ?></span>
                        <?php
                        if ( !empty($question->content) ) {
                            $resume = wp_trim_words($question->content, 18 );
                        } else {
                            $resume = wp_trim_words($question->resume, 18 );
                        }
                        $resume = stripslashes($resume);
                        ?>
                        <p><?php echo $resume ; ?></p>
                    </li>
                    <li>
                        <span class="<?php echo $pending ? 'en-cours' : 'repondu' ?>"><?php echo Config::$labelAffection[$question->id_affectation] ?></span>
                    </li>
                </ul>
            </li>
            <?php $i++; ?>
        <?php endforeach; ?>

	</ul> 

	<a class="js-account-questions-button" href="<?php get_home_url() ?>/notaires/<?php echo $notaire->id ; ?>/questions">Toutes mes questions</a>


</div>

