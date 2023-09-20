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

/****************** Frameworks ****************/
/* Genesis */
seokey_helper_require_file( 'genesis',  SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/****************** Builders ****************/
/* Divi */
seokey_helper_require_file( 'divi',  SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );

/****************** Themes ****************/
/* Astra */
seokey_helper_require_file( 'theme-astra',  SEOKEY_PATH_ROOT . 'third-party/', 'everyone' );


/****************** I18n ****************/
/* First : Check multilingual plugin installed */
$multilingual = seokey_helper_cache_data('languages');
if ( $multilingual !== "single" ) {
	switch ($multilingual){
		case "polylang":
		case "polylangpro":
			seokey_helper_require_file('polylang', SEOKEY_PATH_ROOT . 'third-party/i18n/', 'everyone');
			break;
		/*
	case "weglot":
	case "weglotpro":
	//seokey_helper_require_file('weglot', SEOKEY_PATH_ROOT . 'third-party/i18n/', 'everyone');
		break;
	*/
		case "wpmlpro": // wpml free does not exists anymore
			seokey_helper_require_file('wpml', SEOKEY_PATH_ROOT . 'third-party/i18n/', 'everyone');
			break;
		default:
			seokey_helper_require_file('single', SEOKEY_PATH_ROOT . 'third-party/i18n/', 'everyone');
			// Only show if multilingual plugin active & not WPML / Polylang
			add_filter('seokey_filter_admin_notices_launch', 'seokey_admin_compatibility_i18n', 10);
			break;
	}
}else{
	seokey_helper_require_file('single', SEOKEY_PATH_ROOT . 'third-party/i18n/', 'everyone');
}

/*
 * Warning for I18n compatibility
 */
function seokey_admin_compatibility_i18n( $args ) {
	$title = '<p>' . esc_html__( 'Warning: Internationalization compatibility', 'seo-key' ) . '</p>';
	$text     = '<p>' . esc_html_x( 'SEOKEY is not yet compatible with all translation plugins (only Polylang & WPML for now).', 'notification text for a new content discovered', 'seo-key' ) . '</p>';
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
	return $args;
}

// TODO move into specific file
add_action( 'after_setup_theme', 'seokey_thirdparty_hello_elementor_metadesc_disable' );
/**
 * Remove Hello Elementor meta description tag
 *
 * @since 1.6.5
 */
function seokey_thirdparty_hello_elementor_metadesc_disable() {
    remove_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );
}