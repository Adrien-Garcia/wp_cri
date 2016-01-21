<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['matiere']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php echo $this->custom_form->create($model->name,array('enctype'=>true,'action' =>$this->action)); ?>
    <?php echo $this->custom_form->input('label',array('label' => Config::$titleFieldAdminForm['label'])); ?>
    <?php echo $this->custom_form->input('code',array('label' => Config::$titleFieldAdminForm['code'])); ?>
    <?php echo $this->custom_form->input('short_label',array('label' => Config::$titleFieldAdminForm['short_label'])); ?>
    <?php echo $this->custom_form->checkbox_input('displayed', array('label' => Config::$titleFieldAdminForm['displayed'])); ?>
    <?php echo $this->custom_form->checkbox_input('question', array('label' => Config::$titleFieldAdminForm['question'])); ?>
    <?php echo $this->custom_form->textarea_input('meta_title',array('label' => 'Méta title','value' => $object->meta_title)); ?>
    <?php echo $this->custom_form->textarea_input('meta_description',array('label' => 'Méta description','value' => $object->meta_description)); ?>
    <?php echo $this->custom_form->file_input('picto',array('label'=>'Picto ( Dimension max : '.Config::$maxWidthHeight['width'].'x'.Config::$maxWidthHeight['height'].' )','type'=>'file')); ?>
    <?php if( $object->picto ): ?>
    <img src=" <?php echo $object->picto ?>" alt="picto" with="50" height="75"/>
    <?php endif ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>
</div>