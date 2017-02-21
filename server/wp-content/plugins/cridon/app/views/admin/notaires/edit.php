<div class="mvc-title">
    <h2>Notaire</h2>
</div>
<div class="mvc-form">
    <?php
    /*
     * L'URL du formulaire est configuré au listing des notaires
     * Tous les inputs du formulaire seront tous des inputs textes et grisés.
     */
    ?>
    <?php echo $this->form->create($model->name,array('action'=>'index')); ?>
    <?php echo $this->form->input('category',array( 'type'=>'text','label' => 'Catégorie','disabled' => true )); ?>
    <?php echo $this->form->input('client_number',array( 'type'=>'text','label' => 'Numéro client','disabled' => true )); ?>
    <?php echo $this->form->input('crpcen',array( 'type'=>'text','label' => 'Numéro CRPCEN','disabled' => true )); ?>
    <?php echo $this->form->input('web_password',array( 'type'=>'text','label' => 'Mot de passe Web','disabled' => true )); ?>
    <?php echo $this->form->input('tel_password',array( 'type'=>'text','label' => 'Mot de passe Tel','disabled' => true )); ?>
    <?php
        $sigle = '';
        if( !empty( $object->entite ) && !empty( $object->entite->sigle ) ){
            $sigle = $object->entite->sigle->label;
        }
    ?>
    <?php
    /*
     * tel_password est utilisé pour afficher le Sigle.
     * Le champ Sigle n'existe pas dans le Notaire, du coup tel_password est utilisé pour générer l'input du formulaire.
     */
    ?>
    <?php echo $this->form->input('tel_password',array( 'type'=>'text','label' => 'Sigle','value' => $sigle,'disabled' => true )); ?>
    <?php
    $entite = '';
    if( !empty($object->entite)){
        $entite = $object->entite->office_name;
    }
    ?>
    <?php echo $this->form->input('crpcen',array( 'type'=>'text','label' => 'Nom de l\'office','value' => $entite,'disabled' => true )); ?>
    <?php echo $this->form->input('code_interlocuteur',array( 'type'=>'text','label' => 'Code interlocuteur','disabled' => true )); ?>
    <?php
        $civility = '';
        if( !empty( $object->civilite ) ){
            $civility = $object->civilite->label;
        }
    ?>
    <?php echo $this->form->input('id_civilite',array( 'type'=>'text','label' => 'Titre','value' => $civility,'disabled' => true )); ?>
    <?php echo $this->form->input('first_name',array( 'type'=>'text','label' => 'Nom','disabled' => true )); ?>
    <?php echo $this->form->input('last_name',array( 'type'=>'text','label' => 'Prénom','disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Email','disabled' => true )); ?>
    <?php echo $this->form->input('id_fonction',array( 'type'=>'text','label' => 'Fonction (ID)','disabled' => true)); ?>
    <?php
        $fonction = '';
        if( !empty( $object->fonction ) ){
            $fonction = $object->fonction->label;
        }
    ?>
    <?php echo $this->form->input('code_interlocuteur',array( 'type'=>'text','label' => 'Fonction (Texte)','value' => $fonction,'disabled' => true )); ?>
    <?php
        $adress_1 = $adress_2 = $adress_3 = $cp = $city = $office_email_adress_1 = $office_email_adress_2 = $office_email_adress_3 = '';
        if( !empty( $object->entite ) ){
            $adress_1 = $object->entite->adress_1;
            $adress_2 = $object->entite->adress_2;
            $adress_3 = $object->entite->adress_3;
            $cp = $object->entite->cp;
            $city = $object->entite->city;
            $office_email_adress_1 = $object->entite->office_email_adress_1;
            $office_email_adress_2 = $object->entite->office_email_adress_2;
            $office_email_adress_3 = $object->entite->office_email_adress_3;
        }
    ?>
    <?php
    /*
     * Le champ 'email_adress' est utilisé à chaque fois car adress_1,adress_2 ... n'appartient pas au modèle Notaire.
     * Les valeurs sont initialisés par celui du modèle Entite lié.
     */
    ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Ligne adresse 1','value'=>$adress_1,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Ligne adresse 2','value'=>$adress_2,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Ligne adresse 3','value'=>$adress_3,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'CP','value'=>$cp,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Ville','value'=>$city,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Email office','value'=>$office_email_adress_1,'disabled' => true )); ?>
    <?php echo $this->form->input('email_adress',array( 'type'=>'text','label' => 'Email 2 office','value'=>$office_email_adress_2,'disabled' => true)); ?>
    <?php echo $this->form->input('tel_portable',array( 'type'=>'text','label' => 'Email 3 office','value'=>$office_email_adress_3,'disabled' => true )); ?>
    <?php
        $date_modified = '';
        if( !empty($object->date_modified) ){
            $date = new DateTime( $object->date_modified );
            $date_modified = $date->format('d/m/Y');//Convertir en FR
        }
    ?>
    <?php echo $this->form->input('date_modified',array( 'type'=>'text','label' => 'Date de modification','value'=> $date_modified,'disabled' => true )); ?>
    <?php echo $this->form->end('Valider'); ?>
</div>
