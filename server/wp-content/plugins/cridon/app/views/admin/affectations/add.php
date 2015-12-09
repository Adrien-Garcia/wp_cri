<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['affectation']['add'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name); ?>
    <?php echo $this->form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['add']); ?>
</div>