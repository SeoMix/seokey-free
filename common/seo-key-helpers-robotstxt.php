<?php
/**
 * Load SEOKEY Robots.txt functions
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 * @see     seo-key-helpers.php
 *
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

/**
 * Robots.txt content
 *
 * @author  Daniel Roch
 * @since 0.0.1
 *
 * @notes Be careful : if you change first or last line of this function, please check the seokey_helper_files() delete function !
 */
function seokey_robots_txt_content() {
	// Default robot content
    $output =   __( "# BEGIN SEOKEY Robots.txt file : DO NOT EDIT our rules", "seo-key" ) . "\n";
	$output .=   __( "# No content has been added to this file: Search engines should be able to crawl all public URLs found on your website", "seo-key" ) . "\n";
	$output .=  __( "# If you don't want Search Engines to index some of your content, robots.txt is not a good solution: you should remove all links to these URLs AND add a noindex tag", "seo-key" ) . "\n";
	$output .=  "User-agent: *";
	// Sitemap
	$sitemap = get_option( 'seokey_sitemap_creation' );
	if ( 1 == $sitemap || "done" == $sitemap || "running" == $sitemap ) {
        $output .= "\n\n" . 'Sitemap: ' . site_url( '/sitemap-index.xml' );
	}
    // Extra rules ?
	$extra_output = apply_filters( 'seokey_filter_robots_txt_content_extra', '' );
	// Final content
	$output = $output . $extra_output . "\n\n" . __( "# END SEOKEY Robots.txt file (add your custom rules below)", "seo-key" ) . "\n";
	// Final filter
	$output = apply_filters( 'seokey_filter_robots_txt_content', $output );
	// Return Content
	return sanitize_textarea_field( $output ); // textarea to keep line breaks
}