<?php
/**
 * Load SEOKEY configuration data
 *
 * @Loaded  on 'plugins_loaded'
 *
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

/************************************************************************
************************ Functions with manual data ********************
************************************************************************/

/**
 * Admin menus : Get list of all SEOKEY admin menus
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see seokey_helper_admin_get_main_menu_title()
 * @see seokey_helper_user_get_capability()
 * @return array (array) $menus Array of menus names and capability (it includes main menu name)
 */
function seokey_helper_admin_get_menus() {
	// Local Caching of menus to prevent file calculations
	static $menus;
	// Menus already defined, use them
	if ( isset( $menus ) ) {
		return $menus;
	}
	$menus = [];
	// We need to add our main menu to get things done correctly for admin functions (menus and admin bar)
	$menus[0] = [
		'title'      => seokey_helper_admin_get_main_menu_title(),
		'slug'       => SEOKEY_SLUG,
		'capability' => seokey_helper_user_get_capability( 'editor' ),
	];
	// Audit Menu
	$menus[10] = [
		'title'      => esc_html_x( 'Audit', 'Audit admin menu name', 'seo-key' ),
		'slug'       => SEOKEY_SLUG . '-audit',
		'capability' => seokey_helper_user_get_capability( 'editor' )
	];
	// Audit Menu
	$menus[30] = [
		'title'      => esc_html_x( 'SEO optimizations', 'Automatic optimizations admin menu name', 'seo-key' ),
		'slug'       => SEOKEY_SLUG . '-automatic-seo',
		'capability' => seokey_helper_user_get_capability( 'editor' )
	];
	// Redirection Menu
	$menus[50] = [
		'title'      => esc_html_x( 'Redirections', 'Redirections admin menu name', 'seo-key' ),
		'slug'       => SEOKEY_SLUG . '-redirections',
		'capability' => seokey_helper_user_get_capability( 'editor' ),
	];
	// Settings Menu
	$menus[70] = [
		'title'      => esc_html_x( 'Settings', 'Settings admin menu name', 'seo-key' ),
		'slug'       => SEOKEY_SLUG . '-settings',
		'capability' => seokey_helper_user_get_capability( 'admin' )
	];
	// Support Menu
	$menus[100] = [
		'title'      => esc_html_x( 'Help', 'Support admin menu name', 'seo-key' ),
		'slug'       => SEOKEY_SLUG . '-support',
		'capability' => seokey_helper_user_get_capability( 'editor' )
	];
	/**
	 * Filter SEOKEY admin menus
	 *
	 * @since 0.0.1
	 *
	 * @param (string) $menus Array of existing menus
	 */
	$menus = apply_filters( 'seokey_filter_get_admin_menus', $menus );
	// Load each file for each menu
	foreach ( $menus as $menu ) {
		if ( ! array_key_exists( 'function', $menu ) ) {
		    $filename = SEOKEY_PATH_ADMIN . 'admin-pages/admin-pages-' . seokey_helper_url_get_clean_plugin_slug( $menu['slug'] ) . '.php';
		    require_once( $filename );
        }
	}
	return $menus;
}

add_filter( 'plugin_action_links_' . SEOKEY_DIRECTORY_ROOT, 'seokey_admin_plugin_action_links', SEOKEY_PHP_INT_MAX );
/**
° Add plugin primary links (on plugins.php page)
 *
° @since  0.0.1
° @author  Daniel Roch
 *
° @param array $links basic links for plugin page listing
° @hook plugin_action_links_{PLUGIN_FILE}
° @return mixed|void (array) $links additional links for plugin page listing
 */
function seokey_admin_plugin_action_links( $links ) {
	// Add Dashboard Link
	$links['dashboard'] = '<a href="' . seokey_helper_admin_get_link() . '">' . esc_html__( 'Dashboard' ) . '</a>';
	// Add Settings Link
	$links['settings'] = '<a href="' . seokey_helper_admin_get_link( 'settings' ) . '">' . esc_html__( 'Settings' ) . '</a>';
	/**
	° Filter and return SEOKEY links
	 *
	° @param (array) $links Array of existing links
	 *
	° @since 0.0.1
	 *
	 */
	return apply_filters( 'seokey_filter_admin_plugin_action_links', $links );
}

add_filter( 'plugin_row_meta', 'seokey_admin_plugin_links_row_meta', 10, 2 );
/**
° Add plugin secondary links (plugins.php page)
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook plugin_row_meta
° @param array $links Default links for plugin page listing
° @param string $file Current plugin file name
° @return array Additional links for plugin page listing
 */
function seokey_admin_plugin_links_row_meta( $links, $file ) {
	// Is it the correct plugin file?
	if( $file === SEOKEY_DIRECTORY_ROOT ) {
		// Add SEOKEY Support Link
		$links[] = '<a href="' . seokey_helper_admin_get_link( 'support' ) . '">' . esc_html_x( 'Support', 'Link anchor in admin page plugin listing', 'seo-key' ) . '</a>';
	}
	// Return our custom plugins.php links
	return $links;
}

add_filter( 'set-screen-option', 'seokey_audit_screen_option_save', 11, 3 );
/**
 * Save Screen options
 */
function seokey_audit_screen_option_save( $status, $option, $value ) {
    if ( 'seokey_audit_per_page' == $option ) {
        return $value;
    }
    if ( 'seokey_redirections_per_page' == $option ) {
        return $value;
    }
}


/*************************************************************************
 ************************° Functions with automatic data *****************
 ************************************************************************/

/**
° Admin main menu title : Get name of SEOKEY main menu
 *
° @author Daniel Roch
° @since  0.0.1
 *
° @return string SEOKEY main menu title
 */
function seokey_helper_admin_get_main_menu_title() {
	return esc_html__( 'Dashboard', 'seo-key' );
}

/**
° Returns the screen base for the page you need
 *
° @since 0.0.1
° @author Julio potier
 *
° @param string $page The slug of the page you need
° @return string The screen base slug
 */
function seokey_helper_admin_get_page_screen_base( $page ) {
	return sanitize_title( 'seokey_page_seo-key-' . $page );
}

/**
° Returns the full group slug for the page you need
 *
° @since 0.0.1
° @author Julio Potier
 *
° @param string $page The slug of the page you need
° @return string The group slug
 */
function seokey_settings_api_get_page_group( $page ) {
	return sanitize_title( 'seokey_' . $page . '_fields' );
}

/**
° Returns the full section slug for the section you need
 *
° @author Julio potier
° @since 0.0.1
 *
° @param string $page The slug of the page you need
° @param string $section The slug of the section you need
° @return string The section slug you need
 */
function seokey_settings_api_get_page_section( $page, $section ) {
	return sanitize_title( 'seokey-section-' . $section );
}

/**
° Returns the full field slug for the section you need
 *
° @since 0.0.1
° @author Julio potier
 *
° @param string $page The slug of the page you need
° @param string $section The slug of the section you need
° @param string $field The slug of the field you need
° @return string The field slug you need
 */
function seokey_settings_api_get_page_field( $page, $section, $field ) {
	return sanitize_title( 'seokey-field-' . $section . '-' . $field );
}

/**
° SEOKEY Settings pages
 *
° @notes:° Define pages that will contain settings
°° a page can contain only one setting form
°° each form contains only one setting group
°° a form can contain one or several settings sections
°° each setting section begin with an H2
°° each section can contain one or several setting fields (a database option)
 *
° @author  Daniel Roch
° @since   0.0.1
 *
° @uses page   // string, required     // Admin screen ID (seokey_helper_get_current_screen() then $page->ID)
° @uses group  // string, required     // Settings Group Name (all settings located in the same form)
° @return array (array) $settings_pages Array of admin pages that will include settings
 *
 */
function seokey_settings_api_get_config_pages() {
	$settings_pages = [];
	foreach ( seokey_helper_admin_get_menus() as $menu ) {
		$_slug = seokey_helper_url_get_clean_plugin_slug( $menu['slug'] );
		// Add admin page with a setting form
		$settings_pages[] = [
			// Admin screen ID. You CAN NOT choose what you want.
			'page'  =>  seokey_helper_admin_get_page_screen_base( $_slug ),
			// Group Name : define a group name for your page. Choose what you want.
			'group' =>  seokey_settings_api_get_page_group( $_slug )
		];
	}
  return $settings_pages;
}

/**
° SEOKEY Settings sections
 *
° @notes Configure settings sections for all settings forms
°° You MUST use the same page slug if you want several section within the same form ('group' value)
°° You MUST use an available page slug from seokey_settings_api_get_config_pages() function
°° Each section can have one or several section fields
 *
° @author  Daniel Roch
° @since   0.0.1
 *
° @see seokey_settings_api_get_config_pages()
° @return mixed|void (array) $sections Array of setting sections for all forms
 */
function seokey_settings_api_get_config_sections() {
	/**
	° Filter SEOKEY Settings : sections for all settings form
	*
	° @since 0.0.1
	*
	° @param (string) Array of existing settings sections
	*/
	return apply_filters( 'seokey_filter_get_config_sections', [] );
}

/**
° SEOKEY Settings Fields
 *
° @notes Configure each setting field (each "option" in database)
°° You MUST use an available section name from seokey_settings_api_get_config_sections() function
°° The Setting API class file handle all Data Validation
° Availables types :
° 'type' => 'textarea', 'text', 'number', 'email', 'url', 'radio', 'select', 'checkbox'
° A custom type can be defined with seokey_filter_setting_callback_switch hook
° for custom types, you must use seokey_filter_setting_sanitize filter to add correct data sanitization (defaut sanitize_text_field)
 *
° @author  Daniel Roch
° @since   0.0.1
 *
° @see seokey_settings_api_get_config_sections()
° @return mixed|void (array) $fields Array of each field option
 */
function seokey_settings_api_get_config_fields() {
	/**
	° Filter and return SEOKEY Settings : setting fields for all settings form
	*
	° @since 0.0.1
	*
	° @param (string) Array of existing settings fields
	*/
	return apply_filters( 'seokey_filter_get_config_fields', [] );
}

add_filter( 'seokey_filter_admin_bar', 'seokey_medias_library_menu_adminbar' );
/**
 * Media Library ALT Editor Menu admin bar
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook seokey_filter_admin_bar
 */
function seokey_medias_library_menu_adminbar( $menus ) {
	$menus[500] = [
		'title'         => __( 'ALT Editor', 'seo-key' ),
		'slug'          => 'list&seokeyalteditor=yes',
		'capability'    => seokey_helper_user_get_capability( 'editor' ),
		'baseurl'       => 'upload.php?mode=',
	];
	return $menus;
}