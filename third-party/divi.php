<?php
/**
 * Third party: Divi
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

add_filter( 'et_builder_load_requests', 'seokey_thirdparty_divi_audit_builder', 100 );
/**
 * When audit is running in background, tell DIVI to enable it's builder and all associated shortcodes
 *
 * @since   1.6.0
 * @author  Daniel Roch
 *
 * @return  array $data all actions that must trigger DIVI shortocdes
 */
function seokey_thirdparty_divi_audit_builder( $data ){
	$data['action'][] = 'seokey_audit';
	return $data;
}

add_filter( 'seokey_filter_meta_desc_value_singular_postcontent', 'seokey_thirdparty_divi_filter_metadesc_content' );
/**
 * When user is using admin, DIVI does not load it's shortcode. Here is a workaround to generate a default meta description.
 *
 * @since   1.6.0
 * @author  Daniel Roch
 *
 * @return  string $content Post content
 */
function seokey_thirdparty_divi_filter_metadesc_content( $content ){
    $theme = wp_get_theme();
    if( function_exists('et_is_builder_plugin_active') || $theme->template == "Divi" ) {
        // Divi is here
        $content = preg_replace('/\[et_pb_.+?\]/', '', $content );
        $content = preg_replace('/\[\/et_pb_.+?\]/', '', $content );
        $content = strip_tags($content);
        $content = trim($content);
    }
    return $content;
}