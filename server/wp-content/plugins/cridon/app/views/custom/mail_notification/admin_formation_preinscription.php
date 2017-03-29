<!-- DEMANDE DE PRE-INSCRIPTION -->
<h1>Demande de Pré-inscription</h1>
<br /><br />

<span class="s"><?php echo $notaire['crpcen']; ?>,</span> <span class="s"><?php echo $notaire['lname']; ?> <?php echo $notaire['fname']; ?></span>
    <a href="mailto:<?php echo $notaire['mail']; ?>"><?php echo $notaire['mail']; ?></a>
<br /><br /><br />
Formation : <span class="section"><?php echo $name ; ?></span> <br /><br />
<span class="newsletter_date">le <?php echo $date ; ?></span><br/>
<?php if (!empty(trim($organisme))) : ?>
<span class="introduction">au <?php echo $organisme ; ?></span><br />
<?php endif; ?>
<br />
<i>Nombre de participant(s)</i> : <?php echo $participants ; ?><br />
<!-- Fin -->