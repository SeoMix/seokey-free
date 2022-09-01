<?php
/**
 * Audit Background processing class
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


// TODO Comment
class SeoKey_Class_Audit_Trigger extends SeoKey_Class_Singleton {

    // Define instance for this class
    protected static $_instance;

    // Define our background process
    protected $background_process;

    // Always fire a background process
    protected function _init() {
        $this->background_process = new SeoKey_Class_Audit_Background_Process;
    }

    // Public function for running an audit
    public function run_audit() {
        if ( $this->background_process->is_audit_running() ) {
            return false;
        }
        // Delimiter and task counter
        $sep = '||';
        $tasklist = [];
        // Prepare start task
        $firsttask = '_start' . $sep . 'launch_tasks';
        $this->background_process->push_to_queue( $firsttask );
        $tasklist[] = $firsttask;
        // Content issues
        $task_list = seokey_audit_task_list_content();
        // Prepare Queue for content issues
        if ( !empty ( $task_list ) ) {
            foreach ($task_list as $type => $task_list_details) {
                foreach ($task_list_details as $task) {
                    $task = 'content' . $sep . $type . $sep . $task;
                    $tasklist[] = $task;
                    $this->background_process->push_to_queue($task);
                }
            }
        }
        // Technical issues
        $task_list = seokey_audit_task_list_technical();
        // Prepare Queue for technical issues
        if ( !empty ( $task_list ) ) {
            foreach ($task_list as $type => $task_list_details) {
                foreach ($task_list_details as $task) {
                    $task = 'technical' . $sep . $type . $sep . $task;
                    $tasklist[] = $task;
                    $this->background_process->push_to_queue($task);
                }
            }
        }
        // Prepare final task
        $endtask = '_end' . $sep . 'launch_tasks';
        $this->background_process->push_to_queue( $endtask );
        $tasklist[] = $endtask;
        // Count tasks
        update_option( 'seokey_audit_content_list', $tasklist, false );
        update_option( 'seokey_audit_tasks_count_types', count( $tasklist ), false );
        // Launch all wings
        update_option( 'seokey_audit_running', true, false );
        $this->background_process->save()->dispatch();
        // The end
        return true;
    }
}