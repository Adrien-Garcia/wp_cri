<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['document']['add'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
    <?php echo $this->custom_form->input('name', array('label' => 'Nom')); ?>
    <?php echo $this->custom_form->file_input('file_path'); ?>
    <?php echo $this->custom_form->hidden_input('download_url'); ?>
    <?php echo $this->select->select('type',array('label' => 'Type','attr'=>'type','model' => $model->name, 'options' => $options ),$object); ?>
    <?php echo $this->custom_form->end(Config::$btnTextAdmin['add']); ?>
</div>