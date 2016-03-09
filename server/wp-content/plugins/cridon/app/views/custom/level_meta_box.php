<?php //echo '<pre>'; die(print_r($oModel)); ?>
<select name="cri_post_level">
<?php
foreach( $aLevel as $key => $value ){
    $oHayStack->id = $value;
    echo '<option'.check( $oModel->level, $oHayStack ).' value="'.$value.'">'.$key.'</option>';
}
?>
</select>