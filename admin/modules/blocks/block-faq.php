<?php
/**
 * FAQ block
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
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

add_action( 'enqueue_block_editor_assets', 'seokey_blocks_faq_assets' );
// Import block built JS and translations
function seokey_blocks_faq_assets() {
    wp_enqueue_script(
        // the name - also generally {namespace/blockname}
        'seokey-blocks-faq',
        // where the javscript is located
        SEOKEY_URL_ASSETS . 'js/build/bloc-faq/seokey-blocks-faq.js',
        // and dependencies WordPress needs to serve up for us
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' )
    );
	wp_set_script_translations( 'seokey-blocks-faq', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
    // Import CSS for editor
    wp_register_style( 'seokey-blocks-faq-admin-style', SEOKEY_URL_ASSETS . 'css/blocks/seokey-blocks-faq-admin.css' );
    wp_enqueue_style( 'seokey-blocks-faq-admin-style' );
};

add_action( 'enqueue_block_assets', 'seokey_blocks_enqueue_faq_style', PHP_INT_MAX );
// Import CSS for front
function seokey_blocks_enqueue_faq_style() {
	if ( has_block( 'seokey/faq-block' ) ) {
		wp_enqueue_style( 'seokey-blocks-faq-front-style', SEOKEY_URL_ASSETS . 'css/blocks/seokey-blocks-faq-front.css', false, SEOKEY_VERSION );
	}
}

add_action( 'admin_enqueue_scripts', 'seokey_blocks_enqueue_faq_scripts' );
function seokey_blocks_enqueue_faq_scripts( ) {
	global $pagenow;
	// Only execute if we are on page creation or edition
	if ( ( $pagenow == 'post.php' ) || ( $pagenow == 'post-new.php' ) ) {
		// JS for replacing other FAQ blocks with the SeoKey one
		wp_enqueue_script( 'seokey-blocks-faq-import', esc_url( SEOKEY_URL_ASSETS . 'js/build/bloc-faq/import/seokey-blocks-faq-import.js' ), array( 'jquery', 'wp-i18n' ) );
		wp_set_script_translations( 'seokey-blocks-faq-import', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
	}
}

add_action( 'wp_head', 'seokey_blocks_schema_for_faq' );
// Add schema.org FAQ schema if the FAQ block is on page
function seokey_blocks_schema_for_faq(){
    // Check if FAQ block is here
    if ( has_block( 'seokey/faq-block' ) ) {
		global $post;
        // Get all blocks
		$blocks = parse_blocks( $post->post_content );
        // Prepare the schema
		$schema_faq = array(
			'@context'  => "http://schema.org",
			'@type'     => "FAQPage",
			'mainEntity' => array()
		);
		foreach ( $blocks as $block ) {
            // Retrieve SEOKEY FAQ block(s)
			if ( 'seokey/faq-block' === $block['blockName'] ) {
                // For each question/response, add it to the schema
				foreach ( $block['attrs']['faq'] as $qr ) {
					$qr['response'] = str_replace( '<br>', ' ', $qr['response'] ); // Clean <br> from RichText
					$object         = array (
						'@type' => 'Question',
						'name'  => esc_html( $qr['question'] ),
						'acceptedAnswer' => array (
							'@type' => 'Answer',
							'text'  => esc_html( $qr['response'] ),
						)
					);
					array_push( $schema_faq['mainEntity'], $object );
				}
			}
		}
		// Filter if we need to customise the main entity
		$schema_faq['mainEntity'] = apply_filters( 'seokey_filter_blocks_faq_schema', $schema_faq['mainEntity'] );
        // If we have at least one question/response, put the schema in the <head>
		if ( !empty( $schema_faq['mainEntity'] ) ) echo '<script type="application/ld+json">' . json_encode( $schema_faq ) . '</script>';
    }
}