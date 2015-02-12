<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define( 'WP_ROCKET_ADVANCED_CACHE', true );
$rocket_cache_path = 'C:\Devl\wp_maestro\server/wp-content/cache/wp-rocket/';
$rocket_config_path = 'C:\Devl\wp_maestro\server/wp-content/wp-rocket-config/';

if ( file_exists( 'C:\Devl\wp_maestro\server\wp-content\plugins\wp-rocket\inc\front/process.php' ) ) {
include( 'C:\Devl\wp_maestro\server\wp-content\plugins\wp-rocket\inc\front/process.php' );
}