<?php
/**
 * Audit content Title length
 *
 * @package SEOKEY
 */

// TODO FACTORISATION => class_audit_tasks.php

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Seokey_Audit_Tasks_content_title_length {
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
					'title',
					'title_manual'
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
		// Task verification
		$this->seokey_audit_tasks_content_title_length_get_count( $this->items );
		// Return status (warnings, errors or critical items)
		return $this->seokey_audit_tasks_content_title_length_get_status();
	}

	/**
	 * Get Title caracters count
	 * @return bool
	 */
	public function seokey_audit_tasks_content_title_length_get_count( $data  ) {
		foreach ( $data as $key => $item ) {
			// No content, abort
			if ( empty ( $item['title'] ) ) {
				unset( $this->items[$key] );
			} else {
//				if ( empty($item['title_manual'])) {
//					// TODO not a custom one
//				}
				if ( DOING_AJAX ) {
					$item['title'] = html_entity_decode( stripslashes( ( $item['title'] ) ) );
				}
				$this->items[$key]['count'] = seokey_helper_strlen( htmlspecialchars_decode( $item['title'], ENT_QUOTES  ) );
                $this->items[$key]['title'] = esc_html( $item['title'] );
			}
		}
        unset($data);
	}

	/**
	 * Set status of this task
	 * @return array
	 */
	public function seokey_audit_tasks_content_title_length_get_status() {
		// Define status for each bad item
		foreach ( $this->items as $key => $item ) {
			if ( METATITLE_COUNTER_MIN >= $item['count'] ) {
				$this->tasks_status[$key] = [
					'item_type_global'  => 'post',
					'audit_type'        => 'content',
					'task'              => substr( get_called_class(), 27 ),
					'priority'          => '1critical',
					'datas'             => [
                        'count' => $item['count'],
                        'title' => $item['title'],
                    ],
				];
			} elseif ( METATITLE_COUNTER_MAX < $item['count'] ) {
				$this->tasks_status[$key] = [
					'item_type_global'  => 'post',
					'audit_type'        => 'content',
					'task'              => substr( get_called_class(), 27 ),
					'priority'          => '2error',
                    'datas'             => [
                        'count' => $item['count'],
                        'title' => $item['title'],
                    ],
				];
			}
		}
		// return data
		$this->items = '';
		return $this->tasks_status;
	}
}
