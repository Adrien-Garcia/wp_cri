    <div class="titre">
        <span class="close_layer layer-update-etude_close"></span>
        <span class="texte">
            <?php _e('Modifier'); ?>
            <span><?php _e('les informations de l\'étude'); ?></span>
        </span>
    </div>   

    <form class="js-account-profil-office-modify-form" data-js-ajax-modify-office-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestionetude')); ?>">
    <input type="hidden" class="js-account-profil-office-modify-crpcen" value="<?php echo empty($office['office_crpcen']) ? '' : $office['office_crpcen'] ?>">
        <div class="nom">
            <input type="text" name="etude_name" placeholder="<?php _e('Nom de l\'étude'); ?>" id="etude_name" class="js-account-profil-office-modify-name" value="<?php echo empty($office['office_name']) ? '' : $office['office_name'] ?>" disabled>
        </div>
        <div class="adresse">
            <input type="text" name="etude_adresse" placeholder="<?php _e('Adresse 1'); ?>" id="etude_adresse" class="js-account-profil-office-modify-address-1" value="<?php echo empty($office['office_address_1']) ? '' : $office['office_address_1'] ?>" required>
        </div>
        <div class="adresse">
            <input type="text" name="etude_adresse" placeholder="<?php _e('Adresse 2'); ?>" id="etude_adresse" class="js-account-profil-office-modify-address-2" value="<?php echo empty($office['office_address_2']) ? '' : $office['office_address_2'] ?>">
        </div>
        <div class="adresse">
            <input type="text" name="etude_adresse" placeholder="<?php _e('Adresse 3'); ?>" id="etude_adresse" class="js-account-profil-office-modify-address-3" value="<?php echo empty($office['office_address_3']) ? '' : $office['office_address_3'] ?>">
        </div>
        <div class="cp">
            <input type="text" name="etude_cp" placeholder="<?php _e('Code postal'); ?>" id="etude_cp" class="js-account-profil-office-modify-postalcode" value="<?php echo empty($office['office_postalcode']) ? '' : $office['office_postalcode'] ?>" required>
        </div>
        <div class="ville">
            <input type="text" name="etude_ville" placeholder="<?php _e('Ville'); ?>" id="etude_ville" class="js-account-profil-office-modify-city" value="<?php echo empty($office['office_city']) ? '' : $office['office_city'] ?>" required>
        </div>
        <div class="email">
            <input type="email" name="etude_email" placeholder="<?php _e('Email'); ?>" id="etude_email" class="js-account-profil-office-modify-email" value="<?php echo empty($office['office_email']) ? '' : $office['office_email'] ?>">
        </div>
        <div class="tel">
            <input type="text" name="etude_tel" placeholder="<?php _e('Téléphone'); ?>" id="etude_tel" class="js-account-profil-office-modify-phone" value="<?php echo empty($office['office_phone']) ? '' : $office['office_phone'] ?>">
        </div>
        <div class="fax">
            <input type="text" name="etude_tel_fax" placeholder="<?php _e('Téléphone Fax'); ?>" id="etude_tel_fax" class="js-account-profil-office-modify-fax" value="<?php echo empty($office['office_fax']) ? '' : $office['office_fax'] ?>">
        </div>

        <div class="message-erreur js-account-profil-office-modify-message"></div>
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>