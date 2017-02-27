<?php echo $this->custom_form->create($model->name, array('enctype'=>true,'action' =>$this->action)); ?>
<?php echo $this->custom_form->input('name', array('label' => Config::$titleFieldAdminForm['evenement'])); ?>
<?php echo $this->custom_form->date_input('date', array(
    'label' => Config::$titleFieldAdminForm['date'],
    'value' => isset($object) && property_exists($object, 'date') ? $object->date : date('Y-m-d')
)); ?>
