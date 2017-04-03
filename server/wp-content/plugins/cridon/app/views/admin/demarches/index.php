<h2><?php echo MvcInflector::pluralize_titleize($model->name); ?></h2>

<?php

$helperName = 'admin_view';

?>

<form id="posts-filter" action="<?php echo MvcRouter::admin_url(); ?>" method="get">
    <p class="filter-box">
        <select id="jsContentFilter" name="option">
            <option value="0"> --- Toutes --- </option>
            <?php
            foreach (Config::$labelWorflowFormation as $value => $label):
            ?>
            <option value="<?php echo $value ?>" <?php echo (isset($_GET['option']) && $_GET['option'] == $value ? 'selected' : ''); ?>> <?php echo $label ?> </option>
            <?php
            endforeach;
            ?>
        </select>
    </p>
    <?php
    require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/search.php';
    ?>

</form>

<?php
require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/nav.php';
?>


<div class="clear"></div>

<table class="widefat post fixed" cellspacing="0">

    <thead>
    <?php echo $helper->admin_header_cells($this); ?>
    </thead>

    <tfoot>
    <?php echo $helper->admin_header_cells($this); ?>
    </tfoot>

    <tbody>
    <?php echo $this->{$helperName}->admin_table_cells($this, $objects); ?>
    </tbody>

</table>

<hr style="margin-top: 30px;">

<div class="mvc-form">
    <h2><?php echo Config::$titleAdminForm['demarche']['export'] ?></h2>
    <?php echo $this->custom_form->create($model->name, array('enctype'=>true,'action' =>$this->action)); ?>
    <?php echo $this->custom_form->checkbox_input('export_complet', array('label' => Config::$titleFieldAdminForm['export_complet'])); ?>
    <br />
    <div class="export-dates">
    <?php echo $this->custom_form->date_input('export_start_date', array(
        'label' => Config::$titleFieldAdminForm['export_start_date'],
        'value' => date_create()->modify('-1 month')->format('d-m-Y')
    )); ?>
    <?php echo $this->custom_form->date_input('export_end_date', array(
        'label' => Config::$titleFieldAdminForm['export_end_date'],
        'value' => date('d-m-Y')
    )); ?>
    </div>
    <br />

    <?php echo $this->form->end(Config::$btnTextAdmin['export']); ?>

    <?php if (isset($exportUrl)) : ?>
        <p style="margin-left: 15px;">
            <a href="<?php echo $exportUrl; ?>" target="_blank">Export réussi</a>
        </p>
    <?php endif; ?>

    <?php if (isset($exportedFiles)) : ?>
        <div style="margin-left: 15px;">
            <h3>Liste des fichiers exportés (du plus récent au plus ancien) : </h3>
            <ul style="list-style-type: disc; margin-left: 30px;">
                <?php foreach ($exportedFiles as $exportedFile) : ?>
                    <li>
                        <a href="<?php echo $exportedFile['url']; ?>" target="_blank"><?php echo $exportedFile['label']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <script type="text/javascript">
    //<![CDATA[
    	jQuery('#DemarcheExportComplet').on('change', function() {
            if (this.checked) {
                jQuery('.export-dates').hide();
            } else {
                jQuery('.export-dates').show();
            }
        }).trigger('change');
    //]]>
    </script>
</div>


<?php
require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/nav.php';
?>

<br class="clear" />

