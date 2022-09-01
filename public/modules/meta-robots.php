<?php
/**
 * Load Content Visibility Manager
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

if ( !is_admin() ) {
    add_action('pre_get_posts', 'seokey_meta_robot_noindex_remove_from_loop');
    /**
     * Exclude private contents from main website listings
     *
     * @param $query OBJECT Current $Query object (loop)
     * @return void
     * @author Daniel Roch
     * @since  0.0.1
     *
     * @hook   pre_get_posts
     */
    function seokey_meta_robot_noindex_remove_from_loop( $query ) {
		// If user has activated the option, or when user has not made any choice yet
	    $disabled = seokey_helper_get_option( 'seooptimizations-hide-noindexed', 'yes' );
	    if ( 'yes' === $disabled || (string) 1 === $disabled ) {
		    // Do not alter menus
		    if ( $query->get( 'post_type' ) == 'nav_menu_item' ) {
			    return;
		    }
		    // If content is private, remove it from main loop
		    if ( ! $query->is_singular() && ! $query->is_admin() && $query->is_main_query() ) {
			    $key = 'seokey-content_visibility';
			    $query->set( 'meta_query',
				    array(
					    'relation' => 'OR',
					    // Keep posts where user has not yet defined the private/public value
					    array(
						    'key'     => $key,
						    'value'   => '0',
						    'compare' => 'NOT EXISTS',
					    ),
					    // And exclude private post
					    array(
						    'key'     => $key,
						    'value'   => 1,
						    'compare' => '!=',
					    ),
				    )
			    );
		    }
	    }
    }
}

add_filter( 'xmlrpc_methods', 'seokey_meta_robot_xmlrpc' );
/**
 * Add X-robot tag to XMLRPC headers
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @param array XMLRPC headers
 * @return array XMLRPC headers
 */
function seokey_meta_robot_xmlrpc( $methods ) {
	if ( ! headers_sent() ) {
        header( 'X-Robots-Tag: noindex, follow', TRUE );
    }
    return $methods;
}

add_action( 'template_redirect', 'seokey_meta_robot_noindex_trigger', 20 );
function seokey_meta_robot_noindex_trigger() {
    global $wp_version;
    // Do we have a superior WordPress version (5.7)
    if ( version_compare( $wp_version, '5.7.0' ) > 0 ) {
        add_filter( 'wp_robots', 'seokey_meta_robot_noindex_checker', SEOKEY_PHP_INT_MAX );
    } else {
        seokey_meta_robot_noindex_checker_before_5_7();
    }
}

function seokey_meta_robot_noindex_checker_force( array $robots ){
	unset($robots['follow']);
	unset($robots['max-image-preview']);
	unset($robots['index']);
	unset($robots['max-video-preview']);
	unset($robots['max-snippet']);
	$robots['noarchive'] = true;
	$robots['noindex'] = true;
	$robots['nofollow'] = true;
	return $robots;
}

/**
 * Handle noindex and canonical conditions
 *
 * @author Julio Potier, Daniel Roch
 * @since  0.0.1
 *
 * @hook wp_head
 * @note Author pages are not here because it would be redirected
 */
function seokey_meta_robot_noindex_checker( array $robots ){
    // TODO refactor this code with seokey_meta_robot_noindex_checker_before_5_7 function
    // Entire website is no private
	if ( true === (bool) get_option( 'blog_public' ) ) {
        if ( is_search() ) {
            unset($robots['follow']);
            unset($robots['max-image-preview']);
            $robots['noarchive'] = true;
            $robots['noindex'] = true;
            $robots['nofollow'] = true;
        }
        // We assume every content should be indexed
        $local_checked  = $global_checked = false;
        // Post types
        if ( is_singular() ) {
            // Post type globally private ?
            if ( ! empty( seokey_helper_get_option( 'cct-cpt', [] ) ) ) {
				// Post type is set to noindex ?
	            $global_checked = seokey_helper_is_global_checked( 'posts', get_post_type() );
            }
            // This specific $post is private ?
            $local_checked = (bool) get_post_meta( get_the_ID(), 'seokey-content_visibility', true );
        }
        // blog page
        elseif ( is_home() || is_front_page() ) {
            // This $post is private ?
            $local_checked = (bool) get_post_meta( get_option( 'page_for_posts' ), 'seokey-content_visibility', true );
        }
        // Taxonomies
        elseif ( is_tax() || is_category() || is_tag() ) {
            // Get data about our term
            $queriedobject = get_queried_object();
            // Taxonomy globally private ?
            if ( ! empty( seokey_helper_get_option( 'cct-taxo', [] ) ) ) {
	            // Taxonomy is set to noindex
	            $global_checked = seokey_helper_is_global_checked( 'taxonomies', $queriedobject->taxonomy );
            }
            // This term is private ?
            $local_checked = (bool) get_term_meta( $queriedobject->term_id, 'seokey-content_visibility', true );
        }
        // Archives
        elseif ( is_post_type_archive() ) {
            // Get data about our post type
            $queriedobject = get_queried_object();
            if ( ! empty( $queriedobject->name ) && post_type_exists( $queriedobject->name ) ) {
                // Post type archive globally private ?
	            $global_checked = seokey_helper_is_global_checked( 'posts', $queriedobject->name );
                // This archive is private ?
                $local_checked = (bool) get_option( 'seokey-content_visibility-' . $queriedobject->name );
            }
        } elseif ( is_author() ) {
            $local_checked = (bool) get_user_meta( get_the_author_meta('ID' ), 'seokey-content_visibility', true );
        } elseif ( is_search() ) {
            $global_checked = true;
        }
        // Do we need a noindex tag ?
        if ( $global_checked || $local_checked ) {
            unset($robots['follow']);
            unset($robots['max-image-preview']);
            $robots['noarchive'] = true;
            $robots['noindex'] = true;
            $robots['nofollow'] = true;
        } else {
            unset($robots['max-image-preview']);
            $robots['index'] = true;
            $robots['follow'] = true;
            $robots['max-image-preview'] = 'large';
            $robots['max-snippet'] = '-1';
            $robots['max-video-preview'] = '-1';
        }
    }
    return $robots;
}

/****************************** Before 5.7 ****************************/

/**
 * Handle noindex and canonical conditions (before WP 5.7)
 *
 * @author Julio Potier, Daniel Roch
 * @since  0.0.1
 *
 * @hook wp_head
 * @note Author pages are not here because it would be redirected
 */

function seokey_meta_robot_noindex_checker_before_5_7() {
	// Don't do anything on admin, error and search pages (WordPress will handle it itself)
	if ( is_404() || is_admin() ) {
		return;
	}
	// Entire website is private ?
	if ( false === (bool) get_option( 'blog_public' ) ) {
		// Add our noindex tag everywhere
		add_action( 'seokey_action_head', 'seokey_content_visibility_robot_noindex_print', 2 );
		// Remove default noindex and all canonical tags (our own tag and the WordPress one)
		remove_action( 'seokey_action_head', 'seokey_head_meta_canonical' );
		remove_action( 'wp_head', 'noindex', 1 );
		remove_action( 'wp_head', 'rel_canonical' );
	} else {
		// Remove default noindex tag, we will handle it
		remove_action( 'wp_head', 'noindex', 1 );
		// We assume every content should be indexed
		$local_checked  = $global_checked = false;
		// Post types
		if ( is_singular() ) {
			// Post type globally private ?
			if ( ! empty( seokey_helper_get_option( 'cct-cpt', [] ) ) ) {
				// Post type is set to noindex
				$global_checked = seokey_helper_is_global_checked( 'posts', get_post_type() );
			}
			// This specific $post is private ?
			$local_checked = (bool) get_post_meta( get_the_ID(), 'seokey-content_visibility', true );
		}
		// blog page
		elseif ( is_home() || is_front_page() ) {
			// This $post is private ?
			$local_checked = (bool) get_post_meta( get_option( 'page_for_posts' ), 'seokey-content_visibility', true );
		}
		// Taxonomies
		elseif ( is_tax() || is_category() || is_tag() ) {
			// Get data about our term
			$queriedobject = get_queried_object();
			// Taxonomy globally private ?
			if ( ! empty( seokey_helper_get_option( 'cct-taxo', [] ) ) ) {
				// Taxonomy is set to noindex
				$global_checked = seokey_helper_is_global_checked( 'taxonomies', $queriedobject->taxonomy );
			}
			// This term is private ?
			$local_checked = (bool) get_term_meta( $queriedobject->term_id, 'seokey-content_visibility', true );
		}
		// Archives
		elseif ( is_post_type_archive() ) {
			// Get data about our post type
			$queriedobject = get_queried_object();
			if ( ! empty( $queriedobject->name ) && post_type_exists( $queriedobject->name ) ) {
				// Post type archive globally private ?
				$global_checked = seokey_helper_is_global_checked( 'posts', $queriedobject->name );
				// This archive is private ?
				$local_checked = (bool) get_option( 'seokey-content_visibility-' . $queriedobject->name );
			}
        } elseif ( is_author() ) {
            $local_checked = (bool)get_user_meta(get_the_author_meta('ID'), 'seokey-content_visibility', true);
        } elseif ( is_search() ) {
			$global_checked = true;
		}
		// Do we need a noindex tag ?
		if ( $global_checked || $local_checked ) {
			// Add noindex tag
			add_action( 'seokey_action_head', 'seokey_meta_robot_noindex_add', 2 );
			// Remove canonical tags
			remove_action( 'wp_head', 'rel_canonical' );
			remove_action( 'seokey_action_head', 'seokey_head_meta_canonical' );
		}
		// Go on : it's a good content
		else {
			add_action( 'seokey_action_head', 'seokey_meta_robot_add', 2 );
		}
	}
}

/**
 * Print noindex if necessary
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook   wp_head
 * @return void the html tag
 */
function seokey_meta_robot_noindex_add() {
	echo "<meta name='robots' content='noindex, nofollow, noarchive' />\n";
}

/**
 * Classic meta robots for all content
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook   wp_head
 * @return void the html tag
 */
function seokey_meta_robot_add() {
	echo "<meta name='robots' content='index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' />\n";
}