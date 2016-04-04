<?php if (CriIsNotaire()) : ?>
<div id="layer-cridonline" style="display:none;">
    <div class="block js-home-block-link js-account-cridonline-validation-step1">
        <div class="content">
            <div class="h2">
                <span><?php _e('Veuillez imprimer, remplir et nous envoyer ce formulaire pour mise en place du prélèvement :'); ?></span>
            </div>
            <a href="/formulaire-prelevement.pdf" download class="js-account-cridonline-validation-toggle">Télécharger le formulaire</a>
        </div>
    </div>
    <div class="block js-home-block-link js-account-cridonline-validation-step2" style="display:none;">
        <div class="content">
            <div class="h2">
                <span><?php _e('Merci pour votre souscription, le service est désormais activé.'); ?></span>
                <span><?php _e('Un email de confirmation vous a été envoyé .'); ?></span>
            </div>
            <a href="/veilles">J'ai compris : découvrir l'offre de veille</a>
        </div>
    </div>
</div>
<?php endif; ?>
