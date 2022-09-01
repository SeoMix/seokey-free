<?php
/**
 * WP-Background Processing classes
 *
 * @Loaded  on 'init'
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 * @see     seo-key-helpers.php
 *
 * @see https://github.com/deliciousbrains/wp-background-processing
 * @author Delicious Brains Inc.
 * @package SEOKEY
*/

/**
 * Security
 *
 * Prevent direct access to this file
 */
if (!defined('ABSPATH')) {
    die('You lost the key...');
}
// Load classes
if ( ! class_exists( 'SeoKey_WP_Async_Request' ) ) {
	require_once SEOKEY_PATH_COMMON . 'classes-background_processing/wp-async-request.php';
}
if ( ! class_exists( 'SeoKey_WP_Background_Process' ) ) {
    require_once SEOKEY_PATH_COMMON . 'classes-background_processing/wp-background-process.php';
}
