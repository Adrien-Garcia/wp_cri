<h2>Modification d'un document sécurisé du Cridon</h2>

<?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
<?php echo $this->custom_form->file_input('file_path'); ?>
<?php echo $this->custom_form->hidden_input('download_url'); ?>
<?php echo $this->select->select('type',array('label' => 'Type','attr'=>'type', 'options' => $options ),$object); ?>
<?php echo $this->custom_form->end('Update'); ?>