<?php
/**
 * Sitemap public functions
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
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

/**
 * Disable Core sitemap functionality
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @return  void
 */
add_filter( 'wp_sitemaps_enabled', '__return_false' );

add_action( 'send_headers', 'seokey_sitemap_header_x_robots' );
/**
 * Add x-robot tag to sitemap files (only work on rewrited sitemaps, not on files)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @return  void
 */
function seokey_sitemap_header_x_robots() {
	if ( seokey_helper_is_sitemap() ) {
		header( "X-Robots-Tag: noindex", true );
	}
}