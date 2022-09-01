<?php
/**
 * Improve media upload (may prevent some 404 error and crawl errors)
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/**
 * Remove accent from file name on upload (prevent some crawl error)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook    sanitize_file_name
 */
add_filter('sanitize_file_name', 'remove_accents' );


add_filter( 'sanitize_file_name_chars', 'seokey_medias_file_name_remove_special_char', 10, 1 );
/**
 * Remove special characters from file name on upload (prevent some crawl error)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook    sanitize_file_name_chars
 * @param array $special_chars Default disallowed secial characters
 * @return array New disallowed secial characters
 */
function seokey_medias_file_name_remove_special_char( $special_chars = array() ) {
	// Special characters
	$special_chars = array_merge( array( '’', '‘', '“', '”', '«', '»', '‹', '›', '—', '€', '©', '^' ), $special_chars );
	// Return filtered data
	return $special_chars;
}