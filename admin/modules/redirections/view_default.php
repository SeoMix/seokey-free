<?php
/**
 * Admin Redirection module : view redirection list and form
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
    <?php esc_html_e ('Add a redirection', 'seo-key'); ?>
</h2>
<?php include_once plugin_dir_path( __FILE__ ) . 'form.php'; ?>

<h2>
    <?php esc_html_e('Active redirections', 'seo-key'); ?>
</h2>
<p>
    <?php esc_html_e ( 'Find below all your current manual and active redirections:', 'seo-key');?>
</p>

<?php
    echo seokey_helper_loader( 'redirection');
    // Display redirection list
    seokey_redirections_display_default();