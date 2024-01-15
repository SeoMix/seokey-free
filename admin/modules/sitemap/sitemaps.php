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

add_action('update_option_WPLANG', 'seokey_sitemap_generate_sitemap_switch_language_settings', 10, 2);
/**
 * Replace sitemap language when site language is changed in WordPress settings
 *
 * @since  1.8.0
 * @author Arthur Leveque
 *
 * @hook   update_option_WPLANG
 **/
function seokey_sitemap_generate_sitemap_switch_language_settings($old_value, $new_value) {
	// Delete current sitemap to prevent it to be still active in the database and files
	require_once( dirname( __file__ ) . '/sitemaps-delete.php' );
	$sitemap_delete = new Seokey_Sitemap_Delete();
	$sitemap_delete->seokey_sitemap_delete_files();
	// Create the new sitemap
	new Seokey_Sitemap_Lastmod();
    // Inform user that the sitemap creation is running
	update_option( 'seokey_sitemap_creation', 'running', true );
}

add_action( 'template_redirect', 'seokey_redirect_404_sitemaps' );
/**
 * Redirect on sitemaps index if the sitemap we are currently heading to is disabled or deleted
 *
 * @since  1.8.1
 * @author Arthur Leveque
 *
 * @hook   template_redirect
 **/
function seokey_redirect_404_sitemaps() {
	// If we are on a 404 page
	if( is_404() ) {
		// If we are on a sitemap page continue, else ignore
		if ( seokey_helpers_is_sitemaps_page() ) {
			// If we have a multilingual site, check for language of sitemap, else just redirect to sitemaps index
			if ( isset( seokey_helper_cache_data('languages')['lang'] ) ) {
				// Get language from URL by getting the last 7 letters (ex : FRA.xml) and getting only the language code
				$language = substr( esc_url( seokey_helper_url_get_current() ), -7, 3);
				// Redirect to the sitemaps index of the language we have in the URL
				wp_redirect( rtrim( get_home_url(), '/' ) . seokey_helpers_get_sitemap_base_url() . 'sitemap-index-' . $language . '.xml', 301 );
			} else {
				wp_redirect( rtrim( get_home_url(), '/' ) . seokey_helpers_get_sitemap_base_url(), 301 );
			}
			exit; // Always exit after wp_redirect()
		}
	}
}