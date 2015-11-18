<h2>Edit Question</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('srenum',array('label' => 'N° question')); ?>
<?php echo $this->form->input('client_number',array('label' => 'Numéro de client')); ?>
<?php echo $this->form->input('sreccn',array('label' => 'Interlocuteur (Notaire)')); ?>
<?php echo $this->form->belongs_to_dropdown('Support', $aSupport, array('style' => 'width: 200px;', 'empty' => false)); ?>
<?php echo $this->form->belongs_to_dropdown('Competence', $aCompetence, array('style' => 'width: 200px;', 'empty' => false)); ?>
<?php echo $this->form->has_many_dropdown('Competences', $aCompetence, array('select_id'=>'competence_select_n','select_name'=>'competence_select_n','label'=> 'Autres compétences','style' => 'width: 200px;', 'empty' => true)); ?>
<?php echo $this->form->belongs_to_dropdown('Affectation', $aAffectation, array('style' => 'width: 200px;', 'empty' => false)); ?>
<?php echo $this->form->input('resume',array('label' => 'Objet de la question')); ?>
<?php echo $this->form->textarea_input('content',array('label' => 'Texte de la question')); ?>
<?php echo $this->form->input('juriste',array('label' => 'Juriste principal')); ?>
<?php echo $this->form->end('Update'); ?>
<?php
    if( !empty( $aObjectQuestion) ){
        echo '<label>Document question</label><a href="'.$aObjectQuestion[0]->download_url.'" title="Télécharger" target="_blank"><span class="dashicons dashicons-download"></span></a><br/>';
    }
    if( !empty( $aObjectAnswer) ){
        echo '<label>Document réponse</label><a href="'.$aObjectAnswer[0]->download_url.'" title="Télécharger" target="_blank"><span class="dashicons dashicons-download"></span></a>';
    }
?>