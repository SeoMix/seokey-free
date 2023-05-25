<?php
/**
 * Audit assets functions
 *
 * @Loaded on 'init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_audit_page' );
/**
* Enqueue assets (CSS) for Option Reading Menu
*
* @author  Daniel Roch
*
* @uses    wp_enqueue_style()
*
* @hook    admin_enqueue_scripts
*
* @since   0.0.1
*/
function seokey_enqueue_admin_audit_page() {
    // CSS for setting pages
    $current_screen = seokey_helper_get_current_screen();
    if ( $current_screen->base === 'seokey_page_seo-key-audit' ) {
        // Enqueue settings CSS and JS
	    seokey_enqueue_admin_common_scripts();
        wp_enqueue_style('seokey-audit', esc_url(SEOKEY_URL_ASSETS . 'css/seokey-audit.css'), false, SEOKEY_VERSION );
	    if ( seokey_helpers_is_free() ) {
		    wp_enqueue_style( 'seokey-common-free', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common-free.css' ), false, SEOKEY_VERSION );
	    }
        // Get current audit status
        $audit_status = new SeoKey_Class_Audit_Background_Process();
        $audit_status = $audit_status->is_audit_running() ? 'true' : 'false';
        // Audit tabs + audit button
        wp_enqueue_script('seokey-score', SEOKEY_URL_ASSETS . 'js/seokey-score.js', array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION );
	    wp_enqueue_script( 'seokey-audit', SEOKEY_URL_ASSETS . 'js/seokey-audit.js', [ 'wp-i18n' ], SEOKEY_VERSION, false );
	    // Localize script arguments
        $args = array(
            // Ajax URL
            'ajaxurl' => admin_url('admin-ajax.php'),
            // PHP function to trigger a new audit
            'launch_action' => '_seokey_audit_ajax_launch',
            // Security nonce
            'security' => wp_create_nonce('seokey_audit_ajax'),
            // Audit running ?
            'audit_status' => $audit_status,
        );
        wp_localize_script('seokey-audit', 'adminAjaxaudit', $args);
        // Audit tables
        wp_enqueue_script('seokey-audit-tables', SEOKEY_URL_ASSETS . 'js/seokey-audit-tables.js', array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION);
	    // Tell WP to load translations for our JS.
	    wp_set_script_translations( 'seokey-audit-tables', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
	    // Localize script arguments
        $args = array(
            // Ajax URL
            'ajaxurl' => admin_url('admin-ajax.php'),
            // PHP function to display our data
            'display_action' => '_seokey_audit_display_table',
            // PHP function to display URL List
            'display_action_url' => '_seokey_audit_display_table_url',
            // Security nonce
            'security' => wp_create_nonce('seokey_audit_table_list'),
        );
        wp_localize_script('seokey-audit-tables', 'adminAjax', $args);
    }
}