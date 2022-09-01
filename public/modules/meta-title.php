<?php
/**
 * Load Title Generators
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

add_filter( 'pre_get_document_title', 'seokey_head_meta_title', 200 );
/**
 * Front-office Meta title rendering in <head> (based on WP core function)
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @notes Based on default WordPress Title Generation function
 * @global integer $page  Page number of a single post.
 * @global integer $paged Page number of a list of posts.
 * @hook pre_get_document_title
 * @return mixed|void (string) $title Final Title Page content (without <title> and </title>)
 */
function seokey_head_meta_title() {
	// Initial data
	$pagination = seokey_helper_get_paged();
	$object     = get_queried_object();
	$title      = array(
		'title' => '',
	);
	// If it's a 404 page, use a "Page not found" title.
	if ( is_404() ) {
		$title['title'] = esc_html_x( 'Page not found', 'Title page for 404 pages', 'seo-key' );
	// If it's a search page, use a dynamic search results title
	} elseif ( is_search() ) {
		/* translators: %s: Search query. */
		$title['title'] = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );
	// Front page (static front page OR homepage default post listing)
	} elseif ( is_front_page() ) {
		// Static front page ?
		if ( is_page() ) {
			$title['title'] = seokey_meta_title_value( 'front_page_static', $object->ID );
		// Blog listing front page ?
		} else {
			$title['title'] = seokey_meta_title_value( 'front_page_blog' );
		}
	// Blog page (as a classic page)
	} elseif ( is_home() ) {
		$title['title'] = seokey_meta_title_value( 'home', $object->ID );
	// Any post type
	} elseif ( is_singular() ) {
		$title['title'] = seokey_meta_title_value( 'singular', $object->ID );
	// Post Type Archive
	} elseif ( is_post_type_archive() ) {
		$title['title'] = seokey_meta_title_value( 'post_type_archive', 0, $args = array (
			'name'  => $object->name,
			'label' => $object->label
		) );
	// Any taxonomy
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$title['title'] = seokey_meta_title_value( 'taxonomy', $object->term_id, $args = array(
			'name' => $object->name
		) );
	// Any Author
	} elseif ( is_author() ) {
		$title['title'] = seokey_meta_title_value( 'user', $object->ID, $args = array(
			'name' => $object->display_name
		) );
	}
	// Add pagination if necessary.
	if ( $pagination >= 2 && ! is_404() ) {
		/* translators: %s: page number (ex page 3) */
		$title['page'] = sprintf( __( 'Page %s', 'seo-key' ),$pagination );
		// Check if we need to truncate our meta
		$title['title'] = seokey_helper_meta_length( $title['title'], ( METATITLE_COUNTER_MAX - strlen($title['page']) ) );
	}
	/**
	 * Filters the separator for the document title.
	 * It's the default WordPress filter
	 *
	 * @package WordPress
	 * @since   4.4.0
	 *
	 * @see     document_title_separator hook
	 *
	 * @param string $sep Document title separator. Default '-'.s
	 */
	$sep = apply_filters( 'document_title_separator', '-' );
	/**
	 * Filters the parts of the document title.
	 * It's the default WordPress filter
	 *
	 * @param array $title   {
	 *                       The document title parts.
	 *
	 * @type string $title   Title of the viewed page.
	 * @type string $page    Optional. Page number if paginated.
	 * @type string $tagline Optional. Site description when on home page.
	 * @type string $site    Optional. Site title when not on home page and option validated
	 * }
	 * @package WordPress
	 *
	 * @since   4.4.0
	 *
	 * @see     document_title_parts hook
	 *
	 */
	$title = apply_filters( 'document_title_parts', $title );
	// It's time to clean our title
	$title = implode( " $sep ", array_filter( (array) $title ) );
	$title = esc_html( $title );
	$title = capital_P_dangit( $title );

	/**
	 * Filter and return final <title> tag
	 *
	 * @param (string) $title <title> meta
	 *
	 * @since 0.0.1
	 *
	 */
	return apply_filters( 'seokey_filter_head_meta_title', $title );
}



/**
 * Back-office Meta title generator
 * Used to create an optimized title outside the loop (for back office request where seokey_meta_title_value can not be directly used)
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @param integer $ID
 * @param string $type content type (term, singular, author, etc.)
 * @param string $data taxonomy name for terms, or CPT name for Post type archive
 * @notes Used to create an optimized title outside the loop
 * @return mixed|void (string) $title Final Title Page content (without <title> and </title>)
 */
function seokey_head_get_meta_title( $ID, $type, $data = '' ) {
    // Get data
    $ID = (int) $ID;
    switch ( $type ) {
        case 'singular':
            $frontpage_id   = get_option( 'page_on_front' );
            $blog_id        = get_option( 'page_for_posts' );
            if ( !empty ( $frontpage_id ) ) {
                if ( $ID === $frontpage_id ) {
                    $type = 'front_page';
                } elseif ( $ID === $blog_id ) {
                    $type = 'blog_page';
                }
            } elseif ( empty ( $ID ) ) {
                $type = 'front_blog';
            } else {
                $type = 'singular';
            }
            break;
        case 'taxonomy':
            $term = get_term( $ID, $data );
            $name = $term->name;
            break;
        case 'post_type_archive':
            $type   = 'post_type_archive';
            $cpt    = get_post_type_object( $data );
            $name   = $cpt->name;
            $label  = $cpt->label;
            break;
        case 'author':
            $display_name = get_the_author_meta( 'display_name', $ID );
            break;
    }
    // let's create an optimized title
    $title      = array(
        'title' => '',
    );
    if ( 'front_page' === $type ) {
        // Static front page ?
        $title['title'] = seokey_meta_title_value('front_page_static', $ID );
    } elseif ( 'front_blog' === $type ) {
        // Blog listing front page ?
        $title['title'] = seokey_meta_title_value( 'front_page_blog' );
    } elseif ( 'blog_page' === $type ) {
        // Blog page (as a classic page)
        $title['title'] = seokey_meta_title_value('home', $ID );
    // Any post type
    } elseif ( 'singular' === $type ) {
        $title['title'] = seokey_meta_title_value( 'singular', $ID );
    // Post Type Archive
    } elseif ( 'post_type_archive' === $type ) {
        $title['title'] = seokey_meta_title_value( 'post_type_archive', 0, $args = array (
            $name,
            $label
        ) );
    // Any taxonomy
    } elseif ( 'taxonomy' === $type ) {
        $title['title'] = seokey_meta_title_value( 'taxonomy', $ID, $args = array(
            $name
        ) );
    // Any Author
    } elseif ( 'author' === $type ) {
        $title['title'] = seokey_meta_title_value( 'user', $ID, $args = array(
            $display_name
        ) );
    }
    /**
     * Filters the separator for the document title.
     * It's the default WordPress filter
     *
     * @package WordPress
     * @since   4.4.0
     *
     * @see     document_title_separator hook
     *
     * @param string $sep Document title separator. Default '-'.s
     */
    $sep = apply_filters( 'document_title_separator', '-' );
    /**
     * Filters the parts of the document title.
     * It's the default WordPress filter
     *
     * @param array $title   {
     *                       The document title parts.
     *
     * @type string $title   Title of the viewed page.
     * @type string $page    Optional. Page number if paginated.
     * @type string $tagline Optional. Site description when on home page.
     * @type string $site    Optional. Site title when not on home page and option validated
     * }
     * @package WordPress
     *
     * @since   4.4.0
     *
     * @see     document_title_parts hook
     *
     */
    $title = apply_filters( 'document_title_parts', $title );
    // It's time to clean our title
    $title = implode( " $sep ", array_filter( (array) $title ) );
    $title = wptexturize( $title );
    $title = convert_chars( $title );
    $title = esc_html( $title );
    $title = capital_P_dangit( $title );
    /**
     * Filter and return final <title> tag
     *
     * @param (string) $title <title> meta
     *
     * @since 0.0.1
     *
     */
    return apply_filters( 'seokey_filter_head_meta_title', $title );
}

/**
 * Meta title generator when add_them_support function is not activated
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @param string $title content title
 * @return mixed|void (string) $title Final Title Page content (without <title> and </title>)
 */
add_filter( 'wp_title', 'seokey_head_meta_title', SEOKEY_PHP_INT_MAX  );