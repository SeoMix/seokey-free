<?php
/**
 * Third party: Polylang
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

if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' )) {

	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_polylang_sitemaps' );
	// Exclude fake post types from sitemaps
	function seokey_thirdparty_polylang_sitemaps($excluded) {
		$excluded['taxonomy'][] = 'language';
		$excluded['taxonomy'][] = 'term-translations';
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_polylang_settings', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_polylang_settings($default){
		unset($default['language']);
		unset($default['term-translations']);
		return $default;
	}
	
	add_filter( 'seokey_settings_filter_taxonomy_choice', 'seokey_thirdparty_polylang_exclude_taxo', 500 );
	// Remove from taxonomy choices for each post type
	function seokey_thirdparty_polylang_exclude_taxo( $default ){
		$default[] = 'post_translations';
		$default[] = 'language';
		$default[] = 'term-translations';
		return $default;
	}
}

add_filter('seokey_filter_head_canonical_url', 'seokey_thirdparty_polylang_canonical');
// TODO Comment
function seokey_thirdparty_polylang_canonical($current_url){
	if ( function_exists('pll_home_url')) {
		$home_url = untrailingslashit( pll_home_url() );
		$bad_home_url = untrailingslashit( home_url() );
		$current_url = str_replace( $bad_home_url, $home_url, $current_url);
	}
	return $current_url;
}