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
	
	<?php
	//show saved options message
	if ( false !== $_REQUEST['updated'] ) : ?>
	<div><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; ?>
	
	<form method="post" action="options.php">
	
		<?php settings_fields( 'theme_settings' ); ?>
		<?php $options = get_option( 'theme_settings' ); ?>
		<div>
      <?php array("textarea_name" => "theme_settings[footer_block_content_top]");
       ?>
      <h3><?php echo __( 'Footer blocks content','bonestheme' )?></h3>
      
      <label for="theme_settings[footer_block_content_top]"><?php echo __( 'Top footer block (all screen size)',"bonestheme" ); ?></label>
      <br/>
      <?php wp_editor( $options['footer_block_content_top'] , "themesettingsfooterblockcontenttop",array("textarea_name" => "theme_settings[footer_block_content_top]")) ?>
      <br/>
      <br/>
      <label for="theme_settings[footer_block_content_bottom]"><?php echo __( 'Bottom footer block (only on desktop)',"bonestheme" ); ?></label>
      <br/>
      <?php wp_editor( $options['footer_block_content_bottom'] , "themesettingsfooterblockcontentbottom",array("textarea_name" => "theme_settings[footer_block_content_bottom]")) ?>

      <br/>
    </div>

    <hr>

		<div>
			
			<h3><?php echo __( 'Google Tracking','bonestheme' )?></h3>
			
			<input id="theme_settings[enable_tracking]" name="theme_settings[enable_tracking]" type="checkbox" value="1" <?php checked( '1', $options['enable_tracking'] ); ?> />
			<label for="theme_settings[enable_tracking]"><?php echo __( 'Enable Google Tracking',"bonestheme" ); ?></label>
			<br/>
			<br/>
			<label for="theme_settings[tracking]"><?php echo __( 'Enter your analytics tracking code','bonestheme' ); ?></label>
			<br />
			<textarea id="theme_settings[tracking]" name="theme_settings[tracking]" rows="10" cols="70"><?php esc_attr_e( $options['tracking'] ); ?></textarea>
		
		</div>
		
		<hr>
		
		<div>
		
            <h3><?php echo __( 'Slider Options','bonestheme' ) ?></h3>
            
            <h4><?php echo __( 'General','bonestheme' ) ?></h4>
            
            <input id="full" type="radio" name="theme_settings[slider]" value="full"<?php checked( 'full' == $options['slider'] ); ?> />
            <label for="full"><?php echo __( 'Enable "Full Slider"',"bonestheme" ); ?></label><br />
            <input id="normal" type="radio" name="theme_settings[slider]" value="normal"<?php checked( 'normal' == $options['slider'] ); ?> />
            <label for="normal"><?php echo __( 'Enable "Normal Slider"',"bonestheme" ); ?></label><br />
            <input id="none" type="radio" name="theme_settings[slider]" value="none"<?php checked( 'none' == $options['slider'] ); ?> />
            <label for="none"><?php echo __( 'Disable Slider',"bonestheme" ); ?></label><br><br>
        
            <input id="theme_settings[enable_responsive]" name="theme_settings[enable_responsive]" type="checkbox" value="1" <?php checked( '1', $options['enable_responsive'] ); ?> />
			<label for="theme_settings[enable_responsive]"><?php echo __( 'Enable Responsive Slider',"bonestheme" ); ?></label>
        
            <h4><?php echo __( 'BxSlider parameters','bonestheme' ) ?></h4>
            
            <label><?php echo __( 'Mode',"bonestheme" ); ?></label>
            <select id="theme_settings[mode]" name="theme_settings[mode]">
              <option value="horizontal" <?php selected( 'horizontal' == $options['mode'] ); ?>>horizontal</option> 
              <option value="vertical" <?php selected( 'vertical' == $options['mode'] ); ?>>vertical</option>
              <option value="fade" <?php selected( 'fade' == $options['mode'] ); ?>>fade</option>
            </select><br><br>
            
            <label><?php echo __( 'Speed',"bonestheme" ); ?></label>
            <input id="theme_settings[speed]" type="text" name="theme_settings[speed]" value="<?php esc_attr_e( $options['speed'] ); ?>"><br><br>
            
            <label><?php echo __( 'InfiniteLoop',"bonestheme" ); ?></label>
            <select id="theme_settings[infiniteLoop]" name="theme_settings[infiniteLoop]">
              <option value="true" <?php selected( 'true' == $options['infiniteLoop'] ); ?>>true</option> 
              <option value="false" <?php selected( 'false' == $options['infiniteLoop'] ); ?>>false</option>
            </select><br><br>
            
            <label><?php echo __( 'Pager',"bonestheme" ); ?></label>
            <select id="theme_settings[pager]" name="theme_settings[pager]">
              <option value="true" <?php selected( 'true' == $options['pager'] ); ?>>true</option> 
              <option value="false" <?php selected( 'false' == $options['pager'] ); ?>>false</option>
            </select><br><br>
            
            <label><?php echo __( 'PagerType',"bonestheme" ); ?></label>
            <select id="theme_settings[pagerType]" name="theme_settings[pagerType]">
              <option value="full" <?php selected( 'full' == $options['pagerType'] ); ?>>full</option> 
              <option value="short" <?php selected( 'short' == $options['pagerType'] ); ?>>short</option>
            </select><br><br>
            
            <label><?php echo __( 'Controls',"bonestheme" ); ?></label>
            <select id="theme_settings[controls]" name="theme_settings[controls]">
              <option value="true" <?php selected( 'true' == $options['controls'] ); ?>>true</option> 
              <option value="false" <?php selected( 'false' == $options['controls'] ); ?>>false</option>
            </select><br><br>
            
        </div>
		
		<hr>
		
		<div>
 
            <h3><?php echo __( 'Mobile Menu Options','bonestheme' ) ?></h3>
            
            <input id="theme_settings[enable_menu]" name="theme_settings[enable_menu]" type="checkbox" value="1" <?php checked( '1', $options['enable_menu'] ); ?> />
			<label for="theme_settings[enable_menu]"><?php echo __( 'Enable Mobile Menu',"bonestheme" ); ?></label>
            
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