<h2><?php echo MvcInflector::pluralize_titleize($model->name); ?></h2>

<?php

$helperName = 'admin_noview';

require WP_PLUGIN_DIR.'/cridon/app/views/admin/common/index.php';
