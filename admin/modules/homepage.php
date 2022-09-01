<?php
/**
 * Handle Homepage title and meta desc sync
 *
 * @Loaded on plugins_loaded + is_admin() + capability administrator
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

add_filter( 'option_seokey-field-metas-metatitle', 'seokey_settings_add_title_meta_static_synchronisation_title' );
/**
* Sync meta title for homepage: global option use post meta
*
* @since   1.0.0
* @author  Daniel Roch
*
* @param mixed $pre_option unfiltered option value for homepage title
*/
function seokey_settings_add_title_meta_static_synchronisation_title( $pre_option ) {
	$front_option = get_option('page_on_front');
	if ( $front_option > 0 ) {
		return get_post_meta( $front_option, 'seokey-metatitle', true );
	}
	return $pre_option;
}

add_filter( 'option_seokey-field-metas-metadesc', 'seokey_settings_add_title_meta_static_synchronisation_metadesc' );
/**
* Sync meta description for homepage: global option use post meta
*
* @since   1.0.0
* @author  Daniel Roch
*
* @param mixed $pre_option unfiltered option value for homepage meta description
*/
function seokey_settings_add_title_meta_static_synchronisation_metadesc( $pre_option ) {
	$front_option = get_option('page_on_front');
	if ( $front_option > 0 ) {
		return get_post_meta( $front_option, 'seokey-metadesc', true );
	}
	return $pre_option;
}

add_action( 'updated_option', 'seokey_settings_add_title_meta_static_synchronisation_update', 10, 3  );
/**
* Sync meta title and meta desc for homepage: sync data on updated option
*
* @since   1.0.0
* @author  Daniel Roch
*
* @param  string $option option to sync
* @param  mixed $old_value option value before update
* @param  mixed $value option value updated
*/
function seokey_settings_add_title_meta_static_synchronisation_update( $option, $old_value, $value ) {
	seokey_settings_add_title_meta_static_synchronisation( $option, $value );
}

add_action( 'added_option', 'seokey_settings_add_title_meta_static_synchronisation_add', 10, 2  );
/**
* Sync meta title and meta desc for homepage: sync data on added option
*
* @since   1.0.0
* @author  Daniel Roch
*
* @param  string $option option to sync
* @param  mixed $value option value added
*/
function seokey_settings_add_title_meta_static_synchronisation_add( $option, $value ) {
	seokey_settings_add_title_meta_static_synchronisation( $option, $value );
}

/**
* Sync meta title and meta desc for homepage: if page on front is defined, update post meta
*
* @since   1.0.0
* @author  Daniel Roch
*
* @param  string $option option to sync
* @param  mixed $value option value added/updated
*/
function seokey_settings_add_title_meta_static_synchronisation( $option, $value ) {
	$front_option = get_option('page_on_front');
	if ( $front_option > 0 ) {
		if ( 'seokey-field-metas-metatitle' === $option ) {
			update_post_meta( $front_option, 'seokey-metatitle', $value );
		}
		if ( 'seokey-field-metas-metadesc' === $option ) {
			update_post_meta( $front_option, 'seokey-metadesc', $value );
		}
	}
}