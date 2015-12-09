<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['competence']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name); ?>
    <?php echo $this->form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
    <?php echo $this->form->belongs_to_dropdown('Matiere', $aMatiere, array('label' => 'Matière','style' => 'width: 200px;', 'empty' => false)); ?>
    <?php echo $this->form->checkbox_input('displayed', array('label' => Config::$titleFieldAdminForm['displayed'])); ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>
</div>