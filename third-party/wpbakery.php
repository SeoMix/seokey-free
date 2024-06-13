<?php
/**
 * Third party: WP Bakery Page Builder
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

if ( class_exists( 'WPBMap' ) ) {
	add_filter ( 'seokey_filter_audit_single_data_content', 'seokey_thirdparty_wpbakery_audit_single_data_content' );
	function seokey_thirdparty_wpbakery_audit_single_data_content( $content ) {
		// First, replace images
		preg_match( '/\[vc_single_image image="(\d+)" img_size="(\w+)"[^\]]*\]/', $content, $matches );
		if( isset( $matches[1] ) ) {
			$url        = wp_get_attachment_image_url( (int) $matches[1], $matches[2] );
			$img        = sprintf( '<img src="%s" />', $url );
			$content    = str_replace( $matches[0], $img, $content );
		}
		// Now activate all WP Bakery shortcodes
		WPBMap::addAllMappedShortcodes();
		$content = do_shortcode( stripslashes( $content ) );
		// Return correct content
		return $content;
	}
}