<?php
/**
 * Third party: Elementor
 *
 * @Loaded on plugins_loaded + wizard done
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

if ( is_plugin_active( 'elementor/elementor.php' ) ) {
	
	// TODO noindex on elementor_library
	
	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_elementor_sitemaps' );
	// Exclude fake post types from sitemaps
	function seokey_thirdparty_elementor_sitemaps($excluded) {
		$excluded['cpt'][] = 'elementor_library';
		$excluded['taxo'][] = 'elementor_library_type';
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_elementor_settings', 500 );
	// Exclude post types from settings
	function seokey_thirdparty_elementor_settings($default){
		unset($default['elementor_library']);
		return $default;
	}
	
	add_filter( 'seokey_settings_filter_taxonomy_choice', 'seokey_thirdparty_elementor_exclude_taxo', 500 );
	// Remove from taxonomy choices for each post type
	function seokey_thirdparty_elementor_exclude_taxo( $default ){
		$default[] = 'elementor_library_type';
		return $default;
	}
	
	add_filter( 'seokey_filter_helper_audit_content_data', 'seokey_thirdparty_elementor_get_all_content', 1, 2 );
	/**
	 * Get better content from elementor pages (sometimes the_content does not returns everything)
	 *
	 * @param string $content content of the post
	 * @param mixed $post post values
	 * @since   1.8.2
	 * @author  Arthur Leveque
	 *
	 */
	function seokey_thirdparty_elementor_get_all_content( $content, $post ){
		$is_elementor_page =  get_post_meta( $post->ID, '_elementor_edit_mode', true );
		// Check if we are on an Elementor page
		if ( $is_elementor_page ) {
			if ( class_exists("\\Elementor\\Plugin") ) {
				$pluginElementor = \Elementor\Plugin::instance();
				$content         = $pluginElementor->frontend->get_builder_content_for_display( $post->ID );
			}
		}
		return $content;
	}
}