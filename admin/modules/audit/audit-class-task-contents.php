<?php
/**
 * Audit Task class content loader : load content for a task
 *
 * @Loaded on 'init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Load content for a task
class SeoKey_Audit_Launch_task_load_content {
    /**
     * Get all posts
     */
    public function run( $args ) {
        // Ignore this run because we asked for this audit to stop
        if ( 1 == get_option( 'seokey_option_audit_stop ' ) ) {
            return '';
        }
        $noindex    = ( !empty( $args['noindex'] ) ) ? $args['noindex'] : 'exclude';
	    $subtype    = ( !empty( $args['subtype'] ) ) ? $args['subtype'] : 'no';
        $values     = ( !empty( $args['values'] ) )  ? $args['values']  : '';
        $metaquery  = ( !empty( $args['meta_query'] ) )  ? $args['meta_query']  : 'no';
        $post_id    = ( !empty( $args['post_id'] ) )  ? $args['post_id']  : '';
        // What content do we need ?
        switch ( $args['type'] ) {
            case 'posts':
                return $this->load_posts( $args['type'], $values, $args['task'], $noindex, $subtype, $post_id, $metaquery );
	        case 'medias':
		        return $this->load_medias( $args['type'], $values, $args['task'], $noindex, $subtype );
                break;
            // TODO Terms
            // TODO Authors
            // TODO FILTER
        }
    }

    // $task is not yet used
    public function load_posts( $type, $values, $task, $noindex, $subtype, $post_id, $metaquery ) {
        $items = [];
        // All public CPT in options
	    $post_types = ( "no" === $subtype ) ? seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => true ] ) ) : $subtype;
        if ( empty( $post_types ) ) {
	        return;
        }
        // Get all posts
        $args = array(
            'numberposts'           => 125,
            'post_type'             => $post_types, // use "any" to get all post types
            'orderby'               => 'date',
            'no_found_rows'         => true,
            'ignore_sticky_posts'   => true,
            'lang'                  => '',
            'order'                 => 'DESC',
        );
        unset($post_types);
        // Include meta query if there is any
        if ( $metaquery !== 'no' && is_array( $metaquery ) ) {
            $args['meta_query'] = array(
                'relation' => 'AND',
                $metaquery
            );
        }
        if ( 'include' !== $noindex ) {
            $visibility_metaquery = array(
                'relation' => 'OR',
                // Include posts where user has not yet defined the private/public value
                array(
                    'key'       => 'seokey-content_visibility',
                    'value'     => '0',
                    'compare'   => 'NOT EXISTS',
                ),
                // but exclude private posts
                array(
                    'key'       => 'seokey-content_visibility',
                    'value'     => 1,
                    'compare'   => '!=',
                ),
            );
            // if there is not already a meta_query in $args, create it, else push it in
            if ( empty( $args['meta_query'] ) ) {
                $args['meta_query'] = $visibility_metaquery;
            } else {
                array_push( $args['meta_query'], $visibility_metaquery );
            }
        }
        if ( ! empty( $post_id ) ) {
            $args['include'] = $post_id;
        }
        $post_list = get_posts( $args );
        // Keep only what we will need
        if ( ! empty( $post_list ) ) {
            foreach ( $post_list as $post ) {
                // Return only some data
                if ( !empty( $values) ) {
                    $current_item = $this->seokey_helper_audit_content_get_data( $post, $values, $task );
                }
                // Needed values were not defined, return all values for this task
                else {
                    // Get all values here
                    $current_item = $this->seokey_helper_audit_content_get_data( $post, [ "all" ], $task );
                }
                // Always add ID value
                $current_item = array_merge( $current_item, ['id' => $post->ID ] );
                // Send data for this task
                $items[$post->ID] = $current_item;
            }
        }
        // unset our var
        unset( $post_list );
        unset( $current_item );
        return $items;
    }

	public function load_medias( $type, $values, $task, $noindex, $subtype ) {
		$args = array(
			'post_type'         => 'attachment',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'numberposts'       => -1,
			'post_mime_type'    => 'image',
		);
		if ( 'no-alt' === $values[0] ) {
			$args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key' => '_wp_attachment_image_alt',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key' => '_wp_attachment_image_alt',
						'value' => '',
						'compare' => '='
					)
				);
		}
		// The Query
		$posts      = [];
		$contents   = get_posts( $args );
		foreach ( $contents as $content ) {
			$posts[] = get_object_vars( $content);
		}
		unset( $contents );
		return $posts;
	}


    public function seokey_helper_audit_content_get_data( $post, $values, $task ) {
        $item = [];
		// May be useful to remove/add filters
		do_action ( 'seokey_action_helper_audit_content_get_data', $task );
        foreach ( $values as $value ) {
            switch ($value) {
                case "content":
	                $content = apply_filters( 'the_content', $post->post_content );
	                $content = apply_filters( 'seokey_filter_helper_audit_content_data', $content, $post );
                    $item = array_merge( $item, [
                        'content' => $content,
                    ] );
                    break;
                case "title":
                    $item = array_merge( $item, [
                        'title' => seokey_head_get_meta_title($post->ID, 'singular'),
                    ] );
                    break;
                case "title_manual":
                    $item = array_merge( $item, [
                        'title_manual' => get_post_meta($post->ID, 'seokey-metatitle', true),
                    ] );
                    break;
	            case "metadesc":
		            $item = array_merge( $item, [
			            'metadesc' => seokey_meta_desc_value( 'singular', $post->ID, $args = array(), false ),
		            ] );
		            break;
                case "metadesc_manual":
                    $item = array_merge( $item, [
                        'metadesc_manual' => get_post_meta($post->ID, 'seokey-metadesc', true),
                    ] );
                    break;
                case "excerpt":
                    $item = array_merge( $item, [
                        'excerpt' => seokey_meta_desc_value('singular', $post->ID),
                    ] );
                    break;
                case "keyword":
                    $item = array_merge( $item, [
                        'keyword' => get_post_meta( $post->ID, 'seokey-main-keyword', true ),
                    ] );
                    break;
                case "permalink":
                    $item = array_merge( $item, [
                        'permalink' => get_permalink($post->ID),
                    ] );
                    break;
                case "permalink_no_domain":
                    // Slugs functions
                    $permalink  = get_permalink( $post->ID );
                    $url        = preg_replace('#^.+://[^/]+#', '', $permalink);
                    $url        = seokey_helper_url_remove_slashes($url, "both");
                    $item = array_merge( $item, [
                        'permalink_no_domain' => $url,
                    ] );
                    break;
                case "slug":
                    $item = array_merge( $item, [
                        'slug' => $post->post_name,
                    ] );
                    break;
                case "date":
                    $item = array_merge( $item, [
                        'date' => $post->post_date_gmt, // TODO fix consistency with singular audit date format
                    ] );
                    break;
                case "last_date":
                    $item = array_merge( $item, [
                        'last_date' => $post->post_modified_gmt, // TODO fix consistency with singular audit date format
                    ] );
                    break;
                case "author":
                    $item = array_merge( $item, [
                        'author' => $post->post_author,
                    ] );
                    break;
                case "all":
                    // default content
                    $content = apply_filters( 'the_content', $post->post_content );
                    // all data
                    $item = [
                        'content'               => apply_filters( 'seokey_filter_helper_audit_content_data', $content ),
                        'title'                 => seokey_head_get_meta_title( $post->ID, 'singular' ),
                        'title_manual'          => get_post_meta( $post->ID, 'seokey-metatitle', true ),
                        'metadesc'              => seokey_meta_desc_value( 'singular', $post->ID, $args = array(), false ),
                        'metadesc_manual'       => get_post_meta( $post->ID, 'seokey-metadesc', true ),
                        'excerpt'               => seokey_meta_desc_value('singular', $post->ID),
                        'keyword'               => get_post_meta( $post->ID, 'seokey-main-keyword', true ),
//                        'permalink'             => $permalink,
//                        'permalink_no_domain'   => $url,
//                        'slug'                  => $post->post_name,
//                        'date'                  => $post->post_date_gmt,
//                        'last_date'             => $post->post_modified_gmt,
//                        'author'                => $post->post_author,
                    ];
                    break;
            }
        }
        unset($values);
        return $item;
    }
}