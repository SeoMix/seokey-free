<?php
/**
 * Third party: Plugi Yoast SEO
 *
 * @Loaded on plugins_loaded + wizard done
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

// Switch from Yoast breadcrumbs to SEOKEY breadcrumbs
if ( !function_exists('yoast_breadcrumb') && !is_admin() ) {
	function yoast_breadcrumb( $before = '', $after = '' ) {
		if ( empty( $before ) && empty( $after ) ) {
			echo seokey_breacrumbs_print();
		} else {
			echo seokey_breacrumbs_print( $before, $after );
		}
	}
}

// Avoid error with OceanWP and their Yoast functions
if ( !class_exists( 'WPSEO_Options' ) ) {
	class WPSEO_Options {
		public static function get( $key, $default_value = null ) {
			return $true;
		}
	}
}

add_action ( 'template_redirect', 'seokey_thirdparty_yoast_sitemaps', 20000 );
/**
 * Redirect 404 sitemaps to SEOKEY main sitemap
 */
function seokey_thirdparty_yoast_sitemaps() {
	if ( is_404() ) {
		// Is it a Yoast sitemap
		if ( true === str_ends_with( seokey_helper_url_get_current(), '-sitemap.xml' ) ) {
			// User has defined good and bad content ?
			if ( ! empty(get_option('seokey-field-cct-cpt'))) {
				// SEOKEY Sitemaps index URL
				$custom_sitemaps = home_url( '/sitemap-index.xml') ;
				// Do you need another URL to redirect to ?
				$redirecturl = apply_filters( 'seokey_filter_sitemap_native_redirect', $custom_sitemaps, seokey_helper_url_get_current() );
			}
			// User has not defined good and bad content : sitemaps are not yet available
			else {
				$redirecturl = home_url();
			}
			wp_safe_redirect( esc_url( $redirecturl ), 301 );
			die;
		}
	}
}