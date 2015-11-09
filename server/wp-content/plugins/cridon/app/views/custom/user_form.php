<?php
// header
if( !empty( $user ) && ( $user instanceof WP_User ) ){
    ?>
    <div>
    <?php
} else {
?>
><div>
<?php
}
?>
<table class="form-table">
    <tbody>
        <tr class="form-field">
            <th scope="row">
                <label for="id_erp">
                    Id ERP
                </label>
            </th>
            <td>
                <input id="id_erp" type="text" autocorrect="off" autocapitalize="none" value="<?php echo $id_erp; ?>" name="id_erp">
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="last_connection">
                Dernière connexion
                </label>
            </th>
            <td>
                <input id="last_connection" type="text" autocorrect="off" autocapitalize="none" value="<?php echo $last_connection; ?>" name="last_connection" disabled="disabled">
            </td>
        </tr>
    </tbody>
</table>
<?php
// footer
if( !empty( $user ) && ( $user instanceof WP_User ) ){
?>
</div>
<?php
} else {
?>
</div
<?php
}

