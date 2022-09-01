<?php
/**
 * Public Robots.txt function
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

// Change default robots.txt content in case our file creation have failed (keep in mind that a physical file is way faster to crawl for Google)
add_filter( 'robots_txt', 'seokey_robots_txt_content', SEOKEY_PHP_INT_MAX );
