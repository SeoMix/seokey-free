<?php
/**
 * Admin Redirection module : main file for admin redirection page
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
}

// Tell notices where they should appear...
// See https://github.com/WordPress/wordpress-develop/blob/78f451030b75a5c55c6cc1a4dae5b833ce9e003e/src/js/_enqueues/admin/common.js#L1083
echo '<div class="wp-header-end"></div>';

// Tab nav
echo seokey_redirections_display_nav_tabs();
$type = ( ! empty( $_GET['tab']  ) ) ? sanitize_title( $_GET['tab'] ) : 'default';
?>
<section class="seokey-redirections-tools">
    <?php
        // Redirection list and form
        switch( $type ) {
            // Redirection errors
            case "errors":
                include_once plugin_dir_path( __FILE__ ) . 'view_errors.php';
                break;
            // Automatic guessed redirections
            case "guessed":
                include_once plugin_dir_path( __FILE__ ) . 'view_guessed.php';
                break;
            default:
                include_once plugin_dir_path( __FILE__ ) . 'view_default.php';
                break;
        }
    ?>
</section>