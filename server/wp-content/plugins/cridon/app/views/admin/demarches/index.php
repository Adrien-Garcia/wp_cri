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

<?php
require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/nav.php';
?>

<br class="clear" />

