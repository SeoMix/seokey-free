<?php
/**
 * Load SEOKEY Admin pages functions
 *
 * @Loaded on plugins_loaded + is_admin() + capability author
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

add_filter( 'admin_body_class', 'seokey_admin_page_body_class' );
// TODO Comment
function seokey_admin_page_body_class( $classes ) {
    $screen       = seokey_helper_get_current_screen();
    // Are we in the dashboard page ?
    if ( true === str_starts_with( $screen->base, 'seokey_page' ) ||
         true === str_starts_with( $screen->base, 'toplevel_page_seo-key' ) ) {
        $classes .= ' seokey-pages';
    }
    return $classes;
}



add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_header', 20 );
// TODO Comment
function seokey_admin_page_header() {
    $screen       = seokey_helper_get_current_screen();
    $current_page = $screen->base;
    $current_wizard = get_option( 'seokey_option_first_wizard_seokey_notice_wizard' );
    if ( 'goodtogo' === $current_wizard ) {
        // Are we in the dashboard page ?
        if ( $current_page !== 'seokey_page_seo-key-wizard' ) { ?>
            <h1><?php esc_html_e( get_admin_page_title() );?></h1>
            <div class='seokey-section-dark'>
                <div class='seokey-heading'>
                    <div id="main-title">- <?php esc_html_e( get_admin_page_title() );?></div>
                    <?php
                    $text = sprintf( __('V.%s','seo-key'),
                        SEOKEY_VERSION );
                    $text = apply_filters( 'seokey_filter_admin_page_header_text', $text );
                    ?>
                    <p class="seokey-title-heading"><?php echo wp_kses_post( $text );?></p>
                </div>
            </div>
        <?php
	        do_action( 'seokey_action_admin_pages_wrapper_print_notices_after_title' );
        }
    }
}

/**
 * Generate default admin page content
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @see seokey_admin_menus()
 */
function seokey_admin_pages() {
	// Ensure all of our function will trigger our custom action
	do_action( 'seokey_action_admin_pages_wrapper' );
}

add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_content_begin', 10 );
/**
 * Generate default admin page content header
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook seokey_action_admin_page_content
 * @see seokey_action_admin_pages_wrapper()
 * @see get_admin_page_title()
 * @return void (string) HTML content
 */
function seokey_admin_page_content_begin() {
	echo '<section class="wrap seokey-wrapper">';
	do_action( 'seokey_action_admin_pages_wrapper_print_notices' );
}

add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_content_end', 1000 );
/**
 * Generate default admin page content footer
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook seokey_action_admin_page_content
 * @see seokey_action_admin_pages_wrapper()
 * @return void (string) HTML content
 */
function seokey_admin_page_content_end() {
	echo '</section>';
}

//add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_content', 50 );
/**
 * Generate admin pages content
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook seokey_action_admin_pages_wrapper
 * @see seokey_admin_page_content_*()
 * @return void (string) $render Main menu content
 */
function seokey_admin_page_content() {
	// If our Setting API class is ready to be used
	if ( class_exists( 'SeoKeySettingsAPI' ) ) {
		// We may need some settings here, lets check this out
		$settings_api = SeoKeySettingsAPI::get_instance();
		$settings_api->seokey_config_register_setting_sections_and_fields();
		// Current page name
		global $hook_suffix;
		// Get all pages with settings
		$settings_sections = seokey_settings_api_get_config_sections();
		$settings_sections = array_unique( wp_list_pluck( $settings_sections, 'ID' ) ); // settings
		foreach ( $settings_sections as $key => $value ) {
			$settings_sections[$key] = 'seokey_page_seo-key-' . $value;
		}
		// If we have settings for this page, display form
		if ( in_array( $hook_suffix, $settings_sections, true ) ) {
			// Show settings form for this specific page
			$settings_api->seokey_settings_api_forms();
		}
	}
}

add_action( 'admin_init', 'seokey_admin_settings_api_init', 5 );
/**
 * Settings API : launch our setting API
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook admin_init
 */
function seokey_admin_settings_api_init() {
	// Do not load settings API on ajax calls
	if ( defined( 'DOING_AJAX' ) ) {
		return;
	}
	// If our Setting API class is ready to be used
	if ( class_exists( 'SeoKeySettingsAPI' ) ) {
		// Load only one instance of our setting class API
		$settings_api = SeoKeySettingsAPI::get_instance();
		// Define all pages with a setting form
		$settings_api->set_pages( seokey_settings_api_get_config_pages() );
		// Define all sections fields
		$settings_api->set_sections( seokey_settings_api_get_config_sections() );
		// Define all fields
		$settings_api->set_fields( seokey_settings_api_get_config_fields() );
		// Admin_init (register options, setting fields and setting sections)
		$settings_api->seokey_config_register_setting();
	}
}
