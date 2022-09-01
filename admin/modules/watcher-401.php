<?php
/**
 * Check if website is password protected
 *
 * @Loaded on plugins_loaded + is_admin() + capability editor
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

add_action( 'admin_init', 'seokey_admin_401_checker', 1 );
/**
 * Handle 401 check
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook admin_init
 */
function seokey_admin_401_checker() {
	// TODO add a CRON to check this periodically
	// Wizard not finished yet
	if ( 'goodtogo' !== get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
		return;
	}
	// Check this only if user is Editor or above
	if ( current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		$current_screen         = htmlspecialchars_decode( seokey_helper_url_get_current() );
		$settings_url           = htmlspecialchars_decode( admin_url('admin.php?page=seo-key-settings') );
		$settings_url_updated   = htmlspecialchars_decode( admin_url('admin.php?page=seo-key-settings&settings-updated=true') );
		// Only when on our settings page
		if ( $current_screen === $settings_url || $current_screen === $settings_url_updated ) {
			// Transient duration
			$timer = DAY_IN_SECONDS;
			$transient = 'seokey_admin_401_checker';
			// First check 401
			if ( false === get_transient( $transient ) ) {
				$login      = get_option('seokey-field-tools-htpasslogin');
				$password   = get_option('seokey-field-tools-htpasspass');
				if (!empty ($login) && !empty($password)) {
					$args['headers'] = [
						'Authorization' => 'Basic ' . base64_encode($login . ':' . $password)
					];
				}
				$args['sslverify']= false;
				// Check 401 with authentification
				$headers = wp_remote_get(home_url(), $args);
				if ( !is_wp_error( $headers ) ) {
					if ( 401 === $headers['response']['code'] ) {
						set_transient($transient, 'protected', $timer );
					} else {
						set_transient($transient, 'not protected', $timer );
					}
				} else {
					set_transient($transient, 'error', $timer );
				}
			}
		}
	}
}

add_filter('seokey_filter_admin_notices_launch', 'seokey_admin_401_notice', SEOKEY_PHP_INT_MAX );
/**
 * Notice for 401 detection
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook seokey_filter_admin_notices_launch
 */
function seokey_admin_401_notice($args) {
	$transient = 'seokey_admin_401_checker';
	if ( 'protected' === get_transient( $transient ) ) {
		$title = '<p>' . esc_html__('Your website is password protected', 'seo-key') . '</p>';
		$tools = sanitize_title( __( 'Tools', 'seo-key' ) );
		$url = seokey_helper_admin_get_link('settings') . '#' . $tools;
		$text = '<p>' . esc_html_x('You have a htpasswd protection. Please enter your login and password on the Tools Tabs here: ', 'notification text for the htpasswd notification', 'seo-key');
		$text.= '<a href="' . $url . '">' . esc_html__( 'Settings page', 'seo-key' ) . '</a></p>';
		$new_args = array(
			sanitize_title('seokey_notice_401'), // Unique ID.
			$title, // The title for this notice.
			$text, // The content for this notice.
			[
				'scope' => 'global', // Dismiss is per-user instead of global.
				'type' => 'error', // Can be one of info, success, warning, error.
				'screens_exclude' => ['seokey_page_seo-key-wizard'],
				'capability' => seokey_helper_user_get_capability('editor'), // only for theses users and above
				'alt_style' => false, // alternative style for notice
				'option_prefix' => 'seokey_option_401_dismissed', // Change the user-meta or option prefix.
				'state' => 'permanent',
			]
		);
		array_push($args, $new_args);
	}
	return $args;
}

add_action( 'added_option', 'seokey_admin_401_watcher', 5 );
add_action( 'updated_option', 'seokey_admin_401_watcher', 5 );
/**
 * When htpass options are changed, reset 401 check
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook updated_option
 */
function seokey_admin_401_watcher( $option_name ){
	$transient = 'seokey_admin_401_checker';
	if ( $option_name === "seokey-field-tools-htpasspass" || $option_name === "seokey-field-tools-htpasslogin" ) {
		delete_transient( $transient );
	}
}