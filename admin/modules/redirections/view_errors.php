<?php
/**
 * Admin Redirection module : view and deal with WordPress automatic redirections
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     common/seo-key-config.php
 * @see     admin/admin-menus/admin-links-menus.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}?>

<h2>
    <?php esc_html_e('404 errors detected', 'seo-key'); ?>
</h2>
<p>
    <?php
    esc_html_e( 'Google may have discovered 404 error pages on your website. You should fix them.', 'seo-key');
    ?>
</p>


<?php
    if ( false === seokey_helpers_is_free() ) {
        echo seokey_helper_loader( 'redirection');
    }
    // Error list
    seokey_redirections_display_error();