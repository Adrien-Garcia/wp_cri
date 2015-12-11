<?php $notaire = CriNotaireData() ?>
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
                <?php if (!empty($notaire->etude->adress_1)): ?>
				<span><?php echo $notaire->etude->adress_1 ?></span>
                <?php endif; ?>
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
            <?php if (!empty($notaire->etude->tel) || !empty($notaire->etude->fax)): ?>
			<div class="contact">
                <?php if (!empty($notaire->etude->tel)): ?>
				<span>Tel <?php echo $notaire->etude->tel ?></span>
                <?php endif; ?>
                <?php if (!empty($notaire->etude->fax)): ?>
				<span>Fax <?php echo $notaire->etude->fax ?></span>
                <?php endif; ?>
			</div>
            <?php endif; ?>
		</div>
		<div class="notaire">
			<div class="nom">
				<span><?php echo $notaire->last_name ?> <?php echo $notaire->first_name ?></span>
			</div>
            <?php if (
                    !empty($notaire->adress_1) ||
                    !empty($notaire->adress_2) ||
                    !empty($notaire->adress_3) ||
                    !empty($notaire->cp) ||
                    !empty($notaire->city)): ?>
			<div class="adresse">

                <?php if (!empty($notaire->adress_1)): ?>
                    <span><?php echo $notaire->adress_1 ?></span>
                <?php endif; ?>
                <?php if (!empty($notaire->adress_2)): ?>
                    <span><?php echo $notaire->adress_2 ?></span>
                <?php endif ?>
                <?php if (!empty($notaire->adress_3)): ?>
                    <span><?php echo $notaire->adress_3 ?></span>
                <?php endif ?>
                    <span>
                        <?php echo (!empty($notaire->cp)) ? $notaire->cp : "" ; ?> - <?php echo (!empty($notaire->city)) ? $notaire->city : "" ; ?>
                    </span>
			</div>
            <?php endif;  ?>
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
	<form method="post" action="/notaires/<?php echo $notaire->id ?>/profil" class="form-centre-interet">
		<ul>
			<?php foreach($matieres as $key => $matiere): ?>
			<li>
				<label class="<?php echo ($matiere['subscribed']) ? ' select ' : ' unselect ' ?> js-account-profil-subscription-button">
					<input type="checkbox" id="" class=" js-account-profil-subscription" name="matieres[]" value="<?php echo $key ?>" <?php echo ($matiere['subscribed']) ? 'checked="checked"' : '' ?>>
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
            <?php if ($notaire->newsletter == 0 ):?>
            <?php _e('Vous n\'êtes pas inscrit à notre newsletter.'); ?>
            <?php else : ?>
            <?php _e('Vous êtes inscrit à notre newsletter selon vos centres d\'interets.'); ?>
            <?php endif; ?>
        </div>
        <form action="/notaires/<?php echo $notaire->id ?>/profil" method="post" accept-charset="utf-8" id="newsletterFormId1" class="form-newsletter">
            <input type="hidden" name="userEmail" value="<?php echo $notaire->email_adress ?>" id="userEmail" placeholder="<?php _e('Votre adresse email'); ?>">
            <input type="hidden" name="disabled" value="<?php echo $notaire->newsletter; ?>">
            <input type="submit" name="submit" value="<?php _e( ($notaire->newsletter == 0 ? "S'inscrire" : "Me désinscrire" ) ); ?>">
        </form>
        <div id="newsletterMsgId">
        </div>

</div>