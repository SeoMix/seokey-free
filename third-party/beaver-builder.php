<?php
/**
 * Third party: Beaver Builder
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

if ( is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) || is_plugin_active( 'bb-plugin/fl-builder.php' ) ) {
	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_bb_sitemaps' );
	// Exclude fake post types from sitemaps
	function seokey_thirdparty_bb_sitemaps( $excluded ) {
		$excluded['cpt'][]  = 'fl-builder-template';
		$excluded['taxo'][] = 'fl-builder-template-category';
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_bb_settings', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_bb_settings( $default ) {
		unset( $default['fl-builder-template'] );
		return $default;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_taxonomies', 'seokey_thirdparty_bb_settings_taxo', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_bb_settings_taxo( $default ) {
		unset( $default['fl-builder-template-category'] );
		return $default;
	}
	
	add_filter( 'seokey_settings_filter_taxonomy_choice', 'seokey_thirdparty_bb_exclude_taxo', 500 );
	// Remove from taxonomy choices for each post type
	function seokey_thirdparty_bb_exclude_taxo( $default ) {
		$default[] = 'fl-builder-template';
		return $default;
	}
}