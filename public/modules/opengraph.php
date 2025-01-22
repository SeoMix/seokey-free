<?php
/**
 * Load OpenGraph generator
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


$option = seokey_helper_get_option('seooptimizations-opengraph');
// Option is active, continue
if ( 1 !== (int) $option ) {
	return;
}

// Deactivate if concurrent plugin is active
$active_plugins = (array) get_option( 'active_plugins', array() );
if ( is_multisite() ) {
	$multisite_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
	if ( $multisite_plugins ) { $active_plugins = array_merge( $active_plugins, $multisite_plugins );}
}
sort( $active_plugins );
$active_plugins = array_unique( $active_plugins );
// plugin list based on JetPack similar function (in their function check_open_graph())
$open_graph_conflicting_plugins = array(
    '2-click-socialmedia-buttons/2-click-socialmedia-buttons.php',  // 2 Click Social Media Buttons
    'add-link-to-facebook/add-link-to-facebook.php',                // Add Link to Facebook
    'add-meta-tags/add-meta-tags.php',                              // Add Meta Tags
    'complete-open-graph/complete-open-graph.php',                  // Complete Open Graph
    'easy-facebook-share-thumbnails/esft.php',                      // Easy Facebook Share Thumbnail
    'heateor-open-graph-meta-tags/heateor-open-graph-meta-tags.php', // Open Graph Meta Tags by Heateor
    'facebook/facebook.php',                                        // Facebook (official plugin)
    'facebook-awd/AWD_facebook.php',                                // Facebook AWD All in one
    'facebook-featured-image-and-open-graph-meta-tags/fb-featured-image.php', // Facebook Featured Image & OG Meta Tags
    'facebook-meta-tags/facebook-metatags.php',                     // Facebook Meta Tags
    'wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php', // Facebook Open Graph Meta Tags for WordPress
    'facebook-revised-open-graph-meta-tag/index.php',               // Facebook Revised Open Graph Meta Tag
    'facebook-thumb-fixer/_facebook-thumb-fixer.php',               // Facebook Thumb Fixer
    'facebook-and-digg-thumbnail-generator/facebook-and-digg-thumbnail-generator.php', // Fedmich's Facebook Open Graph Meta
    'network-publisher/networkpub.php',                             // Network Publisher
    'nextgen-facebook/nextgen-facebook.php',                        // NextGEN Facebook OG
    'social-networks-auto-poster-facebook-twitter-g/NextScripts_SNAP.php', // NextScripts SNAP
    'og-tags/og-tags.php',                                          // OG Tags
    'opengraph/opengraph.php',                                      // Open Graph
    'open-graph-protocol-framework/open-graph-protocol-framework.php', // Open Graph Protocol Framework
    'seo-facebook-comments/seofacebook.php',                        // SEO Facebook Comments
    'seo-ultimate/seo-ultimate.php',                                // SEO Ultimate
    'sexybookmarks/sexy-bookmarks.php',                             // Sexy Bookmarks
    'shareaholic/sexy-bookmarks.php',                               // Shareaholic
    'sharepress/sharepress.php',                                    // SharePress
    'simple-facebook-connect/sfc.php',                              // Simple Facebook Connect
    'social-discussions/social-discussions.php',                    // Social Discussions
    'social-sharing-toolkit/social_sharing_toolkit.php',            // Social Sharing Toolkit
    'socialize/socialize.php',                                      // Socialize
    'squirrly-seo/squirrly.php',                                    // SEO by SQUIRRLY
    'only-tweet-like-share-and-google-1/tweet-like-plusone.php',    // Tweet, Like, Google +1 and Share
    'wordbooker/wordbooker.php',                                    // Wordbooker
    'wpsso/wpsso.php',                                              // WordPress Social Sharing Optimization
    'wp-caregiver/wp-caregiver.php',                                // WP Caregiver
    'wp-facebook-like-send-open-graph-meta/wp-facebook-like-send-open-graph-meta.php', // WP Facebook Like Send & Open Graph Meta
    'wp-facebook-open-graph-protocol/wp-facebook-ogp.php',          // WP Facebook Open Graph protocol
    'wp-ogp/wp-ogp.php',                                            // WP-OGP
    'zoltonorg-social-plugin/zosp.php',                             // Zolton.org Social Plugin
    'wp-fb-share-like-button/wp_fb_share-like_widget.php',          // WP Facebook Like Button
    'open-graph-metabox/open-graph-metabox.php',                    // Open Graph Metabox
    'seo-by-rank-math/rank-math.php',                               // Rank Math
    'slim-seo/slim-seo.php',                                        // Slim SEO
    // added by SEOKEY
    'og/og.php',                                                    // OG — Better Share on Social Media
    'jetpack-social/jetpack-social.php',                            // JetPack Social
);
if ( ! empty( $active_plugins ) ) {
	foreach ( $open_graph_conflicting_plugins as $plugin ) {
		if ( in_array( $plugin, $active_plugins, true ) ) {
			return;
		}
	}
}
$twitter_cards_conflicting_plugins = array(
	'twitter/twitter.php',                       // Twitter plugin
	'eewee-twitter-card/index.php',              // Eewee Twitter Card.
	'ig-twitter-cards/ig-twitter-cards.php',     // IG:Twitter Cards.
	'jm-twitter-cards/jm-twitter-cards.php',     // JM Twitter Cards.
	'kevinjohn-gallagher-pure-web-brilliants-social-graph-twitter-cards-extention/kevinjohn_gallagher___social_graph_twitter_output.php',  // Pure Web Brilliant's Social Graph Twitter Cards Extension.
	'twitter-cards/twitter-cards.php',           // Twitter Cards.
	'twitter-cards-meta/twitter-cards-meta.php', // Twitter Cards Meta.
	'wp-to-twitter/wp-to-twitter.php',           // WP to Twitter.
	'wp-twitter-cards/twitter_cards.php',        // WP Twitter Cards.
);
if ( ! empty( $active_plugins ) ) {
	foreach ( $twitter_cards_conflicting_plugins as $plugin ) {
		if ( in_array( $plugin, $active_plugins, true ) ) {
			return;
		}
	}
}

// We can continue, add our data
add_action( 'seokey_action_head', 'seokey_head_data_opengraph');
/**
 * Add OpenGraph and Twitter Card data in <head>
 *
 * @author Daniel Roch
 * @since  2.0.0
 *
 * @hook seokey_action_head
 * @hook wp_head
 */
function seokey_head_data_opengraph() {
	// Define variables
	$data               = $thumbdata = [];
	$settings_option    = seokey_helper_get_option( 'schemaorg-context' );

	// OpenGraph and Twitter URL
	$data['og:url']             = seokey_helper_url_get_current( false );
	$datatwitter['twitter:url'] = $data['og:url'];

	// OpenGraph Content type
	// Default value
	$data['og:type'] = 'article';
	// Website value
	if ( is_front_page() ) {
		$data['og:type'] = 'website';
	}
	// Profile value and extra profile data
	elseif( is_author() ) {
		$data['og:type']    = 'profile';
		$user_id            = get_query_var('author');
		$user_raw_meta_data = get_user_meta( $user_id );
		if ( ! empty( $user_raw_meta_data['first_name'][0] ) ) {
			$data['og:first_name'] = esc_attr( $user_raw_meta_data['first_name'][0] );
		}
		if ( ! empty( $user_raw_meta_data['last_name'][0] ) ) {
			$data['og:last_name'] = esc_attr( $user_raw_meta_data['last_name'][0] );
		}
	}

	// Opengraph and twitter card metas
	$data['og:title']                   = esc_attr( seokey_head_meta_title() );
	$data['og:description']             = esc_attr( seokey_head_meta_description() );
	$datatwitter['twitter:title']       = $data['og:title'];
	$datatwitter['twitter:description'] = $data['og:description'];

	// OpenGraph Images
	$ogimage = false;
	// Images for Post Types
	if ( is_singular() ) {
		global $post;
		$ogimage = get_the_post_thumbnail_url( $post );
		if ( false !== $ogimage ) {
			$data['og:image']               = esc_url( $ogimage );
			$data['og:image:secure_url']    = $data['og:image'];
			$datatwitter['twitter:image']   = $data['og:image'];
			$thumbid                        = get_post_thumbnail_id( $post );
			$thumbalt                       = get_post_meta( $thumbid, '_wp_attachment_image_alt', true );
			if ( false !== $thumbalt ) {
				$data['og:image:alt'] = esc_attr( $thumbalt );
			}
			// for later use
			$thumbdata = wp_get_attachment_metadata( $thumbid );
		}
	}
	//No image, switch to main SEOKEY settings
	if ( false === $ogimage ) {
		if ( ! empty( $settings_option ) ) {
			switch ( $settings_option ) {
				case 'person':
					$ogimage = seokey_helper_get_option( 'schemaorg-schema-person-image' );
					$option  = seokey_helper_get_option( 'schemaorg-schema-person' );
					break;
				case 'local_business':
					$ogimage = seokey_helper_get_option( 'schemaorg-schema-local_business-image' );
					$option  = seokey_helper_get_option( 'schemaorg-schema-local_business' );
					break;
			}
			if ( ! empty( $ogimage ) ) {
				$data['og:image']               = esc_url( $ogimage );
				$data['og:image:secure_url']    = $data['og:image'];
				$datatwitter['twitter:image']   = $data['og:image'];
				if ( ! empty( $option['name'] ) ) {
					$data['og:image:alt'] = esc_attr( $option['name'] );
				}
				// for later use
				$thumbdata                      = wp_get_attachment_metadata( attachment_url_to_postid( $data['og:image'] ) );
			}
		}
	}
	// We have data about an image, add width and height open graph data
	if ( !empty( $thumbdata ) ) {
		if ( !empty( $thumbdata['width'] ) ) {
			$data['og:image:width'] = (int) $thumbdata['width'];
		}
		if ( !empty( $thumbdata['height'] ) ) {
			$data['og:image:height'] = (int) $thumbdata['height'];
		}
	}

	// OpenGraph Content extra data
	if ( is_singular() && !is_front_page() && !is_home() ) {
		// OpenGraph Author for this content
		global $post;
		$author_display_name            = get_the_author_meta( 'display_name', $post->post_author );
		$data['article:author']         = esc_attr( $author_display_name );
		$datatwitter['twitter:creator'] = $data['article:author'];

		// OpenGraph Publisher for this content
		if ( ! empty( $settings_option ) ) {
			// TODO later do it for companies
			if ( 'person' === $settings_option ) {
				$sameas_option = seokey_helper_get_option( 'schemaorg-schema-person-sameas' );
			}
			// There is data
			if ( ! empty( $sameas_option ) ) {
				foreach ( $sameas_option as $url ) {
					// Check if Twitter or X URL
					$username = seokey_helper_metas_get_facebook_URL( $url );
					if ( $username ) {
						$data['article:publisher']       = $username;
						break;
					}
				}
			}
		}
	}

	// Open Graph Section
	if ( is_tax() || is_tag() || is_category() ) {
		$data['article:section'] = esc_attr( single_term_title( '', false ) );
	} elseif ( is_singular() && !is_home() ) {
		// Terms selected for this post type
		$post_type  = get_post_type();
		$taxonomies = get_object_taxonomies( $post_type );
		if ( ! empty( $taxonomies ) ) {
			$choice     = seokey_helper_get_option( 'cct-taxonomy-choice-' . $post_type );
			$taxonomy   = ( empty( $choice) ) ? $taxonomies[0] : $choice;
			$terms      = seokey_breacrumbs_data_get_taxonomy_terms( get_the_ID(), array(), $taxonomy );
			// Do we have terms ?
			if ( ! empty( $terms ) ) {
				$term_data = get_term_by( 'id', $terms[0], $taxonomy );
				$data['article:section'] = esc_attr( $term_data->name );
			}
		}
	}

	// Time, it is time !
	if ( is_singular() && !is_front_page() ) {
		$data['og:published_time'] = esc_attr( get_the_date( 'Y-m-d\TH:i:sP', false ) );
		$data['og:updated_time']   = esc_attr( get_the_modified_date( 'Y-m-d\TH:i:sP', false ) );
	}

	// OpenGraph Language
	$data['og:locale'] = esc_attr( get_locale() );

	// OpenGraph Website name
	$data['og:site_name'] = esc_attr( get_bloginfo('name') );

	/**
	 * Filters OpenGraph Data
	 *
	 * @since 2.0.0
	 *
	 * @param array  $data   All opengraph data generated for this URL
	 */
	$data = apply_filters( 'seokey_filter_head_data_opengraph', $data );
	// Add all OpenGraph data to <head>
	foreach ( $data as $key => $opengraph ) {
		echo '<meta property="' . $key . '" content="' . esc_attr( $opengraph ) . '">' . "\n";
	}

	// TwitterCard Data global
	$datatwitter['twitter:card']       = 'summary_large_image';

	// TwitterCard Site Account for website
	if ( ! empty( $settings_option ) ) {
		// TODO later do it for companies
		if ( 'person' === $settings_option ) {
			$sameas_option = seokey_helper_get_option( 'schemaorg-schema-person-sameas' );
		}
		// There is data
		if ( ! empty( $sameas_option ) ) {
			foreach ( $sameas_option as $url ) {
				// Check if Twitter or X URL
				$username = seokey_helper_metas_get_twitter_username( $url );
				if ( $username ) {
					$datatwitter['twitter:site']       = $username;
					break;
				}
			}
		}
	}

	/**
	 * Filters Twitter Card Data
	 *
	 * @since 2.0.0
	 *
	 * @param array  $data   All Twitter Card data generated for this URL
	 */
	$datatwitter = apply_filters( 'seokey_filter_head_data_twitter', $datatwitter );
	// Add all Twitter card data to <head>
	foreach ( $datatwitter as $key => $twittercard ) {
		echo '<meta name="' . $key . '" content="' . esc_attr( $twittercard ) . '">' . "\n";
	}
}

/**
 * Check if URL is a X or Twitter URL
 *
 * @author Daniel Roch
 * @since  2.0.0
 */
function seokey_helper_metas_get_twitter_username( $url ) {
	// Cleaning data
	$url = esc_url( trim( $url ) );
	// Get host
	$host = parse_url( $url, PHP_URL_HOST );
	// Check if Twitter or X host
	$valid_domains = ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'];
	if ( !in_array( $host, $valid_domains ) ) {
		return false;
	}
	// This is a Twitter or X Host, extract data
	$username = trim( parse_url( $url, PHP_URL_PATH), '/ ');
	// Return Result
	return ( !empty ( $username) ) ? $username : false;
}

/**
 * Check if URL is a Facebook URL
 *
 * @author Daniel Roch
 * @since  2.0.0
 */
function seokey_helper_metas_get_facebook_URL( $url ) {
	// Cleaning data
	$url = esc_url( trim( $url ) );
	// Get host
	$host = parse_url( $url, PHP_URL_HOST );
	// Check if Twitter or X host
	$valid_domains = [ 'facebook.com', 'www.facebook.com' ];
	if ( !in_array( $host, $valid_domains ) ) {
		return false;
	}
	return $url;
}