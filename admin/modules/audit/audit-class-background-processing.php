<?php
// TODO COMMENT


/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );


// TODO COMMENT
class SeoKey_Class_Audit_Background_Process extends SeoKey_WP_Background_Process {

    // Lets give a name to our stuff
    protected $prefix = 'seokey';
    protected $action = 'audit';

    /**
     * Is process running.
     * Check whether the current process is already running
     */
    public function is_audit_running() {
        return $this->is_process_running();
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        wp_raise_memory_limit();
        gc_disable();
        if ( ! $item || ! is_string( $item ) ) {
            seokey_dev_write_log('incorrect task');
            return false;
        }
        // Get details about our task
        $details = explode('||', $item);
        switch ( $details[0] ) {
            // Get our very first audit task (it will help us triggering some values
            case '_start':
                // Clean all before processing
                seokey_audit_helper_clear_data();
                // load content
                // TODO terms, authors and more
                $loader = new SeoKey_Audit_Launch_task_load_content();
                $args =         $args = [
                    'type'  => 'posts',
                    'values' => [
                        'content',
                    ],
                    'task'  => '',
                ];
                // $values and task names are not yet used, will be useful later to add filtering functionalities
                $items = $loader->run( $args );
                // How many content did we checked ?
                update_option( 'seokey_audit_content_count ', count( $items ), false );
                unset($items);
                break;
            // Let's handle our tasks, one at the time
            case 'content':
            case 'technical':
                // Load task file
                $task_name = sanitize_title($details[0] . '_' . $details[2]);
                $file = SEOKEY_PATH_ADMIN . 'modules/audit/tasks/' . $task_name . '.php';
                if ( !file_exists($file) ) {
                    // No task file, abort
                    seokey_dev_write_log('Error task : no file');
                    seokey_dev_write_log($task_name);
                    // remove task from list
                    return false;
                }
                // keep going, load file
                seokey_helper_require_file($task_name, SEOKEY_PATH_ADMIN . 'modules/audit/tasks/', 'editor');
                // Launch task if it exists
                $class = 'Seokey_Audit_Tasks_' . $task_name;
                if ( class_exists ( $class ) ) {
                    // Run task now
                    $run = new $class();
                    unset($class);
                    // Save data
                    $saving_data = new SeoKey_Audit_Launch_task_save_result();
                    $saving_data->run( $run->tasks_status );
                    unset ($saving_data);
                    // task done, let write it
                    $current_tasks_done = get_option('seokey_audit_tasks_list_done');
	                $names = seokey_audit_get_task_name( $details[2] );
                    $name = $names[$details[2]];
                    $current_tasks_done[] = $name;
                    update_option( 'seokey_audit_tasks_list_done', $current_tasks_done, true );
                    unset ($current_tasks_done);
                    unset ($names);
                    unset ($name);
                    unset ($run);
                }
                break;
            // Get our last audit task (it will help us triggering some values)
            case '_end':
                seokey_audit_helper_renew_data();
                break;
            // TODO Filter for other task types ?
        }
        gc_enable();
        gc_collect_cycles();
        sleep(1.5);
        // task completed, remove it from queue with false return value
        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        delete_option('seokey_option_audit_stop' );
        parent::complete();
    }

}


function seokey_audit_helper_clear_data(){
    // Empty audit tables
    global $wpdb;
    $table_name = esc_sql($wpdb->base_prefix . 'seokey_audit');
    $wpdb->query( 'TRUNCATE TABLE ' . $table_name );
    // delete last audit date, we will need to start over
    delete_option( 'seokey_audit_global_last_update' );
    // Delete various data and counts
    delete_option( 'seokey_audit_content_count' );
    delete_option( 'seokey_audit_global_url_count' );
    delete_transient( 'seokey_transient_audit_issues_type_count' );
}


function seokey_audit_helper_renew_data() {
    // Count URL with issues
    global $wpdb;
    $table   = $wpdb->base_prefix . 'seokey_audit';
    $results = $wpdb->get_var( "SELECT COUNT( DISTINCT item_id ) as URL FROM $table WHERE item_id NOT IN ( 0 );" );
    update_option( 'seokey_audit_global_url_count', (int) $results, false );
	$results = $wpdb->get_var( "SELECT COUNT( DISTINCT item_id ) as URL FROM $table WHERE item_id NOT IN ( 0 ) AND priority NOT IN ( 4 );" );
	update_option( 'seokey_audit_global_url_count_withoutinfo', (int) $results, false );
    // Get all task names and priorities for all audit tasks
    $results = $wpdb->get_results( "SELECT task, priority FROM " . $table . ' ORDER BY priority ASC' );
    // Convert std objects to array
    $array = json_decode( json_encode( $results ), true );
    unset($results);
    // Sort from min priority to max priority
    usort($array, "seokey_cb_audit_global_task_min_score");
    // now just keep one priority for each audit task
    $final = array();
    foreach ( $array as $values ) {
        $task = $values['task'];
        $priority = $values['priority'];
        if ( !isset ( $final[$task] ) ) {
            $final[$task] = $priority;
        }
    }
    unset($array);
    // save data
    update_option( 'seokey_audit_global_task_max_score', $final, false );
	// Define Last audit date
	update_option( 'seokey_audit_global_last_update', current_time( 'timestamp', false ), false );
    // Count each issue type and new score
    seokey_audit_global_data_issues_count();
    seokey_audit_global_data_score();
    sleep(1);
    // Audit is not running anymore
    delete_option( 'seokey_audit_tasks_list_done' );
    delete_option( 'seokey_audit_tasks_list_done_since_lasttime' );
    delete_option( 'seokey_audit_running_401_test' );
    delete_option( 'seokey_audit_running' );
    sleep(1);
}

// TODO Comments
function seokey_cb_audit_global_task_min_score($a, $b) {
    return $a["priority"] - $b["priority"];
}