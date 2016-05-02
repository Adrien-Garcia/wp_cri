<div id="layer-update-mdp" class="popup" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Demande'); ?>
            <span><?php _e('de nouveau mot de passe'); ?></span>
        </span>
    </div>   

    <form action="" method="">
        <div class="Email">
            <input type="text" name="profil_email" placeholder="<?php _e( 'Email', 'cridon' ); ?>" id="profil_email" required>
        </div>
        <div class="Confirm-email">
            <input type="text" name="profil_confirm_email" placeholder="<?php _e('Confirmation de l\'email'); ?>" id="profil_confirm_email" required>
        </div>
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>

    
</div>