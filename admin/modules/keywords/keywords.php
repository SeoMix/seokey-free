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

		$type = ( ! empty( $_GET['tab']  ) ) ? sanitize_title( $_GET['tab'] ) : 'contents';
		if ( 'keywords' === $type ) {
			$text = '<div class="flexbox">';
				$text   .= '<div class="notice-flexboxcolumn">';
					$text   .= '<h3>' . __( 'What should I do here?', 'seo-key' ) . '</h3>';
					$text   .= '<p>' . __( 'This menu show you every keyword or phrase you have targeted.', 'seo-key' ) . '</p>';
					$text   .= '<p>' . __( 'This will help you see your results (average position, clicks, impressions), but most importantly, <strong>it allows us to tell you what your next action is for each content</strong>.', 'seo-key' ) . '</p>';
				$text   .= '</div>';
				$text   .= '<div class="notice-flexboxcolumn">';
					$text   .= '<h3>' . __( 'How should I choose a keyword?', 'seo-key' ) . '</h3>';
					$text   .= '<p>' . __( 'The choice of keywords is crucial. Here are some tips:', 'seo-key' ) . '</p>';
					$text   .= '<ul>';
					$text   .= '<li>' . __( '<strong>Choose a keyword that makes sense</strong> (e.g. "Beginner cooking course" rather than "beginner" or just "course")', 'seo-key' ) . '</li>';
					$text   .= '<li>' . __( "<strong>Related content must meet the user's needs</strong>. Ask yourself the following question: what is the user looking for when he search this phrase into Google?", 'seo-key' ) . '</li>';
					$text   .= '</ul>';
					$text   .= '<p>' . __( 'To add a targeted keyword or phrase, just use our metabox while editing your contents', 'seo-key' ) . '</p>';
				$text   .= '</div>';
				$text   .= '<div class="notice-flexboxcolumn">';
					$text   .= '<h3>' . __( 'Where does the data comes from?', 'seo-key' ) . '</h3>';
					$text   .= '<p>' . __( 'Positions, clicks and impressions comes from your Search Console.', 'seo-key' ) . '</p>';
					$text   .= '<p>' . __( "Keep in mind that all data can't be shown into your WordPress if a have a lot of contents, and it may not be 100% accurate (Google doesn't give access to all your data)", 'seo-key' ) . '</p>';
				$text   .= '</div>';
			$text .= '</div>';
		} elseif ( 'contents' === $type ) {
			$text = '<div class="flexbox">';
			$text   .= '<div class="notice-flexboxcolumn">';
			$text   .= '<h3>' . __( 'What should I do here?', 'seo-key' ) . '</h3>';
			$text   .= '<p>' . __( 'This menu displays all the contents of your website.', 'seo-key' ) . '</p>';
			$text   .= '<p>' . __( 'This lets you see your results (clicks and impressions), but especially <strong>the content you need to work on</strong>.', 'seo-key' ) . '</p>';
			$text   .= '</div>';
			$text   .= '<div class="notice-flexboxcolumn">';
			$text   .= '<h3>' . __( 'What content should I improve?', 'seo-key' ) . '</h3>';
			$text   .= '<p>' . __( 'Keeping in mind the advice given for each content, you can:', 'seo-key' ) . '</p>';
			$text   .= '<ul>';
			$text   .= '<li>' . __( "<strong>work on content that doesn't get any clicks</strong> (Google and web users don't find it relevant)", 'seo-key' ) . '</li>';
			$text   .= '<li>' . __( "<strong>add a keyword or target phrase for content that doesn't already have one</strong>. This will help us give you the right advice", 'seo-key' ) . '</li>';
			$text   .= '<li>' . __( "<strong>optimize your content for the targeted keyword</strong> (if your average position is not yet perfect)", 'seo-key' ) . '.</li>';
			$text   .= '</ul>';
			$text   .= '</div>';
			$text   .= '<div class="notice-flexboxcolumn">';
			$text   .= '<h3>' . __( 'Where does the data comes from?', 'seo-key' ) . '</h3>';
			$text   .= '<p>' . __( 'Positions, clicks and impressions comes from your Search Console.', 'seo-key' ) . '</p>';
			$text   .= '<p>' . __( "Keep in mind that all data can't be shown into your WordPress if a have a lot of contents, and it may not be 100% accurate (Google doesn't give access to all your data)", 'seo-key' ) . '.</p>';
			$text   .= '</div>';
			$text .= '</div>';
		}
		if ( 'contents' === $type || 'keywords' === $type ) {
			echo '<div id="seokey_keyword_notification" class="seokey-notice notice-info">
				<span class="notice-icon"></span>
				<span class="notice-content">' . $text . '</span>
			</div>';
		}
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
		if ( isset( $_GET['tab'] ) && "keywords" === $_GET['tab'] ) {
			// Enqueue JS
			wp_enqueue_script( 'seokey-keywords-tables', SEOKEY_URL_ASSETS . 'js/seokey-keywords.js', array(
				'jquery',
				'wp-i18n'
			), SEOKEY_VERSION );
			// Tell WP to load translations for our JS.
			wp_set_script_translations( 'seokey-keywords-tables', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
			// Localize script arguments
			$args = array(
				// Ajax URL
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				// PHP function to display keyword List
				'display_action_url' => '_seokey_keywords_display_table',
				// Security nonce
				'security' => wp_create_nonce( 'seokey_keywords_table_list' ),
			);
			wp_localize_script( 'seokey-keywords-tables', 'adminAjax', $args );
		} elseif ( empty( $_GET['tab'] ) || "contents" === $_GET['tab'] ) {
			// Enqueue JS
			wp_enqueue_script( 'seokey-keywords-tables-content', SEOKEY_URL_ASSETS . 'js/seokey-keywords-content.js', array(
				'jquery',
				'wp-i18n'
			), SEOKEY_VERSION );
			// Tell WP to load translations for our JS.
			wp_set_script_translations( 'seokey-keywords-tables-content', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
			// Localize script arguments
			$args = array(
				// Ajax URL
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				// PHP function to display keyword List
				'display_action_url' => '_seokey_keywords_display_table_content',
				// Security nonce
				'security' => wp_create_nonce( 'seokey_keywords_table_list' ),
			);
			wp_localize_script( 'seokey-keywords-tables-content', 'adminAjax', $args );
		}
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
include_once SEOKEY_PATH_ADMIN . 'modules/keywords/table-keyword.php';

// Include Content WP_LIST_TABLE class
include_once SEOKEY_PATH_ADMIN . 'modules/keywords/table-content.php';


// Display table Ajax calls
add_action('wp_ajax__seokey_keywords_display_table', '_seokey_keywords_display_table_callback');
/**
 * Action wp_ajax for fetching the first time all table structure
 */
function _seokey_keywords_display_table_callback() {
	// Nonce
	check_ajax_referer('seokey_keywords_table_list', 'security');
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
	}
	// New table
	$wp_list_table = new seokey_WP_List_Table_keywords();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
	$wp_list_table->prepare_items();
	// capture data
	ob_start();
	$wp_list_table->display();
	$display = ob_get_clean();
	// return json encoded table
	die(
	json_encode(
		array(
			"display" => $display
		)
	)
	);
}

// Display table Ajax calls
add_action('wp_ajax__seokey_keywords_display_table_content', '_seokey_keywords_display_table_content_callback');
/**
 * Action wp_ajax for fetching the first time all table structure
 */
function _seokey_keywords_display_table_content_callback() {
	// Nonce
	check_ajax_referer('seokey_keywords_table_list', 'security');
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
	}
	// New table
	$wp_list_table = new seokey_WP_List_Table_contents();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
	$wp_list_table->prepare_items();
	// capture data
	ob_start();
	$wp_list_table->display();
	$display = ob_get_clean();
	// return json encoded table
	die(
		json_encode(
			array(
				"display" => $display
			)
		)
	);
}