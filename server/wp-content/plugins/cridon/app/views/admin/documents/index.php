<h2><?php echo MvcInflector::pluralize_titleize($model->name); ?></h2>

<form id="posts-filter" action="<?php echo MvcRouter::admin_url(); ?>" method="get">

    <input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo mvc_admin_url(array('controller' => 'documents')); ?>" />

    <p class="filter-box">
        <select id="documentFilter" name="documentFilter">
            <option value="all"> --- Toutes --- </option>
            <?php foreach ($options as $k => $v) : ?>
                <option value="<?php echo $k ?>" <?php echo (isset($_GET['option']) && $_GET['option'] == $k ? 'selected' : ''); ?>> <?php echo $v ?> </option>
            <?php endforeach; ?>
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
        <?php echo $this->admin_document->admin_table_cells($this, $objects); ?>
    </tbody>
    
</table>

<?php
require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/nav.php';
?>

<br class="clear" />
