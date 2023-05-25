<?php
/**
 * Load import functions for Rankmath
 *
 * @Loaded on data import + plugins_loaded + is_admin() + capability admin
 * @see wp_ajax_seokey_import_callback()
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

// Get RankMath data
$rankmath_wpseo        = get_option( 'rank-math-options-general' );

// Category base
$value = 0;
if ( !empty( $rankmath_wpseo['strip_category_base'] ) ) {
	if ( 'on' === $rankmath_wpseo['strip_category_base'] ) {
		$value = 1;
	}
}
update_option('seokey-field-metas-category_base', $value, true );

// Google meta HTML
if ( isset( $rankmath_wpseo['google_verify'] ) ) {
	update_option('seokey-field-search-console-searchconsole-google-verification-code', esc_html( $rankmath_wpseo['google_verify'] ), true );
}
unset($rankmath_wpseo);

// Get RankMath data
$rankmath_wpseo_titles        = get_option( 'rank-math-options-titles' );

// Front page metas (if front page is a classic blog page)
$frontpage_id = get_option( 'page_on_front' );
if ( empty( $frontpage_id ) ) {
	if ( !empty( $rankmath_wpseo_titles['homepage_title'] ) ) {
		$data = seokey_helper_import_parsing( $rankmath_wpseo_titles['homepage_title'], 'homepage' );
		update_option( 'seokey-field-metas-metatitle', sanitize_text_field( $data ), true );
	}
	if ( !empty( $rankmath_wpseo_titles['homepage_description'] ) ) {
		$data = seokey_helper_import_parsing( $rankmath_wpseo_titles['homepage_description'], 'homepage' );
		update_option( 'seokey-field-metas-metadesc', sanitize_textarea_field( $data ), true );
	}
	unset($data);
} else {
	// Title
	$data = get_post_meta( $frontpage_id, 'rank_math_title', true );
	if ( empty( $data ) ) {
		$data = $rankmath_wpseo_titles[ 'homepage_title' ];
	}
	if ( '' != $data ) {
		update_post_meta( $frontpage_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ) ) );
		update_option( 'seokey-field-metas-metatitle', sanitize_text_field( $data ), true );
	}
	// Meta description
	$data = get_post_meta( $frontpage_id, 'rank_math_description', true );
	if ( empty( $data ) ) {
		$data = $rankmath_wpseo_titles[ 'homepage_description' ];
	}
	if ( '' != $data ) {
		update_post_meta( $frontpage_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ), ENT_QUOTES ) ) );
		update_option( 'seokey-field-metas-metadesc', sanitize_textarea_field( $data ), true );
	}
	unset($data);
}

// Authors pages
$author_pages = array( 'author' );
if ( !empty( $rankmath_wpseo_titles['disable_author_archives'] ) ) {
	if ( 'on' === $rankmath_wpseo_titles['disable_author_archives'] ) {
		$author_pages = array( 'i_am_a_dummy_value' );
	}
}
update_option( 'seokey-field-cct-pages', $author_pages, true );

// CPT global settings (show/hide, best taxonomy)
$all_cpts = seokey_helper_import_get_cpts_public();
$cctcpt = [ 'i_am_a_dummy_value' ];
if ( !empty( $all_cpts ) ) {
	foreach ( $all_cpts  as $post_type ) {
		$name = 'pt_' . $post_type . '_robots';
		// Tell which one to keep (noindex)
		if ( !empty( $rankmath_wpseo_titles[$name] ) ) {
			if ( !in_array( 'noindex', $rankmath_wpseo_titles[$name] ) ) {
				$cctcpt[] = $post_type;
			}
		}
		// Define main taxonomy for each post type
		$name = 'pt_' . $post_type . '_primary_taxonomy';
		if ( !empty( $rankmath_wpseo_titles[$name] ) ) {
			if ( 'off' !== $rankmath_wpseo_titles[$name] ) {
				update_option( 'seokey-field-cct-taxonomy-choice-' . $post_type, $rankmath_wpseo_titles[ $name ], true );
			}
		}
	}
	unset( $name );
	update_option( 'seokey-field-cct-cpt', $cctcpt, true );
}
unset( $all_cpts );

//  Global taxonomies
$all_taxos = seokey_helper_import_get_taxonomies();
if ( !empty( $all_taxos ) ) {
	foreach ( $all_taxos as $taxonomy ) {
		$tax    = get_object_vars( $taxonomy )['name'];
		$name   = 'tax_' . $tax . '_robots';
		// Tell which one to keep (noindex)
		if ( !empty( $rankmath_wpseo_titles[$name] ) ) {
			if ( !in_array( 'noindex', $rankmath_wpseo_titles[$name] ) ) {
				$ccttaxo[] = $tax;
			}
		}
	}
	unset( $name );
	unset( $tax );
	update_option( 'seokey-field-cct-taxo', $ccttaxo, true );
}
unset( $ccttaxo );
unset( $cctcpt );
// Do not unset $all_taxos yet, we will need them later

// Post types archives
$post_types = get_post_types( array( 'has_archive' => true ) );
if ( !empty( $post_types ) ) {
	foreach ( $post_types as $post_type ) {
		$name = 'pt_' . $post_type . '_archive_title';
		if ( isset( $rankmath_wpseo_titles[ $name ] ) ) {
			$data = seokey_helper_import_parsing(  $rankmath_wpseo_titles[ $name ], 'post_type_archive', '', $post_type );
			update_option( 'seokey-metatitle-' . $post_type, sanitize_text_field( $data ), true );
		}
		$name = 'pt_' . $post_type . '_archive_description';
		if ( isset( $rankmath_wpseo_titles[$name] ) ) {
			$data = seokey_helper_import_parsing( $rankmath_wpseo_titles[$name], 'post_type_archive', '', $post_type );
			update_option( 'seokey-metadesc-' . $post_type, sanitize_textarea_field( htmlspecialchars_decode( $data, ENT_QUOTES ) ), true );
		}
	}
	unset( $name );
}
// Purge data
unset( $post_types );

// Iterate $posts
// we may need to cool down
global $wpdb;
$totalcount = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
$batch      = 175;
$offset     = 0;
while ( $offset < $totalcount ) {
	$args     = [
		'posts_per_page'            => $batch,
		'post_type'                 => 'any',
		'post_status'               => 'any',
		'no_found_rows'             => true,
		'update_post_term_cache'    => false,
		'ignore_sticky_posts'       => true,
		'offset'                    => $offset,
	];
	// TODO later improve to exclude earlier attachment post type
	$postlist = get_posts( $args );
	if ( $postlist ) {
		foreach ( $postlist as $post ) {
			if ( 'attachment' !== $post->post_type ) {
				// Title
				$data = get_post_meta( $post->ID, 'rank_math_title', true );
				if ( empty( $data ) ) {
					$data = $rankmath_wpseo_titles[ 'pt_' . $post->post_type . '_title' ];
				}
				if ( '' != $data ) {
					update_post_meta( $post->ID, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) ) );
				}
				// Meta description
				$data = get_post_meta( $post->ID, 'rank_math_description', true );
				if ( empty( $data ) ) {
					$data = $rankmath_wpseo_titles[ 'pt_' . $post->post_type . '_description' ];
				}
				if ( '' != $data ) {
					update_post_meta( $post->ID, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ), ENT_QUOTES ) ) );
				}
				// Main keyword
				$data = get_post_meta( $post->ID, 'rank_math_focus_keyword', true );
				if ( '' != $data ) {
					if ( str_contains( $data, ',') ) {
						$data = explode( ",", $data );
						$data = $data[0];
					}
					$keyword = sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) );
					if ( ! add_post_meta( $post->ID, 'seokey-main-keyword', $keyword, true ) ) {
						update_post_meta ( $post->ID, 'seokey-main-keyword', $keyword );
					}
				}
				// Noindex
				$data = get_post_meta( $post->ID, 'rank_math_robots', true );
				if ( is_array( $data ) ) {
					if ( in_array( 'noindex', $data ) ) {
						update_post_meta( $post->ID, 'seokey-content_visibility', 1 );
					} else {
						delete_post_meta( $post->ID, 'seokey-content_visibility' );
					}
				} else {
					delete_post_meta( $post->ID, 'seokey-content_visibility' );
				}
				unset($data);
			}
		}
	}
	$offset += $batch;
	usleep(50000);
}
unset($postlist);
unset($totalcount);
unset($batch);
unset($offset);
unset($args);

// Iterate terms
$taxonomies     = array_keys( $all_taxos );
unset( $all_taxos );
foreach ( $taxonomies as $taxonomie ) {
	// get terms data
	$taxonomy_meta = '';
	if ( !empty ( $all_tax_metas[$taxonomie] ) ) {
		$taxonomy_meta = $all_tax_metas[$taxonomie];
	}
	$terms = get_terms( array(
		'taxonomy' => $taxonomie,
		'hide_empty' => false,
	) );
	// Iterate
	foreach ( $terms as $term ) {
		// Title
		$data = get_term_meta( $term->term_id, 'rank_math_title', true );
		if ( empty ( $data ) ) {
			$data = $rankmath_wpseo_titles[ 'tax_' . $term->taxonomy . '_title' ];
		}
		if ( '' != $data ) {
			update_term_meta( $term->term_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'term', $term->term_id ) ) );
		}
		// Meta description
		$data = get_term_meta( $term->term_id, 'rank_math_description', true );
		if ( empty ( $data ) ) {
			$data = $rankmath_wpseo_titles[ 'tax_' . $term->taxonomy . '_description' ];
		}
		if ( '' != $data ) {
			update_term_meta( $term->term_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'term', $term->term_id ), ENT_QUOTES ) ) );
		}
		// Noindex
		$data = get_term_meta( $term->term_id, 'rank_math_robots', true );
		if ( is_array( $data ) ) {
			if ( in_array( 'noindex', $data ) ) {
				update_term_meta( $term->term_id, 'seokey-content_visibility', 1 );
			} else {
				delete_term_meta( $term->term_id, 'seokey-content_visibility' );
			}
		} else {
			delete_post_meta( $post->ID, 'seokey-content_visibility' );
		}
	}
}
unset( $taxonomies );
unset( $terms );

// Iterate users
$users = get_users();
foreach ( $users as $key => $user ) {
	// Title
	$data = get_user_meta( $user->ID, 'rank_math_title', true );
	if ( empty( $data ) ) {
		$data = $rankmath_wpseo_titles['author_archive_title'];
	}
	$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
	update_user_meta( $user->ID, 'seokey-metatitle', esc_html( $data ) );

	// Metadesc
	$data = get_user_meta( $user->ID, 'rank_math_description', true );
	if ( empty( $data ) ) {
		$data = $rankmath_wpseo_titles['author_archive_description'];
	}
	$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
	update_user_meta( $user->ID, 'seokey-metadesc', esc_html( $data ) );

	// Noindex
	$data = get_user_meta( $user->ID, 'rank_math_robots', true );
	if ( is_array( $data ) ) {
		if ( in_array( 'noindex', $data ) ) {
			update_user_meta( $user->ID, 'seokey-content_visibility', 1 );
		} else {
			delete_user_meta( $user->ID, 'seokey-content_visibility' );
		}
	} else {
		delete_user_meta( $user->ID, 'seokey-content_visibility' );
	}
	// Purge data
	unset( $users[$key] );
}
// Purge data
unset( $users );

// Schema.org: perso
if ( $rankmath_wpseo_titles['knowledgegraph_type'] === "person" ) {
	
	// Logo
	if ( ! empty( $rankmath_wpseo_titles['knowledgegraph_logo'] ) ) {
		update_option( 'seokey-field-schemaorg-schema-person-image', esc_url( $rankmath_wpseo_titles['knowledgegraph_logo'] ), true );
	}
	// Do not delete others correct values
	$previous_values = seokey_helper_get_option( 'schemaorg-schema-person' );
	if ( false !== $previous_values ) {
		$person = $previous_values;
	}
	// Name
	if ( ! empty( $rankmath_wpseo_titles['knowledgegraph_name'] ) ) {
		$person['name'] = sanitize_text_field( $rankmath_wpseo_titles['knowledgegraph_name'] );
	}
	// Update
	if ( false !== $person ) {
		update_option( 'seokey-field-schemaorg-schema-person', $person, true );
		update_option( 'seokey-field-schemaorg-context', 'person', true );
	}
}
// Schema.org: Company
else {
	// Do not delete others correct values
	$previous_values = seokey_helper_get_option( 'schemaorg-schema-local_business' );
	if ( false !== $previous_values ) {
		$company = $previous_values;
	}
	// Company name
	if ( ! empty( $rankmath_wpseo_titles['knowledgegraph_name'] ) ) {
		$company['name'] = sanitize_text_field( $rankmath_wpseo_titles['knowledgegraph_name'] );
	}
	// Address
	if ( ! empty( $rankmath_wpseo_titles['local_address'] ) ) {
		$address = $rankmath_wpseo_titles['local_address'];
		if ( ! empty( $address['streetAddress'] ) ) {
			$company['streetAddress'] = esc_html( $address['streetAddress'] );
		}
		if ( ! empty( $address['addressLocality'] ) ) {
			$company['addressLocality'] = esc_html( $address['addressLocality'] );
		}
		if ( ! empty( $address['postalCode'] ) ) {
			$company['postalCode'] = esc_html( $address['postalCode'] );
		}
		if ( ! empty( $address['addressCountry'] ) ) {
			$company['addressCountry'] = esc_html( $address['addressCountry'] );
		}
	}
	// Phone
	if ( ! empty( $rankmath_wpseo_titles['phone_numbers'] ) ) {
		$numbers    = $rankmath_wpseo_titles['phone_numbers'];
		$found_key  = array_search('customer support', array_column( $numbers, 'type'));
		// Main phone number found
		if  ( !empty ( $found_key ) ) {
			$phone = ( !empty( $numbers[$found_key]['number'] ) ) ? $numbers[$found_key]['number'] : '';
		} else {
			// Get first one
			if ( !empty( $numbers[0] ) ) {
				$phone = ( !empty( $numbers[0]['number'] ) ) ? $numbers[0]['number'] : '';
			}
		}
		if ( !empty( $phone )) {
			$company['telephone'] = esc_html( $phone );
		}
	}
	if ( false !== $company ) {
		update_option( 'seokey-field-schemaorg-schema-local_business', $company, true );
		update_option( 'seokey-field-schemaorg-context', 'local_business', true );
	}
	// Logo
	if ( ! empty( $rankmath_wpseo_titles['knowledgegraph_logo'] ) ) {
		update_option( 'seokey-field-schemaorg-schema-local_business-image', esc_url( $rankmath_wpseo_titles['knowledgegraph_logo'] ), true );
	}
	// Price Range
	if ( ! empty( $rankmath_wpseo_titles['price_range'] ) ) {
		$values['pricerangemin'] = esc_html( $rankmath_wpseo_titles['price_range'] );
		$values['pricerangemax'] = esc_html( $rankmath_wpseo_titles['price_range'] );
		update_option( 'seokey-field-schemaorg-schema-local_business-pricing', $values, true );
	}
	// Opening Hours
	if ( !empty( $rankmath_wpseo_titles['opening_hours'] ) ) {
		$days = $rankmath_wpseo_titles['opening_hours'];
		if ( is_array($days) ) {
			$data       = [];
			$days_done  = [];
			foreach ( $days as $day ) {
				$currentday = $day['day'];
				if ( !array_key_exists( $day['day'], $days_done ) ) {
					$time                     = explode( '-', $day['time'] );
					$opens                    = $time[0];
					$close                    = $time[1];
					$days_done[ $currentday ] = $currentday;
					$data[]       = [
						'opens'     => esc_html( $opens ),
						'closes'    => esc_html( $close ),
						'dayOfWeek' => [
							'0' => esc_html( $currentday ),
						]
					];
				}
			}
			update_option( 'seokey-field-schemaorg-schema-local_business-openinghoursspecification', $data, true );
		}
	}
	unset($rankmath_wpseo_titles);
}