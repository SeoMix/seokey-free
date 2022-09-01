<?php
/**
 * Automatic Optimization content
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
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

/**
° Automatic optimization header display
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_action_setting_sections_after
° @param integer $id_section Current section ID
° @return void
 */
function seokey_automatic_optimizations_top() {
	$current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
	if ( 'goodtogo' !== $current_wizard ) {
		echo '<p>' . esc_html__( 'SEOKEY is way simpler than any other SEO plugin. Just click on the button below to fix your SEO!', 'seo-key' ) . '</p>';
		echo '<div id="wizard-choices">';
		echo '<button class="button button-primary button-large button-hero" id="automatic_optimizations_list_button">' . esc_html__( 'Fix all my WordPress', 'seo-key' ) . '</button>';
		echo '<button class="button button-secondary button-large button-hero" id="automatic_optimizations_list_button_choices">' . esc_html__( 'Let me choose', 'seo-key' ) . '</button>';
		echo '</div>';
		echo seokey_helper_loader( 'automatic-optimizations' );
	}
}

/**
° Automatic optimization list display
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_action_setting_sections_after
° @param integer $id_section Current section ID
° @return void
 */
function seokey_automatic_optimizations() {
	echo '<p id="automatic_optimizations_list_text">' . esc_html__( 'SEOKEY will automatically optimize the rest of your WordPress site: meta tags, pings, speed, sitemaps, crawl...', 'seo-key' ) . '</p>';
	echo '<button class="button button-primary button-large button-hero" id="automatic_optimizations_list_button2">' . esc_html__( 'Fix my WordPress', 'seo-key' ) . '</button>';
	// Data
	$checked = '';
	$class = 'automatic_optimizations_item';
	$class_span = 'automatic_optimization_span';
	echo '<section id="automatic_optimizations_list" data-version="' . SEOKEY_VERSION . '">';
		echo '<section id="automatic_optimizations_list_items" class="nohidemanual" data-settings="' . $checked . '">';
		foreach ( seokey_automatic_optimizations_list() as $version ) {
			foreach ( $version as $item => $description ) {
				echo '<div class="' . $class . '">
	                <input class="automatic_optimization_input" type="checkbox"'. $checked .'>
	                <label>' . key( $description ) . '</label> <span class="' . $class_span . '">' . $description[key( $description )] . '</span>
	            </div>';
			}
		}
	foreach ( seokey_automatic_optimizations_list_manual() as $version ) {
		foreach ( $version as $item => $description ) {
			echo '<div class="' . $class . ' hideifnecessary">
	                <input class="automatic_optimization_input" type="checkbox"'. $checked .'>
	                <label>' . $item . '</label> <span class="' . $class_span . '">' . $description . '</span>
	            </div>';
		}
	}
	echo '</section>';
}

/**
° Automatic optimization list
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_action_setting_sections_after
° @param integer $id_section Current section ID
° @return void
 */
function seokey_automatic_optimizations_list(){
	$array[] = [
        'Titles and meta descriptions'      => [
			__( 'Titles and meta descriptions', 'seo-key' ) => __( "No more %%title%% option: SEOKEY already create default optimized titles and meta descriptions", 'seo-key' ),
	        ],
        'Date Archive'                      => [
	        __( 'Date Archive', 'seo-key' )  => __( "We disabled date archives: you will have less poor content for Google", 'seo-key' ),
        ],
        'Header cleaning'                   => [
		        __( 'Header cleaning', 'seo-key' )  => __( "We removed useless content on the HTML head of your pages", 'seo-key' ),
        ],
        'Custom post types archive page'    => [
		        __( 'Custom post types archive page', 'seo-key' ) => __( "If you have custom Post Types with an archive page, we've added an admin page to help you configure them", 'seo-key' ),
        ],
        'Login Page'                        => [
			__( 'Login Page', 'seo-key' ) => __( "We cleaned content on your login page", 'seo-key' ),
        ],
        'Medias'                            => [
			__( 'Medias', 'seo-key' ) => __( "No more harmful attachment pages (and links to them)", 'seo-key' ),
        ],
        'Pings'                             => [
			__( 'Titles and meta descriptions', 'seo-key' ) => __( "No more harmful pings", 'seo-key' ),
        ],
        'Writing'                           => [
			__( 'Writing', 'seo-key' ) => __( "We removed bad options from both Gutenberg and TinyMCE editors", 'seo-key' ),
        ],
        'Robots.txt file'                   => [
			__( 'Robots.txt file', 'seo-key' ) => __( "An improved robots.txt has been created", 'seo-key' ),
        ],
        'Improved RSS feeds'                => [
			__( 'Improved RSS feeds', 'seo-key' ) => __( "We cleaned your RSS feeds: no more duplicate content or useless RSS feeds", 'seo-key' ),
        ],
        'Schema.org'                        => [
			__( 'Schema.org', 'seo-key' ) => __( "We've added schema.org markup to improve all of your contents", 'seo-key' ),
		],
        'WordPress sitemaps'                => [
			__( 'WordPress sitemaps', 'seo-key' ) => __( "We replaced native WordPress sitemaps with powerful custom sitemaps", 'seo-key' ),
        ],
        'User metas'                        => [
			__( 'User metas', 'seo-key' ) => __( "We added some new fields to each user: fill them to help Google understand your authors", 'seo-key' ),
        ],
        'Performance'                       => [
			__( 'Performance', 'seo-key' ) => __( "If you are using performance plugins such as WP-Rocket, we removed harmful 404 errors on old cached files", 'seo-key' ),
        ],
		// TODO Retirer ou corriger WIDGETS ?
		// __( 'Widgets', 'seo-key' )                          => __( "We removed harmful widgets", 'seo-key' ),
	];
    return apply_filters( 'seokey_filter_automatic_optimizations_list', $array );
}

/**
° Manual optimization list
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_action_setting_sections_after
° @param integer $id_section Current section ID
° @return void
 */
function seokey_automatic_optimizations_list_manual(){
	// TODO les effets de transition
	$array[] = [
			__( 'Pagination optimizations', 'seo-key' )         => __( "We improved pagination for better crawl management", 'seo-key' ),
			__( 'Comments cleaning', 'seo-key' )                => __( "No more harmful and duplicated content caused by your comments", 'seo-key' ),
			__( 'Secondary RSS feeds', 'seo-key' )              => __( "We removed your secondary RSS feeds: no more crawl for those useless URL", 'seo-key' ),
	];
	return $array;
}