
<div id="layer-collaborateur-delete" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Suppression'); ?>
            <span><?php _e('d\'un collaborateur'); ?></span>
        </span>
    </div>

    <div class="block">
        <p>Confirmer la suppression du collaborateur ? </p>

        <form class="js-account-collaborateur-delete-validation-form" data-js-ajax-delete-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
            <input type="hidden" value="" class="js-account-collaborateur-delete-validation-id" />
            <div class="message-erreur js-account-collaborateur-delete-validation-message"></div>
            <input type="submit" value="<?php _e("supprimer"); ?>">
        </form>
    </div>
</div>

