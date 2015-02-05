<?php

//get theme options
$options = get_option( 'theme_settings' );

// check if the enable tracking is on, then show it
if ($options['enable_tracking'] == true) : ?>

	<?php echo stripslashes($options['tracking']); ?>
	
<?php endif; ?>