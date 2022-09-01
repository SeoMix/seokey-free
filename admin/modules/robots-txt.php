<?php
/**
 * Admin Robots.txt function
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

add_action( 'init', 'seokey_robots_txt_check_file' );
/**
 * Warning if robots.txt file is already created by user (or another plugin)
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_robots_txt_check_file() {
    // TODO perf : faire un transient ou CRON pour ne pas checker en continu ou alors tester dans certains cas uniquement
	$robots = ABSPATH . 'robots.txt';
	if( ! file_exists( $robots ) ) {
        seokey_helper_files( 'create', 'robots' );
    }
}