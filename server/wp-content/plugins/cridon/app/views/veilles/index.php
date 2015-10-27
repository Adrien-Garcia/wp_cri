<?php query_posts('order=DESC'); ?>
<?php 
    resetGlobalVars();
    include TEMPLATEPATH.'/archive-veilles.php';
?>