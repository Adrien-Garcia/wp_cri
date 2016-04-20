<div id="layer-update-etude" class="popup" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Modifier'); ?>
            <span><?php _e('les informations de l\'étude'); ?></span>
        </span>
    </div>   

    <form action="" method="post">
        <div class="nom">
            <input type="text" name="etude_name" placeholder="<?php _e('Nom de l\'étude'); ?>" id="etude_name" required>
        </div>
        <div class="adresse">
            <input type="text" name="etude_adresse" placeholder="<?php _e('Adresse'); ?>" id="etude_adresse" required>
        </div>
        <div class="etude">
            <input type="text" name="etude_cp" placeholder="<?php _e('Code postal'); ?>" id="etude_cp" required>
        </div>
        <div class="ville">
            <input type="text" name="etude_ville" placeholder="<?php _e('Ville'); ?>" id="etude_ville" required>
        </div>
        <div class="email">
            <input type="text" name="etude_email" placeholder="<?php _e('Email'); ?>" id="etude_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
        </div>
        <div class="tel">
            <input type="text" name="etude_tel" placeholder="<?php _e('Téléphone'); ?>" id="etude_tel">
        </div>
        <div class="fax">
            <input type="text" name="etude_tel_fax" placeholder="<?php _e('Téléphone Fax'); ?>" id="etude_tel_fax">
        </div>
        
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        
    </form>

    
</div>