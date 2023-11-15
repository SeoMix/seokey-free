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

	add_filter( 'seokey_filter_image_html_tag', 'seokey_thirdparty_astra_img_alts', 10, 2 );
	// Fix when sometimes the alt does not apply on front and audit because of $attachment_id not passing
	function seokey_thirdparty_astra_img_alts( $filtered_image, $attachment_id ) {
		// If $attachment_id did not pass
		if ( $attachment_id === 0 ) {
			// Regex
			$regex = '/uag-image-(\d+)/';
			// Check if we have the "uag-image-ID" in the classes to get the image ID
			if ( preg_match( $regex, $filtered_image, $matches ) ) {
				if ( $matches[1] ) {
					$image_id = $matches[1];
					$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					// If we aimed correctly, add the alt
					if ( !empty( $alt ) ) {
						// Add ALT value to HTML
						$filtered_image = str_replace( 'alt=""', 'alt="' . htmlspecialchars( esc_attr( $alt ) ) . '"', $filtered_image, $test );
					} 
				}
			}
		}
		return $filtered_image;
	}
}