<?php
// TODO COMMENTS

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Tab nav
seokey_keywords_display_nav_tabs();
// Content
seokey_admin_keyword_menu_content_keywords();

$last_update = get_option( 'seokey-gsc-last-api-call' );
if ( false !== $last_update ) {
	$text = esc_html__( 'Want more SEO data? Go PRO!', 'seo-key' );
	$text .= __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY PRO</a>", 'seo-key' );
	echo '<span class="seokey-gopro">' . $text . '</span>';
}