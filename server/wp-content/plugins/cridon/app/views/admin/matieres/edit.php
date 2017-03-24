<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['matiere']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
    <?php echo $this->custom_form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
    <?php echo $this->custom_form->input('code',array('label' => Config::$titleFieldAdminForm['code'])); ?>
    <?php echo $this->custom_form->input('short_label',array('label' => Config::$titleFieldAdminForm['short_label'])); ?>
    <?php echo $this->custom_form->checkbox_input('displayed', array('label' => 'Concerne les veilles et flashs (apparaît dans les choix de notifications)')); ?>
    <?php echo $this->custom_form->checkbox_input('question', array('label' => 'Concerne les questions')); ?>
    <?php echo $this->custom_form->checkbox_input('formation', array('label' => 'Concerne les formations')); ?>
    <div style="position: relative;">
        <?php echo $this->custom_form->input('color',array('label' => Config::$titleFieldAdminForm['color'])); ?>
        <?php if( $object->color ): ?>
        <span style="top: 2px; left: 364px;position: absolute;background: <?php echo $object->color ?>; padding: 3px .5em;">&nbsp;</span>
        <?php endif; ?>
    </div>
    <?php echo $this->custom_form->textarea_input('meta_title',array('label' => 'Méta title','value' => $object->meta_title)); ?>
    <?php echo $this->custom_form->textarea_input('meta_description',array('label' => 'Méta description','value' => $object->meta_description)); ?>
    <?php echo $this->custom_form->file_input('picto',array('label'=>'Picto ( Dimension max : '.Config::$maxWidthHeight['width'].'x'.Config::$maxWidthHeight['height'].' )','type'=>'file')); ?>
    <?php if( $object->picto ): ?>
    <img src=" <?php echo $object->picto ?>" alt="picto" with="50" height="75"/>
    <?php endif ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>
</div>
