<?php
/**
 * Load Editors TinyMCE and Gutenberg Module
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
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

add_filter( 'tiny_mce_before_init', 'seokey_editor_tinymce_remove_h1', SEOKEY_PHP_INT_MAX );
/**
 * Remove the H1 from TinyMCE
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   tiny_mce_before_init
 * @param  object (array)  $mceInit Contains the settings before the load of TinyMCE
 * @return mixed (array) $mceInit Contains the modified settings
 */
function seokey_editor_tinymce_remove_h1( $mceInit ) {
	// Something already filtered that before us
	if ( isset( $mceInit['block_formats'] ) ) {
		$mceInit['block_formats'] = trim( str_replace( ['Heading 1=h1', ';;'], ['', ';'], $mceInit['block_formats'] ), ';' );
	} else { // No one did, lets clean all this !
		$mceInit['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Address=address;Pre=pre';
	}
	return $mceInit;
}