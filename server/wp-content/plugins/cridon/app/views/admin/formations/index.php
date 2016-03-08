<h2><?php echo MvcInflector::pluralize_titleize($model->name); ?></h2>

<form id="posts-filter" action="<?php echo MvcRouter::admin_url(); ?>" method="get">

    <input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo mvc_admin_url(array('controller' => 'formations')); ?>" />

    <p class="filter-box">
        <select id="formationFilter" name="formationFilter">
            <option value="all"> --- Toutes --- </option>
            <option value="old" <?php echo (isset($_GET['option']) && $_GET['option'] == 'old' ? 'selected' : ''); ?>> Formations passées </option>
            <option value="new" <?php echo (isset($_GET['option']) && $_GET['option'] == 'new' ? 'selected' : ''); ?>> Formations à venir </option>
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
        <?php echo $this->admin_post->admin_table_cells($this, $objects); ?>
    </tbody>
    
</table>

<div class="tablenav">

    <div class="tablenav-pages">
    
        <?php echo paginate_links($pagination); ?>
    
    </div>

</div>

<br class="clear" />