<select name="cri_post_level">
<?php
foreach( $aLevel as $value ){
    echo '<option'.check( $oModel, $value, 'level' ).' value="' . $value->id . '">' . $value->label . '</option>';
}
?>
</select>