<?php
if (CriIsNotaire()) :
    $notaire = CriNotaireData();
    ?>
    <script type="text/javascript">
        //<![CDATA[
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'userID': '<?php echo $notaire->client_number ?>',
            'Group': '<?php echo $notaire->category ?>',
            'CRPCEN': '<?php echo $notaire->crpcen ?>',
            'etudeName': '<?php echo $notaire->etude->office_name ?>',
            'userName': '<?php echo $notaire->last_name ?> <?php echo $notaire->first_name ?>',
            'etudePostalCode': '<?php echo $notaire->etude->cp ?>',
            'userFonction': '<?php echo $notaire->fonction->label ?>'
        });
        //]]>
    </script>
<?php endif; ?>
<?php

//get theme options
$options = get_option( 'theme_settings' );

// check if the enable tracking is on, then show it
if (isset($options['enable_tracking']) && $options['enable_tracking'] == true) : ?>

	<?php echo stripslashes($options['tracking']); ?>

<?php endif; ?>

