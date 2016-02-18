<?php
echo 'Désolé, vous ne pouvez pas ajouter de questions depuis le Backoffice';
die;
?>
    <h2>Add Question</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('name'); ?>
<?php echo $this->form->end('Add'); ?>