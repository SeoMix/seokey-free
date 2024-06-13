<?php
/**
 * Third party: Event Calendar
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

if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {

	add_filter('pre_get_document_title', 'seokey_thirdparty_event_calendar_title', PHP_INT_MAX );
	function seokey_thirdparty_event_calendar_title( $title ) {
		if ( is_singular( 'tribe_events') ) {
			$ID 		= get_the_ID();
			// Check if title already defined
			$mytitle 	= seokey_helper_cache_data( 'seokey_thirdparty_event_calendar_title_' . $ID );
			// Undefined ? get correct title tag
			if ( null === $mytitle ) {
				$title = seokey_meta_title_value( 'singular', $ID );
				seokey_helper_cache_data( 'seokey_thirdparty_event_calendar_title_' . $ID, $title );
			}
		}
		if ( is_tax( 'tribe_events_cat') ) {
			$ID 		= get_queried_object_id();
			// Check if title already defined
			$mytitle 	= seokey_helper_cache_data( 'seokey_thirdparty_event_calendar_title_tax_' . $ID );
			// Undefined ? get correct title tag
			if ( null === $mytitle ) {
				$title = seokey_meta_title_value( 'taxonomy', $ID );
				seokey_helper_cache_data( 'seokey_thirdparty_event_calendar_title_tax_' . $ID, $title );
			}
		}
		return ($title);
	}

}