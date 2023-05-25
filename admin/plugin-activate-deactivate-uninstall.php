<?php
/**
 * Set activation, deactivation & uninstall methods
 *
 * @Loaded  on 'register_deactivation_hook'
 * @Loaded  on 'register_uninstall_hook'
 * @Loaded  on 'register_activation_hook'
 *
 *
 * @see     seokey_load()
 * @see     seokey_plugin_activation()
 * @see     seokey_plugin_deactivation()
 * @see     seokey_plugin_uninstall()
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/**
 * CLASS SEOKEY activation, deactivation and uninstall methods
 *
 * @since   0.0.1
 * @author  Léo Fontin
 *
 */
class SeoKeyActivateDeactivate {
	/**
	 * @var    (object) $instance Singleton
	 * @access publics
	 * @static
	 */
	public static $instance = null;

	/**
	 * What do we need to do ?
	 * @var array list of action for each action type
	 */
	protected $actions = [
		'activation'   => [
			'check_versions',
			'create_custom_tables',
			'files_add',
			'flush_permalinks', // Always last
		],
		'deactivation' => [
			'deactivate_crons',
			'sitemaps_enable_core',
			'sitemaps_delete',
            'files_delete_deactivate',
			// TODO later delete metabox order value for users
			'flush_permalinks', // Always last
		],
		'uninstall'    => [
			'delete_data_custom_tables',
            'delete_data_metas',
			'delete_data_options',
            'files_delete_uninstall',
            'flush_permalinks', // Always last
		]

	];

	/**
	 * Don't do anything while constructing class
	 * SeoKeyActivateDeactivate constructor.
	 */
	public function __construct() {}
	/**
	 * Get only one instance of our stuff
	 * @return SeoKeyActivateDeactivate
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	// Unserializing instances of this class is forbidden
	public function __wakeup() {}
	// Cloning of this class is forbidden
	private function __clone() {}

	/**
	 * Init
	 *
	 * @author  Léo Fontin
	 * @since   0.0.1
	 *
	 * @hook register_activation_hook()
	 * @hook register_deactivation_hook()
	 * @hook register_uninstall_hook()
	 * @param string $action Action name to use during activation, deactivation or uninstall
	 */
	public function init( $action ) {
	    $continue = true;
		// Is this action available ?
		if ( ! empty( $this->actions[ $action ] ) ) {
			// Foreach action
			foreach ( $this->actions[ $action ] as $method ) {
				// What do we need to do ?
				$function = 'seokey_activate_deactivate_' . $method;
				// Can we do it ?
				if ( method_exists( $this, $function ) ) {
				    // For uninstall action, check what user has chosen
                    if ( 'uninstall' === $action && true === str_starts_with( $method, 'delete_data_' ) ) {
                        if ( false === $this->seokey_helper_activate_deactivate_delete_data_check() ) {
                            $continue = false;
                        }
                    }
                    if ( true ===  $continue ) {
                        // Let's go people !
                        call_user_func([$this, $function]);
                    }
                    $continue = true;
				}
			}
		}
	}


	/**
	 * WordPress and PHP version check
	 *
	 * @author  Léo Fontin
	 * @since   0.0.1
	 *
	 * @hook register_activation_hook()
	 */
	protected function seokey_activate_deactivate_check_versions() {
		global $wp_version;
		// Check PHP version
		if ( 0 > version_compare( phpversion(), SEOKEY_PHP_MIN ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( '<strong>%1$s</strong> requires PHP %2$s minimum, your website is currently running the %3$s version.', 'seo-key' ), 'SEO KEY', '<code>' . SEOKEY_PHP_MIN . '</code>', '<code>' . phpversion() . '</code>' ) );
		}
		// Check WordPress version
		if ( 0 > version_compare( $wp_version, SEOKEY_WP_MIN ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( '<strong>%1$s</strong> requires WordPress %2$s minimum, your website is currently running the %3$s version.', 'seo-key' ), 'SEO KEY', '<code>' . SEOKEY_WP_MIN . '</code>', '<code>' . $wp_version . '</code>' ) );
		}
	}

	/**
	 * Flush rewrite rules
	 *
	 * @author  Léo Fontin
	 * @since   0.0.1
	 *
	 * @hook register_activation_hook()
	 * @hook register_deactivation_hook()
	 */
	public function seokey_activate_deactivate_flush_permalinks() {
		flush_rewrite_rules();
	}

	protected function seokey_activate_deactivate_sitemaps_enable_core() {
	    // Do not trigger this if core sitemap are not available (Older WordPress versions)
        global $wp_version;
        if ( 0 <= version_compare( $wp_version, 5.5 ) ) {
            // We need to activate them again before flushing rewrites rules
            add_filter('wp_sitemaps_enabled', '__return_true');
            global $wp_sitemaps;
            // If there isn't a global instance, set and bootstrap the sitemap system.
            if (empty($wp_sitemaps)) {
                $wp_sitemaps = new WP_Sitemaps();
                $wp_sitemaps->init();
                /**
                 * Fires when initializing the Sitemaps object.
                 *
                 * Additional sitemaps should be registered on this hook.
                 *
                 * @param WP_Sitemaps $wp_sitemaps Sitemaps object.
                 * @since 5.5.0
                 *
                 */
                do_action('wp_sitemaps_init', $wp_sitemaps);
            }
            // Flush rules
            flush_rewrite_rules();
        }
	}

	/**
	 * Create custom tables
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @hook register_activation_hook()
	 */
	protected function seokey_activate_deactivate_create_custom_tables() {
		// Redirection table
		require_once SEOKEY_PATH_ADMIN . 'modules/redirections/redirections_sql.php';
		seokey_redirections_create_table();
		seokey_redirections_create_table_bad();
		// Audit table
		require_once SEOKEY_PATH_ADMIN . 'modules/audit/audit_sql.php';
		seokey_audit_create_table();
		// Search Console tables
		require_once SEOKEY_PATH_ADMIN . 'modules/search-console/search-console-sql.php';
		seokey_gsc_create_table();
	}

    /**
     * Helper function : check if we need to delete data (true if necessary)
     *
     * @since   0.0.1
     * @author  Daniel Roch
     */
    public function seokey_helper_activate_deactivate_delete_data_check() {
        if ( 'go' === get_option( 'seo-key-delete-data' ) ) {
            return true;
        }
	    return false;
    }

	/**
	 * Delete custom tables
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @hook register_uninstall_hook()
	 */
	public function seokey_activate_deactivate_delete_data_custom_tables() {
		// Redirection table
		require_once SEOKEY_PATH_ADMIN . 'modules/redirections/redirections_sql.php';
		seokey_redirections_delete_table();
		seokey_redirections_delete_table_bad();
		// Audit table
		require_once SEOKEY_PATH_ADMIN . 'modules/audit/audit_sql.php';
		seokey_audit_delete_table();
		// Search Console tables
		require_once SEOKEY_PATH_ADMIN . 'modules/search-console/search-console-sql.php';
		seokey_gsc_delete_table();

	}

	/**
	 * Delete all sitemaps
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @hook register_deactivation_hook()
    */
	protected function seokey_activate_deactivate_sitemaps_delete(){
        // TODO Delete batch !
        include_once( SEOKEY_PATH_COMMON. 'seo-key-helpers.php' );
        include_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-delete.php' );
		if ( class_exists( 'Seokey_Sitemap_Delete' ) && method_exists( 'Seokey_Sitemap_Delete', 'seokey_sitemap_delete_init' ) ) {
			$sitemap = new Seokey_Sitemap_Delete();
			$sitemap->seokey_sitemap_delete_init();
		}
	}

    /**
     * Create all sitemaps
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook register_deactivation_hook()
     */
    protected function seokey_activate_deactivate_files_add(){
	    seokey_helper_files( 'create', 'muplugin' );
	    seokey_helper_files( 'create', 'mupluginjs' );
	    seokey_helper_files( 'create', 'muplugincss' );
		// If plugin was already submitted, check sitemap creation
	    $current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
        if ( 'goodtogo' === $current_wizard ) {
	        // Activate sitemaps
		    update_option( 'seokey_sitemap_creation', 'running', true );
        }
    }

    /**
     * Clean Crons
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook register_deactivation_hook()
     */
    protected function seokey_activate_deactivate_deactivate_crons(){
        wp_clear_scheduled_hook( "seokey_background-seokey_cron" );
	    wp_clear_scheduled_hook( "do_pings" );
    }

	/**
	 * Delete files
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook register_deactivation_hook()
	 */
	protected function seokey_activate_deactivate_files_delete_deactivate(){
		seokey_helper_files( 'delete', 'robots' );
	}

    /**
     * Delete our files
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook register_deactivation_hook()
     */
    protected function seokey_activate_deactivate_files_delete_uninstall(){
        seokey_helper_files( 'delete', 'muplugin' );
        seokey_helper_files( 'delete', 'mupluginjs' );
        seokey_helper_files( 'delete', 'muplugincss' );
    }

	/**
	 * Delete options
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook register_uninstall_hook()
	 */
	protected function seokey_activate_deactivate_delete_data_options() {
        // Find our options
        global $wpdb;
        $suppress      = $wpdb->suppress_errors();
        $alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE `option_name` LIKE 'seokey%' OR `option_name` LIKE '_transient_seokey%' ");
        $wpdb->suppress_errors( $suppress );
        $alloptions = array();
        foreach ( (array) $alloptions_db as $o ) {
            $alloptions[ $o->option_name ] = $o->option_value;
        }
        // Delete them
        foreach ( $alloptions as $option => $value ) {
            if ( 0 === strpos( $option, 'seokey' ) ) {
                delete_option( $option );
            } elseif ( 0 === strpos( $option, '_transient_seokey' ) ) {
                delete_option( $option );
            }
        }
        // Delete final option
        delete_option( 'seo-key-delete-data' );
    }

	/**
	 * Delete metas
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook register_uninstall_hook()
	 */
	protected function seokey_activate_deactivate_delete_data_metas() {
		global $wpdb;
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE 'seokey_%'");
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}termmeta` WHERE `meta_key` LIKE 'seokey_%'");
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE 'seokey_%'");
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` LIKE 'seokey-%'");
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}termmeta` WHERE `meta_key` LIKE 'seokey-%'");
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE 'seokey-%'");
	}
}