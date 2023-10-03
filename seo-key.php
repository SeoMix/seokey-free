<?php
/**
 * Plugin Name: SEOKEY
 * Plugin URI: https://www.seo-key.com
 * Description:  The Key to WordPress SEO. SEOKEY is a simple and efficient Search Engine Audit and Optimization plugin for WordPress.
 * Author: SeoMix - Daniel Roch
 * Author URI: https://www.seomix.fr
 * Contributors: Daniel Roch, Léo Fontin, Julio Potier, Gauvain Van Ghele
 * Text Domain: seo-key
 * Domain Path: /public/assets/languages/
 * Version: 1.7.1
 * Requires at least: 5.5
 * Tested up to: 6.3.1
 * Requires PHP: 7.2
 * Network: true
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright (C) 2017-2020, SEOKEY - contact@seo-key.com
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/* Prevent errors if SEOKEY pro is active: do not load SEOKEY free */
if ( defined( 'SEOKEY_NAME' ) ) {
	return;
}
/**
 * SEOKEY constants
 *
 * Define SEOKEY constants
 *
 * @since 0.0.1
 * @author Daniel Roch
 */
// Constants that may need to be changed on each update
define( 'SEOKEY_PHP_MIN',               '7.2' );                                                // PHP Minimum Version
define( 'SEOKEY_WP_MIN',                '5.5' );                                                // WP Minimum Version
define( 'SEOKEY_VERSION', 			    '1.7.1' );                                              // SEOKEY actual version
// Static Constants
define( 'SEOKEY_SETTINGS_SLUG', 	    'seokey-settings' );                            	    // SEOKEY Settings Slug in options table
define( 'SEOKEY_HOME', 				    'https://www.seo-key.com/' );                           // SEOKEY Website
define( 'SEOKEY_NAME', 				    'SEOKEY' );                                             // SEOKEY Name
define( 'SEOKEY_SLUG', 				    'seo-key' );                                            // SEOKEY Slug (seo-key)
define( 'SEOKEY_PATH_ROOT', 		    realpath( plugin_dir_path( __FILE__ ) ) . '/' );    // Directory path (xxx/wp-content/plugins/seo-key/)
define( 'SEOKEY_PATH_COMMON', 		    realpath( SEOKEY_PATH_ROOT . 'common/' ) . '/' );	// Common directory path
define( 'SEOKEY_PATH_ADMIN', 		    realpath( SEOKEY_PATH_ROOT . 'admin/' ) . '/' );   // Admin directory path
define( 'SEOKEY_PATH_PUBLIC', 		    realpath( SEOKEY_PATH_ROOT . 'public/' ) . '/' ); 	// Public directory path
define( 'SEOKEY_DIRECTORY_ROOT',        plugin_basename( __FILE__ ) );                      // Admin directory path
define( 'SEOKEY_URL_ASSETS', 		    plugin_dir_url( __FILE__ ) . 'admin/assets/' );     // Assets directory URL
define( 'SEOKEY_URL_ASSETS_PUBLIC',     plugin_dir_url( __FILE__ ) . 'public/assets/' );    // Assets directory URL
define( 'SEOKEY_URL', 		            plugin_dir_url( __FILE__ ) );                       // Plugin directory URL
define( 'SEOKEY_PHP_INT_MAX', 	        PHP_INT_MAX - 20 );                                     // Custom PHP_INT_MAX
define( 'SEOKEY_SITEMAPS_PATH',         wp_upload_dir()['basedir'].'/seokey/sitemaps/' );       // Sitemap folder path

// Audit Constants
define( 'CONTENT_MIN_WORDS_COUNT',      300 );                                                  // Minimum words in content
define( 'CONTENT_MIN_KEYWORD_RATIO',    10 );                                                   // Minimum keyword count in content
define( 'METATITLE_COUNTER_MIN', 	    10 );                                          		    // Minimum length for a meta title
define( 'METATITLE_COUNTER_MAX', 	    65 );                                         		    // Maximum length for a meta title
define( 'METADESC_COUNTER_MIN', 	    40 );                                         		    // Minimum length for a meta description
define( 'METADESC_COUNTER_MAX', 	    155 );                                                  // Maximum length for a meta description
// Warning, hardcode data in seokey-metabox : needs to be fixed

/**
 * SEOKEY main class
 *
 * Launch every function of SEOKEY plugin
 *
 * @since 0.0.1
 * @author Daniel Roch
 */
class SEOKEY_Free {
	/**
	 * Define only one instance of our class
	 * @since   0.0.1
	 * @author  Daniel Roch - Vincent Blée
	 *
	 * @var (array) $instance Singleton
	 * @access private
	 * @static
	 */
	public static $instance = null;
	/**
	 * Construct SEOKEY class & avoid launching concurrent objects
	 *
	 * @author  Daniel Roch - Vincent Blée
	 *
	 * @since   0.0.1
	 */
	// Launch our stuff while constructing class
	public function __construct() {
		self::seokey_load();
	}
	// Unserializing instances of this class is forbidden.
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	// Wakeup of this class is forbidden.
	public function __wakeup() {}
	// Cloning of this class is forbidden.
	private function __clone() {}

	/**
	 * Launch SEOKEY core functions
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public static function seokey_load() {
		// Load language files
		add_action( 'plugins_loaded', array( __CLASS__, 'seokey_plugin_language' ) );
		// Security
		require SEOKEY_PATH_COMMON . 'seo-key-security.php';
		// Get common helpers and functions
		require_once SEOKEY_PATH_COMMON . 'seo-key-helpers.php';
        // Check if Website is multilingual
        if( function_exists('seokey_helpers_get_languages') ){
            seokey_helpers_get_languages();
        }
		// For activation and deactivation purposes
		if ( is_admin() ) {
			// Add activation rules
			register_activation_hook( __FILE__, array( __CLASS__, 'seokey_plugin_activation' ) );
			// Add deactivation rules
			register_deactivation_hook( __FILE__, array( __CLASS__, 'seokey_plugin_deactivation' ) );
			// Add uninstall rules
			register_uninstall_hook( __FILE__, array( __CLASS__, 'seokey_plugin_uninstall' ) );
		}
		// Remove right now native sitemaps generation
		if ( 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
			remove_action( 	'init', 'wp_sitemaps_get_server' );
		}
		// Plugin initialization
		add_action( 'plugins_loaded', array( __CLASS__, 'seokey_plugin_init' ) );
	}

	/**
	 * SEOKEY Language Files
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public static function seokey_plugin_language() {
		load_plugin_textdomain( 'seo-key', false, basename( __DIR__ ) . '/public/assets/languages' );
	}

	/**
	 * Activation function for SEOKEY
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public static function seokey_plugin_activation() {
		require_once SEOKEY_PATH_COMMON . 'seo-key-helpers.php';
		require_once SEOKEY_PATH_ADMIN . 'plugin-activate-deactivate-uninstall.php';
		add_option( 'seokey-activation-deactivation-process', 'seokey-activation-deactivation-process-on', '', true );
		$seokey_plugin = SeoKeyActivateDeactivate::get_instance();
		$seokey_plugin->init( 'activation' );
		delete_option( 'seokey-activation-deactivation-process' );
	}

	/**
	 * Deactivation functions for SEOKEY
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public static function seokey_plugin_deactivation() {
		require_once SEOKEY_PATH_COMMON . 'seo-key-helpers.php';
		require_once SEOKEY_PATH_ADMIN . 'plugin-activate-deactivate-uninstall.php';
		add_option( 'seokey-activation-deactivation-process', 'seokey-activation-deactivation-process-on', '', true );
		$seokey_plugin = SeoKeyActivateDeactivate::get_instance();
		$seokey_plugin->init( 'deactivation' );
		delete_option( 'seokey-activation-deactivation-process' );
	}

	/**
	 * Uninstall functions for SEOKEY
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public static function seokey_plugin_uninstall() {
		require_once SEOKEY_PATH_COMMON . 'seo-key-helpers.php';
		require_once SEOKEY_PATH_ADMIN . 'plugin-activate-deactivate-uninstall.php';
		$seokey_plugin = SeoKeyActivateDeactivate::get_instance();
		$seokey_plugin->init( 'uninstall' );
	}
	
	/**
	 * Initialize every SEOKEY module
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public static function seokey_plugin_init() {
		// Load deprecated functions if necessary
		require SEOKEY_PATH_COMMON . 'seo-key-deprecated.php';
		// Configuration data (menus, settings, etc.)
		require SEOKEY_PATH_COMMON . 'seo-key-config.php';
		// Admin Modules
		require SEOKEY_PATH_ADMIN . 'admin-modules.php';
		// Public Modules
		require SEOKEY_PATH_PUBLIC . 'public-modules.php';
		// Third party Modules
		require SEOKEY_PATH_ROOT . 'third-party/third-party.php';
        // Upgrader
        require_once SEOKEY_PATH_ADMIN . 'plugin-upgrade.php';
		/**
		 *
		 * Fires when SEOKEY has load all files
		 *
		 * @since 0.0.1
		 */
		do_action( 'seokey_loaded' );
	}
}

// Let's go !
SEOKEY_Free::get_instance();