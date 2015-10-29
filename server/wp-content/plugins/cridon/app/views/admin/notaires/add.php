<?php
echo 'Désolé, vous ne pouvez pas ajouter de notaire depuis le Backoffice';
die;
?>
<h2>Add Notaire</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('name'); ?>
<?php echo $this->form->end('Add'); ?>