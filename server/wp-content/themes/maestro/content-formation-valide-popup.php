<div id="layer-valide-formation" class="popup js-formation-formulaire-popup" style="display: none;">

    <div class="titre">
        <span class="close_layer layer-valide-formation_close"></span>
        <span class="texte">
            <?php if (!empty($preinscription)) : ?>
            <?php _e('Pré-inscription à la formation envoyée'); ?>
            <?php elseif (!empty($demandeFormation) || !empty($demandeGenerique)) : ?>
            <?php _e('Demande de formation envoyée'); ?>
            <?php endif; ?>
        </span>
    </div>

    <p>
        <?php if (!empty($preinscription)) : ?>
            <?php _e('Votre demande de pré-inscription a bien été envoyée, vous serez recontactés par le CRIDON LYON prochainement'); ?>
        <?php elseif (!empty($demandeFormation) || !empty($demandeGenerique)) : ?>
            <?php _e('Votre demande de formation a bien été envoyée, vous serez recontactés par le CRIDON LYON prochainement'); ?>
        <?php endif; ?>
    </p>
    <div class="submit">
        <input type="button" onclick="location.href='<?php echo MvcRouter::public_url(array('controller' => 'formations', 'action' => 'calendar')) ?>'" value="J'ai compris : retour au calendrier des formations" />
    </div>
</div>