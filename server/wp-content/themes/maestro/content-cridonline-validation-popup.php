<?php if (CriIsNotaire()) : ?>
<div id="layer-cridonline" class="popup" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Souscription'); ?>
            <span><?php _e('à l\'offre crid\'online'); ?></span>
        </span>
    </div>

    <div class="block js-home-block-link js-account-cridonline-validation-step1">
        <div class="content">
            <p><?php _e('Veuillez imprimer, remplir et nous envoyer ce formulaire accompagné d\'un RIB pour mise en place du prélèvement.'); ?></p>
            <p><?php _e('Une copie de ce formulaire doit également être impérativement adressée à votre banque.'); ?></p>
            <p><?php _e('Nous attirons votre attention sur le fait qu\'à défaut le prélèvement sera rejeté et des frais bancaires vous seront facturés.'); ?></p>
            <a target="_blank" href="<?php echo CONST_CRIDONLINE_DOCUMENT_MANDAT_SEPA_PATH ?>" download class="js-account-cridonline-validation-toggle">Télécharger le formulaire</a>
        </div>
    </div>
    <div class="block js-home-block-link js-account-cridonline-validation-step2" style="display:none;">
        <div class="content">
            <p>
                <?php _e('Merci pour votre souscription, le service est désormais activé.'); ?><br />
                <?php _e('Un email de confirmation vous a été envoyé.'); ?>
            </p>            
            <a href="/veilles"><!-- J'ai compris : découvrir l'offre de veille --> Découvrir l'offre CRID'ONLINE</a>
        </div>
    </div>
</div>
<?php endif; ?>
