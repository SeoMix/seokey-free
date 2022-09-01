<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Admin-Notices class.
 *
 * Handles creating Notices and printing them.
 *
 * @package   WPTRT/admin-notices
 * @author    WPTRT <themes@wordpress.org>
 * @copyright 2019 WPTRT
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/WPTRT/admin-notices
 */

namespace SEOKEYWPTRT\AdminNotices;

/**
 * The Admin_Notice class, responsible for creating admin notices.
 *
 * Each notice is a new instance of the object.
 *
 * @since 1.0.0
 */
class Notices {

	/**
	 * An array of notices.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $notices = [];

	/**
	 * Adds actions for the notices.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function boot() {
		// Print the script to the footer.
		add_action( 'admin_footer', [ $this, 'print_scripts'], 1 );
		add_action( 'current_screen', [ $this, 'boot_later' ] );
	}
	
	public function boot_later() {
		// Add our notification at the right place on classic admin pages
		if ( false === seokey_helpers_is_admin_pages() ||
		     true === seokey_helpers_admin_is_post_type_archive() ||
		     true === seokey_helpers_medias_library_is_alt_editor() ||
		     true === seokey_helpers_redirections_is_redirect_editor()
		) {
			// Trigger our notices on all admin pages except our custom pages
			add_action( 'in_admin_header', [ $this, 'the_notices' ], PHP_INT_MAX );
		}
		// Add the notice with our custom action (only on our full custom admin pages)
		else {
			add_action( 'seokey_action_admin_pages_wrapper_print_notices', [ $this, 'the_notices' ], PHP_INT_MAX );
		}
	}

	/**
	 * Add a notice.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $id      A unique ID for this notice. Can contain lowercase characters and underscores.
	 * @param string $title   The title for our notice.
	 * @param string $message The message for our notice.
	 * @param array  $options An array of additional options to change the defaults for this notice.
	 *                        See Notice::__constructor() for details.
	 * @return void
	 */
	public function add( $id, $title, $message, $options = [] ) {
		$this->notices[ $id ] = new Notice( $id, $title, $message, $options );
	}

	/**
	 * Remove a notice.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $id The unique ID of the notice we want to remove.
	 * @return void
	 */
	public function remove( $id ) {
		unset( $this->notices[ $id ] );
	}

	/**
	 * Get a single notice.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $id The unique ID of the notice we want to retrieve.
	 * @return Notice|null
	 */
	public function get( $id ) {
		if ( isset( $this->notices[ $id ] ) ) {
			return $this->notices[ $id ];
		}
		return null;
	}

	/**
	 * Get all notices.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_all() {
		return $this->notices;
	}

	/**
	 * Prints the notice.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function the_notices() {
		$notices = $this->get_all();
		foreach ( $notices as $notice ) {
			$notice->the_notice();
		}
	}

	/**
	 * Prints scripts for the notices.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function print_scripts() {
		$notices = $this->get_all();
		if ( !empty ($notices) ) {
			$this->enqueue_print_scripts();
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_print_scripts'], SEOKEY_PHP_INT_MAX );
		}

	}
	
	
	/**
	 * Prints scripts for the notices.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function enqueue_print_scripts() {
		wp_enqueue_script(  'seokey-js-notifications', SEOKEY_URL_ASSETS . 'js/seokey-notifications.js', array( 'jquery' ), SEOKEY_VERSION, TRUE );
		wp_localize_script( 'seokey-js-notifications', 'seokey_notifications',
			[
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'security'  => wp_create_nonce( 'seokey_notifications_dismiss' ),
			]
		);
	}
}
