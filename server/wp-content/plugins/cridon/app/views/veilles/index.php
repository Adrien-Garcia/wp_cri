<?php
$objects = criQueryPostVeille();
criWpPost( $objects );
include TEMPLATEPATH.'/archive-veilles.php';
?>