<?php
/**
 * SEOKEY Notification
 *
 * @Loaded on plugins_loaded + is_admin()
 * @see seokey_plugin_init()
 * @see https://github.com/WPTT/admin-notices
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}

// Load core notifications files and functions
seokey_helper_require_file( 'notice',   SEOKEY_PATH_ADMIN . 'api/notifications/', 'contributor' );
seokey_helper_require_file( 'notices',  SEOKEY_PATH_ADMIN . 'api/notifications/', 'contributor' );
seokey_helper_require_file( 'dismiss',  SEOKEY_PATH_ADMIN . 'api/notifications/', 'contributor' );
use SEOKEYWPTRT\AdminNotices\Notices;

add_filter ( 'seokey_admin_notices_allowed_html', 'seokey_admin_notices_allowed_tags' );
/**
 * Allow more tags in admin notification messages
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @param array $args List of current allowed tags in SEOKEY Notifications
 * @return array $args Updated List of allowed tags in SEOKEY Notifications
 */
function seokey_admin_notices_allowed_tags( $tags ){
    $new_tags = [
	    'button'      => [
		    'class' => [],
            'id' => [],
	    ],
        'ul'      => [],
        'li'      => [],
    ];
    $tags = array_merge( $new_tags, $tags );
    return $tags;
}

add_action('in_admin_header',  'seokey_admin_notices_remove_others', SEOKEY_PHP_INT_MAX );
/**
 * Disable all admin notifications on our admin pages
 *
 * @since 0.0.1
 * @author Daniel Roch
 * @return void
 */
function seokey_admin_notices_remove_others() {
	// Check if it's one of our pages
	if ( true === seokey_helpers_is_admin_pages() ) {
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}
}

// needs admin init
add_action ( 'admin_init', 'seokey_admin_notices_launch', SEOKEY_PHP_INT_MAX );
/**
 * Load our notification API
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @return void
 */
function seokey_admin_notices_launch() {
    $seokey_notices_list = apply_filters( 'seokey_filter_admin_notices_launch', array() );
    $seokey_notices = new Notices();
    foreach ( $seokey_notices_list as $notice ) {
        $seokey_notices->add(
            $notice[0],
            $notice[1],
            $notice[2],
            $notice[3]
        );
    }
    $seokey_notices->boot();
}

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_notifications' );
/**
 * Enqueue assets (CSS) for Option Reading Menu
 *
 * @author  Daniel Roch
 * @uses    wp_enqueue_style()
 * @hook    admin_enqueue_scripts
 * @since   0.0.1
 */
function seokey_enqueue_admin_notifications() {
    // Enqueue settings CSS and JS
	wp_enqueue_style( 'seokey-notifications', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-notifications.css' ), false, SEOKEY_VERSION );
}

add_action ( 'current_screen', 'seokey_admin_notices_updated_settings', SEOKEY_PHP_INT_MAX );
/**
 * Display our custom updated settings message
 *
 * @author  Daniel Roch
 * @hook    current_screen
 * @since   0.0.1
 */
function seokey_admin_notices_updated_settings(){
	// Settings have been updated
	if ( isset( $_GET['settings-updated'] ) ) {
		// Only in SEOKEY pages
		add_action( 'seokey_filter_admin_notices_launch', 'seokey_admin_notices_updated_settings_content', 1, 1 );
		if ( true === seokey_helpers_is_admin_pages() ) {
			// But check if this is really our fullcustom pages
			if ( false === seokey_helpers_admin_is_post_type_archive() &&
	             false === seokey_helpers_medias_library_is_alt_editor() &&
	             false === seokey_helpers_redirections_is_redirect_editor()
			) {
				add_action( 'seokey_action_admin_pages_wrapper_print_notices', 'seokey_admin_notices_updated_settings_content' );
			}
		}
	}
}

/**
 * Echo our custom updated settings message content
 *
 * @author  Daniel Roch
 * @hook    current_screen
 * @since   0.0.1
 */
function seokey_admin_notices_updated_settings_content(){
	echo '<div id="seokey_updated_settings" class="seokey-notice notice-success">
		<span class="notice-icon"></span>
		<span class="notice-content">
			<h2 class="notice-title">' . esc_html__( "Settings updated", "seo-key" ) . '</h2>
			<p>' . esc_html__( "Your SEOKEY settings have been updated", "seo-key" ) . '</></p>
		</span>
	</div>';
}