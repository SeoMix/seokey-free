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

class Seokey_Audit_Tasks_content_no_links extends Seokey_Audit_Tasks {
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
				'task'  => 'no_links',
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
		$home = untrailingslashit( home_url() );
		foreach ( $data as $key => $item ) {
			if ( !empty( $item['content'] ) ) {
				$dom = seokey_audit_get_domdocument( $item['content'], true );
				$anchorTags = $dom->getElementsByTagName( 'a' );
				$count = 0;
				//Loop through anchors tags
				foreach( $anchorTags as $anchorTag ) {
					// Get only internal links (or that starts with an /, that will be automaticly filled on the front)
					if ( str_starts_with( $anchorTag->getAttribute( 'href' ), $home ) || str_starts_with( $anchorTag->getAttribute( 'href' ), '/' ) ) {
						// Exclude internal nofollow
						if ( ! str_contains( $anchorTag->getAttribute( 'rel' ), 'nofollow' ) ) {
							$count ++;
						}
					}
				}
				if ( $count > 0 ) {
					unset($this->items[ $key ]);
				} else {
					$this->items[$key] = $item;
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
				'task'             => 'no_links',
				'name'             => 'No links in content',
				'priority'         => '3warning',
				'datas'            => '',
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}