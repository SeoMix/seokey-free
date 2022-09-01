<?php
/**
 * Footer actions
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


add_action( 'wp_footer', 'seokey_footer' );
/**
 * Action seokey dans le footer
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_footer() {
	/* translators: 1: Plugin Name, 2: Plugin Website URL */
	$translation = sprintf( esc_html__( 'BEGIN SEOKEY footer', 'textdomain' ), SEOKEY_NAME, SEOKEY_HOME );
	echo "\n" . '<!-- ' . $translation . ' -->' . "\n";
	do_action( 'seokey_action_footer' );
	echo "\n" . '<!-- END SEOKEY footer -->' . "\n";
}
