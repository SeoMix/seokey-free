<?php
/**
 * Breadcrumbs functions
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

add_shortcode( 'seokey_breadcrumbs', 'seokey_breacrumbs_shortcode' );
// TODO comments
function seokey_breacrumbs_shortcode() {
	return seokey_breacrumbs_print();
}

// TODO comments
function seokey_breacrumbs_data(){
	// Init data
	$data = [];
	$queried_object = get_queried_object();
	global $wp_rewrite;
	// Home (for all)
	$data[] = [
		'position'  => 1,
		'url'       => get_home_url(),
		'name'      => get_bloginfo( 'name' ),
	];
	// Let's start the clock
	$i = 2;

	// Blog page
	if ( is_home() && !is_front_page() ) {
		$data[] = [
			'position' => $i,
			'url'      => get_permalink( $queried_object->ID ),
			'name'     => esc_html( $queried_object->post_title ),
		];
		$i ++;
	}
	// Taxonomy term
	if ( is_tax() || is_tag() || is_category() ) {
		$taxObject      = get_taxonomy( $queried_object->taxonomy );
		$postTypeArray  = $taxObject->object_type;
		if ( is_array( $postTypeArray ) ) {
			$post_type = reset( $postTypeArray );
			$archive    = seokey_breacrumbs_data_get_post_type_archive( $post_type, $i );
			if ( is_array( $archive ) ) {
				if ( !is_category() || ( is_category() && get_post_type_archive_link( 'post' ) !== get_home_url() ) ) {
					$data[] = [
						'position' => $archive['position'],
						'url'      => $archive['url'],
						'name'     => $archive['name'],
					];
					$i++;
				}
			}
		}
		// parents
		$termstoadd = seokey_breacrumbs_data_get_hierarchical_terms( $queried_object->term_id, $queried_object->taxonomy, $i );
		// Push items into $data array
		if ( ! empty( $termstoadd ) ) {
			foreach ( $termstoadd as $item) {
				array_push( $data, $item );
				$i++;
			}
		}
		// Last item
		$data[] = [
			'position' => $i,
			'url'      => get_term_link( $queried_object->term_id ),
			'name'     => esc_html( $queried_object->name ),
		];
		$i++;
	}

	// Author pages
	if ( is_author() ) {
		$data[] = [
			'position' => $i,
			'url'      => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			'name'     => get_the_author_meta( 'display_name' )
		];
		$i++;
	}

	// Post Type Archive (for all post type with post type archive enabled)
	if ( is_post_type_archive() ) {
		$post_type_archive_link = get_post_type_archive_link( $queried_object->name );
		$data[] = [
			'position'  => $i,
			'url'       => $post_type_archive_link,
			'name'      => $queried_object->labels->name,
		];
		$i++;
	}

	// Post types
	if ( is_singular() && ! is_front_page() ) {
		$post_type = get_post_type();
		$archive   = seokey_breacrumbs_data_get_post_type_archive( $post_type, $i );
		if ( is_singular( 'post' ) ) {
			if ( !empty( get_option( 'page_for_posts' ) ) ) {
				$data[] = [
					'position' => $archive['position'],
					'url'      => $archive['url'],
					'name'     => $archive['name'],
				];
				$i ++;
			}
		}
		elseif ( is_array( $archive ) ) {
			$data[] = [
				'position' => $archive['position'],
				'url'      => $archive['url'],
				'name'     => $archive['name'],
			];
			$i ++;
		}
		
		// Terms selected for this post type
		if ( is_singular() ) {
			// TODO select main taxonomy for this post type
			$taxonomies = get_object_taxonomies( $queried_object->post_type );
			if ( ! empty( $taxonomies ) ) {
				$choice = seokey_helper_get_option( 'cct-taxonomy-choice-' . $queried_object->post_type );
				$taxonomy = ( empty( $choice) ) ? $taxonomies[0] : $choice;
				$terms = seokey_breacrumbs_data_get_taxonomy_terms( $queried_object->ID, array(), $taxonomy );
				// Do we have terms ?
				if ( ! empty( $terms ) ) {
					// TODO later later : choose a main term OR select childest term
					$term = $terms[0];
					// Do we have parent terms ?
					$parents = seokey_breacrumbs_data_get_hierarchical_terms( $term, $taxonomy, $i );
					// Push items into $data array
					if ( ! empty( $parents ) ) {
						foreach ( $parents as $item ) {
							array_push( $data, $item );
							$i ++;
						}
					}
					// Get data for current (and last) term
					$term_data = get_term_by( 'id', $term, $taxonomy );
					$data[]    = [
						'position' => $i,
						'url'      => esc_url( get_term_link( $term ) ),
						'name'     => esc_html( $term_data->name ),
					];
					$i ++;
				}
			}
		}
		
		// Get hiearchical data if needs be
		if ( is_post_type_hierarchical( $post_type ) ) {
			// Get all parents data if post type is hierarchical
			$itemtoadd = seokey_breacrumbs_data_get_hierarchical_posts( $queried_object, $i );
			// Push items into $data array
			if ( ! empty( $itemtoadd ) ) {
				foreach ( $itemtoadd as $item) {
					array_push( $data, $item );
					$i++;
				}
			}
		}
		
		// Singular last item
		$data[] = [
			'position'  => $i,
			'url'       => get_permalink( $queried_object->ID ),
			'name'      => esc_html( $queried_object->post_title ),
		];
		$i++;
	}
	
	// Pagination data
	$pagination_data = seokey_helper_get_paged();
	if ( $pagination_data > 1 ) {
		// Paginated posts
		if ( is_singular() ) {
			$title  = get_the_title();
			$url    = trailingslashit( get_permalink() ) . $pagination_data . '/';
		}
		// Paginated Front or Blog page
		elseif ( ( is_home() || is_front_page() ) && is_paged() ) {
			if ( empty( $queried_object ) ) {
				$title  = get_bloginfo( 'name' );
				// Get URl with correct pagination format
				$pagenum_link = html_entity_decode( get_pagenum_link() );
				$query_args   = array();
				$url_parts    = explode( '?', $pagenum_link );
				if ( isset( $url_parts[1] ) ) {
					wp_parse_str( $url_parts[1], $query_args );
				}
				$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
				$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
				$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
				$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
				$url    = trailingslashit( get_home_url() ) . str_replace( '%#%', $pagination_data, $format );
			} else {
				$title = get_the_title( $queried_object->ID );
				$url   = trailingslashit( get_permalink( $queried_object->ID ) ) . $pagination_data . '/';
			}
		}
		// Paginated terms
		else {
			// Fallback in $title for search pages
			$title =  ( empty($queried_object->name) ) ? get_bloginfo( 'name' ) : $queried_object->name;
			$url    = get_pagenum_link( $pagination_data );
		}
		// add pagination data
		$data[] = [
			'position'  => $i,
			'url'       => $url,
			'name'      => sprintf( esc_html__( $title . ' - page %d', 'seo-key' ), $pagination_data ),
		];
	}
	// Return data
	return apply_filters( 'seokey_filter_breacrumbs_data', $data);
}

// TODO comment
function seokey_breacrumbs_data_get_hierarchical_posts( $queried_object, $i ) {
	// Get all parents data if post type is hierarchical
	$parent         = $queried_object->post_parent;
	$nextitemtoadd  = $itemtoadd = array();
	// Get parent posts
	while ( $parent ) {
		$post_object = get_post( $parent );
		// Is it a Post object ?
		if ( is_object( $post_object ) && is_a( $post_object, 'WP_Post' ) ) {
			// Define data
			if ( 1 != get_post_meta( $parent, 'seokey-content_visibility', true ) ) {
				$nextitemtoadd[] = array(
					'url'  => get_permalink( $parent ),
					'name' => get_the_title( $parent ),
				);
			}
		}
		if ( $post_object->post_parent !== 0 ) {
			// Get his parent
			$parent = $post_object->post_parent;
		} else {
			// Unset $parent to kill this loop
			$parent = false;
		}
	}
	// Revers data to have good order
	$nextitemtoadd = array_reverse( $nextitemtoadd );
	// now add our items with correct position
	foreach ( $nextitemtoadd as $item ) {
		$itemtoadd[] = array(
			'position' => $i,
			'url'      => $item['url'],
			'name'     => $item['name'],
		);
		$i++;
	}
	return $itemtoadd;
}

// TODO comment
// Based on wp_get_post_categories function
function seokey_breacrumbs_data_get_taxonomy_terms( $post_id = 0, $args = array(), $tax = 'category' ) {
	$post_id    = (int) $post_id;
	$defaults   = array( 'fields' => 'ids' );
	$args       = wp_parse_args( $args, $defaults );
	$tax_terms  = wp_get_object_terms( $post_id, $tax, $args );
	return $tax_terms;
}

/**
 * Get post type archive link data
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $post_type Post type
 * @param integer $i current item order
 * @return void|array
 */
function seokey_breacrumbs_data_get_post_type_archive( $post_type, $i ) {
	// Post type has a post type archive
	$post_type_archive_link = get_post_type_archive_link( $post_type );
	if ( false !== $post_type_archive_link ) {
		// Exclude Post type archive if noindex is on
		if ( empty( get_option( 'seokey-content_visibility-' . $post_type ) ) ) {
			$post_type_obj = get_post_type_object( $post_type );
			// Specific case for blog page archive
			if( 'post' === $post_type ) {
				$name = get_the_title( get_option( 'page_for_posts' ) );
			} else {
				$name = $post_type_obj->labels->name;
			}
			return [
				'position' => $i,
				'url'      => $post_type_archive_link,
				'name'     => $name,
			];
		}
	}
}

// TODO Comment
function seokey_breacrumbs_data_get_hierarchical_terms( $term, $tax, $i ) {
//	// Do we have parent terms ?
	$parents = get_ancestors( $term, $tax );
	$termsancestorstoadd = $termsancestors = [];
	if ( !empty( $parents ) ) {
		foreach ( $parents as $parent_term ) {
			$parent_term_data = get_term_by('id', $parent_term, $tax );
			if ( 1 != get_term_meta( $parent_term, 'seokey-content_visibility', true ) ) {
				$termsancestorstoadd[] = [
					'position' => $i,
					'url'      => esc_url( get_term_link( $parent_term ) ),
					'name'     => esc_html( $parent_term_data->name ),
				];
				$i ++;
			}
		}
	}
	// Revers data to have good order
	$termsancestorstoadd = array_reverse( $termsancestorstoadd );
	// now add our items with correct position
	foreach ( $termsancestorstoadd as $item ) {
		$termsancestors[] = array(
			'position' => $i,
			'url'      => $item['url'],
			'name'     => $item['name'],
		);
		$i++;
	}
	return $termsancestors;
}

/**
 * Echo breadcrumbs function
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $before HTML before breadcrumb
 * @param string $after HTML after breadcrumb
 * @return void|string
 */
function seokey_breacrumbs_print( $before = '<div id="seokey-breadcrumbs">', $after = '</div>' ) {
	$data   = seokey_breacrumbs_data();
	$html   = '';
	$sep    = ' > ';
	$i      = 0;
	if ( !empty( $data ) ) {
		$count = count( $data );
		$html   = '';
		foreach ( $data as $item ) {
			$i++;
			if ( !empty( $item['name'] ) && !empty( $item['url'] ) ) {
				if ( $i === $count) {
					$html .= esc_html( $item['name'] );
				} else {
					$html .= '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>' . $sep;
				}
			}
		}
	}
	if ( !empty( $html ) ) {
		return $before . $html . $after;
	}
}