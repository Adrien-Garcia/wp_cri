<p class="search-box">
    <label class="screen-reader-text" for="post-search-input">Search:</label>
    <input type="hidden" name="page" value="<?php echo MvcRouter::admin_page_param($model->name); ?>" />
    <input type="text" name="q" value="<?php echo empty($params['q']) ? '' : $params['q']; ?>" />
    <input type="submit" value="Search" class="button" />
</p>
