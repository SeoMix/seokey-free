<?php
/**
 * Audit Configuration
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


/**
 * Audit content tasks list
 * @note : you will also need to add this task in these functions
 * - seokey_audit_get_task_messages_content
 * - seokey_audit_get_task_text_with_count
 * - seokey_audit_get_task_score
 */

function seokey_audit_task_list_content() {
    // Define content tasks with content type
    $tasks = [
        'posts' => [
			// Text
            'words_count',
            'image_alt_missing',
            'no_image',
            'no_links',
	        'title_length',
	        'meta_desc_length',
            'main_keyword_selection',
            // Users
            'author_incomplete_infos',
        ],
    ];
    return $tasks;
}

/**
 * Audit technical tasks list
 * @note : you will also need to add this task in these functions
 * - seokey_audit_get_task_messages_content
 * - seokey_audit_get_task_text_with_count
 * - seokey_audit_get_task_score
 *
 */
function seokey_audit_task_list_technical() {
    // Define technical tasks with content type
    $tasks = [
        'global' => [
            // SeoKey settings
			'incomplete_who_are_you',
        ],
    ];
    return apply_filters( 'seokey_filter_audit_task_list_technical', $tasks );
}

/**
 * Audit types (used for tabs on the audit admin page)
 */
function seokey_audit_global_types() {
    // TODO transient !!!!
    $data = seokey_audit_get_results_by_type();
    $content_count = $technical_count = $all_count = 0;
    foreach ( $data as $item ) {
        $type = $item['audit_type'];
        switch ($type ) {
            case 'content':
                $content_count      = $content_count + $item['count(*)'];
                $all_count          = $all_count + $item['count(*)'];
                break;
            case 'technical':
                $technical_count    = $technical_count + $item['count(*)'];
                $all_count          = $all_count + $item['count(*)'];
                break;
        }
    }
    $tabs = [
        'issues-all'        => [
            'name'  => esc_html__( 'All issues' , 'seo-key' ),
            'count' => (int) $all_count,
        ],
        'issues-content'        => [
            'name'  =>  esc_html__( 'Content issues' , 'seo-key' ),
            'count' => (int) $content_count,
        ],
        'issues-technical'        => [
            'name'  => esc_html__( 'Technical issues' , 'seo-key' ),
            'count' => (int) $technical_count,
        ],
        'all-url'        => [
            'name'  => esc_html__( 'View all URLs with issues' , 'seo-key' ),
            'count' => (int) get_option('seokey_audit_global_url_count_withoutinfo'),
        ],
    ];
    return apply_filters( 'seokey_filter_audit_global_types', $tabs );
}