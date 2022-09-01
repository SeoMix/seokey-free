<?php
/**
 * Third party: Woocommerce
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

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	add_action ( 'template_redirect', 'seokey_thirdparty_woocommerce_check' );
	// Check if bad taxonomies is displayed + remove default meta robots
	function seokey_thirdparty_woocommerce_check(){
		if ( is_front_page() ) {
			if ( !empty( $_GET ) ) {
				if ( !empty( $_GET['product_type'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['product_visibility'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['product_shipping_class'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['taxonomy'] ) ) {
					if ( str_starts_with( $_GET['taxonomy'], 'pa_' ) ) {
						seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
					}
				}
				
			}
		}
		if ( 1 === seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check' ) ) {
			add_filter( 'wp_robots', 'seokey_meta_robot_noindex_checker_force', ( SEOKEY_PHP_INT_MAX + 10 ) );
			remove_action( 'wp_head', 'wp_robots' );
		}
	}
	
	
	add_action( 'seokey_action_head', 'seokey_thirdparty_woocommerce_head', 5 );
	// If bad taxo => no canonical tag
	function seokey_thirdparty_woocommerce_head(){
		if ( 1 === seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check' ) ) {
			remove_action( 'seokey_action_head', 'seokey_head_meta_canonical' );
		}
	}

	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_woocommerce_sitemaps' );
	// Exclude fake taxonomies from sitemaps
	function seokey_thirdparty_woocommerce_sitemaps($excluded) {
		$excluded['taxo'][] = 'product_type';
		$excluded['taxo'][] = 'product_visibility';
		$excluded['taxo'][] = 'product_shipping_class';
		$weirdtaxos      = wc_get_attribute_taxonomies();
		foreach ( $weirdtaxos as $taxo ) {
			$name = wc_attribute_taxonomy_name( $taxo->attribute_name );
			$excluded['taxo'][] = $name;
		}
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_taxonomies', 'seokey_thirdparty_woocommerce_settings', 500 );
	// Exclude fake taxonomies from settings
	function seokey_thirdparty_woocommerce_settings($default){
		unset($default['product_type']);
		unset($default['product_visibility']);
		unset($default['product_shipping_class']);
		$weirdtaxos = wc_get_attribute_taxonomies();
		foreach ( $weirdtaxos as $taxo ) {
			$name = wc_attribute_taxonomy_name( $taxo->attribute_name );
			unset($default[$name]);
		}
		return $default;
	}
}