<?php
/**
 * Audit SQL functions
 *
 * @Loaded on activation and deactivation hooks
 *
 * @see     admin/plugin-activate-deactivate-uninstall.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Create SQL table
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_audit_create_table() {
    // Database global
    global $wpdb;
    // Table name
    $table_name = esc_sql ( $wpdb->base_prefix . 'seokey_audit' );
    // Get SQL Collation
    $collate = seokey_helper_sql_collation();
    // SQL creation
    $sql = "CREATE TABLE `$table_name` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `item_id` int(11),
            `item_name` text,
            `item_type` text,
            `item_type_global` text,
            `audit_type` text,
            `task` text,
            `priority` text,
            `sub_priority` text,
            `datas` longtext,
            PRIMARY KEY (`id`)
    ) $collate";
    //            `status` text,
    // load useful SQL functions
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    /// Do we need to create it ?
    maybe_create_table( $table_name, $sql );
}

/**
 * Delete SQL table on uninstall
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_audit_delete_table() {
    // Get Database data
    global $wpdb;
    // Delete our audit table
    $table_name = $wpdb->base_prefix . 'seokey_audit';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
}
