<?php
/**
 * Audit core class
 *
 * @package SEOKEY
 */

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Seokey_Audit_Tasks_content_meta_desc_length extends Seokey_Audit_Tasks {
	/**
	 * Define only one instance of our class
	 */
	public static $instance = null;
	/**
	 * Construct SEOKEY class & avoid launching concurrent objects
	 */
	// Launch our stuff while constructing class
	public function __construct( $items = '' ) {
		parent::__construct($items);
	}
	
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
					'metadesc',
				],
				'task'  => 'meta_desc_length',
			];
			$this->items = $loader->run( $args );
		}
		parent::load_task();
	}
	
	/**
	 * Task class audit
	 * @return void
	 */
	public function seokey_audit_tasks_audit( $data = '' ) {
		foreach ( $data as $key => $item ) {
			if ( DOING_AJAX ) {
				$item['metadesc'] = html_entity_decode( stripslashes( ( $item['metadesc'] ) ) );
			}
			$item['metadesc'] = htmlspecialchars_decode( $item['metadesc'], ENT_QUOTES );
			// No content
			if ( empty ( $item['metadesc'] ) ) {
				$this->items[$key]['count'] = 0;
                $this->items[$key]['type'] = 'meta_desc_length1';
			} else {
                $count = seokey_helper_strlen( $item['metadesc'] );
                if ( $count < METADESC_COUNTER_MIN )  {
                    $this->items[$key]['count'] = $count;
                    $this->items[$key]['type'] = 'meta_desc_length2';
                } elseif ( $count > METADESC_COUNTER_MAX )  {
                    $this->items[$key]['count'] = $count;
                    $this->items[$key]['type'] = 'meta_desc_length3';
                } else {
                    unset($this->items[$key]);
                }
			}
		}
		parent::seokey_audit_tasks_audit();
	}
	
	/**
	 * Set status of this task
	 * @return void
	 */
	public function seokey_audit_tasks_get_status() {
		foreach ( $this->items as $key => $item ) {
			$this->tasks_status[$key] = [
				'item_type_global' => 'post',
				'audit_type'       => 'content',
				'task'             => 'meta_desc_length',
				'name'             => 'Meta description length',
				'priority'         => '3warning',
                'sub_priority'      => $item['type'],
				'datas'            => [ 'count' => $item['count'] ],
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}