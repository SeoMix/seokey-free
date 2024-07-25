<?php
/**
 * Automatic redirection (this is the way)
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

add_action( 'template_redirect', 'seokey_redirections_useless_rss_feeds', 10 );
/**
 * Redirect Useless Feeds (empty feeds + all secondary feeds according to user option)
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook template_redirect
 * @see seokey_helper_url_get_current()
 * @return void
 */
function seokey_redirections_useless_rss_feeds() {
	// Only for feeds
	if ( ! is_feed () ) {
		return;
	}
	global $wp_query;
	// If there is no post in this feed, redirect to homepage
	// TODO later if user is adding other post type to main RSS feed, use a better count function
	if( ! $wp_query->post_count ) {
		wp_redirect( home_url(), 301 );
		die;
	} else {
		// Remove all secondary feeds (manual option)
		$disabled = seokey_helper_get_option('seooptimizations-rss-secondary', 'yes' );
		// Remove secondary feeds (manual option to disable all or automatic without user choice)
		if ( 'yes' === $disabled || (string) 1 === $disabled ) {
			// Get Current URL with port (ex: https://www.example.com/test:8080)
			$currenturl = seokey_helper_url_get_current();
			// Get homepage main feed URL
			$main_feed_url = user_trailingslashit( home_url( 'feed' ) );
			// Redirect all other feeds to main feed
			if ( $currenturl !== $main_feed_url ) {
				wp_redirect( esc_url( $main_feed_url ), 301 );
				die();
			}
		}
	}
}

add_action ( 'do_feed_rss2', 'seokey_redirections_useless_rss_feeds_comments', 1 );
/**
 * Redirect all comments feeds
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook do_feed_rss2
 * @return void
 */
function seokey_redirections_useless_rss_feeds_comments(){
	if ( is_singular() ) {
		wp_redirect( home_url(), 301 );
		die();
	}
}

add_action( 'template_redirect', 'seokey_redirections_404_pagination' );
/**
 * Redirect 404 paginations
 *
 * @since   0.0.1
 * @author  Leo Fontin
 *
 * @hook template_redirect
 * @return void
 */
function seokey_redirections_404_pagination() {
	if ( is_404() ) {
		// seokey_helper_get_paged
		$paged = get_query_var( 'paged' );
		if ( $paged !== 0 && is_numeric( $paged ) ) {
			global $wp_rewrite;
			$url = preg_replace( "#$wp_rewrite->pagination_base/$paged(/+)?$#", '', esc_url( $_SERVER['REQUEST_URI'] ) );
			wp_safe_redirect( esc_url( $url ), 301 );
			die;
		}
	}
}

add_action( 'template_redirect', 'seokey_redirections_authors', 10 );
/**
 * Redirect authors pages
 *
 * @since   0.0.1
 * @author  Leo Fontin
 *
 * @hook template_redirect
 * @return void
 */
function seokey_redirections_authors() {
	if ( is_author() ) {
		// All author pages are privates ?
		$page = seokey_helper_get_option( 'cct-pages', [] );
		if ( ! empty( $page ) && ! in_array( 'author', $page ) ) {
			wp_safe_redirect( esc_url( get_home_url() ), 301 );
			die;
		}
		// Is this an author pagination ?
        $disabled = seokey_helper_get_option('seooptimizations-pagination-authors', 'yes' );
        // Remove secondary feeds (manual option to disable all or automatic without user choice)
        if ( 'yes' === $disabled || (string) 1 === $disabled ) {
            // Redirect author pages
            $pagination = seokey_helper_get_paged();
            if ( $pagination >= 2 ) {
                $url = get_author_posts_url( get_the_author_meta( 'ID' ) );
                wp_safe_redirect( esc_url( $url ), 301 );
                die;
            }
        }
	}
}

add_action( 'template_redirect', 'seokey_redirections_attachment', 1 );
/**
 * Redirects attachement pages to medias
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   template_redirect
 * @global $post
 * @return void
 **/
function seokey_redirections_attachment() {
	if ( is_attachment() ) {
		// Avoid error if feed URl looks like an attachment page
		$current = seokey_helper_url_get_current();
		if ( str_ends_with( $current, '/feed/') ) {
			wp_safe_redirect( substr( $current, 0, -5 ), 301 );
			die();
		}
		global $post;
		// Redirect directly to file
		wp_safe_redirect( wp_get_attachment_url( $post->ID ), 301 );
		die();
	}
}

add_action( 'template_redirect', 'seokey_redirections_replytocom_URL' );
/**
 * Redirect reply comment url
 *
 * @since  0.0.1
 * @author Léo Fontin
 *
 *
 * @hook   template_redirect
 * @note : Exemple http://seokey.local/comments/?replytocom=205#respond
 * @global $post
 * @return void
 **/
function seokey_redirections_replytocom_URL() {
	if ( is_singular() ) {
		if ( ! empty( $_GET['replytocom'] ) ) {
			global $post;
			$comment_id = ( is_numeric( $_GET['replytocom'] ) ) ? $_GET['replytocom'] : '';
			$url        = ( !empty( $comment_id ) ) ? get_permalink( $post->ID ) . '#comment-' . $comment_id : get_permalink( $post->ID );
			wp_safe_redirect( $url, 301 );
			die();
		}
	}
}

add_action( 'template_redirect', 'seokey_redirections_410', 12 );
/**
 * 410 for old cache files
 *
 * @since  0.0.1
 * @author Léo Fontin
 *
 * @hook   template_redirect
 * @return void
 **/
function seokey_redirections_410() {
	if ( is_404() ) {
		// Where is my content directory ?
		$content_dir = parse_url( content_url() );
		$content_dir = $content_dir['path'];
		// Directory used to send automatic 410 code on 404 error pages
		$check = [
			$content_dir.'/cache', 									// WP Rocket and many other cache plugins
			$content_dir.'/glc_cache', 								// "gravatar local cache"
			$content_dir.'/et-cache', 								// Divi cache
            $content_dir.'/uploads/gravatar-cache', 				// "Harry gravatar cache"
            $content_dir.'/uploads/hummingbird-assets', 			// "Hummingbird assets"
            $content_dir.'/uploads/wphb-cache', 					// "Hummingbird cache"
            $content_dir.'/uploads/siteground-optimizer-assets', 	// "Siteground optimizer cache"
			$content_dir.'/plugins/elementor-pro/assets', 			// "Elementor PRO plugin cache"
			$content_dir.'/litespeed', 								// "LiteSpeed cache"

		];
		/**
		 * Filter cache directory list for automatic 410 codes
		 *
		 * @since 0.0.1
		 * @param (array) $check List of wp-content directories
		 */
		$check          = apply_filters( 'seokey_filter_redirections_410_ressources', $check );
		// 404 errors to exclude
		$check_within = [
			'data:image/svg+xml', 	// SVG images
			'/elementor/css' 		// Elementor cache
		];
		/**
		 * Filter 404 list for automatic 410 codes
		 *
		 * @since 1.9.0
		 * @param (array) $check List of wp-content directories
		 */
		$check_within   = apply_filters( 'seokey_filter_redirections_410_ressources_within', $check_within );
		// Current URL
		$current_url    = seokey_helper_url_get_current();
		$current_url    = strtok( $current_url, "?" );
		$ignore 		= false;
		if ( true === seokey_helper_strpos_array( $current_url, $check ) ) {
			$ignore = true;
		} else {
			foreach( $check_within as $a ) {
        		if ( str_contains( $current_url, $a ) ) {
        			$ignore = 'force';
        		}
    		}
		}
		// Do we need a 410 code ?
		if ( true === $ignore || 'force' === $ignore ) {
			// Get files extensions available in WordPress core
			$extensions = wp_get_ext_types();
			$merged     = call_user_func_array('array_merge', array_values( $extensions ) );
			// Get last part of URL (after the dot)
			$explode    = explode( '.' , $current_url );
			$last_part  = end( $explode );
			// Am i supposed to be a file ?
			if ( 'force' === $ignore || true === in_array( $last_part, $merged ) ) {
				// Change 404 hedaer to 410 header
				header( "HTTP/1.0 410 Gone" );
				// Delete unused variable
				unset($merged);
				// Do not count those URL as 404
				seokey_helper_cache_data('seokey_redirections_error_exclude', true );
			}
		}
	}
}

add_action( 'template_redirect', 'seokey_redirections_archive_date' );
/**
 * Redirect date archives to home
 *
 * @since   0.0.1
 * @author  Leo Fontin
 *
 * @hook template_redirect
 * @return void
 */
function seokey_redirections_archive_date() {
	if ( is_date() ) {
		wp_safe_redirect( get_home_url(), '301' );
		die;
	}
}

/**
 * Redirect 404 for natives sitemaps URL
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @hook template_redirect
 * @return void
 */
add_action ( 'template_redirect', 'seokey_redirections_sitemap_native_redirect', 10000 );
function seokey_redirections_sitemap_native_redirect() {
	if ( is_404() ) {
		// Is it a native sitemap URL ?
		if ( seokey_helper_is_sitemap() ) {
			// User has defined good and bad content ?
			if ( ! empty( get_option('seokey-field-cct-cpt') ) ) {
				$lang		= apply_filters('seokey_filter_sitemap_native_redirect_lang', 'default_lang' );
				// Search for data in both locale then iso2 data (each multingual plugin seems to do whatever they want...)
				$parent_key = seokey_helpers_get_parent_key( seokey_helper_cache_data('languages')["lang"], $lang, 'locale' );
				if ( false === $parent_key ) {
					$parent_key = seokey_helpers_get_parent_key( seokey_helper_cache_data('languages')["lang"], $lang, 'iso2' );
				}
				// Find correct sitemap URL to redirect to
				if ( false !== $parent_key) {
					$sitemap_name	= 'sitemap-index-' . $parent_key. '.xml';
				} else {
					$sitemap_name	= 'sitemap-index-' . seokey_helper_cache_data('languages')['site']['default_lang'] . '.xml';
				}
				$custom_sitemaps    = wp_upload_dir()['baseurl'].'/seokey/sitemaps/' . $sitemap_name;
				// Do you need another URL to redirect to ?
				$redirecturl        = apply_filters( 'seokey_filter_sitemap_native_redirect', $custom_sitemaps, seokey_helper_url_get_current() );
			}
			// User has not defined good and bad content : sitemaps are not yet available
			else {
				$redirecturl = user_trailingslashit( apply_filters( 'seokey_filter_home_url', home_url() ) );
			}
			wp_safe_redirect( esc_url( $redirecturl ), 301 );
			die;
		}
	}
}