<?php
/**
 * Audit List all contents with a noindex tag
 *
 * @package SEOKEY
 */

// TODO FACTORISATION => class_audit_tasks.php

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Seokey_Audit_Tasks_content_noindex_contents {
    /**
     * Define only one instance of our class
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @var (array) $instance Singleton
     * @access private
     * @static
     */
    public static $instance = null;
    /**
     * Construct SEOKEY class & avoid launching concurrent objects
     *
     * @author  Daniel Roch
     *
     * @since   0.0.1
     */
    // Launch our stuff while constructing class
    public function __construct( $items = '' ) {
        $this->items = $items;
        self::load_task();
    }
    // Wakeup of this class is forbidden.
    public function __wakeup() {}
    // Cloning of this class is forbidden.
    private function __clone() {}

    /**
     * All bad items form this audit task
     * @var int
     */
    public $tasks_status = [];
    /**
     * Our content
     * @var array
     */
    protected $items = [];

    /**
     * Task Class logic
     */
    public function load_task() {
        if ( empty ( $this->items ) ) {
            // Load contents for this task
            $loader = new SeoKey_Audit_Launch_task_load_content();
            // Lets define what we will need
            $args = [
                'type'      => 'posts',
                'values'    => [
                    'none',
                ],
                'task'      => substr( get_called_class(), 0, -27 ),
	            'noindex'   => 'include',
            ];
            $this->items = $loader->run( $args );
        }
        // No data : abort
        if ( '' === $this->items ) {
            return '';
        }
        // Task verification
        $this->seokey_audit_tasks_content_noindex( $this->items );
        // Return status (warnings, errors or critical items)
        return $this->seokey_audit_tasks_content_noindex_get_status();
    }

    /**
     * Get words count in content
     * @return bool
     */
    public function seokey_audit_tasks_content_noindex( $data ) {
        foreach ( $data as $key => $item ) {
            $noindex = get_post_meta( $item['id'], 'seokey-content_visibility', true );
            if ( 1 != $noindex ) {
                unset($this->items[$key]);
            }
        }
        unset($data);
    }

    /**
     * Set status of this task
     * @return array
     */
    public function seokey_audit_tasks_content_noindex_get_status() {
        // Define status for each bad item
        foreach ( $this->items as $key => $item ) {
            $this->tasks_status[$key] = [
                'item_type_global'  => 'post',
                'audit_type'        => 'content',
                'task'              => substr( get_called_class(), 27 ),
                'priority'          => '4information',
            ];
            
        }
        // return data
        $this->items = '';
        return $this->tasks_status;
    }
}
