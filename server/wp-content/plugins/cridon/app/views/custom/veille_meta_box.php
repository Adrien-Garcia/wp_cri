<select name="cri_category">
<?php
foreach( $aMatiere as $value ){
    echo '<option'.check( $oVeille,$value ).' value="'.$value->id.'">'.$value->label.'</option>';
}
?>
</select>