<?php
/**
 * Load Canonical Module
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
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

/**
 * Remove default WordPress canonical link
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook wp_head
 * @return  (void)
 */
remove_action( 'wp_head', 'rel_canonical' );


add_action( 'seokey_action_head', 'seokey_head_meta_canonical', 10 );
/**
 * Canonical generator
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook seokey_action_head
 * @return void (string) canonical LINK tag with URL
 */
function seokey_head_meta_canonical() {
	// Do nothing on 404 pages
	if ( is_404() ) {
		return;
	}
	$current_url = seokey_head_meta_canonical_get();
	if ( $current_url ) {
		echo '<link rel="canonical" href="' . user_trailingslashit( esc_url( $current_url ) ) . '" />' . "\n";
	}
}

// TODO Comments
function seokey_head_meta_canonical_get(){
	// Add canonical for all search pages
	if ( is_search()  ) {
		/* Define homepage URL */
		$current_url = get_home_url();
	}
	else {
		/* Globals */
		global $wp, $wp_rewrite;
		if ( empty ( $wp_rewrite->permalink_structure ) ) {
			// If user hasn't defined a permalink structure (he uses "plain" option in Settings > Permalinks)
			$current_url = home_url( '?' . add_query_arg( array(), $wp->query_string ) );
		} else {
			// we have a clean URL structure
			if ( is_singular() ) {
				$current_url = get_permalink();
				$current_url = seokey_helper_get_paginated_url( $current_url, 'post' );
			 }
			// Do not use is_archive() function, it will break with date, author and post type archives
			elseif ( is_tag() || is_category() || is_tax() ) {
				$current_url = get_term_link( get_queried_object()->term_id );
				$current_url = seokey_helper_get_paginated_url( $current_url );
			} else {
				$current_url = home_url( add_query_arg( array(), $wp->request ) );
			}
		}
	}
	/**
	 * SEOKEY Canonical URL filter
	 *
	 * @param    (string) $current_url default canonical url
	 *
	 * @return    (string) $current_url canonical url
	 * @global    (void)
	 * @since   0.0.1
	 * @author  Daniel Roch - SeoMix
	 *
	 */
	$current_url = apply_filters( 'seokey_filter_head_canonical_url', $current_url );
	/* Render Tag in <head> */
	return $current_url;
}