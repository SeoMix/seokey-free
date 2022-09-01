<?php
/**
 * Admin Redirection module
 *
 * @Loaded on plugins_loaded + capability editor
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/* Define useful loading var */
$modules        = SEOKEY_PATH_ADMIN . 'modules/redirections/';

/**
 * SQL functions (create and delete tables)
 */
seokey_helper_require_file( 'redirections_sql',     $modules, 'editor' );

/**
 * Interface functions
 * view.php is loaded from core plugin menu functions (common/seo-key-config.php)
 */
seokey_helper_require_file( 'view_helpers',         $modules, 'editor' );

/**
 * Form functions
 * form.php is loaded from view.php in core plugin menu functions (common/seo-key-config.php)
 */
seokey_helper_require_file( 'form_helpers',         $modules, 'editor' );

/**
 * Redirection list
 */
seokey_helper_require_file( 'redirections_default', $modules, 'editor' );

/**
 * Errors functions
 */
// TODO Factorisation between files redirections_errors & redirections_guessed
//seokey_helper_require_file( 'redirections_errors_pro',  $modules, 'editor' );
seokey_helper_require_file( 'redirections_errors',      $modules, 'editor' );

/**
 * Guessed functions
 */
//seokey_helper_require_file( 'redirections_guessed_pro',  $modules, 'editor' );
seokey_helper_require_file( 'redirections_guessed', $modules, 'editor' );


/**
 * Redirection security helper: Check User Role
 */
function seokey_redirection_check_capabilities() {
    if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
        wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
    }
}

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_redirections' );
/**
 * Enqueue assets (CSS) for Redirection module
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook    admin_enqueue_scripts
 * @uses    wp_enqueue_style()
 * @global $pagenow
 * @return void
 */
function seokey_enqueue_admin_redirections() {
	$screen = seokey_helper_get_current_screen();
	if ( $screen->id === 'seokey_page_seo-key-redirections' ) {
		$currenttab = ( !empty( $_GET["tab"] ) ) ? sanitize_title( $_GET["tab"] ) : "default";
		// Add JS
		switch ( $currenttab ) {
			case 'default':
				wp_enqueue_script( 'seokey-js-redirections', SEOKEY_URL_ASSETS . 'js/seokey-redirections.js', array(
					'jquery'
				), SEOKEY_VERSION, true );
				break;
		}
		// Localize script arguments
		$args = array(
			// Ajax URL
			'ajax_url'            => admin_url( 'admin-ajax.php' ),
			// Where we will need to insert our data
			'table_name_div'      => '#seokey_redirections',
			// Fallback nojs to remove on ajax success
			'table_name_div_nojs' => '#seokey_redirections_no_js',
			// PHP function to display our data
			'display_action'      => '_seokey_redirections_ajax_display_' . sanitize_title( $currenttab ),
			// Security nonce : display list
			'security'            => wp_create_nonce( 'seokey_redirection_list' ),
			// Security nonce : redirection actions
			'ajax_nonce'          => wp_create_nonce( 'seokey-set-redirection' ),
		);
		wp_localize_script( 'seokey-js-redirections', 'adminAjax', $args );
		// Add CSS on all tabs
		wp_enqueue_style('dashicons');
		wp_enqueue_style( 'seokey-css-redirections', SEOKEY_URL_ASSETS . 'css/seokey-redirections.css', 'dashicons', SEOKEY_VERSION );
	}
}