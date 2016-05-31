<?php if ($notaire->etude->subscription_level < 2 && $subscription ):?>
    <?php $isSubcribable = ($notaire->etude->subscription_level < 3 && $subscription) ?>
<?php endif; ?>
<div class="bloc-cridonline niveau-1 <?php echo $notaire->etude->subscription_level == 1 ? ' on' : ''; ?> <?php echo $isSubcribable ? " large" : "" ?>">
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
        <a href="<?php echo CONST_CRIDONLINE_DOCUMENT_REFERENCE_PROMO ?>" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
    </div>
</div>

<div class="bloc-cridonline niveau-2 <?php echo $notaire->etude->subscription_level == 1 ? ' non-actif' : ''; ?><?php echo $notaire->etude->subscription_level == 2 ? ' on' : ''; ?> <?php echo $notaire->etude->subscription_level == 3 ? ' normal' : ''; ?><?php echo $isSubcribable ? " large" : "" ?>">
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
            <li>- OFFRE CHOC</li>
            <li>&nbsp;&nbsp;&nbsp;<strong>Jusqu’à 6 mois offerts</strong></li>
            <li>- Abonnement 2016 non facturé</li>
        </ul>
        <a href="<?php echo CONST_CRIDONLINE_DOCUMENT_PREMIUM_PROMO ?>" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 2 && $subscription ):?>
            <form method="get" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
                <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
                <input type="hidden" name="level" value="<?php echo "2"; ?>" class="js-account-cridonline-level">
                <input type="hidden" name="price" value="<?php echo $priceVeilleLevel2 ?>" class="js-account-cridonline-price">
                <input type="submit" name="submit" value="<?php _e("souscrire à l'offre choc"); ?>">
            </form>
        <?php endif;?>
    </div>
</div>
<div class="bloc-cridonline niveau-3 <?php echo $notaire->etude->subscription_level == 3 ? ' on' : ''; ?><?php echo $notaire->etude->subscription_level == 1 ? '' : ' non-actif'; ?><?php echo $notaire->etude->subscription_level == 2 ? '' : ' non-actif'; ?><?php echo $isSubcribable ? " large" : "" ?>">
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
            <li>- OFFRE CHOC</li>
            <li>&nbsp;&nbsp;&nbsp;<strong>Jusqu’à 6 mois offerts</strong></li>
            <li>ou</li>
            <li>- OFFRE PRIVILEGIEE</li>
            <li>&nbsp;&nbsp;&nbsp;<strong>Jusqu’à 2 000 € HT de remise</strong></li>
            <li>- Abonnement de 2 ans minimum</li>
            <li>- Excellence au prix du Premium la première année</li>
        </ul>
        <a href="<?php echo CONST_CRIDONLINE_DOCUMENT_EXCELLENCE_PROMO ?>" target="_blank" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 3 && $subscription ):?>
        <form method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
            <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
            <input type="hidden" name="level" value="<?php echo "3"; ?>" class="js-account-cridonline-level">
            <input type="hidden" name="price" value="<?php echo $priceVeilleLevel3 ?>" class="js-account-cridonline-price">
                <input type="submit" name="submit" class="bt1" value="<?php _e("Souscrire à l'offre choc"); ?>">            
                <input type="submit" name="submit" class="bt2" value="<?php _e("souscrire à l'offre privilégié"); ?>">
        </form>
        <?php endif;?>
    </div>
</div>
