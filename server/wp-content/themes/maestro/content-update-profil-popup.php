<div id="layer-update-profil" class="popup" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Modifier'); ?>
            <span><?php _e('mes informations'); ?></span>
        </span>
    </div>   

    <form action="" method="post">
        <div class="nom">
            <input type="text" name="profil_nom" placeholder="<?php _e('Nom'); ?>" id="profil_nom" required>
        </div>
        <div class="prenom">
            <input type="text" name="profil_prenom" placeholder="<?php _e('Prénom'); ?>" id="profil_prenom" required>
        </div>
        <div class="fonction">
            <select name="profil_function" placeholder="<?php _e('Fonction'); ?>" id="profil_function" required>
                <option value="" disabled selected><?php _e('Fonction *'); ?></option>            
            </select>
        </div>
        <div class="email">
            <input type="text" name="profil_email" placeholder="<?php _e('Email'); ?>" id="profil_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
        </div>
        <div class="tel">
            <input type="text" name="profil_tel" placeholder="<?php _e('Téléphone fixe'); ?>" id="profil_tel">
        </div>
        <div class="mobile">
            <input type="text" name="profil_tel_portable" placeholder="<?php _e('Téléphone mobile'); ?>" id="profil_tel_portable">
        </div>
        <div class="fax">
            <input type="text" name="profil_fax" placeholder="<?php _e('Téléphone Fax'); ?>" id="profil_fax">
        </div>
        
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>

    
</div>