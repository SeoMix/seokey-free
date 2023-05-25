<?php
/**
 * Admin Redirection module : View functions
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     redirections.php
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

add_action( 'load-seokey_page_seo-key-redirections', 'seokey_redirections_screen_option' );
/**
 * Add Screen options
 */
function seokey_redirections_screen_option() {
	$args = array(
		'label' => esc_html__('Items per page', 'seo-key'),
		'default' => 30,
		'option' => 'seokey_redirections_per_page'
	);
	add_screen_option( 'per_page', $args );
}

/**
 * Main notice for our ALT Editor
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_redirections_notice_content( $args ) {
	$text = '<div class="flexbox">';
		$text .= '<div class="notice-flexboxcolumn">';
			$text .= '<h3>'. __( 'What is a redirection?', 'seo-key' ) . '</h3>';
			$text .= '<p>';
				$text .= __( 'A redirect is an instruction sent to your computer to redirect you to a new URL. For example, you want to see content on website.com/a, but you are automatically redirected to website.com/b.', 'seo-key' );
			$text .= '</p>';
			$text .= '<p>'. __( 'Users usually never realize it, but it helps search engines understand which is the right URL to consider.', 'seo-key' ) . '</p>';
		$text .= '</div>';
		$text .= '<div class="notice-flexboxcolumn">';
			$text .= '<h3>'. __( 'When should I add a redirection?', 'seo-key' ) . '</h3>';
			$text .= '<p>'. __( 'You need to add a redirect when the content URL has changed or when you deleted it.', 'seo-key' ) . '</p>';
			$text .= '<p>';
				$text .= __( '<strong>You also need to add a redirect when Google finds an error page on your website.</strong>', 'seo-key' );
				$text .= seokey_helper_help_messages( '301-404' );
			$text .= '</p>';
		$text .= '</div>';
		$text .= '<div class="notice-flexboxcolumn">';
			$text .= '<h3>'. __( 'What is WordPress doing with automatic redirections?', 'seo-key' ) . '</h3>';
			$text .= '<p>'. __( 'WordPress automatically redirects some error pages by guessing the right URL without telling you about it. But your CMS may be wrong so you need to check them.', 'seo-key' ) . '</p>';
			$text .= '<p>'. __( 'In addition, these redirections are really slow (bad loading time for Google and your server).', 'seo-key' );
			$text .= seokey_helper_help_messages( '301-automatic', true );
			$text .= '</p>';
		$text .= '</div>';
	$text .= '</div>';
	$new_args = array(
		sanitize_title( 'seokey_notice_redirections' ), // Unique ID.
		'',
		$text,
		[
			'scope'         => 'user',       // Dismiss is per-user instead of global.
			'type'          => 'information',    // Make this a warning (orange color).
			'capability'    => seokey_helper_user_get_capability('editor' ), // only for theses users and above
			'alt_style'     => false, // alternative style for notice
			'state'         => 'permanent',
			'class'         => ['notice'],
		]
	);
	array_push($args, $new_args );
	return $args;
}

add_action( 'admin_init', 'seokey_redirections_notice' );
/**
 * Notices on Media Library
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook load-upload.php
 */
function seokey_redirections_notice() {
	// Are we using redirection menu
	if ( true === seokey_helpers_redirections_is_redirect_editor() ) {
		add_filter( 'seokey_filter_admin_notices_launch', 'seokey_redirections_notice_content', 1 );
	}
}

/**
 * Display redirections nav links
 *
 * @since   0.0.1
 * @author  Leo Fontin
 */
function seokey_redirections_display_nav_tabs() {
	$html = '';
	$links_default = array(
		'default' => esc_html__( 'Redirections', 'seo-key'),
	);
	$links      = apply_filters( 'seokey_filter_redirections_display_tools_links', $links_default );
	$base_url   = admin_url( 'admin.php' );
	$base_url   = add_query_arg( 'page', sanitize_title( $_GET['page']),  $base_url );
	$currenttab = ( !empty( $_GET["tab"] ) ) ? sanitize_title( $_GET["tab"] ) : "default";
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
	return $html;
}

// TODO Comments
function seokey_redirection_display_column_source( $item ){
    if( !wp_http_validate_url( $item['source'] ) ) {
        $link = home_url( $item['source'] );
    } else {
        $link = $item['source'];
    }
    $html = '<a class="sourceurl" href="' . esc_url( $link ) . '">' . esc_html( $link ) . '</a>';
    return $html;
}