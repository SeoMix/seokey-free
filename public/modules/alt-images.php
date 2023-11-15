<?php
/**
 * Alternative text for images
 *
 * @Loaded on plugins_loaded
 * @see seokey_plugin_init()
 * @see public-modules.php
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

add_filter( 'wp_content_img_tag', 'seokey_alt_images_front', 10, 3 );
/**
 * Automatically add ALT texts to images when ALT has been added within the Media Library
 *
 * @note Only for WordPress 6.0+
 *
 * @since   1.5.0
 * @author  Daniel Roch
 *
 * @hook wp_content_img_tag
 * @param string $filtered_image HTML content for this image
 * @param string $context Additional context, like the current filter name or the function name from where this was called.
 * @param int $attachment_id Attachment ID
 * @return string $filtered_image HTML content for this image
 */
function seokey_alt_images_front( $filtered_image, $context, $attachment_id ) {
	// Regex
	$regex = '#<img[^>]* alt=(?:\"|\')(?<alt>([^"]*))(?:\"|\')[^>]*>#mU';
	// Extract data from this regex
	preg_match_all( $regex, $filtered_image, $matches );
	// Alt is empty ?
	$matchesAlt = $matches['alt'];
	if ( empty( $matchesAlt[0] ) ) {
		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		// Alt is available on media library
		if ( !empty( $alt ) ) {
			// Add ALT value to HTML
			$filtered_image = str_replace( 'alt=""', 'alt="' . htmlspecialchars( esc_attr( $alt ) ) . '"', $filtered_image );
		}
		// Filter for fix if there is no $attachment_id with certain themes or if changing the HTML is needed for a specific image
		$filtered_image = apply_filters( 'seokey_filter_image_html_tag', $filtered_image, $attachment_id );
	}
	// Return image
	return $filtered_image;
}