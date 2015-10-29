<h2>Notaire</h2>
<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('category',array( 'label' => 'Catégorie' )); ?>
<?php echo $this->form->input('client_number',array( 'label' => 'Numéro client' )); ?>
<?php echo $this->form->input('crpcen',array( 'label' => 'Numéro CRPCEN' )); ?>
<?php
$login = '';

?>
<?php echo $this->form->input('last_name',array( 'label' => 'Identifiant','value' => $object->crpcen.'~'.$object->id_wp_user )); ?>
<?php echo $this->form->input('web_password',array( 'label' => 'Mot de passe Web' )); ?>
<?php echo $this->form->input('tel_password',array( 'label' => 'Mot de passe Tel' )); ?>
<?php
    $sigle = '';
    if( !empty( $object->etude ) && !empty( $object->etude->sigle ) ){
        $sigle = $object->etude->sigle->label;
    }
?>
<?php echo $this->form->input('tel_password',array( 'label' => 'Sigle','value' => $sigle )); ?>
<?php
$etude = '';
if( !empty($object->etude)){
    $etude = $object->etude->office_name;
}
?>
<?php echo $this->form->input('crpcen',array( 'label' => 'Nom de l\'office','value' => $etude )); ?>
<?php echo $this->form->input('code_interlocuteur',array( 'label' => 'Code interlocuteur' )); ?>
<?php
    $civility = '';
    if( !empty( $object->civilite ) ){
        $civility = $object->civilite->label;
    }
?>
<?php echo $this->form->input('id_civilite',array( 'label' => 'Titre','value' => $civility )); ?>
<?php echo $this->form->input('first_name',array( 'label' => 'Nom' )); ?>
<?php echo $this->form->input('last_name',array( 'label' => 'Prénom' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email' )); ?>
<?php echo $this->form->input('id_fonction',array( 'label' => 'Fonction (ID)' )); ?>
<?php
    $fonction = '';
    if( !empty( $object->fonction ) ){
        $fonction = $object->fonction->label;
    }
?>
<?php echo $this->form->input('code_interlocuteur',array( 'label' => 'Fonction (Texte)','value' => $fonction )); ?>
<?php
    $adress_1 = $adress_2 = $adress_3 = $cp = $city = $office_email_adress_1 = $office_email_adress_2 = $office_email_adress_3;
    if( !empty( $object->etude ) ){
        $adress_1 = $object->etude->adress_1;
        $adress_2 = $object->etude->adress_2;
        $adress_3 = $object->etude->adress_3;
        $cp = $object->etude->cp;
        $city = $object->etude->city;
        $office_email_adress_1 = $object->etude->office_email_adress_1;
        $office_email_adress_2 = $object->etude->office_email_adress_2;
        $office_email_adress_3 = $object->etude->office_email_adress_3;
    }
?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 1','value'=>$adress_1 )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 2','value'=>$adress_2 )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 3','value'=>$adress_3 )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'CP','value'=>$cp )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ville','value'=>$city )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email office','value'=>$office_email_adress_1 )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email 2 office','value'=>$office_email_adress_2)); ?>
<?php echo $this->form->input('tel_portable',array( 'label' => 'Email 3 office','value'=>$office_email_adress_3 )); ?>
<?php
    $date_modified = '';
    if( !empty($object->date_modified) ){
        $date = new DateTime( $object->date_modified );
        $date_modified = $date->format('d/m/Y');
    }
?>
<?php echo $this->form->input('date_modified',array( 'label' => 'Date de modification','value'=> $date_modified )); ?>