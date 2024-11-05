<?php
/**
 * Upgrade functions
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

add_action ('seokey_loaded', 'seokey_upgrade_check');
/**
 * Upgrade function
 *
 * @author  Daniel Roch
 * @since 1.5.0
 */
function seokey_upgrade_check() {
	// Only upgrade on admin pages and if wizard has ended
	if ( is_admin() && 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
		$actual_version = get_option( 'seokey_version' );
		// Before 1.5.0
		if ( ! $actual_version ) {
			do_action( 'seokey_first_upgrader', $actual_version );
			update_option( 'seokey_version', SEOKEY_VERSION, true );
		}
		// Already installed but and upgrade may be necessary
		elseif ( SEOKEY_VERSION !== $actual_version ) {
			$new_version = SEOKEY_VERSION;
			do_action( 'seokey_upgrader', $new_version, $actual_version );
			update_option( 'seokey_version', SEOKEY_VERSION, true );
		}
	}
}

add_action( 'seokey_first_upgrader', 'seokey_first_upgrader_function', 10, 1 );
/**
 * Upgrade function form very old versions (before 1.5)
 *
 * @author  Daniel Roch
 * @since 1.5.0
 *
 * @hook seokey_first_upgrader action
 * @param string $actual_version Old SEOKEY version known
 */
function seokey_first_upgrader_function( $actual_version ) {
	////// UPGRADE to 1.5
	// Check if it is an old SEOKEY version (before 1.5)
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->base_prefix . 'seokey_gsc_keywords';
	$continue = true;
	foreach ( $wpdb->get_col( "DESC $table_name", 0 ) as $column ) {
		if ( $column === 'item_id' ) {
			$continue = false;
		}
	}
	// SEOKEY before 1.5
	if ( true === $continue ) {
		// Add missing column to our SQL table
		maybe_add_column(
			$table_name,
			'item_id',
			"ALTER TABLE {$table_name}
    	ADD item_id INT( 11 ) DEFAULT NULL;"
		);
		// Reset Robots.txt
		$directory = ABSPATH;
		$file      = $directory . "robots.txt";
		if ( file_exists( $file ) ) {
			// Get our content
			$content = utf8_decode( wp_remote_retrieve_body( wp_remote_get( trailingslashit( get_site_url() ) . "robots.txt" ) ) );
			if ( str_starts_with( $content, "# BEGIN SEOKEY robots.txt file" ) || str_starts_with( $content, "# BEGIN SEOKEY Robots.txt file" ) ) {
				if ( str_ends_with( $content, "# END SEOKEY Robots.txt file (add your custom rules below)" )
				     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos règles personnalisées ci-dessous)" )
				     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos r?gles personnalis?es ci-dessous)" ) ) {
					seokey_helper_files( 'delete', 'robotsforce' );
					seokey_helper_files( 'create', 'robots' );
				}
			}
		}
	}
	// Upgrade to 1.6.0
	// Update all URL schema in redirection tables
    require_once SEOKEY_PATH_ADMIN . 'modules/redirections/redirections_sql.php';
	seokey_redirections_update_full_url();
	// Create sitemap Folder
	seokey_helper_create_folder( SEOKEY_SITEMAPS_PATH, true );
	// Delete old sitemaps if we found them
	$old_sitemap_list = get_option( 'seokey_option_sitemap_list' );
	if ( ! empty( $old_sitemap_list ) ) {
		foreach ( $old_sitemap_list as $file ) {
			if ( file_exists( ABSPATH . $file ) ) {
				unlink( ABSPATH . $file );
			}
		}
	}
	if ( file_exists( ABSPATH . 'sitemap-seokey-render.xsl' ) ) {
		unlink( ABSPATH . 'sitemap-seokey-render.xsl' );
	}
	// Reset Robots.txt for sitemaps
	$directory = ABSPATH;
	$file      = $directory . "robots.txt";
	if ( file_exists( $file ) ) {
		// Get our content
		$content = utf8_decode( wp_remote_retrieve_body( wp_remote_get( trailingslashit( get_site_url() ) . "robots.txt" ) ) );
		if ( str_starts_with( $content, "# BEGIN SEOKEY robots.txt file" ) || str_starts_with( $content, "# BEGIN SEOKEY Robots.txt file" ) ) {
			if ( str_ends_with( $content, "# END SEOKEY Robots.txt file (add your custom rules below)" )
			     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos règles personnalisées ci-dessous)" )
			     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos r?gles personnalis?es ci-dessous)" ) ) {
				seokey_helper_files( 'delete', 'robotsforce' );
				seokey_helper_files( 'create', 'robots' );
			}
		}
	}
	// Launch new sitemap creation
	update_option( 'seokey_sitemap_creation', 'running', true );
	// Activate OpenGraph
	update_option( 'seokey-field-seooptimizations-opengraph', '1', true );
}

add_action( 'seokey_upgrader', 'seokey_upgrader_function', 10, 2 );
/**
 * Upgrade function (after 1.5)
 *
 * @author  Daniel Roch
 * @since 1.6.0
 *
 * @hook seokey_upgrader action
 * @param string $new_version New SEOKEY version
 * @param string $actual_version Old SEOKEY version known
 */
function seokey_upgrader_function( $new_version, $actual_version ) {
	// Upgrade to 2.0.0 and above
	// Activate OpenGraph
	if ( version_compare( $actual_version, '2.0.0' ) <= 0 ) {
		update_option( 'seokey-field-seooptimizations-opengraph', '1', true );
	}
	// Upgrade to 1.6.0 and above
	if ( version_compare( $actual_version, '1.6.0' ) < 0 ) {
		// Update all URL schema in redirection tables
        require_once SEOKEY_PATH_ADMIN . 'modules/redirections/redirections_sql.php';
        seokey_redirections_update_full_url();
		// Create sitemap Folder
		seokey_helper_create_folder( SEOKEY_SITEMAPS_PATH, true );
		// Delete old sitemaps if we found them
		$old_sitemap_list = get_option( 'seokey_option_sitemap_list' );
		if ( ! empty( $old_sitemap_list ) ) {
			foreach ( $old_sitemap_list as $file ) {
				if ( file_exists( ABSPATH . $file ) ) {
					unlink( ABSPATH . $file );
				}
			}
		}
		if ( file_exists( ABSPATH . 'sitemap-seokey-render.xsl' ) ) {
			unlink( ABSPATH . 'sitemap-seokey-render.xsl' );
		}
		// Reset Robots.txt for sitemaps
		$directory = ABSPATH;
		$file      = $directory . "robots.txt";
		if ( file_exists( $file ) ) {
			// Get our content
			$content = utf8_decode( wp_remote_retrieve_body( wp_remote_get( trailingslashit( get_site_url() ) . "robots.txt" ) ) );
			if ( str_starts_with( $content, "# BEGIN SEOKEY robots.txt file" ) || str_starts_with( $content, "# BEGIN SEOKEY Robots.txt file" ) ) {
				if ( str_ends_with( $content, "# END SEOKEY Robots.txt file (add your custom rules below)" )
				     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos règles personnalisées ci-dessous)" )
				     || str_ends_with( utf8_decode( $content ), "# END SEOKEY robots.txt file (ajoutez vos r?gles personnalis?es ci-dessous)" ) ) {
					seokey_helper_files( 'delete', 'robotsforce' );
					seokey_helper_files( 'create', 'robots' );
				}
			}
		}
		// Launch new sitemap creation
		update_option( 'seokey_sitemap_creation', 'running', true );
	}
    if ( version_compare( $actual_version, '1.6.5' ) < 0 ) {
        // force htaccess rewrite
        flush_rewrite_rules();
    }
}