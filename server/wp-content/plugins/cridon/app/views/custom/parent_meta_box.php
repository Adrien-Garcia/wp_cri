<select name="id_parent">
    <option value="">Aucun</option>
<?php
foreach( $aParent as $value ){

    echo '<option'.check( $oModel,$value ).' value="'.$value->id.'">'.$value->label.'</option>';
}
?>
</select>