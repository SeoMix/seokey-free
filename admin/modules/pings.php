<?php
/**
 * Prevent harmfuls pings
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

add_action( 'transition_post_status', 'seokey_ping_publish_post_transition', 10 );
/**
 * Temporary storage of the content visibility checkbox
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook transition_post_status
 * @note Could have use these parameters : $new_status, $old_status, $post
 */
function seokey_ping_publish_post_transition() {
	// Our mu-plugin is not active, stop what we are doing and let WordPress do his job
	if ( !defined( 'SEOKEY_MUPLUGIN_ACTIVE' ) ) {
		return;
	}
	// Post status is changing, check if user has ticked the "noindex" checkbox
    if ( isset( $_POST['content_visibility'] ) ) {
    	$value = (int) $_POST['content_visibility'];
    	if ( 1 === $value ) {
    		$value = true;
    	}
    	// He has ticked the "noindex" checkbox, we store the value for later use
		wp_cache_add( '_seokey_ping_temporary_data', $value, '', 60 );
    }
}

add_action( 'publish_post', 'seokey_ping_publish_post_hook', 6, 1 );
/**
 * Replace default _publish_post_hook action
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook publish_post
 * @note function based on core function _publish_post_hook
 * @param $post_id
 */
function seokey_ping_publish_post_hook( $post_id )  {
	// Our mu-plugin is not active, stop what we are doing
	if ( !defined( 'SEOKEY_MUPLUGIN_ACTIVE' ) ) {
		return;
	}
	// Specific action for XML RPC publication
	if ( defined( 'XMLRPC_REQUEST' ) ) {
		/**
		 * Fires when _publish_post_hook() is called during an XML-RPC request.
		 *
		 * @since 2.1.0
		 *
		 * @param int $post_id Post ID.
		 */
		do_action( 'xmlrpc_publish_post', $post_id );
	}
	// We are importing data, do nothing
	if ( defined( 'WP_IMPORTING' ) ) {
		return;
	}
	// Is this a private content ?
	$privatecontent = seokey_helper_post_is_private();
	// Ping this post if it's allowed by WordPress and if this is not a private content
	if ( get_option( 'default_pingback_flag' ) && false === $privatecontent ) {
		add_post_meta( $post_id, '_pingme', '1', true );
	}
	// Do core enclosures
	add_post_meta( $post_id, '_encloseme', '1', true );
	// Create trackbacks for this post if this is not a private content
	$to_ping = get_to_ping( $post_id );
	if ( ! empty( $to_ping ) && false === $privatecontent ) {
		add_post_meta( $post_id, '_trackbackme', '1' );
	}
	// Schedule all pings if this is not a private content
	if ( ! wp_next_scheduled( 'do_pings' ) && false === $privatecontent ) {
		wp_schedule_single_event( time(), 'do_pings' );
	}
}

add_action( 'pre_ping', 'seokey_comments_disable_self_pings' );
/**
 * Disable self pings
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook pre_ping
 * @param array $links
 * @return array $links
 */
function seokey_comments_disable_self_pings( &$links ) {
	$homeurl = esc_url( home_url() );
	foreach ( $links as $l => $link ) {
		if ( 0 === strpos( $link, $homeurl ) ) {
			unset( $links[ $l ] );
		}
	}
}