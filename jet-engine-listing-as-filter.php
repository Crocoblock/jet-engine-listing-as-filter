<?php
/**
 * Plugin Name: JetEngine - Listing as filter
 * Plugin URI:
 * Description: Allow to use JetEngine listing as filter for another listing.
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:
 * Text Domain: jet-engine-listing-as-filter
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

define( 'JET_ENGINE_LISTING_AS_FILTER_VERSION', '1.0.0' );

define( 'JET_ENGINE_LISTING_AS_FILTER__FILE__', __FILE__ );
define( 'JET_ENGINE_LISTING_AS_FILTER_PLUGIN_BASE', plugin_basename( JET_ENGINE_LISTING_AS_FILTER__FILE__ ) );
define( 'JET_ENGINE_LISTING_AS_FILTER_PATH', plugin_dir_path( JET_ENGINE_LISTING_AS_FILTER__FILE__ ) );
define( 'JET_ENGINE_LISTING_AS_FILTER_URL', plugins_url( '/', JET_ENGINE_LISTING_AS_FILTER__FILE__ ) );

add_action( 'plugins_loaded', 'jet_engine_listing_as_filter_init' );

function jet_engine_listing_as_filter_init() {
	require JET_ENGINE_LISTING_AS_FILTER_PATH . 'includes/plugin.php';
}
