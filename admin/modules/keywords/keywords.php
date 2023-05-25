<?php
/**
 * Admin Keyword module
 *
 * @Loaded on plugins_loaded + capability editor
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

add_filter( 'seokey_filter_get_admin_menus', 'seokey_keywords_add_menu_page', 1 );
/**
 * Add Keyword menu
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_keywords_add_menu_page( $menus ) {
	$menus[20] = [
		'title'      => esc_html__( 'My Keywords', 'seo-key' ),
		'slug'       => 'seo-key-keywords',
		'capability' => seokey_helper_user_get_capability( 'editor' ),
	];
	return $menus;
}


add_action('seokey_action_admin_pages_wrapper_print_notices_after_title', 'seokey_keywords_add_explanation', 1);
/**
 * Add explanations for the keyword menu
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_keywords_add_explanation( $args ) {
	$current_screen = seokey_helper_get_current_screen();
	if ( $current_screen->base === 'seokey_page_seo-key-keywords' ) {
		// TODO SI pas de data
		$last_update = get_option('seokey-gsc-last-api-call');
		$text = '<div class="flexbox">';
			$text .= '<div class="notice-flexboxcolumn">';
				$text .= '<h3>' . __( 'What should I do here?', 'seo-key' ) . '</h3>';
				$text .= '<p>' . __( 'This menu show you every keyword or phrase you have targeted.', 'seo-key' ) . '</p>';
				$text .= '<p>' . __( 'This will help you see your results (average position, clicks, impressions), but most importantly, <strong>it allows us to tell you what your next action is for each content</strong>.', 'seo-key' ) . '</p>';
			$text .= '</div>';
			$text .= '<div class="notice-flexboxcolumn">';
				$text .= '<h3>' . __( 'How should I choose a keyword?', 'seo-key' ) . '</h3>';
				$text .= '<p>' . __( 'The choice of keywords is crucial. Here are some tips:', 'seo-key' ) . '</p>';
				$text .= '<ul>';
					$text .= '<li>' . __( '<strong>Choose a keyword that makes sense</strong> (e.g. "Beginner cooking course" rather than "beginner" or just "course")', 'seo-key' ) . '</li>';
					$text .= '<li>' . __( "<strong>Related content must meet the user's needs</strong>. Ask yourself the following question: what is the user looking for when he search this phrase into Google?", 'seo-key' ) . '</li>';
				$text .= '</ul>';
		$text .= '<p>' . __( 'To add a targeted keyword or phrase, just use our metabox while editing your contents', 'seo-key' ) . '</p>';
			$text .= '</div>';
		$text .= '<div class="notice-flexboxcolumn">';
		$text .= '<h3>' . __( 'Where does the data comes from?', 'seo-key' ) . '</h3>';
			$text .= '<p>' . __( 'Positions, clicks and impressions comes from your Search Console.', 'seo-key' ) . '</p>';
			$text .= '<p>' . __( 'Keep in mind that all data can\'t be shown into your WordPress if a have a lot of contents, and it may not be 100% accurate', 'seo-key' ) . '</p>';
			if ( false !== $last_update ) {
				$text .= '<p>' . sprintf( __( 'Actual data range: from %1$s to %2$s (<strong>last 3 months</strong>).', 'seo-key' ), date_i18n( $last_update['startDate'] ), date_i18n( $last_update['endDate'] ) );
			}
		$text .= '</div>';
		$text .= '</div>';
		echo '<div id="seokey_keyword_notification" class="seokey-notice notice-info">
			<span class="notice-icon"></span>
			<span class="notice-content">
				' . $text . '
			</span>
		</div>';
	}
}

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_keywords_page' );
/**
 * Enqueue assets (CSS) for Option Reading Menu
 *
 * @author  Daniel Roch
 *
 * @uses    wp_enqueue_style()
 *
 * @hook    admin_enqueue_scripts
 *
 * @since   0.0.1
 */
function seokey_enqueue_admin_keywords_page() {
	// CSS for setting pages
	$current_screen = seokey_helper_get_current_screen();
	if ( $current_screen->base === 'seokey_page_seo-key-keywords' ) {
		// Enqueue CSS
		seokey_enqueue_admin_common_scripts();
		wp_enqueue_style('seokey-keywords', esc_url(SEOKEY_URL_ASSETS . 'css/seokey-keywords.css'), false, SEOKEY_VERSION );
		// Enqueue JS
		wp_enqueue_script('seokey-keywords-tables', SEOKEY_URL_ASSETS . 'js/seokey-keywords.js', array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION);
		// Tell WP to load translations for our JS.
		wp_set_script_translations( 'seokey-keywords-tables', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
		// Localize script arguments
		$args = array(
			// Ajax URL
			'ajaxurl' => admin_url('admin-ajax.php'),
			// PHP function to display keyword List
			'display_action_url' => '_seokey_keywords_display_table',
			// Security nonce
			'security' => wp_create_nonce('seokey_keywords_table_list'),
		);
		wp_localize_script('seokey-keywords-tables', 'adminAjax', $args);
	}
}

/**
 * Get all keywords form BDD or from keywords id list
 *
 * @since   0.0.1
 * @author  Leo Fontin, Daniel Roch
 */
function seokey_gsc_get_keywords( $ids = '' ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'seokey_gsc_keywords';
	// We have an array list? Return data only for them
	if ( is_array( $ids )  && !empty ( $ids ) ) {
		$ids = implode( ',', array_map( 'intval', $ids ) );
		$sql = 'SELECT * FROM ' . $table_name . ' WHERE id IN (' . $ids . ') ORDER BY impressions DESC';
	}
	// Return all keyword data
	else {
		$sql = 'SELECT * FROM ' . $table_name . ' ORDER BY impressions DESC';
	}
	$result     = $wpdb->get_results( $sql );
	$keywords = [];
	if ( ! empty( $result ) ) {
		foreach ( $result as $keyword ) {
			$keywords[ $keyword->id ] = $keyword;
		}
	}
	return $keywords;
}

/**
 * Get top keyword from Search Console data (keyword with best clicks and another one with best impressions)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_gsc_get_top_keyword( $page ) {
	// TODO later : transient
	$topkeyword = [];
	if ( 'none' !== $page['keywords'] && !empty( unserialize( $page['keywords'] ) ) ) {
		$keywords_all   = seokey_gsc_get_keywords( unserialize( $page['keywords'] ) );
		// Get best keyword for this post (with clic data)
		$clics          = wp_list_pluck( $keywords_all, 'clicks' );
		arsort( $clics );
		$cliccount      = $clics;
		$clics          = array_key_first( $clics );
		$cliccount      = reset($cliccount);
		$topkeyword[]   = ( (int) $cliccount > 0 ) ? array(
			'keyword'   => get_object_vars( $keywords_all[ $clics ] )['keyword'],
			'type'      => 'clics',
			'count'     => $cliccount,
		) : false;
		// Get best keyword for this post (with impression data when no clic has been registered)
		$impressions        = wp_list_pluck( $keywords_all, 'impressions' );
		arsort( $impressions );
		$impressionscount   = $impressions;
		$impressions        = array_key_first( $impressions );
		$impressionscount   = reset($impressionscount);
		$topkeyword[] = ( empty ( $clics ) ) ? '' :
			array(
				'keyword'   => get_object_vars( $keywords_all[ $impressions ] )['keyword'],
				'type'      => 'impressions',
				'count'     => $impressionscount,
			);

	}
	if ( !empty( $topkeyword ) ) {
		// Send data to our cache system
		seokey_helper_cache_data( 'seokey_metabox_suggested_keyword', $topkeyword );
	}
	return $topkeyword;
}

/**
 * Get pages from bdd
 * Méthode récupérant toutes les données concernants les pages en BDD
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_gsc_get_pages() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'seokey_gsc_pages';
	$sql        = 'SELECT * FROM ' . $table_name;
	$result     = $wpdb->get_results( $sql );
	$pages = [];
	if ( ! empty( $result ) ) {
		// Ordone les pages en fonctio nde leur ID //
		foreach ( $result as $page ) {
			$pages[ $page->id ] = $page;

			// Relie les ID des mots clés associés à la page //
			if ( ! empty( $page->keywords ) ) {
				$page->keywords = unserialize( $page->keywords );
			}
		}
	}
	return $pages;
}

// Include Keywords WP_LIST_TABLE class
include dirname( __FILE__ ) . '/keyword-table.php';

// Include menu content
include dirname( __FILE__ ) . '/view.php';