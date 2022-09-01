<?php
/**
 * Head functions (cleaning + adding our data)
 *
 * @Loaded on plugins_loaded
 * @see seokey_plugin_init()
 * @see public-modules.php
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

add_action( 'wp_head', 'seokey_head_data', 1 );
/**
 * Add all we need in document <head>
 *
 * @since 0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head, 2
 * @notes Create seokey_action_head_push action in order to be able to add our optimizations (title, canonical and so on)
 * @return void (void)
 */
function seokey_head_data() {
	do_action( 'seokey_action_head' );
}

add_action( 'seokey_action_head', 'seokey_head_data_begin', 1 );
/**
 * Add copyright to our header data
 *
 * @since 0.0.1
 * @author  Daniel Roch - SeoMix
 *
 * @hook seokey_action_head_push
 * @return void (string) Copyright
 */
function seokey_head_data_begin() {
	/* translators: 1: Plugin Name, 2: Plugin Website URL */
	$translation = sprintf( esc_html__( 'BEGIN SEOKEY head. Site optimized by SEOKEY %s', 'seo-key' ), SEOKEY_HOME );
	echo '<!-- ' . $translation . ' -->' . "\n";
}

add_action( 'seokey_action_head', 'seokey_head_data_end', 100 );
/**
 * End copyright to our header data
 
 * @since 0.0.1
 * @author  Daniel Roch - SeoMix
 *
 * @hook seokey_action_head_push
 * @return void (string) Copyright
 */
function seokey_head_data_end() {
	echo '<!-- END SEOKEY head -->' . PHP_EOL;
}

/**
 * Remove Emojis
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head
 * @hook admin_print_scripts
 * @hook wp_print_styles
 * @hook admin_print_styles
 * @hook the_content_feed
 * @hook comment_text_rss
 * @hook wp_mail
 */
remove_action( 'wp_head', 				'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 	'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 		'print_emoji_styles' );
remove_action( 'admin_print_styles', 	'print_emoji_styles' );
remove_filter( 'the_content_feed', 		'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 		'wp_staticize_emoji' );
remove_filter( 'wp_mail', 				'wp_staticize_emoji_for_email' );

/**
 * Remove RSD links
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head
 */
remove_action( 'wp_head', 'rsd_link' );

/**
 * Remove Windows Live Writer Link
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head
 */
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * Remove WP-JSON links
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head
 * @hook template_redirect
 */
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'template_redirect', 'rest_output_link_header' );

/**
 * Remove WordPress shortlinks (?p=) in <head>
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_head
 */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11);

add_action( 'seokey_action_head', 'seokey_head_data_search_console' );
/**
 * Add a Google Search Console Meta verification code
 *
 * @since    0.0.1
 * @author  Daniel Roch
 *
 * @hook seokey_action_head
 */
function seokey_head_data_search_console(){
    $google = get_option( 'seokey-field-search-console-searchconsole-google-verification-code' );
    if ( false !== $google ) {
        echo "<meta name=\"google-site-verification\" content=\"" . esc_attr( $google ) . "\" />\n";
    }
}