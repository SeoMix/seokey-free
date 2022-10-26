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

add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_automatic_seo', 50 );
/**
 * Generate admin support content
 *
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see seokey_admin_page_content_*
 *
 * @hook seokey_action_admin_pages_wrapper, 50
 * @return void (string) $render Main menu content
 */
function seokey_admin_page_automatic_seo() {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the right page ?
	if ( $current_page === 'seokey_page_seo-key-automatic-seo' ) {
		$current_wizard = get_option( 'seokey_option_first_wizard_seokey_notice_wizard' );
		if ( 'goodtogo' === $current_wizard ) {
			echo '<div class="seokey-wrapper-limit">';
				echo '<section>';
					echo wp_kses_post( seokey_admin_page_automatic_seo_content() );
				echo '</section>';
				echo '<section>';
					echo wp_kses_post( seokey_admin_page_automatic_seo_manual_optimizations() );
				echo '</section>';
			echo '<div>';
		}
	}
}

/**
 * Get automatic SEO optimizations
 *
 * @author  Daniel Roch
 * @since   0.0.1
 * @return string
 */
function seokey_admin_page_automatic_seo_content(){
	$render = '<p>' . __( "SEOKEY automatically fixes many technical issues on your WordPress website. Here's what we do:", "seo-key" ) . '</p>';
	foreach ( seokey_automatic_optimizations_list() as $version ) {
		foreach ( $version as $name => $description ) {
			$render .= '<li class="has-explanation" id="' . sanitize_title( $name ) . '"><strong>' . key( $description ) . '</strong> ' . $description[key( $description )] . seokey_helper_help_messages( 'automaticseo-' . sanitize_title( $name ), true ) .'</li>';
		}
	}
	return '<ul id="optimizations-list">' . $render . '</ul>';
}

/**
 * Get manual SEO optimizations
 *
 * @author  Daniel Roch
 * @since   0.0.1
 * @return string
 */
function seokey_admin_page_automatic_seo_manual_optimizations() {
	$render = '<p>' . __( "SEOKEY can also automatically optimize other elements according to your settings:", "seo-key" ) . '</p>';
	$uri    = sanitize_title( __( 'SEO optimizations', 'seo-key' ) );
	$render .= '<a href="' . esc_url( admin_url( 'admin.php?page=seo-key-settings#'. $uri ) ) . '" class="button button-primary button-hero">' . esc_html__( "Manual SEO optimizations", "seo-key" ) . '</a>';
	return $render;
}