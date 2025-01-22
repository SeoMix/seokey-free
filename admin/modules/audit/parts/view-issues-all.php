<?php
/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Get tasks for current tab
$data = seokey_audit_view_tab_tasks();
// Then display data for each issue type
foreach ( $data as $task ) {
     seokey_audit_tab_content( $task );
}

// No issue, nice!
if ( empty( $data ) ) {
    echo '<p class="noissues">' . esc_html__('No issue detected in our limited free audit: congratulations!', 'seo-key') . '</p>';
}

// Display PRO buy button
$text = esc_html__( 'Want more SEO advice? Go PRO!', 'seo-key' );
$text .= __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY PRO</a>", 'seo-key' );
echo '<div class="task task-gopro">
    <div class="seokey-tooltip-parent">' . $text . '</div>
</div>';