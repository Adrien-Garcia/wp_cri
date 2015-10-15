<h2>Edit Juriste</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('label'); ?>
<?php echo $this->form->input('code'); ?>
<?php echo $this->form->input('short_label'); ?>
<?php echo $this->form->checkbox_input('displayed', array('label' => 'Displayed')); ?>
<?php echo $this->form->end('Update'); ?>