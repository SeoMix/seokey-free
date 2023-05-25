<?php
/**
 * Load import functions for seopress
 *
 * @Loaded on data import + plugins_loaded + is_admin() + capability admin
 * @see wp_ajax_seokey_import_callback()
 * @package SEOKEY
 * @author Gauvain Van Ghele
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

// Get Seopress Data
$seopress_titles   = get_option( 'seopress_titles_option_name' );
$seopress_advanced = get_option( 'seopress_advanced_option_name' );

/*********   Category base    ********/
if ( !empty( $seopress_advanced['seopress_advanced_advanced_category_url'] ) ) {
	if ( 1 === (int) $seopress_advanced['seopress_advanced_advanced_category_url'] ) {
        // category not in permalinks ( option is checked )
		update_option('seokey-field-metas-category_base', 1, true );
	}
} else {
    // category is in permalinks
	update_option('seokey-field-metas-category_base', 0, true );
}

/*********   Google Meta html  ********/
    if ( isset( $seopress_advanced['seopress_advanced_advanced_google'] ) && !empty( $seopress_advanced['seopress_advanced_advanced_google'] ) ) {
	update_option('seokey-field-search-console-searchconsole-google-verification-code', esc_html( $seopress_advanced['seopress_advanced_advanced_google'] ), true );
}

/*********  BEGIN Front page options  ********/
// Front page metas (if front page is a classic blog page)
$frontpage_id = get_option( 'page_on_front' );

// if no Front page
if ( empty( $frontpage_id ) ) {

    if ( !empty( $seopress_titles['seopress_titles_home_site_title'] ) ) {
        $data_title_default = seokey_helper_import_parsing(  $seopress_titles['seopress_titles_home_site_title'] , 'homepage' );
        update_option( 'seokey-field-metas-metatitle', $data_title_default, true );

    }
    if ( !empty( $seopress_titles['seopress_titles_home_site_desc'] ) ) {
        $data_desc_default = seokey_helper_import_parsing(  $seopress_titles['seopress_titles_home_site_desc'] , 'homepage' );
        update_option( 'seokey-field-metas-metadesc', $data_desc_default, true );
    }
} else {
    // Title & desc default
    $data_title = get_post_meta($frontpage_id, '_seopress_titles_title', true);
    if( "" !== trim( $data_title ) ){
        update_post_meta( $frontpage_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data_title, 'post', $frontpage_id, 'page' ) ) );
        update_option( 'seokey-field-metas-metatitle', sanitize_text_field( $data_title ), true );
    }
    $data_desc = get_post_meta($frontpage_id, '_seopress_titles_desc', true);
    if( "" !== trim( $data_desc ) ){
        update_post_meta( $frontpage_id, 'seokey-metadesc', sanitize_text_field( seokey_helper_import_parsing( $data_desc, 'post', $frontpage_id, 'page' ) ) );
        update_option( 'seokey-field-metas-metadesc', sanitize_text_field( $data_desc ), true );
    }
}

/*********  Author    ********/
// Authors pages
$author_pages = array( 'author' );
if ( !empty( $seopress_titles['seopress_titles_archives_author_noindex'] ) ) {
	if ( 1 === $seopress_titles['seopress_titles_archives_author_noindex'] ) {
		$author_pages = array( 'i_am_a_dummy_value' );
	}
}
update_option( 'seokey-field-cct-pages', $author_pages, true );

/*********  CPT  ********/
$all_cpts = seokey_helper_import_get_cpts_public();
$cctcpt = [ 'i_am_a_dummy_value' ];

if ( !empty( $all_cpts ) ) {
    foreach ( $all_cpts  as $post_type ) {
	    if ( !empty( $seopress_titles['seopress_titles_single_titles'][ $post_type ] ) ) {
		    $cpt_noindex = $seopress_titles['seopress_titles_single_titles'][ $post_type ]['noindex'] ?? 0;
		    if ( 1 !== (int) $cpt_noindex ) {
			    $cctcpt[] = $post_type;
		    }
		    // Define main taxonomy for each post type
		    // Not in SEOPress free...
	    }
    }
     update_option( 'seokey-field-cct-cpt', $cctcpt, true );
}
unset( $all_cpts );

/*********  BEGIN TAX ********/
$all_taxos = seokey_helper_import_get_taxonomies();
if ( !empty( $all_taxos ) ) {
    foreach ( $all_taxos as $taxonomy ) {
        // Tell which one to keep (noindex)
        $taxo_noindex = $seopress_titles['seopress_titles_tax_titles'][$taxonomy->name]['noindex'] ?? 0;
        if ( 1 !== (int) $taxo_noindex ) {
            $ccttaxo[] = get_object_vars($taxonomy)['name'];
        }
    }
    update_option( 'seokey-field-cct-taxo', $ccttaxo, true );
}
unset( $ccttaxo );
unset( $cctcpt );
// Do not unset $all_taxos yet, we will need them later

/*********  BEGIN PT Archive *******/
$args = array(
    'has_archive'   => true,
);
$output = 'names';
$post_types = get_post_types( $args, $output );
if ( !empty( $post_types ) ) {
    foreach ( $post_types as $post_type ) {
        if ( isset( $seopress_titles[ 'seopress_titles_archive_titles' ][ $post_type ] ) ) {
            $data = seokey_helper_import_parsing( $seopress_titles[ 'seopress_titles_archive_titles' ][ $post_type ]['title'], 'post_type_archive', '', $post_type );
            update_option( 'seokey-metatitle-' . $post_type, sanitize_text_field( $data ), true );
        }
        if ( isset( $seopress_titles[ 'seopress_titles_archive_titles' ][ $post_type ] ) ) {
            $data = seokey_helper_import_parsing( $seopress_titles[ 'seopress_titles_archive_titles' ][ $post_type ]['description'], 'post_type_archive', '', $post_type );
            update_option( 'seokey-metadesc-' . $post_type, sanitize_textarea_field( htmlspecialchars_decode( $data, ENT_QUOTES ) ), true );
        }
    }
}
// Purge data
unset( $post_types );

/*********  BEGIN iterate posts *******/
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
                $data = get_post_meta( $post->ID, '_seopress_titles_title', true );

                if ( empty( $data ) ) {
                    $data = $seopress_titles[ 'seopress_titles_single_titles'][$post->post_type ]['title'];
                }
                if ( '' != $data ) {
                    update_post_meta( $post->ID, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) ) );
                }
                // Meta description
                $data = get_post_meta( $post->ID, '_seopress_titles_desc', true );
                if ( empty( $data ) ) {
                    $data = $seopress_titles[ 'seopress_titles_single_titles'][$post->post_type ]['description'];
                }
                if ( '' != $data ) {
                    update_post_meta( $post->ID, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ), ENT_QUOTES ) ) );
                }
                // Main keyword
                $data = get_post_meta( $post->ID, '_seopress_analysis_target_kw', true );
                if ( '' != $data ) {
	                $keyword = sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) );
	                if ( ! add_post_meta( $post->ID, 'seokey-main-keyword', $keyword, true ) ) {
		                update_post_meta ( $post->ID, 'seokey-main-keyword', $keyword );
	                }
				}
                // Noindex
                $data = get_post_meta( $post->ID, '_seopress_robots_index', true );
                if ( 'yes' == $data ) {
                    // If no index
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

/*********  BEGIN iterate terms *******/
$taxonomies = array_keys( $all_taxos );
unset( $all_taxos );
foreach ( $taxonomies as $taxonomie ) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomie,
        'hide_empty' => false,
    ));
    // Define parent default values
    $data_title_default = $seopress_titles['seopress_titles_tax_titles'][$taxonomie]['title'];
    $data_description_default = $seopress_titles['seopress_titles_tax_titles'][$taxonomie]['description'];
    $data_no_index_default = isset($seopress_titles['seopress_titles_tax_titles'][$taxonomie]['noindex']) ? $seopress_titles['seopress_titles_tax_titles'][$taxonomie]['noindex'] : 0;
    foreach ( $terms as $term ) {
        // Title
        $data_title = get_term_meta( $term->term_id, '_seopress_titles_title', true);
        if ( '' != $data_title ) {
            update_term_meta( $term->term_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data_title, 'term', $term->term_id ) ) );
        }else{
            update_term_meta( $term->term_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data_title_default, 'term', $term->term_id ) ) );
        }
        // Description
        $data_description = get_term_meta( $term->term_id, '_seopress_titles_desc', true);
        if ( '' != $data_description ) {
            update_term_meta( $term->term_id, 'seokey-metadesc', sanitize_text_field( seokey_helper_import_parsing( $data_description, 'term', $term->term_id ) ) );
        }else{
            update_term_meta( $term->term_id, 'seokey-metadesc', sanitize_text_field( seokey_helper_import_parsing( $data_description_default, 'term', $term->term_id ) ) );
        }
        $data_index = get_term_meta($term->term_id,'_seopress_robots_index',true);
        if($data_index == 'yes'){
            // If noindex is defined
            $data_index = 1;
        }else{
            // Else get Parent noindex
            $data_index = $data_no_index_default;
        }
        if ( 1 === $data_index ) {
            update_term_meta( $term->term_id, 'seokey-content_visibility', 1 );
        } else {
            delete_term_meta( $term->term_id, 'seokey-content_visibility' );
        }
    }
}
unset( $terms );
unset( $term );
unset( $taxonomies );

/*********  Users  *******/
$users = get_users();
foreach ( $users as $key => $user ) {
	// Title
	if ( !empty( $seopress_titles['seopress_titles_archives_author_title'] ) ) {
		$data = seokey_helper_import_parsing( $seopress_titles['seopress_titles_archives_author_title'], 'users', $user->ID );
		update_user_meta( $user->ID, 'seokey-metatitle', esc_html( $data ) );
	}
	// Metadesc
	if ( !empty( $seopress_titles['seopress_titles_archives_author_desc'] ) ) {
		$data = seokey_helper_import_parsing( $seopress_titles['seopress_titles_archives_author_desc'], 'users', $user->ID );
		update_user_meta( $user->ID, 'seokey-metadesc', esc_html( $data ) );
	}
	// Purge data
	unset( $users[$key] );
}
// Purge data
unset( $users );

/*********  Schema  *******/
$seopress_social = get_option( 'seopress_social_option_name' );

// Schema.org: person
if ( $seopress_social['seopress_social_knowledge_type'] === "Person" ) {
    // Name
    if ( "" !== $seopress_social['seopress_social_knowledge_name'] ) {

        // Do not delete others correct values
        $previous_values = seokey_helper_get_option( 'schemaorg-schema-person' );
        if ( false !== $previous_values ) {
            $person = $previous_values;
        }
        $person['name'] = $seopress_social['seopress_social_knowledge_name'];
        update_option( 'seokey-field-schemaorg-schema-person', $person, true );
    }
    // Image
    if ( isset( $seopress_social['seopress_social_knowledge_img'] ) ) {
        if ( !empty( $seopress_social['seopress_social_knowledge_img'] ) ) {
            update_option( 'seokey-field-schemaorg-schema-person-image', esc_url( $seopress_social['seopress_social_knowledge_img'] ), true );
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
        if ( "" !== $seopress_social['seopress_social_accounts_'.$link]  ) {
            if ( $link === 'twitter' ) {
                // add prefix url & Delete @ before account twitter = real url
                $seopress_social['seopress_social_accounts_'.$link] = 'https://www.twitter.com/' . substr($seopress_social['seopress_social_accounts_'.$link],1, );
            }
            $sameas[] = esc_url( $seopress_social['seopress_social_accounts_'.$link] );
        }
    }
    $sameas = array_unique( $sameas );
    // Delete keys if values are empty
    foreach($sameas as $key=>$value)
    {
        if( is_null($value) || $value == '' )
        {
            unset($sameas[$key]);
        }
    }
    update_option( 'seokey-field-schemaorg-schema-person-sameas', $sameas, true );
    update_option( 'seokey-field-schemaorg-context', 'person', true );
}
// Schema.org: Company / Organization
else {
    // Company Name
    if ( ! empty( $seopress_social['seopress_social_knowledge_type'] ) ) {
        // Do not delete others correct values
        $previous_values = seokey_helper_get_option( 'schemaorg-schema-local_business' );
        if ( false !== $previous_values ) {
            $company = $previous_values;
        }
        $company['name'] = sanitize_text_field( $seopress_social['seopress_social_knowledge_name'] );
        update_option( 'seokey-field-schemaorg-schema-local_business', $company, true );
    }
    // Logo
    if ( ! empty( $seopress_social['seopress_social_knowledge_img'] ) ) {
        update_option( 'seokey-field-schemaorg-schema-local_business-image', esc_url(  $seopress_social['seopress_social_knowledge_img'] ), true );
    }
    update_option( 'seokey-field-schemaorg-context', 'local_business', true );
}