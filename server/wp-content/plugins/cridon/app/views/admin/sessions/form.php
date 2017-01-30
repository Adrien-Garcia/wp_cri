<?php echo $this->custom_form->create($model->name, array('enctype'=>true,'action' =>$this->action)); ?>
<?php echo $this->custom_form->input('timetable', array('label' => Config::$titleFieldAdminForm['timetable'])); ?>
<?php echo $this->custom_form->date_input('date', array(
    'label' => Config::$titleFieldAdminForm['date'],
    'value' => isset($object) && property_exists($object, 'date') ? $object->date : date('Y-m-d')
)); ?>
<?php echo $this->custom_form->belongs_to_dropdown('Formation', $formations, array('label' => 'Formation','style' => 'width: 200px;', 'empty' => false)); ?>
<?php echo $this->custom_form->belongs_to_dropdown('Lieu', $lieux, array('label' => 'Lieu','style' => 'width: 200px;', 'empty' => false)); ?>
