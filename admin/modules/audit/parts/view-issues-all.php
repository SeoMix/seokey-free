<?php
/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Get tasks for current tab
$data = seokey_audit_view_tab_tasks();

// Display data for each issue type
foreach ( $data as $task ) {
    seokey_audit_tab_content( $task );
}

$text = esc_html__( 'Want more SEO advice? Go PRO!', 'seo-key' );
$text .= __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY PRO</a>", 'seo-key' );

echo '<div class="task task-gopro">';
	echo '<header class="seokey-tooltip-parent">';
	echo $text;
	echo '</header>';
echo '</div>';


if ( empty( $data ) ) {
    echo '<p>';
    esc_html_e('No issue: congratulations!', 'seo-key');
    echo '</p>';
}