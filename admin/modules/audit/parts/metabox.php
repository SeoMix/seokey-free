<?php
/**
 * Content Metabox for SEO Analysis
 *
 * @see     audit.php
 * @package SEOKEY
 */


/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

?>
<section id="seokey-content-audit">
    <a href="<?php echo( esc_url( seokey_helper_admin_get_link('audit') ) );?>" id="seokey-audit-button" class="button button-primary button-hero">
        <?php esc_html_e( 'Go to main audit', 'seo-key' );?>
    </a>
    <p class="description">
        <?php esc_html_e( 'SEO audit of this content:', 'seo-key' ); ?>
    </p>
    <p>
		<?php esc_html_e( "Here are SEOKEY's tips to gain visibility.", 'seo-key' ); ?>
    </p>
    <?php
    // Audit
    seokey_helper_loader('audit-content');
    echo '<div id="seokey-audit-content-optimisations">' . esc_html__( 'No data yet', 'seo-key') . '</div>';
    ?>
</section>