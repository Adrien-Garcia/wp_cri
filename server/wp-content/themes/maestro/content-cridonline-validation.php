<?php if (CriIsNotaire()) : ?>
<div id="layer-cridonline" style="display:none;">

    <div class="block js-home-block-link">
        <div class="content">
            <div class="h2">
                <span><?php _e('Veuillez imprimer, remplir et nous envoyer ce formulaire pour mise en place du prélèvement :'); ?></span>
            </div>
            <a href="formulaire-prelevement.pdf" download>Télécharger le formulaire</a>
        </div>
    </div>

    <div class="block js-home-block-link">
        <div class="content">
            <div class="h2">
                <span><?php _e('Merci pour votre souscription, le service est désormais activé pour '. Config::$daysTrialVeille .' jours, en attendant votre réglèment.'); ?></span>
                <span><?php _e('Un email de confirmation vous a été envoyé .'); ?></span>
            </div>
            <a href="/veilles">Découvrir l'offre de veille</a>
        </div>
    </div>
</div>

<?php endif; ?>
