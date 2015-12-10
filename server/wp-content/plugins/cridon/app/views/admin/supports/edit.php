<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['support']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name); ?>
    <?php echo $this->form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
    <?php echo $this->form->input('value' , array('label' => Config::$titleFieldAdminForm['value'] )); ?>
    <?php echo $this->form->input('label_front' , array('label' => Config::$titleFieldAdminForm['label_front'] )); ?>
    <?php echo $this->form->textarea_input('description' , array('label' => Config::$titleFieldAdminForm['description'], 'value' => $object->description )); ?>
    <?php echo $this->form->checkbox_input('displayed', array('label' => Config::$titleFieldAdminForm['displayed'])); ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>
</div>