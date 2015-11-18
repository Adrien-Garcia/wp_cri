<h2>Add Support</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('label'); ?>
<?php echo $this->form->input('value' , array('label' => 'Valeur' )); ?>
<?php echo $this->form->input('label_front' , array('label' => 'Label Front' )); ?>
<?php echo $this->form->textarea_input('description' , array('label' => 'Description' )); ?>
<?php echo $this->form->checkbox_input('displayed', array('label' => 'Displayed')); ?>
<?php echo $this->form->end('Add'); ?>