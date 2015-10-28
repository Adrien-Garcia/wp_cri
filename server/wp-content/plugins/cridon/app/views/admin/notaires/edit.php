<h2>Notaire</h2>
<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('category',array( 'label' => 'Catégorie' )); ?>
<?php echo $this->form->input('client_number',array( 'label' => 'Numéro client' )); ?>
<?php echo $this->form->input('crpcen',array( 'label' => 'Numéro CRPCEN' )); ?>
<?php echo $this->form->input('last_name',array( 'label' => 'Identifiant' )); ?>
<?php echo $this->form->input('web_password',array( 'label' => 'Mot de passe Web' )); ?>
<?php echo $this->form->input('tel_password',array( 'label' => 'Mot de passe Tel' )); ?>
<?php echo $this->form->input('tel_password',array( 'label' => 'Sigle' )); ?>
<?php echo $this->form->belongs_to_dropdown('Etude',$etudes,array( 'label' => 'Nom de l\'office' )); ?>
<?php echo $this->form->input('code_interlocuteur',array( 'label' => 'Code interlocuteur' )); ?>
<?php echo $this->form->input('code_interlocuteur',array( 'label' => 'Titre' )); ?>
<?php echo $this->form->input('first_name',array( 'label' => 'Nom' )); ?>
<?php echo $this->form->input('last_name',array( 'label' => 'Prénom' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email' )); ?>
<?php echo $this->form->input('id_fonction',array( 'label' => 'Fonction (ID)' )); ?>
<?php echo $this->form->input('code_interlocuteur',array( 'label' => 'Fonction (Texte)' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 1' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 2' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ligne adresse 3' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'CP' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Ville' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email office' )); ?>
<?php echo $this->form->input('email_adress',array( 'label' => 'Email 2 office')); ?>
<?php echo $this->form->input('tel_portable',array( 'label' => 'Email 3 office' )); ?>
<?php echo $this->form->input('tel_portable',array( 'label' => 'Date de modification' )); ?>