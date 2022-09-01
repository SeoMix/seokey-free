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


add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_support', 50 );
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
function seokey_admin_page_support() {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the right page ?
	if ( $current_page === 'seokey_page_seo-key-support' ) {
		if ( true === seokey_helpers_is_free() ) {
			echo '<div class="seokey-wrapper-limit">';
				echo '<h2>' . __( "Go Premium!", "seo-key" ) . '</h2>';
				echo '<p>' . __( "We give you all the keys to succeed:", "seo-key" ) . '</p>';
				echo '<ul>';
					echo '<li>' . __( "A full audit module", "seo-key" ) . '</li>';
					echo '<li>' . __( "Individual advice for each content: what should I do next?", "seo-key" ) . '</li>';
					echo '<li>' . __( "Easily connect your Search Console and get more SEO data", "seo-key" ) . '</li>';
					echo '<li>' . __( "See and fix Google 404 and WordPress automatic redirections", "seo-key" ) . '</li>';
				echo '</ul>';
				echo '<p>' . __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY Premium</a>", 'seo-key' ) . '</p>';
				echo '<h2>' . __( "More info or help?", "seo-key" ) . '</h2>';
				echo '<p>' . __( "Need help with SEOKEY free? Contact us here: <a href='https://wordpress.org/support/plugin/seo-key/'>SEOKEY Free support page</a>.", 'seo-key' ) . '</p>';
				echo '<p>' . __( "Please also check <a href='https://www.seo-key.com/faqs/'>our FAQ</a>.", 'seo-key' ) . '</p>';
				echo '<p>' . __( "You can also read <a href='https://trello.com/b/jauwlc3J/seokey-pro-public-roadmap'>public roadmap</a>. We may already be working on the functionality you need.", 'seo-key' ) . '</p>';
			echo '<div>';
		} else {
			echo '<div class="seokey-wrapper-limit">';
				echo '<p>' . __( "Need help with SEOKEY? Send us an email at <a href='mailto:support@seo-key.com'>support@seo-key.com</a>.", 'seo-key' ) . '</p>';
				echo '<p>' . __( "Please also check <a href='https://www.seo-key.com/faqs/'>our FAQ</a>.", 'seo-key' ) . '</p>';
				echo '<p>' . __( "You can also read <a href='https://trello.com/b/jauwlc3J/seokey-pro-public-roadmap'>public roadmap</a>. We may already be working on the functionality you need.", 'seo-key' ) . '</p>';
			echo '<div>';
		}
	}
}