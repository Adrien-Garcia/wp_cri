<select name="id_parent">
    <option value="">Aucun</option>
<?php
foreach( $aParent as $value ){

    echo '<option'.check( $oModel,$value, 'id_parent' ).' value="'.$value->id.'">'.$value->label.'</option>';
}
?>
</select>