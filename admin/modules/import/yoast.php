<?php
/**
 * Load import functions for yoast
 *
 * @Loaded on data import + plugins_loaded + is_admin() + capability admin
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

// Get Yoast Data
$yoast_wpseo_titles = get_option( 'wpseo_titles' );
$yoast_wpseo        = get_option( 'wpseo' );

// Category base
$value = 0;
if ( !empty( $yoast_wpseo_titles['stripcategorybase'] ) ) {
	if ( 1 === (int) $yoast_wpseo_titles['stripcategorybase'] ) {
		$value = 1;
	}
}
update_option('seokey-field-metas-category_base', $value, true );

// Google meta HTML
if ( isset( $yoast_wpseo['googleverify'] ) ) {
	update_option('seokey-field-search-console-searchconsole-google-verification-code', esc_html( $yoast_wpseo['googleverify'] ), true );
}
unset($yoast_wpseo);

// Front page metas (if front page is a classic blog page)
$frontpage_id = get_option( 'page_on_front' );
if ( empty( $frontpage_id ) ) {
	if ( !empty( $yoast_wpseo_titles['title-home-wpseo'] ) ) {
		$data = seokey_helper_import_parsing( $yoast_wpseo_titles['title-home-wpseo'], 'homepage' );
		update_option( 'seokey-field-metas-metatitle', $data, true );
	}
	if ( !empty( $yoast_wpseo_titles['metadesc-home-wpseo'] ) ) {
		$data = seokey_helper_import_parsing( $yoast_wpseo_titles['metadesc-home-wpseo'], 'homepage' );
		update_option( 'seokey-field-metas-metadesc', $data, true );
	}

} else {
	// Title
	$data = get_post_meta( $frontpage_id, '_yoast_wpseo_title', true );

	if ( empty( $data ) ) {
		$data = $yoast_wpseo_titles[ 'title-page' ];
	}
	if ( '' != $data ) {
		update_post_meta( $frontpage_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ) ) );
		update_option( 'seokey-field-metas-metatitle', $data, true );
	}
	// Meta description
	$data = get_post_meta( $frontpage_id, '_yoast_wpseo_metadesc', true );
	if ( empty( $data ) ) {
		$data = $yoast_wpseo_titles[ 'metadesc-page' ];
	}
	if ( '' != $data ) {
		update_post_meta( $frontpage_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ), ENT_QUOTES ) ) );
		update_option( 'seokey-field-metas-metadesc', $data, true );
	}
	unset($data);
}

// Authors pages
$author_pages = array( 'author' );
if ( !empty( $yoast_wpseo_titles['disable-author'] ) ) {
	if ( true === $yoast_wpseo_titles['disable-author'] ) {
		$author_pages = array( 'i_am_a_dummy_value' );
	}
}
update_option( 'seokey-field-cct-pages', $author_pages, true );

// CPT global settings (show/hide, best taxonomy)
$_builtin   = get_post_types( ['_builtin' => true, 'public' => true ], 'objects' );
$_custom    = get_post_types( ['_builtin' => false, 'public' => true ], 'objects' );
// Merge them
$all_cpts = array_merge( $_builtin, $_custom );
unset($_builtin);
unset($_custom);
// Set the default vales with keys and public bool value
$all_cpts = wp_list_pluck( $all_cpts, 'public' );
// Remove the false bool value and remove the attachment post type
$all_cpts = array_filter( $all_cpts );
unset( $all_cpts['attachment'] );
// Add filter
$all_cpts = apply_filters( 'seokey_filter_settings_add_contents_post_types', $all_cpts );
// Iterate
$all_cpts = array_keys( $all_cpts );
$cctcpt = [ 'i_am_a_dummy_value' ];
if ( !empty( $all_cpts ) ) {
	foreach ( $all_cpts  as $post_type ) {
		$name = 'noindex-' . $post_type;
		// Tell which one to keep (noindex)
		if ( 1 !== (int) $yoast_wpseo_titles[$name] ) {
			$cctcpt[] = $post_type;
		}
		// Define main taxonomy for each post type
		$name = 'post_types-' . $post_type . '-maintax';
		if ( !empty( $yoast_wpseo_titles[$name] ) ) {
			update_option('seokey-field-cct-taxonomy-choice-' . $post_type, $yoast_wpseo_titles[$name], true );
		}
	}
	update_option( 'seokey-field-cct-cpt', $cctcpt, true );
}
unset( $all_cpts );

//  Global taxonomies
$_builtin = get_taxonomies( ['_builtin' => true, 'public' => true ], 'objects' );
$_custom = get_taxonomies( ['_builtin' => false, 'public' => true ], 'objects' );
$all_taxos = array_merge( $_builtin, $_custom );
unset( $_builtin );
unset( $_custom );
// Remove post format taxonomie
if ( isset ( $all_taxos['post_format'] ) ) {
	unset( $all_taxos['post_format'] );
}
$ccttaxo = [ 'i_am_a_dummy_value' ];
if ( !empty( $all_taxos ) ) {
	foreach ( $all_taxos as $taxonomy ) {
		$name = 'noindex-tax-' . get_object_vars( $taxonomy )['name'];
		// Tell which one to keep (noindex)
		if ( 1 !== (int) $yoast_wpseo_titles[$name] ) {
			$ccttaxo[] = get_object_vars( $taxonomy )['name'];
		}
	}
	update_option( 'seokey-field-cct-taxo', $ccttaxo, true );
}
unset( $ccttaxo );
unset( $cctcpt );
// Do not unset $all_taxos yet, we will need them later

// Post types archives
$args = array(
	'has_archive'   => true,
);
$output = 'names';
$post_types = get_post_types( $args, $output );
if ( !empty( $post_types ) ) {
	foreach ( $post_types as $post_type ) {
		if ( isset( $yoast_wpseo_titles[ 'title-ptarchive-' . $post_type ] ) ) {
			$data = seokey_helper_import_parsing( $yoast_wpseo_titles[ 'title-ptarchive-' . $post_type ], 'post_type_archive', '', $post_type );
			update_option( 'seokey-metatitle-' . $post_type, sanitize_text_field( $data ), true );
		}
		if ( isset( $yoast_wpseo_titles['metadesc-ptarchive-' . $post_type] ) ) {
			$data = seokey_helper_import_parsing( $yoast_wpseo_titles[ 'metadesc-ptarchive-' . $post_type ], 'post_type_archive', '', $post_type );
			update_option( 'seokey-metadesc-' . $post_type, sanitize_textarea_field( htmlspecialchars_decode( $data, ENT_QUOTES ) ), true );
		}
	}
}
// Purge data
unset( $post_types );

// Iterate $posts
// we may need to cool down
global $wpdb;
$totalcount = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
$batch      = 175;
$offset     = 0;
while ( $offset < $totalcount) {
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
				$data = get_post_meta( $post->ID, '_yoast_wpseo_title', true );
				if ( empty( $data ) ) {
					$data = $yoast_wpseo_titles[ 'title-' . $post->post_type ];
				}
				if ( '' != $data ) {

					update_post_meta( $post->ID, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) ) );
				}
				// Meta description
				$data = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
				if ( empty( $data ) ) {
					$data = $yoast_wpseo_titles[ 'metadesc-' . $post->post_type ];
				}
				if ( '' != $data ) {
					update_post_meta( $post->ID, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ), ENT_QUOTES ) ) );
				}
				// Main keyword
				$data = get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true );
				if ( '' != $data ) {
					$keyword = sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) );
					if ( ! add_post_meta( $post->ID, 'seokey-main-keyword', $keyword, true ) ) {
						update_post_meta ( $post->ID, 'seokey-main-keyword', $keyword );
					}
					unset($keyword);
				}
				// Noindex
				$data = (int) get_post_meta( $post->ID, '_yoast_wpseo_meta-robots-noindex', true );
				if ( 1 === $data ) {
					update_post_meta( $post->ID, 'seokey-content_visibility', 1 );
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
unset($totalcount);
unset($batch);
unset($offset);
unset($args);
unset($postlist);

// Iterate terms
$taxonomies = array_keys( $all_taxos );
$all_tax_metas = get_option( 'wpseo_taxonomy_meta' );
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
		if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_title'] ) ) {
			$data = $taxonomy_meta[$term->term_id]['wpseo_title'];
		} else {
			$data = $yoast_wpseo_titles[ 'title-tax-' . $term->taxonomy ];
		}
		if ( '' != $data ) {
			update_term_meta( $term->term_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'term', $term->term_id ) ) );
		}
		// Meta description
		if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_desc'] ) ) {
			$data = $taxonomy_meta[$term->term_id]['wpseo_desc'];
		} else {
			$data = $yoast_wpseo_titles[ 'metadesc-tax-' . $term->taxonomy ];
		}
		if ( '' != $data ) {
			update_term_meta( $term->term_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'term', $term->term_id ), ENT_QUOTES ) ) );
		}
		// Noindex
		if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_noindex'] ) ) {
			$data = $taxonomy_meta[$term->term_id]['wpseo_noindex'];
			if ( 'noindex' === $data ) {
				$data = 1;
			}
		}
		if ( 1 === $data ) {
			update_term_meta( $term->term_id, 'seokey-content_visibility', 1 );
		} else {
			delete_term_meta( $term->term_id, 'seokey-content_visibility' );
		}
	}
}
unset( $terms );
unset( $term );
unset( $taxonomies );


// Iterate users
$users = get_users();
foreach ( $users as $key => $user ) {
	// Title
	$data = get_user_meta( $user->ID, 'wpseo_title', true );
	if ( empty( $data ) ) {
		$data = $yoast_wpseo_titles['title-author-wpseo'];
	}
	$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
	update_user_meta( $user->ID, 'seokey-metatitle', esc_html( $data ) );
	// Metadesc
	$data = get_user_meta( $user->ID, 'wpseo_metadesc', true );
	if ( empty( $data ) ) {
		$data = $yoast_wpseo_titles['metadesc-author-wpseo'];
	}
	$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
	update_user_meta( $user->ID, 'seokey-metadesc', esc_html( $data ) );
	// Noindex
	$data = get_user_meta( $user->ID, 'wpseo_noindex_author', true );
	$data = ( "on" === $data ) ? 1 : 0;
	if ( 0 === $data ) {
		delete_user_meta( $user->ID, 'seokey-content_visibility' );
	} else {
		update_user_meta( $user->ID, 'seokey-content_visibility', $data );
	}
	// Purge data
	unset( $users[$key] );
}
// Purge data
unset( $users );


// Schema.org: person
if ( $yoast_wpseo_titles['company_or_person'] === "person" ) {
	// Name
	if ( !empty( $yoast_wpseo_titles['company_or_person_user_id'] ) ) {
		// Do not delete others correct values
		$previous_values = seokey_helper_get_option( 'schemaorg-schema-person' );
		if ( false !== $previous_values ) {
			$person = $previous_values;
		}
		$person['name'] = get_the_author_meta( 'display_name', $yoast_wpseo_titles['company_or_person_user_id'] );
		update_option( 'seokey-field-schemaorg-schema-person', $person, true );
		update_option( 'seokey-field-schemaorg-context', 'person', true );
	}
	// Image
	if ( isset( $yoast_wpseo_titles['person_logo_meta'] ) ) {
		if ( !empty( $yoast_wpseo_titles['person_logo_meta']['url'] ) ) {
			update_option( 'seokey-field-schemaorg-schema-person-image', esc_url( $yoast_wpseo_titles['person_logo_meta']['url'] ), true );
		}
	}
	$social = [
		'twitter',
		'facebook',
		'instagram',
		'linkedin',
		'myspace',
		'pinterest',
		'soundcloud',
		'tumblr',
		'youtube',
		'wikipedia'
	];
	// Do not delete others correct values
	$previous_values = seokey_helper_get_option( 'schemaorg-schema-person-sameas' );
	if ( false !== $previous_values ) {
		$sameas = $previous_values;
	}
	foreach ( $social as $link ) {
		if ( !empty( $value = get_the_author_meta( $link, $yoast_wpseo_titles['company_or_person_user_id'] ) ) ) {
			if ( $link === 'twitter' ) {
				$value = 'https://www.twitter.com/' . $value;
			}
			$sameas[] = esc_url( $value );
		}
	}
	$sameas = array_unique( $sameas );
	update_option( 'seokey-field-schemaorg-schema-person-sameas', $sameas, true );
}
// Schema.org: Company
else {
	// Company Name
	if ( ! empty( $yoast_wpseo_titles['company_name'] ) ) {
		// Do not delete others correct values
		$previous_values = seokey_helper_get_option( 'schemaorg-schema-local_business' );
		if ( false !== $previous_values ) {
			$company = $previous_values;
		}
		$company['name'] = sanitize_text_field( $yoast_wpseo_titles['company_name'] );
		update_option( 'seokey-field-schemaorg-schema-local_business', $company, true );
		update_option( 'seokey-field-schemaorg-context', 'local_business', true );
	}
	// Logo
	if ( ! empty( $yoast_wpseo_titles['company_logo'] ) ) {
		update_option( 'seokey-field-schemaorg-schema-local_business-image', esc_url( $yoast_wpseo_titles['company_logo'] ), true );
	}
}