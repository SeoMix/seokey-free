<?php
/**
 * Date archive functions
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
 * @see seokey_plugin_init()
 * @see public-modules.php
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

add_filter( 'day_link',     'seokey_archive_date_remove_archive_page_link' );
add_filter( 'month_link',   'seokey_archive_date_remove_archive_page_link' );
add_filter( 'year_link',    'seokey_archive_date_remove_archive_page_link' );
/**
 * Replace Date link with an #
 *
 * @since   0.0.1
 * @author  Leo Fontin
 *
 * @hook day_link
 * @hook month_link
 * @hook year_link
 * @return String #
 */
function seokey_archive_date_remove_archive_page_link() {
	return '#';
}
