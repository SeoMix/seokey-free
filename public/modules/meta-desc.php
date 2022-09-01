<?php
/**
 * Load Meta Description Generator
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

/**
 * Displays a meta description html tag
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @return string|void Meta description HTML or void if bad URL
 */
function seokey_head_meta_description() {
	// Initial data
	$object         = get_queried_object();
	$pagination     = seokey_helper_get_paged();
	$description    = '';
	// If it's a 404 page or a search page, abort
	if ( is_404() || is_search() ) {
		return;
	}
	// Front page
	if ( is_front_page() ) {
		// Static front page ?
		if ( is_page() ) {
			$description = seokey_meta_desc_value( 'front_page_static', $object->ID );
		}
		// Blog listing front page ?
		else {
			$description = seokey_meta_desc_value( 'front_page_blog' );
		}
	}
	// Blog page (as a static page)
	elseif ( is_home() ) {
		$description = seokey_meta_desc_value( 'home', $object->ID );
	}
	// Any post type
	elseif ( is_singular() ) {
		$description = seokey_meta_desc_value( 'singular', $object->ID );
	}
	// Post type archive => use the post type archive title
	elseif ( is_post_type_archive() ) {
		$description = seokey_meta_desc_value( 'post_type_archive', 0, $args = array (
			'name'          => $object->name,
			'label'         => $object->label,
			'description'   => $object->description,
		) );
	}
	// Taxonomy archive => use the term title
	elseif ( is_tax() || is_category() || is_tag() ) {
		$description = seokey_meta_desc_value( 'taxonomy', $object->term_id, $args = array (
			'description' => $object->description,
		) );
	}
	// Author archive, use the author's display name
	elseif ( is_author() ) {
		$description = seokey_meta_desc_value( 'user', $object->ID, $args = array(
			'name' => $object->display_name
		) );
	}
	// Pagination data
	if ( $pagination >= 2 ) {
		// Paginated $post
		if ( is_singular() ) {
			$content = ltrim( $object->post_content );
			// Ignore nextpage at the beginning of the content
			if ( 0 === strpos( $content, '<!--nextpage-->' ) ) {
				$content = substr( $content, 15 );
			}
			$pages    = explode( '<!--nextpage-->', $content );
			$maxpages = count( $pages );
		} // Default pagination
		else {
			global $wp_query;
			$maxpages = $wp_query->max_num_pages;
		}
		// Render pagination text
		$pagination_text = sprintf( __( 'Page %d of %s', 'seo-key' ), $pagination, $maxpages );
		// Truncate if necessary
		$description = seokey_helper_meta_length( $description, ( METADESC_COUNTER_MAX - strlen( $pagination_text ) ) );
		// Add pagination to (truncated) text
		$sep = apply_filters( 'document_title_separator', '-' );
		$description = $description . ' ' . $sep . ' ' . $pagination_text;
	}
	/**
	 * Filter and return final description
	 *
	 * @param (string) $description <meta desc> value
	 *
	 * @since 0.0.1
	 */
	return apply_filters( 'seokey_filter_head_meta_description', $description );
}

add_action( 'seokey_action_head', 'seokey_head_meta_description_add', 5 );
/**
 * Displays a meta description html tag
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook   seokey_action_head
 * @return void the html tag
 */
function seokey_head_meta_description_add() {
	$description = seokey_head_meta_description();
	if ( ! empty( $description ) ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
}