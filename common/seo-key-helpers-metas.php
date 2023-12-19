<?php
/**
 * Load SEOKEY metas functions
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
 * SEOKEY Title generator
 *
 * Retrieve the default optimized title for each content ($default = true) or get the user one ($default = false)
 *
 * @author    Daniel Roch
 * @since     0.0.1
 *
 * @notes   Available "Types" and specific arguments : front_page_static, front_page_blog, home, post_type_archive (label & name), singular, taxonomy (name), user (name)
 *
 * @param   string $type content type
 * @param   integer $ID content id
 * @param   array $args specific arguments for some content (label and name)
 * @param   bool $default Return user value (false) or default value (true). Default value is false
 * @return  mixed|void Final meta title (without <title>)
 */
function seokey_meta_title_value( $type, $ID = 0, $args = array(), $default = false ) {
	// Empty default value
	$default_value  = '';
	// No ID ? You're doing it wrong
	if ( !is_int ( $ID ) ) {
		$message = __FUNCTION__ .' Line '. __LINE__ . ' Title generation error : bad ID'; // Do not translate.
		seokey_dev_write_log( $message );
	}
	// Get or generate title
	switch ( $type ) {
		// For Static front page, singular content and blog page
		case 'front_page_static':
		case 'singular':
		case 'home':
			$user_value  	= get_post_meta( $ID, 'seokey-metatitle', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				global $post;
				$default_value = apply_filters('single_post_title', get_the_title( $ID ), $post );
			}
			break;
		// For blog front page (WordPress default configuration)
		case 'front_page_blog':
			$user_value  	= seokey_helper_get_option( 'metas-metatitle' );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value 	= get_bloginfo( 'name', 'display' );
				$tagline 		= get_bloginfo( 'description', 'display' );
				if ( ! empty( $tagline ) ) {
					$sep = apply_filters( 'document_title_separator', '-' );
					$default_value .= ' ' . $sep . ' ' . $tagline;
				}
			}
			break;
		// For post type archive
		case 'post_type_archive':
			$user_value 	= get_option( 'seokey-metatitle-' . $args['name'] );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value = $args['label'];
			}
			break;
		// For taxonomy term
		case 'taxonomy':
			$user_value  	= get_term_meta( $ID, 'seokey-metatitle', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value 	= $args['name'];
			}
			break;
		// For author pages
		case 'user':
			$user_value  	= get_user_meta( $ID, 'seokey-metatitle', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value = sprintf( __( '%s, author', 'seo-key' ), $args['name'] );
			}
			break;
		// Is there really other cases ?
		default:
			$user_value = false;
			$default_value = 'Title generation error';
			seokey_dev_error( __FUNCTION__, __LINE__, 'Title generation error' ); // Do not translate.
			break;
	}
	// What do we want ? The user value or the default one (true) ?
	if ( true === $default || ! $user_value ) {
		// truncate the default one
		$title = $default_value;
	} else {
		$title = $user_value;
	}
	// Filter
	$title =  apply_filters( 'seokey_filter_meta_title', $title );
	// Return data
	return esc_html( wp_strip_all_tags( $title ) );
}

/**
 * SEOKEY Meta Description generator
 *
 * @author    Daniel Roch
 * @since     0.0.1
 *
 * @param $type string (string) content type
 * @param int $ID ID Of the element
 * @param $args array specific arguments for some content (label and name)
 * @param bool $default
 * @return mixed|void (string) $description meta description
 * @notes Available "Types" and specific arguments : front_page_static, front_page_blog, home, post_type_archive (label & name), singular, taxonomy (name), user (name)
 */
function seokey_meta_desc_value( $type, $ID = 0, $args = array(), $default = false ) {
	// Empty default value
	$default_value  = '';
	// No ID ? You're doing it wrong
	if ( !is_int ( $ID ) ) {
		$message = __FUNCTION__ .' Line '. __LINE__ . ' Meta desc generation error bad ID'; // Do not translate.
		seokey_dev_write_log( $message );
	}
	// Get or generate meta desc
	switch ( $type ) {
		// For Static front page, singular content and blog page
		case 'front_page_static':
		case 'singular':
		case 'home':
			$user_value  	= get_post_meta( $ID, 'seokey-metadesc', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
                /**
                 * Filter post_content before creating a default meta description
                 *
                 * @param (string) $content Post content
                 *
                 * @since 1.6.0
                 */
                $base_content   = apply_filters ( 'seokey_filter_meta_desc_value_singular_postcontent', get_post($ID)->post_content );
				if ( !has_shortcode( $base_content, "edd_profile_editor" ) ) { // Fix for EDD not displaying login error messages
					$post           = esc_html( trim( strip_tags( do_shortcode( $base_content ) ) ) );
					$default_value  = substr( $post, 0, strrpos( substr( $post, 0, METADESC_COUNTER_MAX ), ' ') );
					if ( ! $default_value ) {
						$default_value = seokey_helper_post_content_extract();
					}
				}
			}
			break;
		// For blog front page (WordPress default configuration)
		case 'front_page_blog':
			$user_value  	= seokey_helper_get_option( 'metas-metadesc' );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value = get_bloginfo( 'description', 'display' );
				if ( ! $default_value ) {
					$default_value = seokey_helper_post_content_extract();
				}
			}
			break;
		// For post type archive
		case 'post_type_archive':
			$user_value 	= get_option( 'seokey-metadesc-' . $args['name'] );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value 	=  $args['description'];
				if ( ! $default_value ) {
					$default_value 	=  $args['label'];
				}
			}
			break;
		// For taxonomy term
		case 'taxonomy':
			$user_value  	= get_term_meta( $ID, 'seokey-metadesc', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value = $args['description'];
				if ( is_admin() ) {
					$term = get_term( $ID );
				} else {
					$term = get_queried_object();
				}
				$user_value = sprintf( __('Archive for: %s','seo-key'), $term->name );
			}
			break;
		// For author pages
		case 'user':
			$user_value  	= get_user_meta( $ID, 'seokey-metadesc', true );
			// No user value or we want the default value
			if ( empty( $user_value ) || true === $default ) {
				$default_value = get_the_author_meta( 'description', $ID );
				if ( ! $default_value ) {
					$default_value = sprintf(
					/* translators: 1:User Display Name 2:Name of the website */
						__( '%s is an author on %s website.', 'seo-key'), get_the_author_meta( 'display_name', $ID ), get_bloginfo( 'name' )
					);
				}
			}
			break;
		// Is there really other cases ?
		default:
			$user_value = false;
			$default_value = 'Meta desc generation error';
			seokey_dev_error( __FUNCTION__, __LINE__, 'Meta desc generation error' ); // Do not translate.
			break;
	}
	// Fix default value (line breaks)
	$default_value = trim( preg_replace( '/\s+/', ' ', $default_value ) );
	// What do we want ? The user value or the default one ?
	if ( true === $default || empty( $user_value ) ) {
		// truncate the default one
		$description = seokey_helper_meta_length( $default_value, METADESC_COUNTER_MAX );
	} else {
		$description = $user_value;
	}
	// Filter
	$description =  apply_filters( 'seokey_filter_meta_description', $description );
	// Return data
	return esc_html( wp_strip_all_tags( $description ) );
}


/**
 * Truncate meta for correct length (Default length set to METATITLE_COUNTER_MAX)
 *
 * @param $content ( post title )
 * @param int $count Meta length (character count)
 *
 * @return string
 * @since  0.0.1
 *
 * @author Daniel Roch
 * @hook   wp_head, 1
 */
function seokey_helper_meta_length( $content, $count = 0 ) {
	$count = $count ?: METATITLE_COUNTER_MAX;
	if ( strlen( $content ) > $count ) {
		// Get default ellipsis
		$more = apply_filters( 'excerpt_more', ' [&hellip;]' );
		// Truncate correctly words
		$count = $count + strlen( $more );
		$content =substr( $content, 0, strrpos( substr( $content, 0, $count ), ' ') ) . $more;
	}
	/**
	 * Filter and return truncated meta title
	 *
	 * @param (string) $content <meta desc> content
	 *
	 * @since 0.0.1
	 */
	return apply_filters( 'seokey_filter_helper_meta_length', $content );
}
