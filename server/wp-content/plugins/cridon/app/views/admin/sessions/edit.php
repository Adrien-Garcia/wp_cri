<div class="mvc-title">
    <h2><?php echo Config::$titleAdminForm['session']['edit'] ?></h2>
</div>
<div class="mvc-form">
    <?php
    require 'form.php';
    ?>
    <?php echo $this->form->end(Config::$btnTextAdmin['update']); ?>
</div>
