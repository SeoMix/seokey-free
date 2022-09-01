<?php
/**
 * Third party Loader
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

/* Load function is_plugin_active if needed  */
if ( ! function_exists('is_plugin_active')) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/****************** Plugins ****************/
/* Yoast SEO */
seokey_helper_require_file( 'yoast-seo',        SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/* Woocommerce */
seokey_helper_require_file( 'woocommerce',      SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/* Elementor */
seokey_helper_require_file( 'elementor',        SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/* Beaver Builder */
seokey_helper_require_file( 'beaver-builder',   SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/****************** Themes ****************/
/* Astra */
seokey_helper_require_file( 'theme-astra',  SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );


/****************** I18n ****************/
/* Polylang */
seokey_helper_require_file( 'polylang',     SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

add_filter( 'seokey_filter_admin_notices_launch', 'seokey_admin_compatibility_i18n', 10 );
/*
 * Warning for I18n compatibility
 */
function seokey_admin_compatibility_i18n( $args ) {
	$plugins        = get_option( 'active_plugins' );
	$pluginscheck   = [
		'weglot'        => 'weglot/weglot.php',
		'polylang'      => 'polylang/polylang.php',
		'polylangpro'   => 'polylang-pro/polylang.php',
		'wpml'          => 'sitepress-multilingual-cms/sitepress.php',
	];
	$continue = false;
	foreach ( $plugins as $plugin ) {
		if ( in_array( $plugin, $pluginscheck ) ) {
			$continue = true;
		}
	}
	if ( true === $continue ) {
		$title = '<p>' . esc_html__( 'Warning: Internationalization compatibility', 'seo-key' ) . '</p>';
		$text     = '<p>' . esc_html_x( 'SEOKEY is not yet compatible with translation plugins such as Polylang or WPML.', 'notification text for a new content discovered', 'seo-key' ) . '</p>';
		$text     .= '<p>' . esc_html_x( 'Please wait our next update before using SEOKEY.', 'notification text for a new content discovered', 'seo-key' ) . '</p>';
		$new_args = array(
			sanitize_title( 'seokey_notice_content_watcher' ), // Unique ID.
			$title, // The title for this notice.
			$text, // The content for this notice.
			[
				'scope'           => 'global', // Dismiss is per-user instead of global.
				'type'            => 'error', // Can be one of info, success, warning, error.
				'screens_exclude' => [ 'seokey_page_seo-key-wizard' ],
				'capability'      => seokey_helper_user_get_capability( 'editor' ), // only for these users and above
				'alt_style'       => false, // alternative style for notice
				'state'           => 'permanent',
			]
		);
		array_push( $args, $new_args );
	}
	return $args;
}