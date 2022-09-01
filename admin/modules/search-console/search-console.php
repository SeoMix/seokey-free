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

if ( seokey_helpers_is_free() ) {
	add_filter( 'seokey_filter_get_config_sections', 'seokey_gsc_settings_add_tab', SEOKEY_PHP_INT_MAX );
	function seokey_gsc_settings_add_tab( $tabs ) {
		// Add one group section
		$tabs[] = [
			// Section Unique ID
			'ID'      => 'settings',
			// Name of the section
			'name'    => 'search-console',
			// Tabname. If you want this section in a tabulation navigation, this field must be filled
			'tabname' => __( 'Search Console', 'seo-key' )
		];
		return $tabs;
	}
	
	add_action( 'seokey_action_setting_sections_before', 'seokey_gsc_config_render', SEOKEY_PHP_INT_MAX );
	function seokey_gsc_config_render( $id_section ) {
		if ( $id_section === 'seokey-section-search-console' ) {
			// Are we in the dashboard page ?
			$screen       = seokey_helper_get_current_screen();
			$current_page = $screen->base;
			if ( $current_page === 'seokey_page_seo-key-settings' ) {
				echo '<h2>' . __( 'Automatic connection to Search Console is only available in the PRO version.', 'seo-key') . '</h2>';
				echo '<p>' . __( 'Upgrade now to improve your SEO: get more data, and automatically submit your sitemap!', 'seo-key') . '</p>';
				echo '<p>' . __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.fr/tarifs/'>Buy SEOKEY Premium</a>", 'seo-key' ) . '</p>';
				
			}
		}
	}
	
	class Seokey_SearchConsole {
		
		/**
		 * Render content for metabox
		 * Retoune les données des stats pour la métabox sur les posts, term et auteur
		 *
		 * @since   0.0.1
		 * @author  Leo Fontin
		 *
		 * @param $post
		 *
		 * @return String
		 */
		public function seokey_gsc_metabox_render( $type = 'post', $item = false ) {
			// Get data for this specific content
			$clics    = __( '--- click', 'seo-key' );
			$position    = __( '--- average position', 'seo-key' );
			$keywords    = __( '--- known keywords', 'seo-key' );
			$whattodo   = seokey_audit_whattodo( get_the_ID(), false );
			// Render
			$render = '<section id="seokey-metabox-figures">
				<div>' . $clics . '</div>
				<div>' . $position . '</div>
				<div>' . $keywords . '</div>
				<div class="has-explanation">
					' . __( "Suggestion: ", "seo-key" ) . '<span class="seokey-whattodo-text ' . $whattodo["id"] . '">' .$whattodo['worktodo'] .
				          seokey_helper_help_messages( $whattodo["id"], true ).'</span>
				</div>
			</section>';
			echo $render;
			return false;
		}
	}
}