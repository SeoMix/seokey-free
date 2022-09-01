<?php
/**
 * Admin Redirection error tab functions
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     admin-module.php
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

if ( !function_exists( 'seokey_filter_redirections_display_tools_links_guessed' ) ) {
    
    /* Menu */
	add_filter( 'seokey_filter_redirections_display_tools_links', 'seokey_filter_redirections_display_tools_links_guessed', 8, 1 );
	function seokey_filter_redirections_display_tools_links_guessed( $links ) {
		$links['guessed'] = esc_html__( 'Automatic redirections', 'seo-key' );
		return $links;
	}
	
	function seokey_redirections_display_guessed_count() {
		return 0;
	}
    
    /**
     * Display Redirections table
     *
     * @since   0.0.1
     * @author  Daniel Roch
     */
    function seokey_redirections_display_guessed(){
	    echo '<p><strong>' . __( 'WordPress automatic redirections detection is only available in the PRO version.', 'seo-key') . '</strong></p>';
	    echo '<p>' . __( 'Upgrade now to improve your SEO!', 'seo-key') . '</p>';
	    echo '<p>' . __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.fr/tarifs/'>Buy SEOKEY Premium</a>", 'seo-key' ) . '</p>';
    }
}