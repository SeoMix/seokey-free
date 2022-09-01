<?php
/**
 * Admin Redirection module : SQL functions
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     redirections.php
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

/**
 * Create redirection table on plugin installation
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_redirections_create_table() {
	// Database global
	global $wpdb;
	// Table name
	$table_name = $wpdb->base_prefix . 'seokey_redirections';
	// Get SQL Collation
	$collate = seokey_helper_sql_collation();
	// SQL creation
	$sql        = "CREATE TABLE `$table_name` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `source` text,
                `target` text,
                `type` text,
                `status` int(11) DEFAULT NULL,
                `hits` int(11) DEFAULT NULL,
                `hits_last_at` datetime DEFAULT NULL,
                `group` text,
                PRIMARY KEY (`id`)
        ) $collate";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	maybe_create_table( $table_name, $sql );
}

/**
 * Create bad URL table on plugin installation (used in order to store errors and guessed redirections)
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_redirections_create_table_bad() {
	// Database global
	global $wpdb;
	// Get SQL Collation
	$collate = seokey_helper_sql_collation();
	// Table name for bad URL (404, redirections guessed, etc.)
	$table_name = $wpdb->base_prefix . 'seokey_redirections_bad';
	// Get SQL Collation
	// SQL creation
	$sql        = "CREATE TABLE `$table_name` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `source` text,
                `target` text,
                `type` text,
                `hits` int(11) DEFAULT NULL,
                `hits_last_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
        ) $collate";
	maybe_create_table( $table_name, $sql );
}

/**
 * Delete redirection table when we delete the plugin
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_redirections_delete_table() {
	global $wpdb;
	$table_name = $wpdb->base_prefix . 'seokey_redirections';
	$sql        = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query( $sql );
}

/**
 * Delete bad URL table when we delete the plugin
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_redirections_delete_table_bad() {
	global $wpdb;
	$table_name = $wpdb->base_prefix . 'seokey_redirections_bad';
	$sql        = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query( $sql );
}
