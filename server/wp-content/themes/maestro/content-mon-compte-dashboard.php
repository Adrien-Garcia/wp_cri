<div class="mon-solde">
	<h2><?php _e('Mon solde'); ?></h2>
    <?php $notaire = CriNotaireData(); ?>
	<div class="solde-pts">
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
			       	id="solde-circle-path" ></path>
			</g>
		</svg>

		<div class="point sel-solde-data" id="js-solde-data" data-solde="<?php echo ($notaire->solde >= 0) ? $notaire->solde : "0"; ?>" data-solde-max="<?php echo $notaire->quota ?>">
			<div class="pts" >
                <?php echo $notaire->solde ?> <span>pt<?php echo ($notaire->solde < 2 && $notaire->solde > -2) ? "" : "s" ?></span>
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

	<ul>
		<li class="js-home-block-link">
			<a href="#"><span class="date">Question du 09.10.2015</span></a> <!-- Lien vers la page liste des question avec une ancre sur la question cliqué !-->
			<ul>
				<li>					
					<img src="" alt="">
				</li>
				<li>
					<span class="matiere">Droit social</span>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas lobortis accumsan nisi, non molestie sem fringilla non. Proin tempus lacus eget nisi accumsan, nec dapibus quam pulvinar.</p>
				</li>
				<li>
					<span class="en-cours">en cours</span>
					<span class="repondu">répondu</span>
				</li>
			</ul>
		</li>
		<li class="js-home-block-link">
			<a href="#"><span class="date">Question du 09.10.2015</span></a>
			<ul>
				<li>					
					<img src="" alt="">
				</li>
				<li>
					<span class="matiere">Droit social</span>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas lobortis accumsan nisi, non molestie sem fringilla non. Proin tempus lacus eget nisi accumsan, nec dapibus quam pulvinar.</p>
				</li>
				<li>
					<span class="en-cours">en cours</span>
					<span class="repondu">répondu</span>
				</li>
			</ul>
		</li>
		<li class="js-home-block-link">
			<a href="#"><span class="date">Question du 09.10.2015</span></a>
			<ul>
				<li>					
					<img src="" alt="">
				</li>
				<li>
					<span class="matiere">Droit social</span>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas lobortis accumsan nisi, non molestie sem fringilla non. Proin tempus lacus eget nisi accumsan, nec dapibus quam pulvinar.</p>
				</li>
				<li>
					<span class="en-cours">en cours</span>
					<span class="repondu">répondu</span>
				</li>
			</ul>
		</li>
	</ul> 

	<a href="#">Toutes mes questions</a>


</div>

