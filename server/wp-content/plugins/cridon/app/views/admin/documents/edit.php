<h2>Edit Document</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('file_path'); ?>
<?php echo $this->form->input('download_url'); ?>
<?php echo $this->select->select('type',array('label' => 'Type','attr'=>'type', 'options' => $options ),$object); ?>
<?php echo $this->form->end('Update'); ?>