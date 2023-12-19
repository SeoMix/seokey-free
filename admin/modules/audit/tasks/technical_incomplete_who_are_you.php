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

class Seokey_Audit_Tasks_technical_incomplete_who_are_you extends Seokey_Audit_Tasks {
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
		$this->items = true;
		parent::load_task();
	}

	/**
	 * Task class audit
	 * @return void
	 */
	public function seokey_audit_tasks_audit( $data = '' ) {
		parent::seokey_audit_tasks_audit();
	}

	/**
	 * Set status of this task
	 * @return void
	 */
	public function seokey_audit_tasks_get_status() {
        $incomplete_schema = false; // To check later if we need to report the problem
        $schema_context = seokey_helper_get_option( 'schemaorg-context' ); // Get the schema context
        // If we have infos of the context and the user did not select "I don't want to display this information on Google"
        if ( $schema_context ) {
			$schema = seokey_helper_get_option( 'schemaorg-schema-' . $schema_context ); // Get the schema option
            if ( $schema ) {
                // Go through all the schema
                foreach ( $schema as $value ) {
                    // If the value is empty, pass $incomplete_schema to true and break the foreach, no need to go further
                    if ( empty( trim( $value ) ) ) {
                        $incomplete_schema = true;
                        break;
                    }
                }
                // If we have no problems yet, check for the image
                if ( !$incomplete_schema ) {
                    $image = seokey_helper_get_option( 'schemaorg-schema-' . $schema_context . '-image' ); // Get the image
                     // If we have no image, pass $incomplete_schema to true
                    if ( empty( trim( $image ) ) ) {
                        $incomplete_schema = true;
                    }
                }
            }
		}
        // If there is at least one problem, report this task
		if ( $incomplete_schema ) {
			$settings = esc_url( admin_url( 'admin.php?page=seo-key-settings#who-are-you' ) );
			$this->tasks_status[] = [
				'item_type_global' => 'global',
				'audit_type'       => 'technical',
				'task'             => 'incomplete_who_are_you',
				'name'             => __( 'Incomplete information about Website owner', 'seo-key' ),
				'priority'         => '3warning',
				'datas'             => ['settings' => $settings ],
			];
		} elseif ( '0' === $schema_context ) {
			$settings = esc_url( admin_url( 'admin.php?page=seo-key-settings#who-are-you' ) );
			$this->tasks_status[] = [
				'item_type_global' => 'global',
				'audit_type'       => 'technical',
				'task'             => 'incomplete_who_are_you',
				'name'             => __( 'No information about Website owner', 'seo-key' ),
				'priority'         => '3warning',
				'datas'             => ['settings' => $settings ],
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}