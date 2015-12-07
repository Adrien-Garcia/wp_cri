<h2>Edition d'un solde</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('client_number'); ?>
<?php echo $this->form->input('quota'); ?>
<?php echo $this->form->input('type_support'); ?>
<?php echo $this->form->input('nombre'); ?>
<?php echo $this->form->input('points'); ?>
<?php echo $this->form->input('date_arret'); ?>
<?php echo $this->form->end('Update'); ?>