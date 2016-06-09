    <div class="titre">
        <span class="close_layer layer-update-profil_close"></span>
        <span class="texte">
            <?php _e('Modifier'); ?>
            <span><?php _e('mes informations'); ?></span>
        </span>
    </div>   

    <form class="js-account-profil-modify-form" data-js-ajax-modify-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
        <input type="hidden" name="collaborator_id" class="js-account-profil-modify-id" value="<?php echo empty($collaborator['id']) ? '' : $collaborator['id'] ?>">
        <input type="hidden" name="action" class="js-account-profil-action" value="<?php echo empty($collaborator['action']) ? '' : $collaborator['action'] ?>">
        <div class="nom">
            <input type="text" name="profil_nom" placeholder="<?php _e('Nom'); ?>" id="profil_nom" class="js-account-profil-modify-lastname" value="<?php echo empty($collaborator['lastname']) ? '' : $collaborator['lastname'] ?>" required>
        </div>
        <div class="prenom">
            <input type="text" name="profil_prenom" placeholder="<?php _e('Prénom'); ?>" id="profil_prenom" class="js-account-profil-modify-firstname" value="<?php echo empty($collaborator['firstname']) ? '' : $collaborator['firstname'] ?>" required>
        </div>
        <div class="fonction">
            <select name="profil_function" id="profil_function" disabled>
                <?php if(!empty($notaire_functions[0]) && !empty($notaire_functions[0]->notaire_fonction_label)) : ?>
                    <option><?php echo $notaire_functions[0]->notaire_fonction_label; ?></option>
                <?php endif ?>
            </select>
        </div>
        <div class="email">
            <input type="email" name="profil_email" placeholder="<?php _e('Email '); ?>" id="profil_email" class="js-account-profil-modify-email" value="<?php echo empty($collaborator['emailaddress']) ? '' : $collaborator['emailaddress'] ?>">
        </div>
        <div class="tel">
            <input type="text" name="profil_tel" placeholder="<?php _e('Téléphone fixe'); ?>" id="profil_tel" class="js-account-profil-modify-phone" value="<?php echo empty($collaborator['phone']) ? '' : $collaborator['phone'] ?>">
        </div>
        <div class="mobile">
            <input type="text" name="profil_tel_portable" placeholder="<?php _e('Téléphone mobile'); ?>" id="profil_tel_portable" class="js-account-profil-modify-mobilephone" value="<?php echo empty($collaborator['mobilephone']) ? '' : $collaborator['mobilephone'] ?>">
        </div>
        <div class="fax">
            <input type="text" name="profil_fax" placeholder="<?php _e('Fax'); ?>" id="profil_fax" class="js-account-profil-modify-fax" value="<?php echo empty($collaborator['fax']) ? '' : $collaborator['fax'] ?>">
        </div>
        <div class="message-erreur js-account-profil-modify-message"></div>
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>