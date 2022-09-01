<?php
/**
 * Improve RSS Feeds
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

add_action( 'after_setup_theme', 'seokey_rss_activate_all_feeds', 1 );
/**
 * Activate feed support (in case they are not)
 *
 * @since 0.0.1
 * @author  Daniel Roch
 *
 * @see wp-includes/theme.php : add_theme_support()
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/
 *
 * @hook after_setup_theme
 * @global string $wp_version WordPress version.
 */
function seokey_rss_activate_all_feeds() {
    // Activate feeds (just in case they are not)
    add_theme_support( 'automatic-feed-links' );
}

add_action('do_feed',      'seokey_rss_clean_useless_data', 1);
add_action('do_feed_rdf',  'seokey_rss_clean_useless_data', 1);
add_action('do_feed_rss',  'seokey_rss_clean_useless_data', 1);
add_action('do_feed_rss2', 'seokey_rss_clean_useless_data', 1);
add_action('do_feed_atom', 'seokey_rss_clean_useless_data', 1);
/**
 * Deeply clean RSS feeds
 *
 * @author  Daniel Roch
 * @since  0.0.1
 *
 * @uses __return_false()
 * @hook after_setup_theme
 */
function seokey_rss_clean_useless_data() {
    // Remove RSS Comments data and links
    add_filter( 'comments_open',        '__return_false', 10 );
    add_filter( 'get_comments_number',  '__return_false', 10 );
    // Removes Emoji in RSS
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
}

add_action( 'get_header', 'seokey_rss_remove_useless_feeds', SEOKEY_PHP_INT_MAX );
/**
 * Remove useless feeds
 *
 * @author  Daniel Roch
 * @since 0.0.1
 *
 * @uses __return_false()
 * @hook init
 */
function seokey_rss_remove_useless_feeds() {
	// TODO remove feeds for private taxonomies
    // Remove secondary comment feeds
	if ( is_singular() ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
	$disabled = seokey_helper_get_option('seooptimizations-rss-secondary', 'yes' );
	// Remove secondary feeds (manual option to disable all or automatic without user choice)
	if ( 'yes' === $disabled || (string) 1 === $disabled ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
    // Remove comments feeds
    add_filter( 'feed_links_show_comments_feed', '__return_false' );
    // Remove feeds if no post are published in it
	// TODO later if user is adding other post type to main RSS feed, use a better count function
	$count = wp_count_posts();
    if ( 0 === $count->publish ) {
        remove_action( 'wp_head', 'feed_links', 2 );
    }
}

add_filter( 'excerpt_length', 'seokey_rss_length', 1000 );
/**
 * Increase RSS Length
 *
 * @author  Daniel Roch
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook excerpt_length
 * @param integer $length Excerpt length (default to 55)
 * @return integer Excerpt length
 */
function seokey_rss_length( $length ) {
    // Only change value for feeds
    if ( is_feed() ) {
        return 100;
    }
    // Return value
    return $length;
}

 /**
 * Force RSS excerpt
 *
 * @author  Daniel Roch
 * @since  0.0.1
 *
 * @hook option_rss_use_excerpt
 * @return integer 1 (true)
 */
add_filter( 'option_rss_use_excerpt', '__return_true', 10 );

add_filter( 'the_excerpt_rss', 'seokey_rss_content_add_readmore_link', 10 );
/**
 * RSS Content
 * - Add read more text and link
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook the_excerpt_rss
 * @param string $content Excerpt content
 * @return string) Excerpt content
 */
function seokey_rss_content_add_readmore_link( $content ) {
    // "Read More" string
    $read_more_text = esc_html__( 'Read more:', 'seo-key' );
    // Enhance HTML markup
    $excerpt_more = '<br><strong>' . $read_more_text . '</strong>';
    /**
     * RSS excerpt "Read More" text and html Filter
     *
     * @since  0.0.1
     * @author  Daniel Roch
     *
     * @param   (integer) $excerpt_more Excerpt text Value before link
     * @return  (integer) $excerpt_more Excerpt text Value before link
     */
    $excerpt_more = apply_filters( 'seokey_filter_rss_content_read_more', $excerpt_more );
    // "Read More" link
    global $post;
    $excerpt_more_link  = ' <a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . apply_filters( 'the_title_rss', get_the_title( $post->ID ) ) . '</a>';
    // Render final "Read More" link on RSS feeds
    $excerpt_more = $excerpt_more . $excerpt_more_link;
    // Improve content with Read More link
    $content = $content . $excerpt_more;
	// Return Data
    return $content;
}

add_filter( 'excerpt_more', 'seokey_rss_no_default_excerpt_more', 100 );
/**
 * RSS Empty Read More Content Ellipse
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook excerpt_more
 * @return void|string Void for feeds, default string elsewhere
 */
function seokey_rss_no_default_excerpt_more( $value ) {
    if ( is_feed() ) {
        __return_empty_string();
    }
    return $value;
}

add_action( 'pre_get_posts', 'seokey_rss_remove_private_content' );
/**
 * Exclude private contents from feeds
 *
 * @since  0.0.1
 * @author  Daniel Roch
 *
 * @hook pre_get_posts
 * @param $query
 * @return void
 */
function seokey_rss_remove_private_content( $query ) {
	if ( $query->is_feed() ) {
		// If user has activated the option, or when user has not made any choice yet
		$disabled = seokey_helper_get_option( 'seooptimizations-hide-noindexed', 'yes' );
		if ( 'yes' === $disabled || (string) 1 === $disabled ) {
			$key = 'seokey-content_visibility';
			$query->set( 'meta_query',
				array(
					'relation' => 'OR',
					// Include posts where user has not yet defined the private/public value
					array(
						'key'     => $key,
						'value'   => '0',
						'compare' => 'NOT EXISTS',
					),
					// but exclude private posts
					array(
						'key'     => $key,
						'value'   => 1,
						'compare' => '!=',
					),
				)
			);
		}
	}
}