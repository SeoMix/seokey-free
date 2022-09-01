<?php
/**
 * Audit functions
 *
 * @Loaded on plugins_loaded + is_admin() + capability editor
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Audit files directory
$modules        = SEOKEY_PATH_ADMIN . 'modules/audit/';

// Get assets functions
seokey_helper_require_file( 'audit-assets',    $modules, 'contributor' );

// Get task core class
seokey_helper_require_file( 'class_audit_tasks',    $modules . 'tasks/', 'contributor' );

// Get helpers functions
seokey_helper_require_file( 'audit-helpers',    $modules, 'contributor' );

// Get score and count functions
seokey_helper_require_file( 'audit-helpers-score',    $modules, 'contributor' );

// Get view functions
seokey_helper_require_file( 'audit-helpers-view',    $modules, 'editor' );

// Get messages functions
seokey_helper_require_file( 'audit-helpers-messages',    $modules, 'contributor' );

// Load WP List Table Class to display each issue
seokey_helper_require_file( 'audit-wp-list-table-errors',    $modules, 'contributor' );

// Load audit class for content loading
seokey_helper_require_file( 'audit-class-task-contents',    $modules, 'contributor' );

// Load audit class for audit saving
seokey_helper_require_file( 'audit-class-task-save',    $modules, 'editor' );

// Load audit class for background processing
seokey_helper_require_file( 'audit-class-background-processing',    $modules, 'contributor' );

// Load audit class for firing or killing an audit process
seokey_helper_require_file( 'audit-class-start-stop',    $modules, 'contributor' );

// Activate individual content audit ($posts and $terms)
add_action( 'admin_init', 'seokey_audit_init_audit_content', SEOKEY_PHP_INT_MAX );
function seokey_audit_init_audit_content() {
	seokey_helper_require_file( 'audit-single-content', SEOKEY_PATH_ADMIN . 'modules/audit/', 'contributor' );
	new Seokey_Audit_Content();
}

// Get audit Configuration
seokey_helper_require_file( 'audit-configuration',    $modules, 'contributor' );



add_action('wp_ajax__seokey_audit_ajax_launch', '_seokey_audit_ajax_launch_callback');
/**
 * Action wp_ajax for audit stop&go
 */
function _seokey_audit_ajax_launch_callback() {
    // Nonce
    check_ajax_referer('seokey_audit_ajax', 'security');
    // User role
    if (!current_user_can(seokey_helper_user_get_capability('editor'))) {
        wp_die(__('Failed security check', 'seo-key'), SEOKEY_NAME, 403);
    }
    // What do we want to do ?
    $type = $_GET["type"];
    if ( 'run' === $type ) {
        // Start over : check if audit is running
        $audit_status = get_option( 'seokey_audit_running' );
        // No audit, launch one
        if ( true !== $audit_status ) {
	        // Check CRON before
//	        seokey_helper_cron_check();
	        // Tell people an audit is running
            update_option( 'seokey_audit_running', true, false );
            // Launch
            SeoKey_Audit_get_instance()->run_audit();
            // send json succes to ajax request
            wp_send_json_success('Audit launched');
        }
        // Audit already running, do nothing
        else {
            wp_send_json_error('Audit already running');
        }
        die();
    }
//    elseif ( 'stop' === $type ) {
//        SeoKey_Audit_get_instance()->stop_audit();
//        wp_send_json_success('Ajax request triggered STOP END');
//        die();
//    }
    // We did nothing, this is a mess
    wp_send_json_error('Ajax request failure');
    die();
}



add_action( 'seokey_loaded', 'SeoKey_Audit_get_instance' );
/**
 * Make always available our launch and go process (it will also launch an audit background process)
 */
function SeoKey_Audit_get_instance() {
    return SeoKey_Class_Audit_Trigger::get_instance();
}



add_action( 'wp_ajax__seokey_audit_get_status', '_seokey_audit_get_status_callback' );
function _seokey_audit_get_status_callback() {
    // Nonce
    check_ajax_referer('seokey_audit_ajax', 'security');
    // User role
    if (!current_user_can(seokey_helper_user_get_capability('editor'))) {
        wp_die(__('Failed security check', 'seo-key'), SEOKEY_NAME, 403);
    }
    $audit_status   = get_option('seokey_audit_running');
    $audit_401      = get_option('seokey_audit_running_401_test');
    if ( 1 == $audit_status ) {

        // One time check for pasword protection
        if ( true !== $audit_401 ) {
            // If user has set htpass data in SEOKEY, use it
            $args = [];
            $login      = esc_html( get_option( 'seokey-field-tools-htpasslogin' ) );
            $password   = esc_html( get_option( 'seokey-field-tools-htpasspass' ) );
            if ( !empty ( $login ) && !empty( $password ) ) {
                $args['headers'] = [
                    'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password )
                ];
            }
            $args['sslverify']= false;
            // Check 401
            $headers = wp_remote_get( home_url(), $args );
            if ( !is_wp_error ( $headers ) ) {
                if ( 401 === $headers['response']['code']) {
					$tools = sanitize_title( __( 'Tools', 'seo-key' ) );
                    $datas = [
                        'status' => 401,
                        'setting_url' => seokey_helper_admin_get_link('settings') . '#' . $tools
                    ];
                    // Send success with data
	                delete_transient( 'seokey_admin_401_checker' );
                    wp_send_json_success( $datas );
                    die;
                } else {
                    update_option ( 'seokey_audit_running_401_test', true, true );
                }
            }
        }

	    // Get all tasks done since the begining
	    $tasks_done = ( empty( $tasks_done = get_option('seokey_audit_tasks_list_done') ) ) ? [] : $tasks_done;
	   
		// Get previous task calls list
	    $tasks_done_since_lasttime = get_option('seokey_audit_tasks_list_done_since_lasttime');
	    if ( empty ( $tasks_done_since_lasttime ) ) {
		    $tasks_done_since_lasttime = $tasks_done;
	    } else {
	    	// diff
		    $tasks_done_since_lasttime = array_diff( $tasks_done, $tasks_done_since_lasttime );
	    }
	    update_option( 'seokey_audit_tasks_list_done_since_lasttime', $tasks_done );
	    // Define al data
	    $tools = sanitize_title( __( 'Tools', 'seo-key' ) );
	    $datas = [
            'tasks_remaining_count'     => get_option('seokey_audit_tasks_count_types') - count ( $tasks_done ),
            'tasks_done'                => $tasks_done,
            'tasks_done_since_lasttime' => $tasks_done_since_lasttime,
            'setting_url'               => seokey_helper_admin_get_link( 'settings' ) . '#' . $tools
        ];
	    // Send succes with data
        wp_send_json_success( $datas );
        die();
    } else {
        wp_send_json_error('No audit running');
        die();
    }
}

add_action( 'wp_ajax__seokey_audit_kill_process', '_seokey_audit_kill_process_callback' );
// TODO comment
function _seokey_audit_kill_process_callback() {
	// Nonce
	check_ajax_referer('seokey_audit_ajax', 'security');
	// User role
	if (!current_user_can(seokey_helper_user_get_capability('editor'))) {
		wp_die(__('Failed security check', 'seo-key'), SEOKEY_NAME, 403);
	}
	// Clear all corrupted data
	seokey_audit_helper_clear_data();
	delete_option( 'seokey_audit_running' );
	delete_option( 'seokey_audit_global_data_score_now' );
	delete_option( 'seokey_audit_global_data_score_old' );
	delete_option( 'seokey_audit_global_issues_count_now' );
	delete_option( 'seokey_audit_tasks_count_types' );
    delete_option( 'seokey_audit_running' );
    delete_option( 'seokey_audit_running_401_test' );
	delete_option( 'seokey_audit_global_url_count_withoutinfo' );
	delete_option( 'seokey_audit_global_task_max_score' );
	delete_option( 'seokey_audit_global_issues_count_old' );
	$audit_status = new SeoKey_Class_Audit_Background_Process();
	$audit_status->cancel_process();
	wp_send_json_success( 'Data cleared' );
	die();
}