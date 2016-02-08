<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['question']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name); ?>
    <?php echo $this->form->input('srenum',array('label' => 'N° question','disabled' => true)); ?>
    <?php echo $this->form->input('client_number',array('label' => 'Numéro de client','disabled' => true)); ?>
    <?php echo $this->form->input('sreccn',array('label' => 'Interlocuteur (Notaire)','disabled' => true)); ?>
    <?php echo $this->form->belongs_to_dropdown('Support', $aSupport, array('style' => 'width: 200px;', 'empty' => false,'disabled' => true)); ?>
    <?php echo $this->form->belongs_to_dropdown('Competence', $aCompetence, array('style' => 'width: 200px;', 'empty' => false,'disabled' => true)); ?>
    <?php echo $this->form->has_many_dropdown('Competences', $aCompetence, array('select_id'=>'competence_select_n','select_name'=>'competence_select_n','label'=> 'Autres compétences','style' => 'width: 200px;', 'empty' => true,'disabled' => true)); ?>
    <?php echo $this->form->input('id_affectation',array('label' => 'Code affectation','disabled' => true)); ?>
    <?php echo $this->form->input('resume',array('label' => 'Objet de la question')); ?>
    <?php echo $this->form->textarea_input('content',array('label' => 'Texte de la question','value' => $object->content)); ?>
    <?php echo $this->form->input('juriste',array('label' => 'Juriste principal','disabled' => true)); ?>
</div>