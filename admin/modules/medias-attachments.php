<?php
/**
 * Load admin menus and links
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
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

add_filter( 'media_view_settings', 'seokey_editor_attachement_default_link');
/**
 * Change default link type for TINYMCE Galleries
 *
 * @since   0.0.1
 * @author  Leo Fontin
 *
 * @hook media_view_settings
 * @param $settings
 * @return mixed
 */
function seokey_editor_attachement_default_link( $settings ) {
	$settings['galleryDefaults']['link'] = 'file';
	return $settings;
}

add_filter( 'attachment_link', 'seokey_admin_attachement_links', 10, 2 );
/**
 * Try to avoid attachment page link inclusion
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook attachment_link
 * @param string $link Attachment Link
 * @param int $post_id Post ID
 * @return string $link Attachment Link
 */
function seokey_admin_attachement_links( $link, $post_id ) {
	// Replace attachment page with a  direct link to media
	$link = wp_get_attachment_url( (int) $post_id );
	return esc_url( $link );
}
