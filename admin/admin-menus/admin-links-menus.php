<?php
/**
 * Load admin menus and links
 *
 * @Loaded on plugins_loaded + is_admin() + capability author
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

add_action( 'admin_menu', 'seokey_admin_menus', 100 );
/**
 * Add menus and sub-menus
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook admin_menu
 * @global $submenu
 * @see add_menu_page()
 * @see add_submenu_page()
 * @see seokey_helper_admin_get_main_menu_title()
 * @see seokey_helper_user_get_capability()
 * @see seokey_helper_admin_get_menus()
 * @see seokey_admin_pages() Content for each page
 */
function seokey_admin_menus() {
	global $submenu;
	// Get sub-menus to add
	$menus = seokey_helper_admin_get_menus();
	ksort($menus);
	// Get main menu title
	$firstmenu = seokey_helper_admin_get_main_menu_title();
    // Add main menu
	add_menu_page(
		SEOKEY_NAME . $firstmenu,
		SEOKEY_NAME,
		seokey_helper_user_get_capability( 'editor' ),
		SEOKEY_SLUG,
		'seokey_admin_pages',
        SEOKEY_URL_ASSETS_PUBLIC . 'img/serrure.svg',
		59
	);
	// Add sub-menus
	foreach ( $menus as $menutoadd ) {
		// Do not add twice our main menu
		if ( $menutoadd === $firstmenu ) {
			continue;
		}
		$title = $menutoadd['title'];
		$function = 'seokey_admin_pages';
		if ( ! empty( $menutoadd['function'] ) ) {
			if ( is_array( $menutoadd['function'] ) ) {
				$function = [$menutoadd['function'][0], $menutoadd['function'][1]];
			} else {
				$function = $menutoadd['function'];
			}
		}
		// Add each submenu
		add_submenu_page(
			SEOKEY_SLUG,
			$title,
			$title,
			$menutoadd['capability'],
			$menutoadd['slug'],
			$function
		);
		// Change the URL if set
		if ( isset( $menutoadd['url'] ) ) {
			end( $submenu );
			$key = key( $submenu );
			$submenu[ $key ][ count( $submenu[ $key ] ) - 1 ] = [
				$title,
				$menutoadd['capability'],
				$menutoadd['url'],
				$title
			];
		}
	}
}

add_action( 'admin_menu', 'seokey_admin_tooltips_menus', SEOKEY_PHP_INT_MAX );
/**
 * Display tooltips in menu
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_tooltips_menus() {
	// TODO move this function into core menu declaration seokey_admin_menus()
	// Redirection submenu tooltip
	global $submenu;
	$count = 0;
	if ( function_exists( 'seokey_redirections_display_errors_count' ) ) {
		$count = seokey_redirections_display_errors_count() + seokey_redirections_display_guessed_count();
	}
	if ( $count > 0 ) {
		$text = sprintf( wp_kses_post( __( 'Redirections <span class="awaiting-mod redirection-menu-count">%d</span>', 'seo-key' ) ), $count );
	} else {
		$text = esc_html_x( 'Redirections', 'Redirections admin menu name', 'seo-key' );
	}
	$key = array_search( __( 'Redirections', 'seo-key' ), array_column( $submenu['seo-key'], '0') );
	$submenu['seo-key'][$key][0] = $text;
	// Audit submenu tooltip
	global $submenu;
	$count_audit = get_option('seokey_audit_global_issues_count_now');
	if ( $count_audit > 0 ) {
		$text = sprintf( wp_kses_post( __( 'Audit <span class="awaiting-mod audit-menu-count">%d</span>', 'seo-key' ) ), $count_audit );
	} else {
		$text = esc_html_x( 'Audit', 'Redirections admin menu name', 'seo-key' );
	}
	$key = array_search( __( 'Audit', 'seo-key' ), array_column( $submenu['seo-key'], '0') );
	$submenu['seo-key'][$key][0] = $text;
	// Menu tooltip
	global $menu;
	$count = $count + $count_audit;
	if ( $count > 0 ) {
		$text = sprintf( wp_kses_post( __( SEOKEY_NAME . '<span class="awaiting-mod seokey-menu-count" id="seokey-menu-count-main-menu">%d</span>', 'seo-key' ) ), $count );
	} else {
		$text = SEOKEY_NAME;
	}
	// see https://core.trac.wordpress.org/ticket/40927
	$search     = array_column( $menu, '0' );
	$found_key     = array_search( 'SEOKEY', $search );
	foreach ( array_keys($menu) as $i => $key ) {
		if ( ($i+1) % ( $found_key + 1 ) ) {
			continue;
		}
		$menu[$key][0] = $text;
	}
}