<h2><?php _e("Souscription à l'offre Crid'online"); ?></h2>

<div id="cridonline-validation-popup">
    <?php echo get_template_part("content","cridonline-validation-popup"); ?>
</div>

<div class="message-offre">
    Vous avez choisi l'offre CRID'ONLINE <?php echo ($level == 2) ? CONST_CRIDONLINE_LABEL_LEVEL_2 : CONST_CRIDONLINE_LABEL_LEVEL_3 ?> pour <?php echo $price ?>€ par an.<br />
    Afin de procéder au règlement par prélèvement SEPA, vous allez pouvoir télécharger le document ci-après :
</div>

<div class="bloc-souscription">
    <form method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-validation-form" data-js-ajax-souscription-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'souscriptionveille'));?>">
        <label class="unselect js-account-cridonline-validation-checkbox"><input type="checkbox" name="CGV" class="js-account-cridonline-validation-cgv" value="value"><span><?php _e("J'ai lu, j'ai compris et j'accepte les CGV"); ?></span></label>
        <a href="" title="CGV" target="_blank"><?php _e("Télécharger les conditions générales de vente (CGV)"); ?></a>
        <input type="hidden" name="crpcen" value="<?php echo $crpcen; ?>" class="js-account-cridonline-validation-crpcen">
        <input type="hidden" name="level" value="<?php echo $level; ?>" class="js-account-cridonline-validation-level">
        <input type="hidden" name="price" value="<?php echo $price ?>" class="js-account-cridonline-validation-price">
        <input type="submit" name="submit" value="<?php _e("souscrire"); ?>">

        <div class="message-erreur js-account-cridonline-validation-message">
        </div>

    </form>
</div>
