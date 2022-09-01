<?php
/**
 * Audit core class
 *
 * @package SEOKEY
 *
 * @note you will need to update these functions:
 * @see seokey_audit_get_task_score()
 * @see seokey_audit_get_task_text_with_count()
 * @see seokey_audit_get_task_name()
 * @see seokey_audit_get_task_messages_content()
 *
 * @todo convert old tasks to new core class model
 * @todo improve factoring to handle message and scoring within each task file
 */

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Seokey_Audit_Tasks {
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
		static::load_task();
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
		// No data : abort
		if ( '' === $this->items ) {
			return '';
		}
		// Task verification
		$this->seokey_audit_tasks_audit( $this->items );
		// Return status (warnings, errors or critical items)
		return $this->seokey_audit_tasks_get_status();
	}

	/**
	 * Task class audit
	 * @return void
	 */
	public function seokey_audit_tasks_audit( $data = '' ) {
		unset( $data );
	}

	/**
	 * Set status of this task
	 * @return array
	 */
	public function seokey_audit_tasks_get_status() {
		// return data
		$this->items = '';
		return $this->tasks_status;
	}
}