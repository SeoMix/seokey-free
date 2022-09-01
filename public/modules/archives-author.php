<?php
/**
 * Improve or remove author archive page
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

add_action( 'pre_get_posts', 'seokey_archive_author_remove_pagination' );
/**
 * Remove Author Pagination
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook pre_get_posts
 * @param $query WP_query object
 * @return void
 */
function seokey_archive_author_remove_pagination( $query ) {
	// Only on author pages
	if ( is_author() ) {
		// Only main queries
		if ( $query->is_main_query() ) {
			$disabled = seokey_helper_get_option('seooptimizations-pagination-authors', 'yes' );
			// Remove secondary feeds (manual option to disable all or automatic without user choice)
			if ( 'yes' === $disabled || (string) 1 === $disabled ) {
				// Don't paginate author pages
				$query->set( 'no_found_rows', true );
			}
		}
	}
}

add_filter( 'author_link', 'seokey_archive_author_remove_link', 20, 2 );
/**
 * Replace authors links if they are private (noindex)
 *
 * @since   0.0.1
 * @author  Leo Fontin, Daniel Roch
 *
 * @hook author_link
 * @param $link
 * @param $author_id
 * @return String Link or Anchor #
 */
function seokey_archive_author_remove_link( $link, $author_id ) {
	// No links for authors if they are all private
	$page = seokey_helper_get_option( 'cct-pages', [] );
	if ( ! empty( $page ) && ! in_array( 'author', $page, true ) ) {
		return '#';
	}
	// No links for each private author
	$authorprivacy = (int) get_the_author_meta( 'seokey-content_visibility', $author_id );
	if ( 1 === $authorprivacy ) {
		return '#';
	}
	return $link;
}
