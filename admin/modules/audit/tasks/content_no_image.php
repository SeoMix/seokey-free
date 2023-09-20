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
			$dom = seokey_audit_get_domdocument( $item['content'] );
			$x = new DOMXPath( $dom );
			$count = count( $x->query( '//img' ) );
			// If woocommerce is active and we do not have images in the content (no need to check further for this task if we already have images)
			if( $count === 0 ) {
				if( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
					// Check if we currently are with a product
					if ( get_post_type( $item['id'] ) === 'product' ) {
						// We need to check if the product have at least 1 image because looking in the content will not show any images. Woocommerce will automatically insert product images on product pages
						$product = wc_get_product( $item['id'] );
						// If we have images for the product, we need to make $count superior than 0
						! empty( $product->get_image_id() ) ? $count ++ : '';
						! empty( $product->get_gallery_image_ids() ) ? $count ++ : '';
					}
				}
			}
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