<?php
/**
 * Third party: Elementor
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

if ( is_plugin_active( 'elementor/elementor.php' ) ) {
	
	// TODO noindex on elementor_library
	
	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_elementor_sitemaps' );
	// Exclude fake post types from sitemaps
	function seokey_thirdparty_elementor_sitemaps($excluded) {
		$excluded['cpt'][] = 'elementor_library';
		$excluded['taxo'][] = 'elementor_library_type';
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_elementor_settings', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_elementor_settings($default){
		unset($default['elementor_library']);
		return $default;
	}
	
	add_filter( 'seokey_settings_filter_taxonomy_choice', 'seokey_thirdparty_elementor_exclude_taxo', 500 );
	// Remove from taxonomy choices for each post type
	function seokey_thirdparty_elementor_exclude_taxo( $default ){
		$default[] = 'elementor_library_type';
		return $default;
	}
}