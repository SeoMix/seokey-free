<?php
/**
 * Sitemaps generator
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

add_action( 'seokey_loaded', 'seokey_sitemap_creation_launch', 1 );
// TODO Comments
function seokey_sitemap_creation_launch() {
    // Wizard not finished yet
    if ( 'goodtogo' !== get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
        return;
    }
    // Sitemap creation running, do not reload it
    if ( 'done' == get_option( 'seokey_sitemap_creation' ) ) {
        return;
    }
    // Launch a sitemap creation
    $background_process = SeoKey_Class_Background_Sitemap_Trigger::get_instance();
    $background_process->run_sitemap_creation_all();
}

/**
 * Get or update LastMod data for each content
 */
require_once( dirname( __file__ ) . '/sitemaps-lastmod.php' );
$lastmod = new Seokey_Sitemap_Lastmod();
$lastmod->watch_lastmod();
/**
 * Create sitemaps
 *
 * @since 0.0.1
 * @author  Léo Fontin
 */
require_once( dirname( __file__ ) . '/sitemaps-render.php' );
new Seokey_Sitemap_Render();

// Load sitemaps background classes
require_once( dirname( __file__ ) . '/class-sitemaps-background.php');

// Add sitemaps to settings
add_action( 'seokey_action_setting_sections_before', 'seokey_settings_add_base_sections_sitemaps_data', 25, 1 );
/**
 * Add link to sitemaps
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook   seokey_action_setting_table_after
 **/
function seokey_settings_add_base_sections_sitemaps_data( $id ) {
	if ( "seokey-section-tools" === $id ) {
		echo '<p class="setting-description has-explanation">';
		esc_html_e( 'Sitemaps XML files', 'seo-key' );
		echo seokey_helper_help_messages( 'sitemaps_data_explanation');
		echo '</p>';
		echo '<p>' . esc_html__( 'SEOKEY creates sitemap files to tell search engines about all of your content.', 'seo-key' ) . '</p>';
		echo '<p>' . esc_html__( 'We create real files (they are faster to load for Google).', 'seo-key' ) . '</p>';
		$sitemap = get_option( 'seokey_sitemap_creation' );
		// Sitemaps created
		if ( 'done' === $sitemap ) {
            foreach (seokey_helper_cache_data('languages')['lang'] as $lang => $v) {
                $sitemaps_url = '<a target="_blank" href="' . seokey_helpers_get_sitemap_base_url( $lang, true ).'sitemap-index-'.$lang.'.xml' . '">' . esc_html__('View sitemaps files', 'seo-key').'</a>';
                echo '<p>' . esc_html__('You can see your sitemaps files here: ', 'seo-key') . $sitemaps_url .' ( '.$v['name'].' )</p>';
            }
		}
		// Wizard variant
		elseif ( false === $sitemap ) {
			echo '<p>' . esc_html__( 'Sitemap files will be created after wizard.', 'seo-key' ) . '</p>';
		}
		// Sitemaps not yet created
		else {
			echo '<p>' . esc_html__( 'Sitemap files are currently being created. Please wait.', 'seo-key' ) . '</p>';
		}
	}
}