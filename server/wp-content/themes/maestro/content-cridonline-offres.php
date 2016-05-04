<?php if ($notaire->etude->subscription_level < 2 && $subscription ):?>
    <?php $isSubcribable = ($notaire->etude->subscription_level < 3 && $subscription) ?>
<?php endif; ?>
<div class="bloc-cridonline niveau-1 on <?php echo $isSubcribable ? "large" : "" ?>">
    <div class="en-tete"></div>
    <div class="content">
        <div class="titre">
            Compris dans votre cotisation générale annuelle
        </div>
        <ul>
            <!-- <li>- Veille juridique référence CRIDON LYON</li> -->
            <li>- Bouquet documentaire complémentaire</li>
            <li>- Fonds officiels WOLTERS KLUWER</li>
            <li>- Actualités Lexbase (30 domaines juridiques)</li>
            <li>- Mise à jour permanente</li>
        </ul>
        <a href="/wp-content/uploads/pdf/Description-CRIDONLINE-reference.pdf" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
    </div>
</div>

<div class="bloc-cridonline niveau-2 off <?php echo $isSubcribable ? "large" : "" ?>">
    <div class="en-tete"></div>
    <div class="content">
        <?php if ($notaire->etude->subscription_level < 2 ):?>
            <div class="prix">
               <?php echo $priceVeilleLevel2 ?><span>€ HT</span>
            </div>
        <?php elseif ($notaire->etude->subscription_level == 2 ) :?>
            <div class="titre">
                Mon abonnement en cours du <?php echo date("d/m/Y", strtotime($notaire->etude->start_subscription_date)) ?> au <?php echo date("d/m/Y", strtotime($notaire->etude->end_subscription_date)) ?>
            </div>
        <?php else : ?>
            <div class="titre">
                Compris dans votre abonnement <?php echo CONST_CRIDONLINE_LABEL_LEVEL_3 ?>
            </div>
        <?php endif; ?>
        <ul>
            <!-- <li>- Veille juridique premium CRIDON LYON</li> -->
            <li>- Documentation juridique d’une partie importante du droit notarial</li>
            <li>- Economie d’abonnements d’autres éditeurs possible</li>
            <li>- Meilleur rapport qualité prix</li>
            <li>- Offre évolutive et mise à jour permanente</li>
        </ul>
        <a href="/wp-content/uploads/pdf/Description-CRIDONLINE-premium.pdf" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 2 && $subscription ):?>
            <form method="get" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
                <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
                <input type="hidden" name="level" value="<?php echo "2"; ?>" class="js-account-cridonline-level">
                <input type="hidden" name="price" value="<?php echo $priceVeilleLevel2 ?>" class="js-account-cridonline-price">
                <input type="submit" name="submit" value="<?php _e("souscrire"); ?>">
            </form>
        <?php endif;?>
    </div>
</div>
<div class="bloc-cridonline niveau-3 off <?php echo $isSubcribable ? "large" : "" ?>">
    <div class="en-tete"></div>
    <div class="content">
        <?php if ($notaire->etude->subscription_level < 3 ):?>
            <div class="prix">
                <?php echo $priceVeilleLevel3 ?><span>€ HT</span>
            </div>
        <?php else : ?>
            <div class="titre">
                Mon abonnement en cours du <?php echo date("d/m/Y", strtotime($notaire->etude->start_subscription_date)) ?> au <?php echo date("d/m/Y", strtotime($notaire->etude->end_subscription_date)) ?>
            </div>
        <?php endif; ?>
        <ul>
           <!--  <li>- Veille juridique excellence CRIDON LYON</li> -->
            <li>- Documentation juridique de la quasi intégralité du droit notarial</li>
            <li>- Economie d’abonnements d’autres éditeurs possible</li>
            <li>- Meilleur rapport qualité prix</li>
            <li>- Offre évolutive et mise à jour permanente</li>
        </ul>
        <a href="/wp-content/uploads/pdf/Description-CRIDONLINE-excellence.pdf" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 3 && $subscription ):?>
        <form method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
            <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
            <input type="hidden" name="level" value="<?php echo "3"; ?>" class="js-account-cridonline-level">
            <input type="hidden" name="price" value="<?php echo $priceVeilleLevel3 ?>" class="js-account-cridonline-price">
            <?php if ($notaire->etude->subscription_level == 2 ):?>
                <input type="submit" name="submit" value="<?php _e("Mettre à jour mon abonnement"); ?>">
            <?php else : ?>
                <input type="submit" name="submit" value="<?php _e("souscrire"); ?>">
            <?php endif; ?>
        </form>
        <?php endif;?>
    </div>
</div>
