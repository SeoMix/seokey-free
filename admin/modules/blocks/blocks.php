<?php
/**
 * Editor blocks
 *
 * @Loaded on plugins_loaded + is_admin() + capability editor
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Blocks files directory
$modules = SEOKEY_PATH_ADMIN . 'modules/blocks/';

// Get assets functions
seokey_helper_require_file( 'block-faq', $modules, 'everyone' );

add_filter( 'block_categories_all', 'seokey_blocks_custom_block_category', 10, 2 );
// Create SeoKey category for Gutemberg blocks
function seokey_blocks_custom_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'seokey-blocks',
				'title' => 'SEOKEY'
			),
		)
	);
}