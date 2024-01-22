<?php
namespace Jet_Engine_Listing_As_Filter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Main file
 */
class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

	/**
	 * Initialize plugin parts
	 *
	 * @return void
	 */
	public function on_init() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			require_once JET_ENGINE_LISTING_AS_FILTER_PATH . 'includes/elementor-integration.php';
			new Elementor_Integration();
		}
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		if ( ! function_exists( 'jet_engine' ) || ! function_exists( 'jet_smart_filters' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'on_init' ), 0 );

	}

}

Plugin::instance();
