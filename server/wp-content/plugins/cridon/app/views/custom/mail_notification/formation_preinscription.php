<!-- DEMANDE DE PRE-INSCRIPTION -->
<h1>Votre demande de pré-inscription a bien été transmise au CRIDON LYON</h1>
<span>Vous serez prochainement recontacté par le CRIDON LYON pour finaliser votre inscription à la session choisie ci-dessous :</span>
<br /><br />



<span class="introduction" style="text-transform: uppercase;">le <?php echo $date ; ?></span><br/>
<?php if (!empty(trim($organisme))) : ?>
    <span class=""><i>Organisme</i> <?php echo $organisme ; ?></span><br />
    <span class=""><i>Horaire</i> : <?php echo $horaire ; ?></span><br />
    <span class=""><i>Lieu</i> : <?php echo $place ?></span>
<?php endif; ?>
<br /><br />

<?php if (!empty(trim($csn))) : ?>
     <img src="<?php echo plugins_url("../../public/images/mail/logo-CSN_2017.jpg", dirname(__FILE__)) ?>" height="40" width="40" alt="Cridon Lyon, partenaire expert du notaire" style="border:none" /><br />
    <span> Numéro csn : <?php echo $csn ; ?></span><br />
<?php endif; ?>
<i>Formation</i> : <span class="section"><?php echo $name ; ?></span><br />
<br />


<i>Nombre de participant(s)</i> : <?php echo $participants ; ?><br />
<!-- Fin -->