<?php query_posts('order=DESC'); ?>
<?php 
    resetGlobalVars();
    include TEMPLATEPATH.'/single-flash.php';
?>