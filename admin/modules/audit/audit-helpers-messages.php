<?php
/**
 * Audit messages functions
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
 * Return audit messages content
 *
 * @return array
 */
function seokey_audit_get_task_messages_content() {
    return apply_filters( 'seokey_filter_audit_get_task_messages_content', [
        // Content messages
	    'title_length'     => [
		    '1' => __( 'Title tag is too short: only %d characters. Here is the current title: <strong>%s</strong>', 'seo-key' ),
		    '2' => __( 'Title tag is too long: %d characters. Here is the current title: <strong>%s</strong>', 'seo-key' ),
	    ],
	    'meta_desc_length'     => [
		    '1' => __( 'Meta description is missing.', 'seo-key' ),
		    '2' => __( 'Meta description is too short: only %d characters.', 'seo-key' ),
		    '3' => __( 'Meta description is too long: %d characters.', 'seo-key' ),
	    ],
        'image_alt_missing' => [
            '3' => __( '%d of %d images do not have an alternative text (ALT) in your content. You need to describe each image for Search Engines.', 'seo-key' ),
        ],
	    'no_links' => [
		    '2' => __( 'No internal links  found in your content: you should add some towards your important pages.', 'seo-key' ),
	    ],
        'main_keyword_selection'   => [
            '4' => __( 'No keyword has been chosen for this content: please choose one to receive more SEO advice.', 'seo-key' ),
        ],
        'words_count'       => [
            '1'  => __( 'Your content is empty.', 'seo-key' ),
            '2'  => __( 'The length of your content is %d words instead of <strong>%d minimum</strong>. You need to expand it.', 'seo-key' ),
            '3'  => __( 'The length of your content is %d words, close to the %d limit. You can do better.', 'seo-key' ),
        ],
    ]);
}

function seokey_audit_get_task_messages_content_subpriority() {
	return apply_filters( 'seokey_filter_audit_get_task_messages_content_subpriority', [
        // Meta desc
            // No meta desc
            'meta_desc_length1' => __('Meta description empty: you should write one', 'seo-key' ),
            // Too short
            'meta_desc_length2' => __('Meta description too short (%s characters): you should expand it', 'seo-key' ),
            // too long
            'meta_desc_length3' => __('Meta description too long (%s characters): you should reduce it', 'seo-key' ),
	]);
}


/**
 * Get task message detail
 */
function seokey_audit_get_task_text_with_count( $text, $count ) {
    $array = apply_filters( 'seokey_filter_audit_get_task_text_with_count', [
        // 'urls' is not a specific task message : it's use for the audit URL tab message
        'urls'                      => _n( '<span class="seokey-issue-count">%s</span> URL with issue', '<span>%s</span> URL with issues', (int) $count, 'seo-key' ),
        // Content tasks
        'title_length'              => _n( '<span class="seokey-issue-count">%s</span> issue with a content title', '<span class="seokey-issue-count">%s</span> issues with titles', (int) $count, 'seo-key' ),
        'meta_desc_length'          => _n( '<span class="seokey-issue-count">%s</span> issue with a meta description', '<span class="seokey-issue-count">%s</span> issues with meta descriptions', (int) $count, 'seo-key' ),
        'image_alt_missing'         => _n( '<span class="seokey-issue-count">%s</span> content with images without descriptions (ALT text)', '<span class="seokey-issue-count">%s</span> contents with images without descriptions (ALT text)', (int) $count, 'seo-key' ),
        'no_links'                  => _n( '<span class="seokey-issue-count">%s</span> content without internal link', '<span class="seokey-issue-count">%s</span> contents without internal links', (int) $count, 'seo-key' ),
        'main_keyword_selection'    => _n( '<span class="seokey-issue-count">%s</span> content without a main keyword chosen', '<span class="seokey-issue-count">%s</span> contents without a main keyword chosen', (int) $count, 'seo-key' ),
        'words_count'               => _n( '<span class="seokey-issue-count">%s</span> content too short', '<span class="seokey-issue-count">%s</span> contents too short', (int) $count, 'seo-key' ),
    ] );
    return sprintf( $array[ $text ], (int) $count  ) ;
}

/**
 * Get task name
 */
function seokey_audit_get_task_name() {
	return apply_filters( 'seokey_filter_audit_get_task_name', [
		// Content tasks
		'title_length'              => esc_html__( 'Issues with meta title', 'seo-key' ),
		'meta_desc_length'          => esc_html__( 'Issues with meta descriptions', 'seo-key' ),
		'image_alt_missing'         => esc_html__( 'Image ALT missing in contents', 'seo-key' ),
		'no_links'                  => esc_html__( 'No internal links in contents', 'seo-key' ),
		'main_keyword_selection'    => esc_html__( 'Main keyword selection', 'seo-key' ),
		'words_count'               => esc_html__( 'Word Count', 'seo-key' ),
	] );
}

// TODO comment
function seokey_audit_get_task_messages_level(){
	return array (
		1 => 'critical',
		2 => 'error',
		3 => 'warning',
		4 => 'information',
	);
}

// TODO comment
function seokey_audit_task_list_global_types() {
    // TODO Filter
    return $tasks = [
        'content'   => esc_html__('Content issues','seo-key' ),
        'technical' => esc_html__('Technical issues','seo-key' ),
    ];
}

// TODO Revoir
// TODO comment
function seokey_audit_message( $score ){
    // switch
    if ( false === $score ) {
        return esc_html__( "No audit data yet ",'seo-key'  );
    }
    $score = (int) $score;
    if ( $score > 85 && $score <= 100 ) {
        $message = __( 'Good job ','seo-key' );
    } elseif ( $score <= 85 ) {
        $message = __( 'Keep working ','seo-key' );
    }
    // return
    // TODO Filter
    return esc_html( $message );
}

// TODO Revoir
// TODO comment
function seokey_audit_message_detail( $score ){
    // switch
    if ( false === $score ) {
        if ( 1 == get_option( 'seokey_audit_running' ) ) {
            return __( 'Please wait', 'seo-key' );
        } else {
            return __( 'You need to launch an audit', 'seo-key' );
        }
    }
    $score = (int) $score;
    // switch
    if ( $score > 90 && $score <= 100 ) {
        $message = __( 'Almost perfect','seo-key' );
    } elseif ( $score > 50 && $score <= 90 ) {
        $message = __( 'Keep going, you are almost there','seo-key' );
    } elseif ( $score > 25 && $score <= 50 ) {
        $message = __( 'You still need to improve your SEO','seo-key' );
    } else {
        $message = __( 'You still need a lot of work to improve your SEO','seo-key' );
    }
    // return
    // TODO Filter
    return esc_html( $message );
}

// TODO comment
function seokey_audit_message_detail_issues() {
    $content_checked = get_option('seokey_audit_content_count');
    $tasks           = get_option('seokey_audit_tasks_count_types');
    echo '<ul id="audit-details">';
        if ( false !== $content_checked ) {
            echo '<li>';
            printf( __( '<span class="seokey-audit-show-numbers">%1$s</span> content checked', 'seo-key' ), (int) $content_checked );
        }  else {
            echo '<li class="notyet">';
            esc_html_e( 'No content checked yet', 'seo-key' );
        }
        echo '</li>';
        if ( false !== $tasks ) {
            echo '<li>';
            printf( __( '<span class="seokey-audit-show-numbers">%1$s</span> audit tasks performed', 'seo-key' ), (int) $tasks - 2 );
        } else {
            echo '<li class="notyet">';
            esc_html_e( 'No SEO issues analyzed yet', 'seo-key' );
        }
        echo '</li>';
    echo '</ul>';
}


add_filter ( 'seokey_filter_audit_tab_content_count', 'seokey_filter_audit_task_count_imagesmedialibrary', 10, 2 );
// TODO Comments (specific case for task count)
function seokey_filter_audit_task_count_imagesmedialibrary( $count, $task ) {
	if ( 'image_alt_media_library' === $task ) {
		return seokey_helper_get_option( 'image_alt_media_library' );
	}
	return $count;
}