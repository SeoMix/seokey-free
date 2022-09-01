<?php
/**
 * Load every SEOKEY ajax call
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
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

// Die for unauthenticated users
add_action( 'wp_ajax_nopriv_seokey_settings_tab', 'seokey_die' );
// Trigger hook for authenticated users
add_action( 'wp_ajax_seokey_settings_tab', 'seokey_ajax_cb_settings_tab' );
/**
 * Save the last visited tab for this user
 *
 * @since 0.0.1
 * @author Julio Potier
 *
 * @hook wp_ajax_seokey_settings_tab
 * @see wp_send_json_error()
 * @see wp_send_json_success()
 **/
function seokey_ajax_cb_settings_tab() {
	// Security (die if incorrect nonce)
	check_ajax_referer( 'seokey-form-settings' );
	// Security : check if user is at least an author
	if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
		wp_send_json_error();
		die;
	}
	// get current user data
	$user = wp_get_current_user();
	$tabs = get_user_meta( $user->ID, 'seokey-settings-tab', true );
	$tabs = is_array( $tabs ) ? $tabs : [];
	$_tab = isset( $_POST['tab'] ) ? sanitize_title( $_POST['tab'] ) : '';
	$page = isset( $_POST['page'] ) ? sanitize_title( $_POST['page'] ) : 'default';
	$tabs[ $page ] = $_tab;
	// Change meta
	update_user_meta( $user->ID, 'seokey-settings-tab', $tabs );
	// good to go
	wp_send_json_success();
}

// Die for unauthenticated users
add_action( 'wp_ajax_nopriv_seokey_audit_tab', 'seokey_die' );
// Trigger hook for authenticated users
add_action( 'wp_ajax_seokey_audit_tab', 'seokey_ajax_cb_audit_tab' );
/**
 * Save the last visited tab for this user
 *
 * @since 0.0.1
 * @author Julio Potier
 *
 * @hook wp_ajax_seokey_settings_tab
 * @see wp_send_json_error()
 * @see wp_send_json_success()
 **/
function seokey_ajax_cb_audit_tab() {
    // Security (die if incorrect nonce)
    check_ajax_referer( 'seokey-audit-tabs-nonce' );
    // Security : check if user is at least an author
    if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
        wp_send_json_error();
        die;
    }
    // get current user data
    $user = wp_get_current_user();
    $tabs = get_user_meta( $user->ID, 'seokey-audit-tab', true );
    $tabs = is_array( $tabs ) ? $tabs : [];
    $_tab = isset( $_POST['tab'] ) ? sanitize_title( $_POST['tab'] ) : '';
    $page = isset( $_POST['page'] ) ? sanitize_title( $_POST['page'] ) : 'default';
    $tabs[ $page ] = $_tab;
    // Change meta
    update_user_meta( $user->ID, 'seokey-audit-tab', $tabs );
    // good to go
    wp_send_json_success();
}
