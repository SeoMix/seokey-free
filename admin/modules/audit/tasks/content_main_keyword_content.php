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
class Seokey_Audit_Tasks_content_main_keyword_content extends Seokey_Audit_Tasks {
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
				'type'          => 'posts',
				'values'        => [
					'content', 'keyword'
				],
				'meta_query'    => [
					'key'       => 'seokey-main-keyword',
					'compare'   => 'EXISTS'
				],
				'task'          => 'main_keyword_content',
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
			// Clean keyword
			$keyword = stripslashes( seokey_audit_clean_string( $item['keyword'] ) );
			if ( DOING_AJAX ) {
				$item['content'] = html_entity_decode( stripslashes( ( $item['content'] ) ) );
			}
			// Apply shortcodes and blocks
			$text = apply_filters( 'the_content', $item['content'] );
			// Clean HTML tags
			$text = wp_strip_all_tags( $text );
			// Get the first 100 words
			$text = wp_trim_words( $text, 100 );
			// Clean the text
			$text = seokey_audit_clean_string( $text );
			$text       = str_replace( "’", "'", $text );
			$keyword    = str_replace( "’", "'", $keyword );
			// If the keyword is in the first 100 words unset, else add to the errors list
			if ( str_contains( $text, $keyword ) ) {
				unset($this->items[ $key ]);
			} else {
				$item['keyword']   = $item['keyword'];
				$this->items[$key] = $item;
			}
		}
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
				'task'             => 'main_keyword_content',
				'name'             => 'Targeted keyword missing at start of content',
				'priority'         => '3warning',
				'datas'            => [ 'keyword' => stripslashes( $item['keyword'] ) ]
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}