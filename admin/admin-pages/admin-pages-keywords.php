<?php
/**
 * Load SEOKEY Admin pages functions
 *
 * @Loaded  on 'init'
 * @Loaded  on is_admin() condition
 * @Loaded  with plugin configuration file + admin-menus-and-links.php
 *
 * - Trigger SEOKEY admin page "contents"
 *
 * @see     seokey_settings_api_get_config_sections()
 * @see     seokey_settings_api_get_config_fields()
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


add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_keywords', 50 );
/**
 * Generate admin support content
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
function seokey_admin_page_keywords() {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the right page ?
	if ( $current_page === 'seokey_page_seo-key-keywords' ) {
		// Include helpful functions
		include SEOKEY_PATH_ADMIN . 'modules/keywords/view-helpers.php';
		// Show time!
		echo '<div class="seokey-wrapper-loading">' . seokey_helper_loader_get() . '</div>';
		echo '<div class="seokey-wrapper-limit">';
			include SEOKEY_PATH_ADMIN . 'modules/keywords/view.php';
		echo '</div>';
	}
}