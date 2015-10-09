<h2><?php echo $object->__name; ?></h2>

<p>
    <?php echo $this->html->link('&#8592; All Flashes', array('controller' => 'flashes')); ?>
</p>
<?php query_posts('order=DESC'); ?>
<?php 
    resetGlobalVars();
    include TEMPLATEPATH.'/single.php';
?>