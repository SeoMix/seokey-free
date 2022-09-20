<?php
/**
 * Load administration assets for our plugin
 *
 * @Loaded on plugins_loaded + capability contributor
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

$status = get_option( 'seokey_option_first_wizard_seokey_notice_wizard' );
// Load admin only assets
if ( is_admin() ) {
 	// Wizard has ended, add all other admin assets !
	if ( 'goodtogo' === $status ) {

        add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_ALT_editor' );
        /**
         * Enqueue assets (CSS) for ALT editor
         *
         * @author  Daniel Roch
         * @since   0.0.1
         *
         * @uses    wp_enqueue_style()
         * @hook    admin_enqueue_scripts
         */
        function seokey_enqueue_admin_ALT_editor() {
            if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
                // Enqueue CSS
                wp_enqueue_style('seokey-common', esc_url(SEOKEY_URL_ASSETS . 'css/seokey-common.css'), false, SEOKEY_VERSION);
                wp_enqueue_style('seokey-alt-editor', esc_url(SEOKEY_URL_ASSETS . 'css/seokey-alt-editor.css'), FALSE, SEOKEY_VERSION);
            }
        }

        add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_main_page' );
        /**
         * Enqueue assets (CSS) for main admin menu
         *
         * @author  Daniel Roch
         * @since   0.0.1
         *
         * @uses    wp_enqueue_style()
         * @hook    admin_enqueue_scripts
         */
        function seokey_enqueue_admin_main_page() {
            // CSS for setting pages
            $current_screen = seokey_helper_get_current_screen();
            if ( $current_screen->base === 'toplevel_page_seo-key' ) {
                // Enqueue settings CSS
                wp_enqueue_script('seokey-score',       esc_url( SEOKEY_URL_ASSETS . 'js/seokey-score.js' ), array('jquery'), SEOKEY_VERSION );
            }
        }

		add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_common_assets', 1 );
		/**
		 * Enqueue common assets (CSS) foradmin pages
		 *
		 * @author  Daniel Roch
		 * @since   0.0.1
		 *
		 * @uses    wp_enqueue_style()
		 * @hook    admin_enqueue_scripts
		 */
		function seokey_enqueue_admin_common_assets() {
			// CSS for setting pages
			$current_screen = seokey_helper_get_current_screen();
			if ( str_starts_with( $current_screen->base, 'seokey_page_seo-key-' ) ) {
				// Enqueue settings CSS
				wp_enqueue_style( 'seokey-common', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common.css' ), false, SEOKEY_VERSION );
				if ( seokey_helpers_is_free() ) {
					wp_enqueue_style( 'seokey-common-free', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common-free.css' ), false, SEOKEY_VERSION );
				}
				// Enqueue settings JS
                wp_enqueue_script('seokey-common-js',       esc_url( SEOKEY_URL_ASSETS . 'js/seokey-common.js' ), array('jquery'), SEOKEY_VERSION );
            }
		}

        add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_settings_page', 15 );
        /**
         * Enqueue assets (CSS) for settings menu
         *
         * @author  Daniel Roch
         * @since   0.0.1
         *
         * @uses    wp_enqueue_style()
         * @hook    admin_enqueue_scripts
         */
        function seokey_enqueue_admin_settings_page() {
            // CSS for setting pages
            $current_screen = seokey_helper_get_current_screen();
            if ( $current_screen->base === 'seokey_page_seo-key-settings' ) {
                // Enqueue settings CSS
                wp_enqueue_style('seokey-style-settings',   esc_url( SEOKEY_URL_ASSETS . 'css/seokey-settings.css' ), FALSE, SEOKEY_VERSION );
                wp_enqueue_style('seokey-automatic',        esc_url( SEOKEY_URL_ASSETS . 'css/seokey-automatic-optimizations.css' ), false, SEOKEY_VERSION);
                wp_enqueue_script('seokey-automatic',       esc_url( SEOKEY_URL_ASSETS . 'js/settings-automatic-optimizations.js' ), array('jquery'), SEOKEY_VERSION );
            }
	        if ( $current_screen->base === 'toplevel_page_seo-key' ) {
		        wp_enqueue_style('seokey-common',    esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common.css' ), false, SEOKEY_VERSION);
		        wp_enqueue_style('seokey-dashboard', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-dashboard.css' ), array('seokey-common'), SEOKEY_VERSION);
	        }
	        if ( $current_screen->base === 'seokey_page_seo-key-wizard' ) {
		        wp_localize_script( 'seokey-admin-settings', 'seokey_gsc',
			        [
				        'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
				        'security'                    => wp_create_nonce( 'seokey_gsc_sec' ),
			        ]
		        );
			}
            wp_enqueue_style('seokey-icons',            esc_url( SEOKEY_URL_ASSETS . 'css/seokey-icons.css' ), false, SEOKEY_VERSION);
        }

		add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_metabox_metas' );
		/**
		 * Enqueue assets (CSS) for metaboxes
		 *
		 * @author  Daniel Roch
		 * @since   0.0.1
		 *
		 * @hook    admin_enqueue_scripts
		 * @uses    wp_enqueue_style()
		 */
		function seokey_enqueue_admin_metabox_metas() {
			// Do we need a metabox
			$goforit = seokey_helper_cache_data('SEOKEY_METABOX' );
			// Edit terms checks
			if ( $goforit === NULL ) {
				$current_screen = seokey_helper_get_current_screen();
				if ( $current_screen->base === 'edit-tags' || $current_screen->base === 'term' ) {
					$goforit = true;
				}
			}
			// Extra check for custom admin post type archive menu
			if ( $goforit === null ) {
				$goforit = seokey_helpers_admin_is_post_type_archive();
				if ( $goforit === false ) {
					return;
				}
			}
			if ( seokey_helpers_is_free() ) {
				wp_enqueue_style( 'seokey-common-free', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common-free.css' ), false, SEOKEY_VERSION );
			}
			// We need a metabox, let's continue
			// JS for admin pages with our metaboxes
			wp_enqueue_script( 'seokey-js-metabox', SEOKEY_URL_ASSETS . 'js/seokey-metabox.js', array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION, TRUE );
			wp_localize_script( 'seokey-js-metabox', 'seokey_metas',
				[
					'ajaxurl'                               => admin_url( 'admin-ajax.php' ),
					'security'                              => wp_create_nonce( 'seokey_audit_content_metabox' ),
					'metatitle_counter_min'                 => METATITLE_COUNTER_MIN,
					'metatitle_counter_max'                 => METATITLE_COUNTER_MAX,
					'metadesc_counter_min'                  => METADESC_COUNTER_MIN,
					'metadesc_counter_max'                  => METADESC_COUNTER_MAX,
					'meta_counter_min_text'                 => esc_html__( 'Not enough text: you have %s missing characters', 'seo-key' ),
					'meta_counter_min_text_single'          => esc_html__( 'Not enough text: you have %s missing character', 'seo-key' ),
					'meta_counter_remaining_text'           => esc_html__( 'Good text: %s remaining characters', 'seo-key' ),
					'meta_counter_remaining_text_single'    => esc_html__( 'Good text: %s remaining character', 'seo-key' ),
					'meta_counter_max_text'                 => esc_html__( 'Too much text: you have %s extra characters', 'seo-key' ),
					'meta_counter_max_text_single'          => esc_html__( 'Too much text: you have %s extra character', 'seo-key' ),
				]
			);
			// and corresponding CSS
			wp_enqueue_style('seokey-common',    esc_url( SEOKEY_URL_ASSETS . 'css/seokey-common.css' ), false, SEOKEY_VERSION);
            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'seokey-metabox',  esc_url( SEOKEY_URL_ASSETS . 'css/seokey-metabox.css' ), ('dashicons'), SEOKEY_VERSION );
		}

		add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_options_discussion' );
		/**
		 * Enqueue assets (CSS) for discussion Menu
		 *
		 * @return void
		 * @since   0.0.1
		 *
		 * @hook    admin_enqueue_scripts
		 * @uses    wp_enqueue_style()
		 * @global $pagenow
		 * @author  Daniel Roch
		 */
		function seokey_enqueue_admin_options_discussion() {
			global $pagenow;
			// add JS on the default discussion admin page
			if ( "options-discussion.php" === $pagenow ) {
				$disabled = seokey_helper_get_option( 'seooptimizations-pagination-comments', 'yes' );
				if ( 'yes' === $disabled || (string) 1 === $disabled ) {
					wp_enqueue_script( 'seokey-js-settings-discussion', SEOKEY_URL_ASSETS . 'js/seokey-settings-discussion.js', array(
						'jquery',
						'wp-i18n'
					), SEOKEY_VERSION, true );
					wp_set_script_translations( 'seokey-js-settings-discussion', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
				}
			}
		}

		add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_options_reading' );
		/**
		 * Enqueue assets (CSS) for Reading Menu
		 *
		 * @return void
		 * @since   0.0.1
		 *
		 * @hook    admin_enqueue_scripts
		 * @uses    wp_enqueue_style()
		 * @global $pagenow
		 * @author  Daniel Roch
		 */
		function seokey_enqueue_admin_options_reading() {
			global $pagenow;
			// add JS on the default reading admin page
			if ( 'options-reading.php' === $pagenow ) {
				wp_enqueue_script( 'seokey-js-settings-reading', SEOKEY_URL_ASSETS . 'js/seokey-settings-reading.js', array(
					'jquery',
					'wp-i18n'
				), SEOKEY_VERSION, TRUE );
				wp_set_script_translations( 'seokey-js-settings-reading', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
			}
		}

		add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_options_permalink' );
		/**
		 * Enqueue assets (CSS) for Permalink Menu
		 *
		 * @return void
		 * @since   0.0.1
		 *
		 * @hook    admin_enqueue_scripts
		 * @uses    wp_enqueue_style()
		 * @global $pagenow
		 * @author  Daniel Roch
		 */
		function seokey_enqueue_admin_options_permalink() {
			global $pagenow;
			// add JS on the default reading admin page
			if ( 'options-permalink.php' === $pagenow ) {
				if ( seokey_helper_get_option( 'metas-category_base' ) ) {
					wp_enqueue_script( 'seokey-js-settings-permalink', SEOKEY_URL_ASSETS . 'js/seokey-settings-permalink.js', array(
						'jquery',
						'wp-i18n'
					), SEOKEY_VERSION, true );
				}
			}
		}
	}
// End admin only assets
}

// Wizard finished, add these assets
if ( 'goodtogo' === $status ) {
	add_action( 'print_media_templates',        'seokey_enqueue_admin_editor' );
	add_action( 'enqueue_block_editor_assets',  'seokey_enqueue_admin_editor' );
	/**
	 * Enqueue assets for Gutenberg
	 *
	 * @return void
	 * @author  Daniel Roch
	 *
	 * @hook    admin_enqueue_scripts
	 * @uses    wp_enqueue_style()
	 * @global $pagenow
	 * @since   0.0.1
	 */
	function seokey_enqueue_admin_editor() {
		wp_enqueue_style( 'seokey-editor', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-editor.css' ), FALSE, SEOKEY_VERSION );
	}
}

add_action( 'wp_enqueue_scripts',       'seokey_enqueue_scripts_logged_in_users', 10 );
add_action( 'admin_enqueue_scripts',    'seokey_enqueue_scripts_logged_in_users', 10 );
/**
 * Enqueue assets for connected users on front office and admin pages (CSS): may be useful for admin bar content
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook    admin_enqueue_scripts
 * @hook    wp_enqueue_scripts
 * @uses    wp_register_style()
 * @uses    wp_enqueue_style()
 * @uses    seokey_helper_user_get_capability()
 * @uses    current_user_can()
 */
function seokey_enqueue_scripts_logged_in_users() {
	// Check user role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'contributor' ) ) ) {
		return;
	}
	// Enqueue public plugin CSS
	wp_enqueue_style( 'seokey-admin-public', esc_url( SEOKEY_URL_ASSETS_PUBLIC ) . 'css/seokey-admin-public.css', false, SEOKEY_VERSION );
}