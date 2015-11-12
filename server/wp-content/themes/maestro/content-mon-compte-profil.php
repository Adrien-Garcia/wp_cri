<?php $notaire = CriNotaireData() ?>
<?php // var_dump($notaire) ?>
<div class="mes-informations" id="sel-compte-profil">

	<h2>Mes informations</h2>

	<div class="img-profil">
	</div>
	<div class="coordonnees">
		<div class="etude">
			<div class="nom">
				<span><?php echo $notaire->etude->office_name ?></span>
			</div>
			<div class="adresse">
				<span><?php echo $notaire->etude->adress_1 ?></span>
				<?php if (!empty($notaire->etude->adress_2)): ?>
					<span><?php echo $notaire->etude->adress_2 ?></span>
				<?php endif ?>
				<?php if (!empty($notaire->etude->adress_3)): ?>
					<span><?php echo $notaire->etude->adress_3 ?></span>
				<?php endif ?>				
				<span><?php echo $notaire->etude->cp.' '.$notaire->etude->city ?></span>
			</div>
			<div class="mail">
				<span id="sel-compte-mail"><?php echo $notaire->etude->office_email_adress_1 ?></span>
			</div>
			<div class="contact">
				<span>Tel ?? <?php // echo $notaire->tel ?></span>
				<span>Fax ?? <?php //echo $notaire->tel ?></span>
			</div>			
		</div>
		<div class="notaire">
			<div class="nom">
				<span><?php echo $notaire->last_name ?> <?php echo $notaire->first_name ?></span>
			</div>
			<div class="adresse">
				<span>??</span>
				
				<span>CP - Ville ??</span>
			</div>
			<div class="mail">
				<span id="sel-compte-mail"><?php echo $notaire->email_adress ?></span>
			</div>
			<div class="contact">
				<?php if (!empty($notaire->tel_portable)): ?>
					<span>Mob  <a href="tel:<?php echo $notaire->tel_portable ?>"><?php echo $notaire->tel_portable ?></a></span>
				<?php endif ?>
				<?php if (!empty($notaire->tel)): ?>
				<span>Tel <a href="tel:<?php echo $notaire->tel ?>"><?php echo $notaire->tel ?></a></span>
				<?php endif ?>
				<?php if (!empty($notaire->fax)): ?>
					<span>Fax <?php echo $notaire->fax ?></span>
				<?php endif ?>
			</div>			
		</div>

		<?php // var_dump($notaire); ?>



		<!--div class="nom">
			<span><?php // echo $notaire->etude->office_name ?></span>
			<?php // echo $notaire->last_name ?> <?php // echo $notaire->first_name ?>
		</div>
		<div class="adresse">
			<span><?php // echo $notaire->etude->adress_1 ?></span>
			<?php // if (!empty($notaire->etude->adress_2)): ?>
				<span><?php // echo $notaire->etude->adress_2 ?></span>
			<?php // endif ?>
			<?php // if (!empty($notaire->etude->adress_3)): ?>
				<span><?php // echo $notaire->etude->adress_3 ?></span>
			<?php // endif ?>
			<span><?php // echo $notaire->etude->cp.' '.$notaire->etude->city ?></span>
		</div>
		<div class="contact">
			<span id="sel-compte-mail"><?php // echo $notaire->email_adress ?></span>
			<span><?php // echo $notaire->tel ?></span>
		</div!-->
	</div>
</div>

<div class="mes-centres-dinterets">

	<h2><?php _e('Mes centres d\'intérêts'); ?></h2>

	<div class="description">
		Epersped ulla con num quasint essimos dolut reium a ium aliquodis prestrum facepe pror modio.Nem se net faccum fugiant, tem estrum saniam nobissit, officia volut etum aut il mil et officid ut faccus seni aligent aut eosam ratquam nis.
	</div>
	<?php 
		$matieres = getMatieresByNotaire();
	 ?>
	<form method="post" action="/notaires/<?php echo $notaire->id ?>/profil">
		<ul>
			<?php foreach($matieres as $key => $matiere): ?>
			<li>
				<label class="<?php echo ($matiere['subscribed']) ? ' select ' : ' unselect ' ?>">
					<input type="checkbox" id="" class="" name="matieres[]" value="<?php echo $key ?>" <?php echo ($matiere['subscribed']) ? 'checked="checked"' : '' ?>>
					<?php echo $matiere['name'] ?>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
		<input type="submit" name="Valider" value="Valider" />
	</form>
</div>

<div class="newsletter">

	<h2><?php _e('Ma newsletter'); ?></h2>
	<div class="description">
		<?php _e('Vous êtes inscrit à notre newsletter selon vos centres d\'interets.'); ?>
	</div>
	<a href="#" title=""><?php _e('Me désinscrire'); ?></a>

</div>