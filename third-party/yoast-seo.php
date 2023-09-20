<?php
/**
 * Third party: Plugi Yoast SEO
 *
 * @Loaded on plugins_loaded + wizard done
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

add_action ( 'template_redirect', 'seokey_thirdparty_yoast_sitemaps', 20000 );
/**
 * Redirect 404 sitemaps to SEOKEY main sitemap
 */
function seokey_thirdparty_yoast_sitemaps() {
	if ( is_404() ) {
		// Is it a Yoast sitemap
		if ( true === str_ends_with( seokey_helper_url_get_current(), '-sitemap.xml' ) ) {
			// User has defined good and bad content ?
			if ( ! empty(get_option('seokey-field-cct-cpt'))) {
				// SEOKEY Sitemaps index URL
				$custom_sitemaps = home_url( '/sitemap-index.xml') ;
				// Do you need another URL to redirect to ?
				$redirecturl = apply_filters( 'seokey_filter_sitemap_native_redirect', $custom_sitemaps, seokey_helper_url_get_current() );
			}
			// User has not defined good and bad content : sitemaps are not yet available
			else {
				$redirecturl = home_url();
			}
			wp_safe_redirect( esc_url( $redirecturl ), 301 );
			die;
		}
	}
}

/**
 * Add Yoast FAQ schema if Yoast block is still here
 *
 * @author  Arthur Leveque
 * @since   1.6.6
 */
if ( !is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
	add_action( 'wp_head', 'seokey_thirdparty_yoast_faq_blocks' );
	/**
	 * Add schema of Yoast FAQ block if Yoast is disabled
	 */
	function seokey_thirdparty_yoast_faq_blocks() {
		// Singular content ?
		if ( is_singular() ) {
			// Check if current content has Yoast faq blocks
			if ( has_block( 'yoast/faq-block' ) ) {
				global $post;
				$blocks = parse_blocks( $post->post_content ); // Get all blocks from content to get the FAQ blocks
				// Prepare the FAQ schema
				$schema_faq = array(
					'@context'   => "http://schema.org",
					'@type'      => "FAQPage",
					'mainEntity' => array()
				);
				foreach ( $blocks as $block ) {
					// Only take FAQ blocks
					if ( 'yoast/faq-block' === $block['blockName'] ) {
						// Go through all questions
						foreach ( $block['attrs']['questions'] as $qr ) {
							// Prepare the question/response
							$object = array(
								'@type'          => 'Question',
								'name'           => seokey_helpers_data_clean_escaped_html( $qr['jsonQuestion'] ),
								'acceptedAnswer' => array(
									'@type' => 'Answer',
									'text'  => seokey_helpers_data_clean_escaped_html( $qr['jsonAnswer'] ),
								)
							);
							// Push the question/response to the schema
							array_push( $schema_faq['mainEntity'], $object );
						}
					}
				}
				// If there is any question/response, add it to <head>
				if ( ! empty( $schema_faq['mainEntity'] ) ) {
					echo '<script type="application/ld+json">' . json_encode( $schema_faq ) . '</script>';
				}
			}
		}
	}
}