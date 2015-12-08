<h2><?php echo Config::$titleAdminForm['solde']['edit'] ?></h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('client_number',array('label' => Config::$titleFieldAdminForm['client_number'])); ?>
<?php echo $this->form->input('quota',array('label' => Config::$titleFieldAdminForm['quota'])); ?>
<?php echo $this->form->input('type_support',array('label' => Config::$titleFieldAdminForm['type_support'])); ?>
<?php echo $this->form->input('nombre'); ?>
<?php echo $this->form->input('points'); ?>
<?php echo $this->form->input('date_arret',array('label' => Config::$titleFieldAdminForm['date_arret'])); ?>
<?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>