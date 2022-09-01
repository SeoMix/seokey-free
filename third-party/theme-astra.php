<?php
/**
 * Third party: Theme ASTRA
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

$theme = wp_get_theme(); // Get  current theme
if ( 'Astra' == $theme->name || 'Astra' == $theme->parent_theme ) {
	
	// TODO noindex on astra-advanced-hook
	
	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_astra_sitemaps' );
	// Exclude fake post types from sitemaps
	function seokey_thirdparty_astra_sitemaps( $excluded ) {
		$excluded['cpt'][] = 'astra-advanced-hook';
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_astra_settings', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_astra_settings( $default ) {
		unset( $default['astra-advanced-hook'] );
		return $default;
	}
}