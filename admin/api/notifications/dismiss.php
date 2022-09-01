<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Handles dismissing admin notices.
 *
 * @package   WPTRT/admin-notices
 * @author    WPTRT <themes@wordpress.org>
 * @copyright 2019 WPTRT
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/WPTRT/admin-notices
 */

namespace SEOKEYWPTRT\AdminNotices;

/**
 * The Dismiss class, responsible for dismissing and checking the status of admin notices.
 *
 * @since 1.0.0
 */
class Dismiss {

	/**
	 * The notice-ID.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $id;

	/**
	 * The prefix we'll be using for the option/user-meta.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $prefix;

	/**
	 * The notice's scope. Can be "user" or "global".
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $scope;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $id     A unique ID for this notice. Can contain lowercase characters and underscores.
	 * @param string $prefix The prefix that will be used for the option/user-meta.
	 * @param string $scope  Controls where the dismissal will be saved: user or global.
	 */
	public function __construct( $id, $prefix, $scope = 'global' ) {
		// Set the object properties.
		$this->id     = sanitize_key( $id );
		$this->prefix = sanitize_key( $prefix );
		$this->scope  = ( in_array( $scope, ['global', 'user'], true ) ) ? $scope : 'global';
		// Handle AJAX requests to dismiss the notice.
		add_action( 'wp_ajax_seokey_dismiss_notice', [ $this, 'ajax_maybe_dismiss_notice'] );
	}

	/**
	 * Check if the notice has been dismissed or not.
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function is_dismissed() {
		// Check if the notice has been dismissed when using user-meta.
		if ( 'user' === $this->scope ) {
			return ( get_user_meta( get_current_user_id(), "{$this->prefix}_{$this->id}", true ) );
		}

		return ( get_option( "{$this->prefix}_{$this->id}" ) );
	}

	/**
	 * Run check to see if we need to dismiss the notice.
	 * If all tests are successful then call the dismiss_notice() method.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function ajax_maybe_dismiss_notice() {
		// Security : Nonce
		check_admin_referer( 'seokey_notifications_dismiss', 'security' );
		// Security : check if user is administrator
		if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
			wp_send_json_error();
			die;
		}
		if ( !isset( $_GET['id'] ) || 'seokey_dismiss_notice' !== $_GET['action'] ) {
			wp_send_json_error('error');
		}
		// Get a clean ID slug and dismiss notice
		$this->dismiss_notice( sanitize_title( $_GET['id'] ) );
	}

	/**
	 * Actually dismisses the notice.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function dismiss_notice( $id ) {
		if ( 'user' === $this->scope ) {
			update_user_meta( get_current_user_id(), "{$this->prefix}_{$id}", true );
			wp_send_json_success($id);
			return;
		}
		update_option( "{$this->prefix}_{$id}", true, false );
		wp_send_json_success($id);
	}
}
