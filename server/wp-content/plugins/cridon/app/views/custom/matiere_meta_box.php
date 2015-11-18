<select name="cri_category">
<?php
foreach( $aMatiere as $value ){
    echo '<option'.check( $oModel,$value ).' value="'.$value->id.'">'.$value->label.'</option>';
}
?>
</select>