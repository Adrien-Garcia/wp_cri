<h2><?php echo Config::$titleAdminForm['competence']['add'] ?></h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('label'); ?>
<?php echo $this->form->belongs_to_dropdown('Matiere', $aMatiere, array('style' => 'width: 200px;', 'empty' => false)); ?>
<?php echo $this->form->checkbox_input('displayed', array('label' => 'Displayed')); ?>
<?php echo $this->form->end(Config::$btnTextAdmin['add']); ?>