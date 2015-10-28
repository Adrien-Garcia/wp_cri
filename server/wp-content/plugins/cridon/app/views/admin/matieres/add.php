<h2>Add Matiere</h2>
<?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
<?php echo $this->custom_form->input('label'); ?>
<?php echo $this->custom_form->input('code'); ?>
<?php echo $this->custom_form->input('short_label'); ?>
<?php echo $this->custom_form->checkbox_input('displayed', array('label' => 'Displayed')); ?>
<?php echo $this->custom_form->file_input('picto',array('Picto ( Dimension max : '.Config::$maxWidthHeight['width'].'x'.Config::$maxWidthHeight['height'].' )','type'=>'file')); ?>
<?php echo $this->form->end('Add'); ?>