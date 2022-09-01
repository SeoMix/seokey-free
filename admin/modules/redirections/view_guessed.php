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
    <?php esc_html_e('Automatic redirections', 'seo-key'); ?>
</h2>
<p>
    <?php
    esc_html_e( 'WordPress may create its own redirections, without warning you. You need to check and validate those redirections.', 'seo-key');
    ?>
</p>


<?php
    if ( false === seokey_helpers_is_free() ) {
        echo seokey_helper_loader( 'redirection');
    }
    // Guessed list
    seokey_redirections_display_guessed();