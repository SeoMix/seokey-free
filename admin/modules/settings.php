<?php
/**
 * Change default settings pages
 *
 * @Loaded on plugins_loaded + is_admin() + capability admin
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

/**
 * Force option of threaded comments and paginated comments
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook pre_option_page_comments
 * @hook pre_option_thread_comments
 */
add_filter( 'pre_option_page_comments',      '__return_zero', 10 );
add_filter( 'pre_option_thread_comments',    '__return_zero', 10 );


add_filter( 'pre_option_rss_use_excerpt',    '__return_true', 10 );
/**
 * Force option rss_settings_option
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook pre_option_rss_use_excerpt
 * @return int Option value
 */
add_filter( 'pre_option_rss_use_excerpt',    '__return_true', 10 );