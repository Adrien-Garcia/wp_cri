<p>
    <?php echo $this->html->link('&#8592; All Veilles', array('controller' => 'veilles')); ?>
</p>
<?php
criWpPost( $object );
include TEMPLATEPATH.'/single-veilles.php';
?>