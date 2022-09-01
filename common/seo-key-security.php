<?php
/**
 * Common Security functions for SEOKEY
 *
 * @Loaded on 'init'
 *
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

/**
 * Get required capability for users
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @param bool (string) $type User Required Capability
 * @return bool (string) $role User Required Capability
 */
function seokey_helper_user_get_capability( $type ) {
	// Allowed types
	$types = [
		'admin'         => 'manage_options',
		'editor'        => 'edit_others_posts',
		'author'        => 'publish_posts',
		'contributor'   => 'edit_posts',
		'guest'         => 'read',
	];
	/**
	 * Filter capability to have access to SEOKEY features
	 *
	 * @since 0.0.1
	 *
	 * @param (string) $types User capabilities.
	 */
	$types = apply_filters( 'seokey_filter_helper_user_get_capability', $types );
	// No type => return false
	if ( ! $type || ! isset( $types[ $type ] ) ) {
		return false;
	}
	// Return the capability
	return $types[ $type ];
}

add_filter( 'admin_page_access_denied', 'seokey_admin_deny_access' );
/**
 * Access message denial for SEOKEY admin pages
 *
 * @hook admin_page_access_denied
 * @global $plugin_page
 * @since  0.0.1
 * @author Daniel Roch
 */
function seokey_admin_deny_access() {
	global $plugin_page;
	// Get main admin page of current plugin seen by user
	$admin_page_parent = get_admin_page_parent();
	// Is it our main plugin URL ?
	$die_in_hell = SEOKEY_SLUG === $admin_page_parent;
	// If it's not our main plugin URL
	if ( ! $die_in_hell ) {
		// If we have data
		if ( isset( $plugin_page ) ) {
			// If it's really our plugin URL
			$die_in_hell = 0 === strpos( $plugin_page, SEOKEY_SLUG );
		}
	}
	// Die in hell if user is trying to incorrectly access SEOKEY Menus !
	if ( $die_in_hell ) {
		wp_die ( sprintf( __( '<h1>Are you trying to break into %s ?</h1><p>We won‘t give you the KEY.</p>', 'seo-key' ), SEOKEY_NAME, 403 ) );
	}
}

add_filter( 'http_request_args', 'seokey_admin_htpasswd_headers', 10, 2 );
/**
 * Filter HTTP Headers to allow SEOKEY ajax or CRON calls with a htpasswd protection
 *
 * @hook http_request_args
 * @since  0.0.1
 * @author Daniel Roch
 */
function seokey_admin_htpasswd_headers( $args, $url ) {
    // Only for internal calls
    if ( 0 === strpos( $url, get_site_url() ) ) {
        // Only for SEOKEY  calls
	    // It's a specific request for SEOKEY
        if ( str_contains( $url, 'seokey_' ) ) {
            $login      = get_option( 'seokey-field-tools-htpasslogin' );
            $password   = get_option( 'seokey-field-tools-htpasspass' );
            if ( !empty ( $login ) && !empty( $password ) ) {
                $args['headers'] = [
                    'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password )
                ];
            }
        }
    }
    return $args;
}