<?php if (CriIsNotaire()) : ?>
<div id="layer-cridonline" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Souscription'); ?>
            <span><?php _e('à l\'offre crid\'online'); ?></span>
        </span>
    </div>

    <div class="block js-home-block-link js-account-cridonline-validation-step1">
        <div class="content">
            <p><?php _e('Veuillez imprimer, remplir et nous envoyer ce formulaire pour mise en place du prélèvement :'); ?></p>
            <a href="/formulaire-prelevement.pdf" download class="js-account-cridonline-validation-toggle">Télécharger le formulaire</a>
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
