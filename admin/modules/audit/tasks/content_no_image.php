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

class Seokey_Audit_Tasks_content_no_image extends Seokey_Audit_Tasks {
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
					'content',
				],
				'task'  => 'no_image',
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
			$count = preg_match_all("/<img/is", $item['content'], $matches);
			if ( $count > 0 ) {
				unset($this->items[ $key ]);
			} else {
				$this->items[$key] = $item;
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
				'task'             => 'no_image',
				'name'             => 'No image in content',
				'priority'         => '3warning',
				'datas'            => '',
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}