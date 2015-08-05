<?php

/* DOC : http://www.wpexplorer.com/wordpress-theme-options/ */

//register settings
function theme_settings_init(){
    register_setting( 'theme_settings', 'theme_settings' );
}

//add settings page to menu
function add_settings_page() {
add_menu_page( __( 'Theme Settings','bonestheme' ), __( 'Theme Settings','bonestheme' ), 'manage_options', 'settings', 'theme_settings_page');
}

//add actions
add_action( 'admin_init', 'theme_settings_init' );
add_action( 'admin_menu', 'add_settings_page' );

//start settings page
function theme_settings_page() {

if ( ! isset( $_REQUEST['updated'] ) )
$_REQUEST['updated'] = false;

?>

<div>

	<h2><?php echo __( 'Theme Settings','bonestheme' ) //your admin panel title ?></h2>
    
    <h3><?php echo __( 'Google Tracking','bonestheme' ) ?></h3>
    	
	<?php
	//show saved options message
	if ( false !== $_REQUEST['updated'] ) : ?>
	<div><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; ?>
	
	<form method="post" action="options.php">
	
		<?php settings_fields( 'theme_settings' ); ?>
		<?php $options = get_option( 'theme_settings' ); ?>
		
		<div>
			
			<input id="theme_settings[enable_tracking]" name="theme_settings[enable_tracking]" type="checkbox" value="1" <?php checked( '1', $options['enable_tracking'] ); ?> />
			<label for="theme_settings[enable_tracking]"><?php echo __( 'Enable Google Tracking',"bonestheme" ); ?></label>
			<br/>
			<br/>
			<label for="theme_settings[tracking]"><?php echo __( 'Enter your analytics tracking code','bonestheme' ); ?></label>
			<br />
			<textarea id="theme_settings[tracking]" name="theme_settings[tracking]" rows="10" cols="70"><?php esc_attr_e( $options['tracking'] ); ?></textarea>
		
		</div>
		
		<p>
			<input name="submit" id="submit" class="button button-primary button-large" value="<?php echo __( 'Save changes','bonestheme' ); ?>" type="submit">
		</p>
		
	</form>

</div><!-- END wrap -->

<?php
}
//sanitize and validate
function options_validate( $input ) {
    global $select_options, $radio_options;
    if ( ! isset( $input['option1'] ) )
        $input['option1'] = null;
    $input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
    $input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );
    if ( ! isset( $input['radioinput'] ) )
        $input['radioinput'] = null;
    if ( ! array_key_exists( $input['radioinput'], $radio_options ) )
        $input['radioinput'] = null;
    $input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );
    return $input;
}
?>