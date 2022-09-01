<?php
/**
 * Load login page hooks
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
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

add_filter( 'login_headerurl', 'seokey_login_logo_home_url' );
/**
 * Change logo href to home URL (in case some bot crawl this URL)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook login_headerurl
 * @return string Home URL
 */
function seokey_login_logo_home_url() {
  return esc_url( site_url() );
}

add_action( 'login_head', 'seokey_login_nofollow', 20 );
/**
 * Improve meta robots on login pages (nofollow added)
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook login_head
 * @return void
 */
function seokey_login_nofollow() {
    global $wp_version;
    // Do we have an inferior WordPress version (5.7)
    if ( version_compare( $wp_version, '5.7.0' ) < 0 ) {
        // Fallback for older versions
        remove_action('login_head', 'wp_robots_sensitive_page');
        // Tell people SEOKEY is adding a better meta
        /* translators: 1: Plugin Name, 2: Plugin Website URL */
	    $translation = sprintf( esc_html__( 'BEGIN SEOKEY head. Site optimized by SEOKEY %s', 'seo-key' ), SEOKEY_HOME );
        echo '<!-- ' . $translation . ' !-->' . "\n";
        // add an improved robot tag
        echo "<meta name='robots' content='noindex, noarchive, nofollow' />\n";
	    echo '<!-- END SEOKEY head -->' . PHP_EOL;
    }
}
add_filter( 'wp_robots', 'seokey_wp_robots_sensitive_page', SEOKEY_PHP_INT_MAX );
function seokey_wp_robots_sensitive_page( array $robots ){
    global $wp_version;
    // Do we have a superior WordPress version (5.7)
    if ( version_compare( $wp_version, '5.7.0' ) > 0 && $GLOBALS['pagenow'] === 'wp-login.php' ) {
        $robots['noarchive'] = true;
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        unset($robots['max-image-preview']);
    }
    return $robots;
}