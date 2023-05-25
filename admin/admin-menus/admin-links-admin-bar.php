<?php
/**
 * Load SEOKEY Admin Bar functions
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

add_action( 'admin_bar_menu', 'seokey_admin_bar', SEOKEY_PHP_INT_MAX );
/**
 * Add SEOKEY Admin Bar
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @hook  admin_bar_menu
 * @see $wp_admin_bar->add_menu( $args )
 * @see seokey_helper_admin_get_link()
 * @see seokey_helper_admin_get_menus()
 * @see seokey_helper_admin_get_main_menu_title()
 * @param (object) $wp_admin_bar WP_Admin_Bar object.
 * @return void
 */
function seokey_admin_bar( $wp_admin_bar ) {
	// Get main menu and sub-menus
	$menus     = apply_filters( 'seokey_filter_admin_bar', seokey_helper_admin_get_menus() );
	// Current should not see main menu ? die here
	if ( ! current_user_can($menus[0]['capability'] ) ) {
		return;
	}
	// Get main menu data
	$count_redirect = 0;
    $count_audit = get_option('seokey_audit_global_issues_count_now');
	$count = $count_redirect + $count_audit;
	// Update main menu textwith counts
	if ( $count > 0 ) {
		$counttext = sprintf( '<span class="seokey-menu-count seokey-menu-count-admin-bar">%d</span>', $count );
		$text = '<span class="ab-item seokey-icon"></span><span class="screen-reader-text">' . SEOKEY_NAME . ' </span> ' . SEOKEY_NAME . $counttext;
	} else {
		$text = '<span class="ab-item seokey-icon"></span><span class="screen-reader-text">' . SEOKEY_NAME . ' </span> ' . SEOKEY_NAME;
	}
	// Add parent admin bar menu
	$wp_admin_bar->add_menu(
		[
			'id'    => SEOKEY_SLUG,
			'title' => $text,
			'href'  => esc_url( seokey_helper_admin_get_link() ),
		]
	);
	$firstmenu = seokey_helper_admin_get_main_menu_title();
	// For all other menus
	foreach ( $menus as $menutoadd ) {
		// Check capability before adding every admin bar menu
		if ( ! current_user_can( $menutoadd['capability'] ) ) {
			continue;
		}
		$menutitle = $menutoadd['title'];
		// Clean slugs
        switch ( $menutoadd['slug'] ) {
            case 'seo-key':
                $slug = $menutoadd['slug'];
                break;
            default:
                $slug = ( !empty( $menutoadd['baseurl'] ) ) ? $menutoadd['slug'] : substr( $menutoadd['slug'], 8 );
                break;
        }
		$base = ( !empty( $menutoadd['baseurl'] ) ) ? $menutoadd['baseurl'] : 'admin.php?page=';
		// Get specific data for all admin link except main menu
		if ( $menutitle !== $firstmenu ) {
			// Create admin link
			$href = seokey_helper_admin_get_link( $slug, $base );
		} else {
			$href = seokey_helper_admin_get_link( '', $base);
		}
		// Specific menu handling
        // TODO Later add filter here
		$extra = '';
		if ( 'audit' === $slug ) {
            if ( $count_audit > 0 ) {
                $extra = sprintf( '<span class="awaiting-mod audit-menu-count-admin-bar">%d</span>', $count_audit );
            }
        }
		// Add menu
		$wp_admin_bar->add_menu(
			[
				'parent' => SEOKEY_SLUG,
				'id'     => $href,
				'title'  => esc_html__( $menutitle, 'seo-key' ) . $extra,
				'href'   => esc_url( $href ),
			]
		);
	}
}

add_action( 'admin_bar_menu', 'seokey_admin_bar_post_type_archive', 205 );
/**
 * Add SEOKEY Admin link to edit a post type archive
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @hook  admin_bar_menu, 205
 * @param (object) $wp_admin_bar WP_Admin_Bar object.
 */
function seokey_admin_bar_post_type_archive( $wp_admin_bar ) {
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		return;
	}
	if ( is_post_type_archive() && !is_admin() ) {
		// get current post type
		global $wp_query;
		$key = $wp_query->query_vars['post_type'];
		// Default slug for a CPT
		$slug = 'edit.php?post_type=' . $key;
		// Natives "post" type and attacments are not necessary
		if ( 'post' === $key || 'attachement' === $key ) {
			return;
		}
		// Add parent admin bar menu
		$wp_admin_bar->add_menu(
			[
				'id'    => SEOKEY_SLUG . '_post_type_archive',
				'title' => __( 'Edit this Post Type Archive', 'seo-key' ),
				'href'  => esc_url( admin_url( $slug . '&page=seo-key-archive_'.$key ) ),
			]
		);
	} elseif ( is_admin() ) {
        if ( true === seokey_helper_cache_data ('seokey_metabox_post_type_archive' ) ) {
            global $typenow;
            // Get the post type object
            $typenow_object = get_post_type_object($typenow);
            if (null !== $typenow_object) {
                $continue = apply_filters( 'seokey_filter_admin_bar_post_type_archive_has_archive', $typenow_object->has_archive );
                if ( true === $continue ) {
                    // Get the front archive link
                    $get_post_type_archive_link = esc_url(get_post_type_archive_link($typenow));
                    // Add parent admin bar menu
                    $wp_admin_bar->add_menu(
                        [
                            'id' => SEOKEY_SLUG . '_post_type_archive',
                            'title' => __('View this Post Type Archive', 'seo-key'),
                            'href' => esc_url($get_post_type_archive_link),
                        ]
                    );
                }
            }
        }
	}
}