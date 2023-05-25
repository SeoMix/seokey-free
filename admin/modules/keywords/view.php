<?php

//* If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Display Keyword table
 *
 * @author  Daniel Roch
 * @since   1.5.0
 *
 * @return void WP List Table HTML
 */
function seokey_admin_keyword_menu_content( $task = '' ) {
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