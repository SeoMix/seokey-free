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

class Seokey_Audit_Tasks_content_author_incomplete_infos extends Seokey_Audit_Tasks {
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
		if ( empty ( $this->items ) && in_array( 'author', seokey_helper_get_option( 'cct-pages', [] ) ) ) {
			// Load contents for this task
			$loader = new SeoKey_Audit_Launch_task_load_content();
			// Lets define what we will need
			$args = [
				'type'       => 'authors',
				'task'       => 'author_incomplete_infos',
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
		// Prepare the metas to check if they exists and if they are empty
        $metas_to_check = [ "birthdate", "company", "jobTitle" ];
		foreach ( $data as $key => $item ) {
            $user_metas = get_the_author_meta( 'seokey_usermetas', $item['id'] );
			// If the user did not fill once the SeoKey user metas, we do not have seokey_usermetas
            if ( !$user_metas ) {
                $this->items[$key] = $item;
            } else {
                $have_no_empty_meta = true; // To check if we have at least one empty meta
                foreach ( $metas_to_check as $meta ) {
					// If we do not have the meta then it is not filled, set the error, else check if it is filled
                    if ( !array_key_exists( $meta, $user_metas ) ) {
                        $this->items[$key] = $item;
                        $have_no_empty_meta = false;
                        break; // No need to go further
                    } else {
						// Check if it is filled
                        if ( empty( trim( $user_metas[$meta] ) ) ) {
                            $this->items[$key] = $item;
                            $have_no_empty_meta = false;
                            break; // No need to go further
                        }
                    }
                }
				// If all user metas are filled, then no problem ! unset this item
                if ( $have_no_empty_meta ){
                    unset( $this->items[$key] );
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
				'item_type_global' => 'author',
				'audit_type'       => 'content',
				'task'             => 'author_incomplete_infos',
				'name'             => __( 'Author have incomplete data', 'seo-key' ),
				'priority'         => '3warning',
			];
		}
		parent::seokey_audit_tasks_get_status();
	}
}