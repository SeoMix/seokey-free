<?php
/**
 * Detect and notify user that other SEO extensions are activated
 *
 * @Loaded on plugins_loaded + is_admin() + capability administrator
 * @see seokey_plugin_init()
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

class SeokeyCheckOtherExtensions {
	/**
	 * @var    (object) $instance Singleton
	 * @access public
	 * @static
	 */
	public static $instance = null;

	/**
	 * SEO plugins list
	 *
	 * @var array
	 */
	protected $plugins_to_check = [
		'all-in-one-seo-pack/all_in_one_seo_pack.php'                       => 'All in one SEO',
		'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'                   => 'All in one SEO PRO',
		'aioseo-local-business/aioseo-local-business.php'                   => 'All in one SEO PRO Local Business',
		'all-in-one-schemaorg-rich-snippets/index.php'                      => 'All in one schema',
		'autodescription/autodescription.php'                               => 'The SEO Framework',
		'boldgrid-easy-seo/boldgrid-easy-seo.php'                           => 'BoldGrid Easy SEO',
		'cds-simple-seo/cds-simple-seo.php'                                 => 'Simple SEO',
		'google-sitemap-generator/sitemaps.php'                             => 'Google sitemap generator',
		'platinum-seo-pack/platinum-seo-pack.php'                           => 'Platinum SEO Pack',
		'premium-seo-pack/index.php'                                        => 'Premium SEO Pack',
		// 'redirection/redirection.php'                                       => 'Redirection',
		'seo-by-10web/seo-by-10web.php'                                     => 'SEO by 10Web',
		'seo-by-rank-math/rank-math.php'                                    => 'RankMath',
		'seo-by-rank-math-pro/rank-math-pro.php'                            => 'RankMath PRO',
		'seo-simple-pack/seo-simple-pack.php'                               => 'SEO SIMPLE PACK',
		'slim-seo/slim-seo.php'                                             => 'Slim SEO',
		'squirrly-seo/squirrly.php'                                         => 'Squirrly SEO (Smart Strategy)',
		'visual-term-description-editor/visual-term-description-editor.php' => 'Visual Term Description Editor',
		'wordpress-seo/wp-seo.php'                                          => 'Yoast SEO',
		'wordpress-seo-premium/wp-seo-premium.php'                          => 'Yoast SEO Premium',
		'wpseo-woocommerce/wpseo-woocommerce.php'                           => 'Yoast SEO Premium WooCommerce',
		'wpseo-news/wpseo-news.php'                                         => 'Yoast SEO Premium Google News',
		// TODO Yoast Local
		// TODO Yoast Video
		'wp-meta-seo/wp-meta-seo.php'                                       => 'WP Meta SEO',
		'wp-searchconsole/wpsearchconsole.php'                              => 'WP Search Console',
		'wp-seo-keyword-optimizer/wsko.php'                                 => "Bavoko SEO Tools",
		'wp-seopress/seopress.php'                                          => 'SEOPress',
		'wp-seopress-pro/seopress-pro.php'                                  => 'SEOPress PRO',
		'xml-sitemap-feed/xml-sitemaps.php'                                 => 'XML sitemap Feed',
	];

	/**
	 * SEO plugins list with import function
	 * @notes use the exact sames values as $plugins_to_check
	 *
	 * @var array
	 */
	public $plugins_with_import = [
		'wordpress-seo/wp-seo.php'          => 'Yoast SEO',
		'seo-by-rank-math/rank-math.php'    => 'RankMath',
        'wp-seopress/seopress.php'          => 'SEOPress',
	];

	/**
	 * Already installed plugins
	 *
	 * @var array
	 */
	public $plugins_installed = [];

	/**
	 * Construct SEOKEY class
	 * Avoid launching concurrent objects
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public function __construct() {
		if( defined( 'DOING_AJAX' ) ) {
			return ;
		}
		// Get installed plugin list up and running
		$this->seokey_admin_check_other_plugins_init();
		// Add core functionnality to deactivate all other plugins
		add_action( 'admin_init', [ $this, 'seokey_admin_check_other_plugins_deactivate'], 10 );
        // Warn user and let him easily deactivate other plugins
		add_filter( 'seokey_filter_admin_notices_launch', [ $this, 'seokey_admin_wizard_notice_check_other_plugins'] );
		// Prevent error if redirect URL does not exist anymore
		add_action( 'admin_menu', [ $this, 'seokey_admin_check_other_plugins_deactivate_prevent_errors'], SEOKEY_PHP_INT_MAX );
		// Reset notifications
		add_action( 'update_option_active_plugins', [ $this, 'seokey_admin_check_other_plugins_reset_notification' ], 10, 3 );
	}

	// Get only one instance of our stuff
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Check already installed plugins
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_admin_check_other_plugins_init() {
		$plugins        = get_option( 'active_plugins' );
		$pluginscheck   = apply_filters( 'seokey_filter_check_other_plugins_init', $this->plugins_to_check );
		$pluginscheck   = array_flip( $pluginscheck );
		foreach ( $plugins as $plugin ) {
			if ( in_array( $plugin, $pluginscheck ) ) {
				$this->plugins_installed[ $plugin ] = $this->plugins_to_check[ $plugin ];
			}
		}
	}

	/**
	 * Reset ntoification for previous dismisssed notification
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public function seokey_admin_check_other_plugins_reset_notification( $old_value, $value, $option ){
		$plugins        = $value;
		$pluginscheck   = apply_filters( 'seokey_filter_check_other_plugins_init', $this->plugins_to_check );
		$pluginscheck   = array_flip( $pluginscheck );
		foreach ( $plugins as $plugin ) {
			if ( in_array( $plugin, $pluginscheck ) ) {
				delete_metadata(
					'user',        // the meta type
					0,             // this doesn't actually matter in this call
					'dismissed_seokey_notice_check_other_plugins', // the meta key to be removed everywhere
					'',            // this also doesn't actually matter in this call
					true           // tells the function "yes, please remove them all"
				);
				return;
			}
		}
	}
	/**
	 * Add a notification notice to alert on other plugins activation
	 *
	 * @since 0.0.1
	 * @author Daniel Roch
	 *
	 * @param array $args List of current SEOKEY Notifications
	 * @return array $args Updated list of current SEOKEY Notifications
	 */

	public function seokey_admin_wizard_notice_check_other_plugins( $args ) {
		if ( current_user_can(seokey_helper_user_get_capability( 'admin' ) ) ) {
			// Notice for all SEO plugins
			if ( ! empty( $this->plugins_installed ) ) {
				$import = false;
				$content = '<p>' . esc_html__( 'Here is the list of these plugins:', 'seo-key' ) . '</p>';
				// List all plugins
				$content .= "<ul>";
				foreach ( $this->plugins_installed as $extension ) {
					$end = '';
					if ( !in_array( $extension, $this->plugins_with_import ) ) {
						$end = esc_html__( ' (import functions not yet available)','seo-key' );
						$import = true;
					}
					$content .= '<li>' . esc_html( $extension ) . $end .'</li>';
				}
				$content .= "</ul>";
				// TODO later improve later for translators (this is going to be a mess...)
				$content .= '<p><strong>' . esc_html__( 'Warning: On deactivation, no data will be automatically imported into SEOKEY', 'seo-key' ) . '</strong>';
				$content .= esc_html__( ' (but no data will be deleted).', 'seo-key' ) . '</p>';
				// Security nonce
				$nonce    = wp_create_nonce( 'seokey_check_other_plugins_deactivate_nonce' );
				$url      = add_query_arg( '_seokeynonce', $nonce, seokey_helper_url_get_current() );
				$url      = add_query_arg( 'seokey_deactivate_other_extensions', true, $url );
				$class = " button-primary";
				if ( true === $import ) {
					$tab = sanitize_title( __( 'Licence & import', 'seo-key' ) );
					$url2   = seokey_helper_admin_get_link('settings') . '#' . $tab;
					$content .= '<a href="' . esc_url( $url2 ) . '" class="button button-primary">' . esc_html__( "Go to import menu", "seo-key" ) . '</a>';
					$class = " button-secondary";
				}
				$content  .= '<a href="' . esc_url( $url ) . '" class="button' . $class . ' ">' . esc_html__( "Deactivate all of these plugins", "seo-key" ) . '</a>';
				$new_args = array(
					sanitize_title( 'seokey_notice_check_other_plugins' ),
					// Unique ID.
					esc_html__( 'Other SEO plugins are installed and may cause conflicts with SEOKEY. ', 'seo-key' ),
					// The title for this notice.
					sprintf( '<div><p>%1$s</p></div>', $content ),
					// The content for this notice.
					[
						'scope'           => 'user',
						// Dismiss is per-user instead of global.
						'type'            => 'error',
						// Can be one of info, success, warning, error.
						'screens_exclude' => [ 'seokey_page_seo-key-wizard' ],
						// Exclude notice in specific screens.
						'option_include'  => [ 'seokey_option_first_wizard_seokey_notice_wizard' => 'goodtogo' ],
						'capability'      => seokey_helper_user_get_capability( 'admin' ),
						// only for theses users and above
						'alt_style'       => false,
						// alternative style for notice
						'option_prefix'   => 'dismissed',
						// Change the user-meta or option prefix.
					]
				);
				array_push( $args, $new_args );
			} else {
				// Do we need another notice after import ?
				$plugin_was_imported = get_option( 'seokey_import_from' );
				if ( ! empty( $plugin_was_imported ) ) {
					if ( ! is_plugin_active( 'wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php' ) ) {
						$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wonderm00ns-simple-facebook-open-graph-tags' ), 'install-plugin_wonderm00ns-simple-facebook-open-graph-tags' );
						$active_url  = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=wonderm00ns-simple-facebook-open-graph-tags%2Fwonderm00n-open-graph.php' ), 'activate-plugin_wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php' );
						$content     = '<p>' . esc_html__( 'The main reason is that it does not improve SEO, so it does not belong in a SEO plugin.', 'seo-key' ) . '</p>';
						$content     .= '<p>' . esc_html__( 'We noticed you imported data from another SEO plugin.', 'seo-key' ) . '</p>';
						$content     .= "<p>" . sprintf( __( 'If you want to add back these Data, we recommend you to install the <a href="%s">Open Graph and Twitter Card Tags</a> plugin, then <a href="%s">activate it</a> .', 'seo-key' ), $install_url, $active_url ) . "</p>";
						$new_args    = array(
							sanitize_title( 'seokey_notice_import_opengraph' ),
							// Unique ID.
							esc_html__( 'SEOKEY does not add OpenGraph and Twitter card Data', 'seo-key' ),
							// The title for this notice.
							sprintf( '<div><p>%1$s</p></div>', $content ),
							// The content for this notice.
							[
								'scope'           => 'user',
								// Dismiss is per-user instead of global.
								'type'            => 'info',
								// Can be one of info, success, warning, error.
								'screens_exclude' => [ 'seokey_page_seo-key-wizard' ],
								// Exclude notice in specific screens.
								'option_include'  => [ 'seokey_option_first_wizard_seokey_notice_wizard' => 'goodtogo' ],
								'capability'      => seokey_helper_user_get_capability( 'admin' ),
								// only for theses users and above
								'alt_style'       => false,
								// alternative style for notice

							]
						);
						array_push( $args, $new_args );
					}
				}
			}
		}
		return $args;
	}

	/**
	 * Deactivate those plugins
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_admin_check_other_plugins_deactivate() {
		// deactivation process ?
		if ( isset( $_GET['seokey_deactivate_other_extensions'] ) ) {
            // Security
            if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) || ! isset( $_GET['_seokeynonce'] ) || ! wp_verify_nonce( $_GET['_seokeynonce'], 'seokey_check_other_plugins_deactivate_nonce' ) ) {
                return;
            }
			if ( ! empty( $this->plugins_installed ) ) {
				foreach ( $this->plugins_installed as $plugin_file => $plugin_name ) {
					if ( is_plugin_active( $plugin_file ) ) {
						deactivate_plugins( $plugin_file );
						if ( ! is_network_admin() ) {
							update_option( 'recently_activated', array( $plugin_file => time() ) + (array) get_option( 'recently_activated' ) );
						} else {
							update_site_option( 'recently_activated', array( $plugin_file => time() ) + (array) get_site_option( 'recently_activated' ) );
						}
					}
				}
			}
			// Clean previous page query args
			$clean_url = remove_query_arg( 'seokey_deactivate_other_extensions', wp_get_referer() );
			$clean_url = remove_query_arg( 'redirectto', $clean_url );
            // Add new query args to prevent some redirection errors
            $clean_url = add_query_arg( 'other_plugins_deactivated', 'true', $clean_url );
            $clean_url = add_query_arg( '_wpnonce', wp_create_nonce( 'seokey_deactivate_other_extensions_nonce' ), $clean_url );
			// Rewrite rules after plugin deactivation
            flush_rewrite_rules();
			// First redirect
			wp_safe_redirect( $clean_url, 301 );
			die;
		}
	}

    /**
     * Prevent error if user is redirected to a deleted URl
     *
     * @note For example, I am in a Yoast menu and I click on the deactivation link (therefore the URL does not exist anymore)
     *
     * @since   0.0.1
     * @author  Daniel Roch
     */
    public function seokey_admin_check_other_plugins_deactivate_prevent_errors() {
	    if ( isset( $_GET['other_plugins_deactivated'] ) && isset( $_GET['page'] ) ) {
		    // Security checks
		    if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
			    return;
		    }
		    // Clean page parameter
		    $page = sanitize_title( $_GET['page'] );
		    // Get currently registered menus
		    global $menu;
		    global $submenu;
		    // Clean menus
		    $menus = array_merge( $menu, $submenu );
		    foreach ( $menu as $menuitem ) {
			    $menu_list[] = $menuitem[2];
		    }
		    foreach ( $submenu as $menuitem ) {
			    foreach ( $menuitem as $subitem ) {
				    $menu_list[] = $subitem[2];
			    }
		    }
		    unset( $menus );
		    $menu_list = array_unique( $menu_list );
		    // Current menu has been found, do nothing
		    if ( in_array( $page, $menu_list, true ) ) {
			    $clean_url = remove_query_arg( 'seokey_deactivate_other_extensions', wp_get_referer() );
			    wp_redirect( $clean_url, 301 );
				die;
		    }
		    // Another test: Current menu has been found, do nothing
		    if ( in_array( $page . '.php', $menu_list, true ) ) {
			    $clean_url = remove_query_arg( 'seokey_deactivate_other_extensions', wp_get_referer() );
			    wp_redirect( $clean_url, 301 );
			    die;
		    }
		    // It seems current URl does not exists anymore, redirect to main page
		    wp_redirect( get_admin_url(), 301 );
		    die;
	    }
    }
}

SeokeyCheckOtherExtensions::get_instance();