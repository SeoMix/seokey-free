<?php
/**
 * Load SEOKEY audit page funcitons
 *
 * @Loaded on 'init' & is_admin()
 *
 * @see seokey_settings_api_get_config_sections()
 * @see seokey_settings_api_get_config_fields()
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

add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_audit', 50 );
/**
 * Generate admin dashboard content
 *
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see seokey_admin_page_content_*()
 *
 * @hook seokey_action_admin_pages_wrapper, 50
 * @return void (string) $render Main menu content
 */
function seokey_admin_page_audit() {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the dashboard page ?
	if ( $current_page === 'seokey_page_seo-key-audit' ) {
		// Display content when wizard has been finished
		$current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
		if ( 'goodtogo' === $current_wizard ) {
	        // Display page
            echo '<div class="seokey-wrapper-limit">';
			    include SEOKEY_PATH_ADMIN . 'modules/audit/parts/view.php';
            echo '</div>';
		}
	}
}

add_filter( 'admin_body_class', 'seokey_admin_page_audit_body_class' );
function seokey_admin_page_audit_body_class( $classes ) {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the dashboard page ?
	if ( $current_page === 'seokey_page_seo-key-audit' ) {
		// Display content when wizard has been finished
		$current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
		if ( 'goodtogo' === $current_wizard ) {
			// Display page
			$classes .= ' seokey-wizard-done';
		}
	}
	return $classes;
}