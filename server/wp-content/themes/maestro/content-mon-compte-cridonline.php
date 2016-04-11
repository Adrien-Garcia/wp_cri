<h2><?php _e("L'offre crid’online"); ?></h2>

<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus</p>

<div class="bloc-cridonline niveau-1">
    <div class="en-tete">
        
    </div>
    <div class="content">
        <div class="titre">
            Compris dans votre cotisation générale annuelle
        </div>
        <ul>
            <li>- Lorem Ipsum dolor sit amet</li>
            <li>- Consectur alt</li>
            <li>- Sed do erusmad tempor</li>
        </ul>
        <a href="" title="plus de detail"><?php _e("Plus de detail"); ?></a>
    </div>
</div>
<div class="bloc-cridonline niveau-2">
    <div class="en-tete"></div>
    <div class="content">
        <div class="prix">
           <?php echo $priceVeilleLevel2 ?><span>€/an</span>
        </div>
        <ul>
            <li>- Lorem Ipsum dolor sit amet</li>
            <li>- Consectur alt</li>
            <li>- Sed do erusmad tempor</li>
        </ul>
        <a href="" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 2 ):?>
            <form method="get" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
                <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
                <input type="hidden" name="level" value="<?php echo "2"; ?>" class="js-account-cridonline-level">
                <input type="hidden" name="price" value="<?php echo $priceVeilleLevel2 ?>" class="js-account-cridonline-price">
                <input type="submit" name="submit" value="<?php _e("souscrire"); ?>">
            </form>
        <?php endif;?>
    </div>
</div>

<div class="bloc-cridonline niveau-3">
    <div class="en-tete"></div>
    <div class="content">
        <div class="prix">
            <?php echo $priceVeilleLevel3 ?><span>€/an</span>
        </div>
        <ul>
            <li>Lorem Ipsum dolor sit amet</li>
            <li>Consectur alt</li>
            <li>Sed do erusmad tempor</li>
        </ul>
        <a href="" title="plus de detail"><?php _e("Plus de detail"); ?></a>
        <?php if ($notaire->etude->subscription_level < 3 ):?>
            <form method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-form" data-js-ajax-validation-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentcridonlineetape2'));?>">
                <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-crpcen">
                <input type="hidden" name="level" value="<?php echo "3"; ?>" class="js-account-cridonline-level">
                <input type="hidden" name="price" value="<?php echo $priceVeilleLevel3 ?>" class="js-account-cridonline-price">
                <input type="submit" name="submit" value="<?php _e("souscrire"); ?>">
            </form>
        <?php endif;?>
    </div>
</div>

<div class="wrap-link">
   <a href="" title="CGV" target="_blank"><?php _e("Télécharger les conditions générales de vente (CGV)"); ?></a> 
</div>
