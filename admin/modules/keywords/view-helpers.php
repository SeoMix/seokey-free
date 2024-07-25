<?php
// TODO COMMENT

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Display keywords nav links
 *
 * @since   1.9.0
 * @author  Daniel ROCh
 */
function seokey_keywords_display_nav_tabs() {
	$html = '';
	$links      = apply_filters( 'seokey_filter_keywords_display_nav_tabs', array() );
	$base_url   = admin_url( 'admin.php' );
	$base_url   = add_query_arg( 'page', sanitize_title( $_GET['page']),  $base_url );
	$currenttab = ( !empty( $_GET["tab"] ) ) ? sanitize_title( $_GET["tab"] ) : "contents";
	foreach ( $links as $key => $type ) {
		if ( $key === $currenttab ) {
			$class = "nav-tab nav-tab-active";
		} else {
			$class = "nav-tab";
		}
		$url = add_query_arg( 'tab', $key, $base_url );
		$html.= '<a href="' . esc_url ( $url ) . '" class="' . $class . '">' . $type . '</a>';
	}
	$html = '<nav role="navigation" class="nav-tab-wrapper">' . $html . '</nav>';
	echo $html;
}

/* Menu */
add_filter( 'seokey_filter_keywords_display_nav_tabs', 'seokey_filter_keywords_display_nav_tabs_keyword', 8, 1 );
function seokey_filter_keywords_display_nav_tabs_keyword( $links ){
	$links['contents'] = esc_html__( 'All contents', 'seo-key' );
	$links['keywords'] = esc_html__( 'Targeted Keywords', 'seo-key' );
	return $links;
}


/**
 * Display Keyword table
 *
 * @author  Daniel Roch
 * @since   1.5.0
 *
 * @return void WP List Table HTML
 */
function seokey_admin_keyword_menu_content_keywords( $task = '' ) {
	// Security
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		return;
	}
	// Load our class
	$wp_list_table = new seokey_WP_List_Table_keywords();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
	$wp_list_table->prepare_items();
	echo '<section id="seokey-keywords-content">';
	// Show everything
	$wp_list_table->display();
	echo '</section>';
}

/**
 * Display Keyword table
 *
 * @author  Daniel Roch
 * @since   1.5.0
 *
 * @return void WP List Table HTML
 */
function seokey_admin_keyword_menu_content_table( $task = '' ) {
	// Security
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		return;
	}
	// Load our class
	$wp_list_table = new seokey_WP_List_Table_contents();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
	$wp_list_table->prepare_items();
	echo '<section id="seokey-keywords-content">';
	// Show everything
	$wp_list_table->display();
	echo '</section>';
}



add_action( 'load-seokey_page_seo-key-keywords', 'seokey_keywords_screen_option' );
/**
 * Add Screen options
 */
function seokey_keywords_screen_option() {
	$args = array(
		'label' => esc_html__('Number of items per page:', 'seo-key'),
		'default' => 20,
		'option' => 'seokey_keywords_per_page'
	);
	add_screen_option( 'per_page', $args );
}