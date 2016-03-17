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

    <p class="search-box">
        <label class="screen-reader-text" for="post-search-input">Search:</label>
        <input type="hidden" name="page" value="<?php echo MvcRouter::admin_page_param($model->name); ?>" />
        <input type="text" name="q" value="<?php echo empty($params['q']) ? '' : $params['q']; ?>" />
        <input type="submit" value="Search" class="button" />
    </p>

</form>

<div class="tablenav">

    <div class="tablenav-pages">
    
        <?php echo paginate_links($pagination); ?>
    
    </div>

</div>

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

<div class="tablenav">

    <div class="tablenav-pages">
    
        <?php echo paginate_links($pagination); ?>
    
    </div>

</div>

<br class="clear" />