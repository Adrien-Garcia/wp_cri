<h2><?php _e("Crid'Online"); ?></h2>

<div id="cridonline-validation">
    <?php echo get_template_part("content","cridonline-validation"); ?>
</div>
<div class="description">
	<?php if ($notaire->etude->subscription_level == 1 ):?>
		<script type="text/javascript">
			//<![CDATA[
			jsvar.newsletter_success_msg = "Inscription terminée avec succès.";
			//]]>
		</script>
	<?php _e('Vous bénéficiez d\'un accès aux veilles de niveau 1.'); ?>
	<?php elseif ($notaire->etude->subscription_level == 2 ):?>
		<script type="text/javascript">
			//<![CDATA[
			jsvar.newsletter_success_msg = "Désinscription terminée avec succès.";
			//]]>
		</script>
		<?php _e('Vous bénéficiez d\'un accès aux veilles de niveau 2 au prix de : '.$notaire->etude->subscription_price.'.'); ?>
        <?php elseif ($notaire->etude->subscription_level == 3 ) : ?>
        <script type="text/javascript">
            //<![CDATA[
            jsvar.newsletter_success_msg = "Désinscription terminée avec succès.";
            //]]>
        </script>
        <?php _e('Vous bénéficiez d\'un accès aux veilles de niveau 3 au prix de : '.$notaire->etude->subscription_price.'.'); ?>
	<?php endif; ?>
</div>

<?php if ($notaire->etude->subscription_level < 2 ):?>
    <form action="/notaires/cridonline" method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-sublevel-form">
        <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-sublevel-crpcen">
        <input type="hidden" name="level" value="<?php echo "2"; ?>" class="js-account-cridonline-sublevel-level">
        <input type="hidden" name="price" value="<?php echo $priceVeilleLevel2 ?>" class="js-account-cridonline-sublevel-price">
        <input type="submit" name="submit" value="<?php _e("S'abonner au niveau 2"); ?>">
    </form>
    <p>Price : <?php echo $priceVeilleLevel2 ?></p>
<?php endif;?>
<?php if ($notaire->etude->subscription_level < 3 ):?>
<form action="/notaires/cridonline" method="post" accept-charset="utf-8" class="form-sublevel js-account-cridonline-sublevel-form">
    <input type="hidden" name="crpcen" value="<?php echo $notaire->crpcen; ?>" class="js-account-cridonline-sublevel-crpcen">
    <input type="hidden" name="level" value="<?php echo "3"; ?>" class="js-account-cridonline-sublevel-level">
    <input type="hidden" name="price" value="<?php echo $priceVeilleLevel3 ?>" class="js-account-cridonline-sublevel-price">
    <input type="submit" name="submit" value="<?php _e("S'abonner au niveau 3"); ?>">
    <p>Price : <?php echo $priceVeilleLevel3 ?></p>
</form>
<?php endif;?>
<div id="subLevelMsgId" class="js-account-cridonline-sublevel-message">
</div>
