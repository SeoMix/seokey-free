<?php
/**
 * Audit image ALT in content
 *
 * @package SEOKEY
 */

// TODO FACTORISATION => class_audit_tasks.php

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Seokey_Audit_Tasks_content_image_alt_missing {
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
                'type'  => 'posts',
                'values' => [
                    'content',
                ],
                'task'  => substr( get_called_class(), 27 ),
                'noindex'   => 'exclude',
            ];
            $this->items = $loader->run( $args );
        }
        // No data : abort
        if ( '' === $this->items ) {
            return '';
        }
        // Task Verification
        $this->seokey_audit_tasks_content_image_alt_missing( $this->items );
        // Return status (warnings, errors or critical items)
        return $this->seokey_audit_tasks_content_image_alt_missing_get_status();
    }

    /**
     * Get missing alt count
     * @return void
     */
    public function seokey_audit_tasks_content_image_alt_missing( $data ) {
        foreach ( $data as $key => $item ) {
            $count = $images_count = 0;
            // No content, abort
            if ( empty ( $item['content'] ) ) {
                unset( $this->items[$key] );
            } else {
                $content = stripslashes($item['content']);
                $dom = new DOMDocument();
                $dom->loadHTML( $content,
                    LIBXML_HTML_NOIMPLIED |      # Make sure no extra BODY
                    LIBXML_HTML_NODEFDTD |              # or DOCTYPE is created
                    LIBXML_NOERROR |                    # Suppress any errors
                    LIBXML_NOWARNING                    # or warnings about prefixes.
                );
                $x = new DOMXPath( $dom );
                foreach ( $x->query( '//img' ) as $node ) {
                    $images_count = $images_count + 1;
                    $alt = stripslashes( $node->getAttribute( 'alt' ) );
                    if ( empty( $alt ) ) {
                        $count++;
                    }
                }
                $this->items[$key]['count']         = $count;
                $this->items[$key]['images_count']  = $images_count;
            }
        }
        unset($data);
    }

    /**
     * Set status of this task
     * @return array
     */
    public function seokey_audit_tasks_content_image_alt_missing_get_status() {
        // Define status for each bad item
        foreach ( $this->items as $key => $item ) {
            if ( $item['count'] > 0 ) {
                $this->tasks_status[$key] = [
                    'item_type_global'  => 'post',
                    'audit_type'        => 'content',
                    'task'              => substr( get_called_class(), 27 ),
                    'priority'          => '3warning',
                    'datas'             => ['count' => $item['count'], 'images_count' => $item['images_count']],
                ];
            }
        }
        // return data
	    $this->items = '';
        return $this->tasks_status;
    }
}
