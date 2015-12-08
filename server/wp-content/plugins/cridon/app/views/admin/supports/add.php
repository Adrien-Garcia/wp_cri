<h2><?php echo Config::$titleAdminForm['support']['add'] ?></h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
<?php echo $this->form->input('value' , array('label' => Config::$titleFieldAdminForm['value'] )); ?>
<?php echo $this->form->input('label_front' , array('label' => Config::$titleFieldAdminForm['label_front'] )); ?>
<?php echo $this->form->textarea_input('description' , array('label' => Config::$titleFieldAdminForm['description'] )); ?>
<?php echo $this->form->checkbox_input('displayed', array('label' => Config::$titleFieldAdminForm['displayed'])); ?>
<?php echo $this->form->end(Config::$btnTextAdmin['add']); ?>