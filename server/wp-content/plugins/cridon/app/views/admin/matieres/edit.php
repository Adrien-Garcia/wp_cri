<h2>Edit Matiere</h2>

<?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
<?php echo $this->custom_form->input('label'); ?>
<?php echo $this->custom_form->input('code'); ?>
<?php echo $this->custom_form->input('short_label'); ?>
<?php echo $this->custom_form->checkbox_input('displayed', array('label' => 'Displayed')); ?>
<?php echo $this->custom_form->file_input('picto',array('label'=>'Picto ( Dimension max : '.Config::$maxWidthHeight['width'].'x'.Config::$maxWidthHeight['height'].' )','type'=>'file')); ?>
<?php if( $this->object->picto ): ?>
<img src=" <?php echo $this->object->picto ?>" alt="picto" with="50" height="75"/>
<?php endif ?>
<?php echo $this->form->end('Update'); ?>