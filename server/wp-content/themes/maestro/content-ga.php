<?php
if (CriIsNotaire()) :
    $notaire = CriNotaireData();
    ?>
    <script>
        dataLayer = [{
            'userID': '<?php echo $notaire->client_number ?>',
            'Group': '<?php echo $notaire->category ?>'
        }];
    </script>
<?php endif; ?>
<?php

//get theme options
$options = get_option( 'theme_settings' );

// check if the enable tracking is on, then show it
if (isset($options['enable_tracking']) && $options['enable_tracking'] == true) : ?>

	<?php echo stripslashes($options['tracking']); ?>

<?php endif; ?>

