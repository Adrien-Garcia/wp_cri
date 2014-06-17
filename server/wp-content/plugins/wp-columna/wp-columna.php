<?php
/*
Plugin Name: WP-Columna
Plugin URI: http://web2webs.com/blog/wp-columna
Description: Add columns to visual editor and the page.
Author: Amir Jahanmast
Version: 1.0
Author URI: http://web2webs.com/
Text Domain: wp-columna
Stable tag: 1.0
Domain Path: /lang
*/

/*  
Copyright 2013  Amir Jahanmst

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WPColumna {
	var $pluginUrl;
	
	public function __construct() {	
		// Set Plugin URL
		$this->pluginUrl = WP_PLUGIN_URL . '/wp-columna';
				
		// Add columns style 
		add_filter( 'mce_css', array($this, 'filter_mce_css') );
		
		// Add columns button
		add_action('init', array($this, 'add_column_button'));
		
		// Add style to page
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_stylesheet'));
		
		// Let translate the Description
		__('Add columns to visual editor and the page.', 'wp-columna');
	}

	public function add_column_button() {
	   	// Don't bother doing this stuff if the current user lacks permissions
	   	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages'))
		return;
	 
	   	// Add only in Rich Editor mode
	   	if ( get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array($this, 'add_column_tinymce_plugin'));
		 	add_filter('mce_buttons', array($this, 'register_column_button'));
	   	}
	}
	
	// Add columns style 
	public function filter_mce_css( $mce_css ) {
		global $current_screen;
		$mce_css .= ', ' . $this->pluginUrl . '/style.css?bogus=' . time();
		return $mce_css;
	}
	
	public function register_column_button($buttons) {
	   array_push($buttons, "Columna1", "Columna2", "Columna3", "Columna4");
	   return $buttons;
	}
	 
	// Load the TinyMCE plugin 
	public function add_column_tinymce_plugin($plugin_array) {
	   $plugin_array['Columna1'] = $this->pluginUrl.'/js/b1.js';
	   $plugin_array['Columna2'] = $this->pluginUrl.'/js/b2.js';
	   $plugin_array['Columna3'] = $this->pluginUrl.'/js/b3.js';
	   $plugin_array['Columna4'] = $this->pluginUrl.'/js/b4.js';
	   return $plugin_array;
	}
	
	public function enqueue_stylesheet() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'wp-columna', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'wp-columna' );
    }
}

$wpColumna = new WPColumna;