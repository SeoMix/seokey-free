<?php
/**
 * Third party: Genesis
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

add_action( 'seokey_loaded', 'seokey_thirdparty_genesis_disable' );
function seokey_thirdparty_genesis_disable() {
	if ( function_exists( 'genesis_disable_seo' ) ) {
		genesis_disable_seo();
	}
}