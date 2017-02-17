<h2><?php echo MvcInflector::pluralize_titleize($model->name); ?></h2>

<form id="posts-filter" action="<?php echo MvcRouter::admin_url(); ?>" method="get">

    <input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo mvc_admin_url(array('controller' => 'formations')); ?>" />

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
        <?php echo $this->admin_post->admin_table_cells($this, $objects); ?>
    </tbody>
    
</table>

<?php
require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/nav.php';
?>


<br class="clear" />
