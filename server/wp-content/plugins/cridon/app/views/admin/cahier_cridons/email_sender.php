<h2>Envoi emails - <?php echo MvcInflector::titleize($model->name) ?></h2>
<br>
<br>
<strong>Envoyer un email de test :</strong><br>
<form method="post" action="<?php echo MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'email_sender'));?>">
    <label for="cridon_parent_id_test">ID du cahier parent (ou 'Cahier principal') à envoyer (colonne ID) :</label><br>
    <input id="cridon_parent_id_test" type="text" name="cahier_cridon_parent_id"><br>

    <label for="test_email">Email de test :</label><br>
    <input id="test_email" type="email" name="test_email"><br>

    <input type="hidden" name="send_to" value="email_test">

    <input type="submit" value="Envoyer">
</form>
<br>
------------------------------------------<br>
------------------------------------------<br>
------------------------------------------<br>
<p>/!\ ENVOI AUX NOTAIRES EN PRODUCTION /!\</p>

<strong>Envoyer l'email à tous les notaires :</strong><br>
<form method="post" action="<?php echo MvcRouter::admin_url(array('controller' => $this->name, 'action' => 'email_sender'));?>"">
    <label for="cahier_cridon_id_prod">ID du cahier parent (ou 'Cahier principal') à envoyer (colonne ID) :</label><br>
    <input id="cahier_cridon_id_prod" type="text" name="cahier_cridon_parent_id"><br>
    <input type="hidden" name="send_to" value="notaires">
    <input type="submit" value="Envoyer">
</form>



