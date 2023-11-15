<?php
/**
 * Check if new CPT or taxonomies have appeared
 *
 * @Loaded on plugins_loaded + is_admin() + capability editor
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

add_action( 'admin_init', 'seokey_admin_content_watcher', 100 );
/**
 * Handle new content watcher
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook wp_loaded
 */
function seokey_admin_content_watcher(){
	// Only on admin pages (not while doing ajax)
	if ( defined( 'DOING_AJAX' ) ) {
		return;
	}
	// Last check was recent or not?
	if ( false === ( $new = get_transient('seokey_transient_admin_content_watcher' ) ) ) {
		// Current user can see notification ?
		if ( current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
			// Did we have saved once our good contents ?
			if ( false === get_option( 'seokey_admin_content_watcher_known' ) ) {
				return;
			}
			// Post types
			$_builtin   = get_post_types( [ '_builtin' => true, 'public' => true ], 'objects' );
			$_custom    = get_post_types( [ '_builtin' => false, 'public' => true ], 'objects' );
			$post_types = array_merge( $_builtin, $_custom );
			unset( $_builtin );
			unset( $_custom );
			unset( $post_types['attachment'] );
			$post_types = array_keys( $post_types );
			// Taxonomies
			$_builtin   = get_taxonomies( [ '_builtin' => true, 'public' => true ], 'objects' );
			$_custom    = get_taxonomies( [ '_builtin' => false, 'public' => true ], 'objects' );
			$taxonomies = array_merge( $_builtin, $_custom );
			unset( $_builtin );
			unset( $_custom );
			unset( $taxonomies['post_format'] );
			$taxonomies = array_keys( $taxonomies );
			// previous known contents
			$current_list = get_option( 'seokey_admin_content_watcher_known', [] );
			// Check diff and get label if necessary
			$new  = [];
			$list = ( is_array( $current_list['posts'] ) ) ? $current_list['posts'] : [];
			foreach ( $post_types as $value ) {
				if ( ! in_array( $value, $list ) ) {
					$name  = get_post_type_object( $value );
					$new[] = $name->label;
				}
			}
			$list = ( is_array( $current_list['taxonomies'] ) ) ? $current_list['taxonomies'] : [];
			foreach ( $taxonomies as $value ) {
				if ( ! in_array( $value, $list ) ) {
					$name  = get_taxonomy( $value );
					$new[] = $name->label;
				}
			}
			set_transient( 'seokey_transient_admin_content_watcher', $new, 150 );
		}
	}
	// trigger notification if necessary
	if ( !empty( $new ) ) {
		// Tell our notification useful data
		seokey_helper_cache_data( 'seokey_new_content', $new );
		// Trigger notification
		add_filter( 'seokey_filter_admin_notices_launch', 'seokey_admin_content_watcher_notification', 10 );
	}

}

/**
 * Handle new content watcher notification content
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook seokey_filter_admin_notices_launch
 */
function seokey_admin_content_watcher_notification( $args ) {
	// data
	$title      = '<p>' . esc_html__('New content discovered', 'seo-key') . '</p>';
	$contents   = sanitize_title( __( 'Contents', 'seo-key' ) );
	$url        = seokey_helper_admin_get_link('settings') . '#' . $contents;
	$new        = seokey_helper_cache_data('seokey_new_content');
	// Final content
	$text   = '<p>' . esc_html_x('SEOKEY has discovered new content types on your website: ', 'notification text for a new content discovered', 'seo-key');
	$text   .= '<strong>' . implode( ', ', $new ) . '</strong>.</p>';
	$text   .= '<p><strong>' . esc_html_x('You need to check your SEO settings.', 'notification text for a new content discovered', 'seo-key' ) . '</strong> ';
	$text   .= wp_kses_post ( sprintf( __( '<a href="%s">Go to your settings page</a>, check public contents and taxonomies, then %ssave your settings%s.', 'seo-key' ), $url, '<strong>', '</strong>' ) . '</p>' );
	$new_args = array(
		sanitize_title('seokey_notice_content_watcher'), // Unique ID.
		$title, // The title for this notice.
		$text, // The content for this notice.
		[
			'scope' => 'global', // Dismiss is per-user instead of global.
			'type' => 'error', // Can be one of info, success, warning, error.
			'screens_exclude' => ['seokey_page_seo-key-wizard'],
			'capability' => seokey_helper_user_get_capability('editor'), // only for theses users and above
			'alt_style' => false, // alternative style for notice
			'state' => 'permanent',
		]
	);
	array_push($args, $new_args);
	return $args;
}

add_action( 'pre_update_option_seokey-field-cct-cpt', 'seokey_admin_content_watcher_update_known' );
/**
 * Save know taxonomies and CPT while updating our cct-cpt option
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook pre_update_option_seokey-field-cct-cpt
 */
function seokey_admin_content_watcher_update_known( $value ) {
	// Post types public
	$post_types = get_post_types(
		array(
			'public' => TRUE
		)
	);
	// but exclude attachments
	unset ($post_types['attachment']);
	// Public taxonomies
	$args = array(
		'public'   => true,
	);
	$taxonomies = get_taxonomies( $args );
	// Let's keep our know contents now
	$content = [
		'posts' => $post_types,
		'taxonomies' => $taxonomies
	];
	update_option( 'seokey_admin_content_watcher_known', $content, true );
	delete_transient('seokey_transient_admin_content_watcher');
	// Let Wordpress handle this option value
	return $value;
}


add_filter( "option_seokey-field-cct-taxo", 'seokey_admin_content_watcher_fix_option_taxo', 10, 2 );
/**
 * Filter seokey-field-cct-taxo to included non configured taxonomies
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook option_seokey-field-cct-taxo
 */
function seokey_admin_content_watcher_fix_option_taxo( $value, $option ){
	if ( defined( 'DOING_AJAX' ) || ! is_admin() ) {
		return $value;
	}
	if ( seokey_helper_user_get_capability( 'admin' ) ) {
		// Get previous know content
		$known = get_option( 'seokey_admin_content_watcher_known' );
		if ( isset ( $known['taxonomies'] ) ) {
			$known = $known['taxonomies'];
		}
		// Get all public taxonomies from WP
		$_builtin = get_taxonomies( ['_builtin' => true, 'public' => true ], 'objects' );
		$_custom = get_taxonomies( ['_builtin' => false, 'public' => true ], 'objects' );
		$all_taxos = array_merge( $_builtin, $_custom );
		// Remove post format taxonomie
		if ( isset ( $all_taxos['post_format'] ) ) {
			unset( $all_taxos['post_format'] );
		}
		// Get only names
		$all_taxos = wp_list_pluck( $all_taxos, 'name' );
		// If new content is discovered, add-it to the final value
		foreach ( $all_taxos as $content ) {
			if ( is_array ( $known) ) {
				if ( ! in_array( $content, $known ) ) {
					array_push( $value, $content );
				}
			}
		}
	}
	// Fix to avoid fatal errors if $value is not an array
	if ( !is_array( $value ) ) {
		$value = array( $value );
	}
	// remove potential duplicate values
	return array_unique( $value );
}

add_filter( "option_seokey-field-cct-cpt", 'seokey_admin_content_watcher_fix_option_cpt', 10, 2 );
/**
 * Filter seokey-field-cct-taxo to included non configured post types
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @hook option_seokey-field-cct-cpt
 */
function seokey_admin_content_watcher_fix_option_cpt( $value, $option ){
	if ( defined( 'DOING_AJAX' ) || ! is_admin() ) {
		return $value;
	}
	if ( seokey_helper_user_get_capability( 'admin' ) ) {
		// Get previous know content
		$known = get_option( 'seokey_admin_content_watcher_known' );
		if ( isset ( $known['posts'] ) ) {
			$known = $known['posts'];
		}
		// Get actual public CPTs from WP
		$_builtin = get_post_types( [ '_builtin' => TRUE, 'public' => TRUE ], 'objects' );
		$_custom = get_post_types( [ '_builtin' => FALSE, 'public' => TRUE ], 'objects' );
		$all_cpts = array_merge( $_builtin, $_custom );
		// Get only names
		$all_cpts = wp_list_pluck( $all_cpts, 'name' );
		// Remove CPT Attachement
		unset( $all_cpts['attachment'] );
		// If new content is discovered, add-it to the final value
		foreach ( $all_cpts as $content ) {
			if ( is_array ( $known) ) {
				if ( ! in_array( $content, $known ) ) {
					array_push( $value, $content );
				}
			}
		}
	}
	// remove potential duplicate values
	return array_unique( $value );
}