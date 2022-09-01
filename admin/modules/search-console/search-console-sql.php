<?php
/**
 * Load search console
 *
 * @Loaded on plugins_loaded
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

/**
 * Create table
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_gsc_create_table() {
    // Database global
    global $wpdb;
    // Get SQL Collation
    $collate = seokey_helper_sql_collation();
    // Search Console Keywords
    $table_name = $wpdb->base_prefix . 'seokey_gsc_keywords';
    // SQL creation : all keywords (even duplicate keywords between URL)
    $sql = "CREATE TABLE `$table_name` ( 
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `keyword` text,
                `status` text,
                `clicks` int(11) DEFAULT NULL,
                `impressions` int(11) DEFAULT NULL,
                `position` float DEFAULT NULL,
                `url` text,
                PRIMARY KEY (`id`)
            ) $collate";
    maybe_create_table( $table_name, $sql );
    // SQL creation : all URL
    $table_name = $wpdb->base_prefix . 'seokey_gsc_pages';
    $sql = "CREATE TABLE `$table_name` ( 
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `url` text,
                `keywords` longtext,
                `clicks` int(11) DEFAULT NULL,
                `impressions` int(11) DEFAULT NULL,
                `position` float DEFAULT NULL,
                `content_id` int(11) DEFAULT NULL,
                `content_type` text,
                PRIMARY KEY (`id`)
            ) $collate";
    maybe_create_table( $table_name, $sql );
    // SQL creation : our targeted keywords !
    $table_name = $wpdb->base_prefix . 'seokey_keywords';
    $sql = "CREATE TABLE `$table_name` ( 
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `keyword` longtext,
                `content_url` text,
                `content_type` text,
                `content_id` INTEGER,
                PRIMARY KEY (`id`)
            ) $collate";
    maybe_create_table( $table_name, $sql );
}

/**
 * Delete tables on plugin uninstall
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_gsc_delete_table() {
    global $wpdb;
    // Delete SC Keyword Table
    $table_name = $wpdb->base_prefix . 'seokey_gsc_keywords';
    $sql        = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
    // Delete SC URL table
    $table_name = $wpdb->base_prefix . 'seokey_gsc_pages';
    $sql        = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
    // Delete User Keyword Table
    $table_name = $wpdb->base_prefix . 'seokey_keywords';
    $sql        = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
}

/**
 * Truncate tables
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_gsc_truncate_table() {
    global $wpdb;
    $table_name_keywords = $wpdb->prefix . 'seokey_gsc_keywords';
    $table_name_pages    = $wpdb->prefix . 'seokey_gsc_pages';
    // Truncate SC Keyword Table
    $sql = 'TRUNCATE TABLE ' . $table_name_keywords;
    $wpdb->query( $sql );
    // Truncate SC URL table
    $sql = 'TRUNCATE TABLE ' . $table_name_pages;
    $wpdb->query( $sql );
}