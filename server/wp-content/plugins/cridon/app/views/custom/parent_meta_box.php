<select name="id_parent">
    <option value="">Aucun</option>
<?php
foreach( $aParent as $value ){

    echo '<option'.check( $oModel,$value, 'id_parent' ).' value="'.$value->id.'">'.$value->post->post_title.'</option>';
}
?>
</select>