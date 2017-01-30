<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['lieu']['add'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->custom_form->create($model->name          ,array('enctype'=>true,'action' =>$this->action)); ?>
    <?php echo $this->custom_form->input('name'                 ,array('label' => Config::$titleFieldAdminForm['name'])); ?>
    <?php echo $this->custom_form->checkbox_input('is_cridon'   ,array('label' => Config::$titleFieldAdminForm['is_cridon'])); ?>
    <?php echo $this->custom_form->input('address'              ,array('label' => Config::$titleFieldAdminForm['address'])); ?>
    <?php echo $this->custom_form->input('postal_code'          ,array('label' => Config::$titleFieldAdminForm['postal_code'])); ?>
    <?php echo $this->custom_form->input('city'                 ,array('label' => Config::$titleFieldAdminForm['city'])); ?>
    <?php echo $this->custom_form->input('phone_number'         ,array('label' => Config::$titleFieldAdminForm['phone_number'])); ?>
    <?php echo $this->custom_form->input('email'                ,array('label' => Config::$titleFieldAdminForm['email'])); ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['add']); ?>
</div>