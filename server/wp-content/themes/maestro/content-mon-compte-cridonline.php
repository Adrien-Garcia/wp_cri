<?php if (!empty($messageError)) : ?>
    <div class="message-erreur"><?php echo $messageError ?></div>
<?php endif; ?>
<h2><?php _e("L'offre crid’online"); ?></h2>

<p>Quel que soit le « produit » choisi, il est conçu pour être au plus proche de vos besoins et de votre organisation. Il est totalement conçu pour le notaire et intégré dans votre portail extranet CRIDON LYON ; difficile de faire plus simple et ergonomique.
Vous avez le choix ! Du plus simple au plus sophistiqué mais toujours au meilleur prix.
</p>

<div class="cridonline-offres">
    <h2><?php _e('Mon abonnement Crid\'online'); ?></h2>
    <div class="description">
        Lorem ipsum dolor sit amet
    </div>
    <?php set_query_var( 'notaire', $notaire ); ?>
    <?php set_query_var( 'priceVeilleLevel2', $priceVeilleLevel2 ); ?>
    <?php set_query_var( 'priceVeilleLevel3', $priceVeilleLevel3 ); ?>
    <?php set_query_var( 'subscription', true ); ?>
    <?php echo get_template_part("content","cridonline-offres"); ?>
</div>

<div class="wrap-link">
   <a href="<?php echo CONST_CRIDONLINE_DOCUMENT_CGUV_SITE ?>" title="CGUV" target="_blank"><?php _e("Télécharger les conditions générales d'utilisation et de vente (CGUV)"); ?></a>
</div>
