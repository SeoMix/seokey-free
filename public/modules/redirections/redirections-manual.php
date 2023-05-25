<?php
/**
 * Manual redirections created by user
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
 * @see seokey_plugin_init()
 * @see public-modules.php
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

class Seokey_Redirections {
	/**
	 * Current URL
	 * @var
	 */
	protected $url;
	
	/**
	 * Seokey_Redirections constructor.
	 * @hook template_redirect
	 */
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'seokey_redirections_init'], 1, 0 );
	}
	
	/**
	 * Get Redirection types
	 */
	public function seokey_redirections_get_types() {
		$types = [
			'direct',
			// 'regex'
		];
		return $types;
	}
	/**
	 * Redirection init
	 */
	public function seokey_redirections_init() {
		// Get current URL without ending slash (no cache and no port to prevent errors on local)
		$url = seokey_helper_url_get_current( false, false );
		// Remove domain from URL
		$this->url = esc_url_raw( seokey_helpers_get_current_domain ( $url ) );
		// Get redirection types
		$types = $this->seokey_redirections_get_types();
		// Let's go to work
		foreach ( $types as $key ) {
			$this->seokey_redirections_check( sanitize_title( $key ) );
		}
	}
	
	/**
	 * Does this URL need to be redirected ?
	 *
	 * @param $type
	 * @return void|array
	 */
	public function seokey_redirections_check( $type = 'direct' ) {
		// Get WordPress redirections
		$redirections = $this->seokey_redirections_get_db( $type );
		if ( ! empty( $redirections ) ) {
			// We have data, keep going !
			foreach ( $redirections as $redirection ) {
				// Check redirection type
				switch ( $type ) {
					case 'direct':
						if ( $redirection->source === htmlspecialchars_decode( $this->url ) || $redirection->source === seokey_helper_url_remove_domain( htmlspecialchars_decode( $this->url ) ) ) {
							$this->seokey_redirections_execute_redirection( $redirection, 'direct' );
						}
						break;
					// TODO Later regex
					default:
						break;
					// End switch
				}
			}
		}
	}
	
	/**
	 * Get all redirected URL in our table
	 *
	 * @param $type
	 *
	 * @return array|bool|object|null
	 */
	public function seokey_redirections_get_db( $type ) {
		$urls = array( htmlspecialchars_decode( $this->url ), htmlspecialchars_decode( seokey_helper_url_remove_domain( $this->url ) ) );
		switch ( $type ) {
			case 'direct':
				$where = [
					'query' => "source IN (%s, %s)",
					'value' => $urls
				];
				break;
			// TODO Later regex
			default:
				break;
			// End switch
		}
		// Get results now
		global $wpdb;
		$query = $wpdb->prepare( "SELECT source, target, status, hits, id FROM {$wpdb->prefix}seokey_redirections WHERE " . $where['query'] , $where['value'] );
		$result = $wpdb->get_results( $query );
		// Return data
		return ( ! empty( $result ) ) ? $result : false;
	}
	
	/**
	 * Trigger redirects
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @param $redirection
	 */
	public function seokey_redirections_execute_redirection( $redirection, $type = 'direct' ) {
		if ( 'direct' === $type ) {
			// Get target
			$target = $redirection->target;
			// Update hits
			$this->seokey_redirections_data_update_hits( $redirection );
			// Redirect
			$status = ( $redirection->status ) ? (int) $redirection->status : (int) 301;
			wp_redirect( esc_url( $target ), $status );
			die();
		}
	}
	
	/**
	 * Update redirection hits
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @param $redirection
	 */
	public function seokey_redirections_data_update_hits( $redirection ) {
		// database stuff
		global $wpdb;
		$table = $wpdb->prefix . 'seokey_redirections';
		$data  = ['hits' => (int) ( $redirection->hits + 1 ), 'hits_last_at' => date( 'Y-m-d G:i:s' ) ];
		$where = ['id' => (int) $redirection->id ];
		$wpdb->update( $table, $data, $where, ['%d', '%s'], ['%d'] );
	}
}
// Launch me baby !
new Seokey_Redirections();