<?php if (!empty($messageError)) : ?>
    <div class="message-erreur"><?php echo $messageError ?></div>
<?php endif; ?>
<h2><?php _e("L'offre crid’online"); ?></h2>

<p>PROMOOOOOOOOOOO
</p>

<div class="cridonline-offres">
    <!-- <h2><?php _e('Mon abonnement Crid\'online'); ?></h2>
    <div class="description">
        Lorem ipsum dolor sit amet
    </div> -->
    <?php set_query_var( 'notaire', $notaire ); ?>
    <?php set_query_var( 'priceVeilleLevel2', $priceVeilleLevel2 ); ?>
    <?php set_query_var( 'priceVeilleLevel3', $priceVeilleLevel3 ); ?>
    <?php set_query_var( 'subscription', true ); ?>
    <?php echo get_template_part("content","cridonline-offres-promo"); ?>
</div>

<div class="wrap-link">
   <a href="<?php echo CONST_CRIDONLINE_DOCUMENT_CGUV_PATH ?>" title="CGUV" target="_blank"><?php _e("Télécharger les conditions générales d'utilisation et de vente (CGUV)"); ?></a>
</div>
