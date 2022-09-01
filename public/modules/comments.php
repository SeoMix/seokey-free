<?php
/**
 * Comments configurations
 *
 * @loaded on plugin_loaded
 * @loaded for everyone
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

add_filter( 'comment_reply_link', 'seokey_comments_remove_replytocom', 10, 1 );
/**
 * Remove comment reply link parameter (ALWAYS)
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook comment_reply_link
 * @return  (string) cleaned URL
 */
function seokey_comments_remove_replytocom( $link ){
	return preg_replace( '`href=(["\'])(?:.*(?:\?|&|&#038;)replytocom=(\d+)#respond)`', 'href=$1#comment-$2', $link );
}

add_filter ('comment_reply_link', 'seokey_comments_clean_reply_link', 10, 1 );
/**
 * Remove nofollow from reply links (avoid pagerank dilution)
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook comment_reply_link
 * @return  (string) cleaned URL
 */
function seokey_comments_clean_reply_link( $formatted_link ){
	return str_replace( " rel='nofollow'", '', $formatted_link );
}

/**
 * Force no replytocom parameter + nofollow
 *
 * @return integer 0
 * @author  Léo Fontin
 *
 * @hook option_thread_comments
 * @since  0.0.1
 */
// If user has activated the option, or when user has not made any choice yet
$disabled = seokey_helper_get_option( 'seooptimizations-replylinks', 'yes' );
if ( 'yes' === $disabled || (string) 1 === $disabled ) {
	add_filter( 'comment_reply_link', '__return_empty_string', 20 );
}

/**
 * Force no paginated comments
 *
 * @since  0.0.1
 * @author  Léo Fontin
 *
 * @hook option_page_comments
 * @return  integer 0
 */
$disabled = seokey_helper_get_option( 'seooptimizations-pagination-comments', 'yes' );
if ( 'yes' === $disabled || (string) 1 === $disabled ) {
	add_filter( 'option_page_comments', '__return_zero', 10 );
}