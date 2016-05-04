<div id="layer-update-mdp" class="popup" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-update-mdp_close"></span>
        <span class="texte">
            <?php _e('Demande'); ?>
            <span><?php _e('de nouveau mot de passe'); ?></span>
        </span>
    </div>

    <form class="js-account-profil-password-form" data-js-ajax-password-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestionpassword')); ?>">
        <div class="Email">
            <input type="email" name="profil_email" placeholder="<?php _e( 'Email', 'cridon' ); ?>" id="profil_email" class="js-account-profil-password-email" required>
        </div>
        <div class="Confirm-email">
            <input type="email" name="profil_confirm_email" placeholder="<?php _e('Confirmation de l\'email'); ?>" id="profil_confirm_email" class="js-account-profil-password-email-validation" required>
        </div>
        <div class="message-erreur js-account-profil-password-message"></div>
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>

    
</div>