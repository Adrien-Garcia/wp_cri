<!-- DEMANDE DE PRE-INSCRIPTION -->
<h1>Demande de Pré-inscription</h1>
<br /><br />

<span class="s"><?php echo $notaire['crpcen']; ?>,</span> <span class="s"><?php echo $notaire['lname']; ?> <?php echo $notaire['fname']; ?></span>
    <a href="mailto:<?php echo $notaire['mail']; ?>"><?php echo $notaire['mail']; ?></a>
<br /><br />

<?php if (!empty(trim($csn))) : ?>
     <img src="<?php echo plugins_url("../../public/images/mail/logo-CSN_2017.jpg", dirname(__FILE__)) ?>" height="40" width="40" alt="Cridon Lyon, partenaire expert du notaire" style="border:none" /><br />
    <span> Numéro csn : <?php echo $csn ; ?></span><br />
<?php endif; ?>
Formation : <span class="section"><?php echo $name ; ?></span>
<br /><br />


<span class="introduction">le <?php echo $date ; ?></span><br/>
<?php if (!empty(trim($organisme))) : ?>
    <span class="">au <?php echo $organisme ; ?></span><br />
    <span class=""><i>Horaire</i> : <?php echo $horaire ; ?></span><br />
    <span class=""><i>Lieu</i> : <?php echo $place ?></span>
<?php endif; ?>
<br /><br />
<i>Nombre de participant(s)</i> : <?php echo $participants ; ?><br />
<!-- Fin -->