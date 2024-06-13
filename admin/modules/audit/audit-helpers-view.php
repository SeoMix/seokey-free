<?php
/**
 * Audit view functions
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

// TODO Comments
function seokey_audit_view_tab_tasks( $type = '') {
    // Get current issue type found with issue count
    $audit_tasks_issues = seokey_audit_get_results_by_task( $type );
    // Let's change key to have task name on main array key
    foreach ( $audit_tasks_issues as $key => $issue ) {
        $audit_tasks_issues[$issue['task']] = $issue;
        unset( $audit_tasks_issues[$key] );
    }
    // Get and sort scoring scale
    $audit_tasks_issues_score = wp_list_pluck( seokey_audit_get_task_score(), 'global' );
    arsort($audit_tasks_issues_score);
    // First, let's sort all available tasks
    $data = [];
    foreach ( $audit_tasks_issues_score as $key => $task_result ) {
        // ONly for task we need
        if ( array_key_exists($key, $audit_tasks_issues ) ){
            $task_name = $key; // h1_in_content for example
            $order = $task_result;
            while (array_key_exists($order, $data)) {
                ++$order;
            }
            $data[$order] = $task_name;
        }
    }
    // Sort task list and return final data
    $sortedlist = array_replace(array_flip($data), $audit_tasks_issues);
    return $sortedlist;
}

/**
 * Audit button Ajax
 * // TODO Comments
 */
function seokey_audit_global_launch_button_ajax() {
    $url = seokey_helper_admin_get_link( 'audit' );
    $url = add_query_arg( '_wpnonce',   wp_create_nonce( 'seokey-audit-global-run-ajax' ), $url );
    $url = add_query_arg( 'action',     'seokey-audit-global-run-ajax', $url );
    $url = add_query_arg( 'type',       'run', $url );
    $button = '<a href="' . esc_url( $url ) . '" id="seokey-audit-button-ajax-run" class="seokey-audit-button seokey-audit-button-ajax button button-hero button-primary">' . esc_html__( 'Launch an SEO Audit', 'seo-key' ) . '</a>';
    echo $button;
}

add_action( 'load-seokey_page_seo-key-audit', 'seokey_audit_screen_option' );
/**
 * Add Screen options
 */
function seokey_audit_screen_option() {
    $args = array(
	    'label' => esc_html__('Number of items per page:', 'seo-key'),
        'default' => 30,
        'option' => 'seokey_audit_per_page'
    );
    add_screen_option( 'per_page', $args );
}

// TODO Comments
function seokey_audit_tab_content( $task ){
    // task name
    $task_name = $task['task'];
    // Get priority for this audit
	$current_tasks_priority = get_option('seokey_audit_global_task_max_score');
    $task_priority = 'task-priority-'. (int) $current_tasks_priority[$task_name];
    // Count
    $count = $task['count(*)'];
	$count = apply_filters( 'seokey_filter_audit_tab_content_count', $count, $task['task'] );
    // Display
    echo '<div data-type="' . esc_attr( $task['audit_type'] ) . '" class="task task-'. sanitize_html_class( strtolower( $task_name ) ) .' ' . esc_attr( $task_priority ) . ' is-closed" data="'. esc_attr( $task_name ) .'">';
    echo '<header class="seokey-tooltip-parent" data-table="' . esc_attr( $task['item_type_global'] ) . '">';
        echo wp_kses_post( seokey_audit_get_task_text_with_count( $task_name, $count )  );
		echo seokey_helper_help_messages( 'audit-task-' . $task_name );
        echo '<a href="#" class="button button-small audit-show-table button-secondary" tab="' . seokey_helper_cache_data( 'seokey-audit-tab-id' ) . '" data="' . esc_attr( $task_name ) . '">' . esc_html__ ('See all', 'seo-key') . '</a>';
    echo '</header>';
    seokey_helper_loader('audit');
    echo '<section class="audit-table" id="' . sanitize_html_class( $task_name . '-' . seokey_helper_cache_data( 'seokey-audit-tab-id' ) ) . '">';
    echo '</section>';
    echo '</div>';
}

/**
 * Display issues counts in audit admin page
 *
 * @since   1.0.0
 * @author  Daniel Roch
 */
function seokey_audit_message_issues_details() {
    $issues_count = (int) get_option( 'seokey_audit_global_issues_count_now' );
    if ( false !== $issues_count ) {
	    $text = wp_kses_post( sprintf( _n( '<span>%s</span> issue', '<span>%s</span> issues', $issues_count, 'seo-key' ), number_format_i18n( $issues_count )  ) );
	    echo '<div id="seokey-audit-issues-wrapper" class="flexboxcolumn">';
	        echo '<h2 id="seokey-audit-issues-h2">' . esc_html__( 'SEO Issues', 'seo-key' ) . '</h2>';
	        echo '<div id="seokey-audit-issues">';
	            echo '<div id="seokey-audit-issues-count"><p>' . $text . '</p></div>';
	            seokey_audit_global_data_issues_type_count();
	        echo '</div>';
	    echo '</div>';
    }
}