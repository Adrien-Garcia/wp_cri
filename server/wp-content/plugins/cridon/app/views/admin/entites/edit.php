<div class="mvc-title">
    <h2>Entites</h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name,array('action'=>'index')); ?>
    <?php echo $this->form->input('is_cridon',array( 'type'=>'text','label' => 'Est le Cridon','disabled' => true )); ?>
    <?php echo $this->form->input('is_organisme',array( 'type'=>'text','label' => 'Est un organisme de formation','disabled' => true )); ?>
    <?php echo $this->form->input('crpcen',array( 'type'=>'text','label' => 'Numéro CRPCEN','disabled' => true )); ?>
    <?php echo $this->form->input('office_name',array( 'type'=>'text','label' => 'Nom de l\'étude','disabled' => true )); ?>
    <?php echo $this->form->input('adress_1',array( 'type'=>'text','label' => 'Adresse 1','disabled' => true )); ?>
    <?php echo $this->form->input('adress_2',array( 'type'=>'text','label' => 'Adresse 2','disabled' => true )); ?>
    <?php echo $this->form->input('adress_3',array( 'type'=>'text','label' => 'Adresse 3','disabled' => true )); ?>
    <?php echo $this->form->input('cp',array( 'type'=>'text','label' => 'Code postal','disabled' => true )); ?>
    <?php echo $this->form->input('city',array( 'type'=>'text','label' => 'Ville','disabled' => true )); ?>
    <?php echo $this->form->input('office_email_adress_1',array( 'type'=>'text','label' => 'Email 1','disabled' => true )); ?>
    <?php echo $this->form->input('office_email_adress_2',array( 'type'=>'text','label' => 'Email 2','disabled' => true )); ?>
    <?php echo $this->form->input('office_email_adress_3',array( 'type'=>'text','label' => 'Email 3','disabled' => true )); ?>
    <?php echo $this->form->input('tel',array( 'type'=>'text','label' => 'Téléphone','disabled' => true )); ?>
    <?php echo $this->form->input('fax',array( 'type'=>'text','label' => 'Fax','disabled' => true )); ?>
    <?php echo $this->form->input('subscription_level',array( 'type'=>'text','label' => 'Niveau cridonline','disabled' => true )); ?>
    <?php echo $this->form->input('next_subscription_level',array( 'type'=>'text','label' => 'Prochain niveau de souscription','disabled' => true )); ?>
    <?php echo $this->form->input('start_subscription_date',array( 'type'=>'text','label' => 'Date de début de souscription','disabled' => true )); ?>
    <?php echo $this->form->input('end_subscription_date',array( 'type'=>'text','label' => 'Date de fin de souscription','disabled' => true )); ?>
    <?php echo $this->form->input('echeance_subscription_date',array( 'type'=>'text','label' => 'Date de d\'échéance souscription','disabled' => true )); ?>
    <?php echo $this->form->input('subscription_price',array( 'type'=>'text','label' => 'Prix de la souscription','disabled' => true )); ?>
    <?php echo $this->form->input('id_sepa',array( 'type'=>'text','label' => 'id SEPA','disabled' => true )); ?>
    <?php echo $this->form->input('offre_promo',array( 'type'=>'text','label' => 'Offre promo souscrite','disabled' => true )); ?>
    <?php echo $this->form->input('code_promo_offre_choc',array( 'type'=>'text','label' => 'Code promo choc','disabled' => true )); ?>
    <?php echo $this->form->input('code_promo_offre_privilege',array( 'type'=>'text','label' => 'Code promo privilège','disabled' => true )); ?>
</div>
