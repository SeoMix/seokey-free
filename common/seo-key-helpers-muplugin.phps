<?php
/**
 * Plugin Name: SEOKEY Free Mu-plugin
 * Description: Remove default publish post hook action to prevent harmful pings
 * Plugin URI: https://www.seo-key.com
 * Author: SeoMix - Daniel Roch
 * Author URI: https://www.seomix.fr
 * Contributors: Daniel Roch
 * Text Domain: seo-key
 * Domain Path: /languages/
 * Version: 0.1
 * Requires at least:  5.5
 * Tested up to: 5.5.3
 * Requires PHP: 7.0
 * Network: true
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright (C) 2017-2020, SEOKEY - contact@seo-key.com
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/* SEOKEY is active ? */
if ( defined( 'SEOKEY_NAME' ) ) {

    /* Tell SEOKEY our mu-plugin is up and running */
    if ( ! defined( 'SEOKEY_MUPLUGIN_ACTIVE' ) ) {
        define ( 'SEOKEY_MUPLUGIN_ACTIVE', true );
    }

    // TODO only if wizard has ended

    add_action( 'plugins_loaded', 'seokey_muplugin_publish_post_hook_remove' );
    /**
     * Remove default publish_post hook to prevent harmful pings
     *
     * @notes: When you want to publish a post, and you set it with a noindex tag, pings will still be triggered of first publication
     *
     * @hook plugins_loaded
     * @since  0.0.1
     * @author Daniel Roch
     **/
    function seokey_muplugin_publish_post_hook_remove(){
        if ( 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
            remove_action( 'publish_post', '_publish_post_hook', 5 );
        }
    }

}
// Only add our deinstallation script if SEOKEY is not active but not yet deinstalled
else {
    /* Tell SEOKEY our mu-plugin is not active */
    if ( ! defined( 'SEOKEY_MUPLUGIN_ACTIVE' ) ) {
        define ( 'SEOKEY_MUPLUGIN_ACTIVE', false );
    }

    add_action( 'admin_footer', 'seokey_muplugin_delete_data_popup_free', PHP_INT_MAX - 20 );
    /**
     * Add JS to plugin page in order to allow user to choose between deleting only files or files and all data on plugin deletion
     *
     * @since  0.0.1
     * @author Daniel Roch
     **/
    function seokey_muplugin_delete_data_popup_free() {
        global $pagenow;
        if ( 'plugins.php' === $pagenow ) {
            // Scripts
            wp_enqueue_script(  'wp-pointer' );
            wp_enqueue_script(  'utils' );
            wp_register_script( 'seokey-muplugin', content_url() . '/mu-plugins/seokey-free-muplugin.js', array( 'jquery', 'wp-i18n' ), 1 );
            wp_enqueue_script(  'seokey-muplugin' );
            wp_localize_script( 'seokey-muplugin', 'seokey_delete_option_free_script',
                [
                    'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
                    'security'                    => wp_create_nonce( 'seokey_delete_option_free' ),
                ]
            );
            // CSS
            wp_enqueue_style(   'seokey-muplugin', content_url() . '/mu-plugins/seokey-free-muplugin.css', '', 1 );
            wp_enqueue_style(   'wp-pointer' );
            wp_enqueue_style(   'seokey-muplugin' );
        }
    }

    // Trigger hook for authenticated users
    add_action( 'wp_ajax_seokey_delete_option_free', 'seokey_delete_option_free' );
    /**
     * Ajax call to tell WordPress if user wants to delete DATA and FILES, or only FILES
     *
     * @since  0.0.1
     * @author Daniel Roch
     **/
    function seokey_delete_option_free(){
        // Security (die if incorrect nonce)
        check_ajax_referer( 'seokey_delete_option_free', 'security' );
        // Security : check if user is an administrator
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Security');
            die;
        }
        if ( 'files' === $_GET['type'] ) {
            delete_option( 'seo-key-delete-data' );
            wp_send_json_success();
        } elseif ( 'all' === $_GET['type'] ) {
            update_option( 'seo-key-delete-data', 'go', 'yes' );
            wp_send_json_success();
        }
         wp_send_json_error('no data');
    }
}