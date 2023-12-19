<?php
/**
 * Handle SEOKEY Installation Wizard
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


add_filter( 'seokey_filter_get_admin_menus', 'seokey_admin_wizard_add_menu_page' );
/**
 * Create a wizard menu page
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array $menus List of current SEOKEY menus
 */
function seokey_admin_wizard_add_menu_page( $menus ) {
    $menus[200] = [
        'title'         => esc_html__('Wizard', 'seo-key'),
        'slug'          => 'seo-key-wizard',
        'capability'    => seokey_helper_user_get_capability('admin'),
    ];
    return $menus;
}


add_action( 'wp_before_admin_bar_render', 'seokey_admin_wizard_page_remove_admin_bar' );
/**
 * Remove Wizard item in admin bar when wizard has ended
 *
 * @since   0.0.1
 * @author  Daniel Roch
 * @return void
 */
function seokey_admin_wizard_page_remove_admin_bar() {
	if ( 'goodtogo' === get_option('seokey_option_first_wizard_seokey_notice_wizard') ) {
		global $wp_admin_bar;
		$menu_id = admin_url( 'admin.php?page=seo-key-wizard');
		$wp_admin_bar->remove_menu($menu_id);
	}
}

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_wizard' );
/**
 * Enqueue assets (CSS) for Option Reading Menu
 *
 * @author  Daniel Roch
 *
 * @uses    wp_enqueue_style()
 *
 * @hook    admin_enqueue_scripts
 *
 * @since   0.0.1
 */
function seokey_enqueue_admin_wizard() {
    // CSS for setting pages
    $current_screen = seokey_helper_get_current_screen();
    if ( $current_screen->base === 'seokey_page_seo-key-wizard' ) {
        // Enqueue settings CSS and JS
	    seokey_enqueue_admin_common_scripts();
        wp_enqueue_style('seokey-settings', esc_url( SEOKEY_URL_ASSETS . 'css/seokey-settings.css' ), false, SEOKEY_VERSION);
	    wp_enqueue_style('seokey-wizard',   esc_url( SEOKEY_URL_ASSETS . 'css/seokey-wizard.css' ), false, SEOKEY_VERSION);
	    wp_enqueue_style('seokey-automatic',   esc_url( SEOKEY_URL_ASSETS . 'css/seokey-automatic-optimizations.css' ), false, SEOKEY_VERSION);
	    wp_enqueue_script('seokey-automatic',  esc_url( SEOKEY_URL_ASSETS . 'js/settings-automatic-optimizations.js' ), array('jquery'), SEOKEY_VERSION );
    }
}

add_action('admin_init', 'seokey_admin_wizard_notice', 1 );
/**
 * Display wizard notice if necessary
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @return void
 */
function seokey_admin_wizard_notice() {
	if( defined( 'DOING_AJAX' ) ) {
		return ;
	}
    if ( false === seokey_admin_wizard_get_status() || 'none' === seokey_admin_wizard_get_status() ) {
        // TODO not if option end
        add_filter('seokey_filter_admin_notices_launch', 'seokey_admin_wizard_notice_start');
    }
}

/**
 * Get current wizard status
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @return string
 */
function seokey_admin_wizard_get_status() {
	return ( !empty( $_GET['wizard-status'] ) ) ? sanitize_title( $_GET['wizard-status'] ) : 'none';
}

// Check if we are trying to update a SEOKEY Wizard option
add_filter('pre_update_option', 'seokey_wizard_pre_update_option', SEOKEY_PHP_INT_MAX, 3 );
function seokey_wizard_pre_update_option( $value, $option, $old_value ) {
    if ( strpos( $option , 'seokey-field' ) === 0 ) {
        $from = wp_get_referer();
        $query = parse_url($from, PHP_URL_QUERY);
        parse_str($query, $params);
        if ( !empty ( $params['page'] ) ) {
            if ( 'seo-key-wizard' === $params['page'] ) {
                update_user_meta(get_current_user_id(), 'seokey_cache_wizard_pre_update_option', 'next');
            }
        }
    }
    return $value;
}

// Check if current user just have updated an option
add_action( 'admin_init', 'seokey_wizard_after_update_option', SEOKEY_PHP_INT_MAX );
function seokey_wizard_after_update_option() {
	// TODO Condition sur la page ?
    $meta = get_user_meta( get_current_user_id(),'seokey_cache_wizard_pre_update_option', true );
    if ( "next" === $meta ) {
        delete_user_meta( get_current_user_id(),'seokey_cache_wizard_pre_update_option' );
        seokey_wizard_continue();
    }
}

// Redirect to next step
function seokey_wizard_continue(){
    // Get next step data
    $step = seokey_wizard_get_next_step_from_referer();
    $key = array_key_first($step );
    $step = array_shift($step);
    // Wizard admin URL
    $current_url = seokey_helper_admin_get_link('wizard');
    // Construct next step URL
    $url = add_query_arg( 'wizard-status', $key, $current_url ) .'#'. sanitize_title( $step[0] );
    wp_redirect(  $url, 301 );
	die();
}

// Get next step
function seokey_wizard_get_next_step_from_referer(){
    // Get all steps
    $steps  = seokey_admin_wizard_steps();
    // Get previous step
    $from   = wp_get_referer();
    $query  = parse_url($from, PHP_URL_QUERY);
    parse_str($query, $params);
    // Find next step
	$status = ( !empty( $params['wizard-status'] ) ) ? $params['wizard-status'] : '1_0_0';
    $nextkey = array_search( $status, array_keys( $steps ) ) + 1;
	$nextkey = array_slice( $steps, $nextkey, 1 );
    return $nextkey;
}

function seokey_wizard_get_next_step( $from = 'start', $search = 1 ){
    // Get all steps
    $steps  = seokey_admin_wizard_steps();
    // Find next step
    $nextkey = array_search( $from, array_keys( $steps ) ) + $search;
    $nextkey = array_slice( $steps, $nextkey, 1 );
    return $nextkey;
}

function seokey_wizard_get_previous_step( $from ){
    // Get all steps
    $steps  = seokey_admin_wizard_steps();
    // Find next step
    $nextkey = array_search( $from, array_keys( $steps ) ) - 1;
    $nextkey = array_slice( $steps, $nextkey, 1 );
    return $nextkey;
}

// Get wizard steps
function seokey_admin_wizard_steps() {
	$steps = array (
        'start' => array (
            _x( 'Start',                     'Wizard Step', 'seo-key' ),
		    'content'),
		'1_0_0'  => array (
            _x( 'Who are you?',             'Wizard Step', 'seo-key' ),
		    'settings'),
		'1_0_1'  => array (
            _x( 'Contents',              'Wizard Step', 'seo-key' ),
		    'settings'),
        '1_0_2'  => array (
	        _x( 'Metas and URL',            'Wizard Step', 'seo-key' ),
	        'settings'),
        '1_0_4'  => array (
	        _x( 'SEO optimizations',   'Wizard Step', 'seo-key' ),
	        'settings'), // Check seokey_admin_page_wizard_steps_content if you move automatic optimization tab
        '1_0_5'    => array (
	        _x( 'Audit',                     'Wizard Step', 'seo-key' ),
	        'content'),
	);
	return apply_filters( 'seokey_filter_admin_wizard_steps', $steps);
}

add_action( 'seokey_action_setting_form_before', 'seokey_admin_wizard_steps_h2');
function seokey_admin_wizard_steps_h2(){
	$step = seokey_admin_wizard_get_status();
	switch ( $step ) {
		case '1_0_0':
			echo '<h2 class="main-title">'.esc_html__( 'Who are you?', 'seo-key' ) .'</h2>';
			break;
		case '1_0_1':
			echo '<h2 class="main-title">'.esc_html__( 'Optimize your contents', 'seo-key' ) .'</h2>';
			break;
		case '1_0_2':
			echo '<h2 class="main-title">'.esc_html__( 'Define your SEO data', 'seo-key' ) .'</h2>';
			break;
		case '1_0_3':
            echo '<h2 class="setting-description has-explanation">';
                echo esc_html__( "Get data from Search Console", "seo-key" );
                echo seokey_helper_help_messages( 'settings-title-search-console' );
            echo '</h2>';
			break;
		case '1_0_4':
			echo '<h2 class="main-title">'.esc_html__( 'Automatic optimizations', 'seo-key' ) .'</h2>';
			break;
		default:
			break;
	}
}

// Trigger settings API only if necessary + check if we need to skip wizard
add_action( 'current_screen', 'seokey_admin_page_wizard_define_content', 20 );
function seokey_admin_page_wizard_define_content() {
    // Only on wizard screens
    $screen = seokey_helper_get_current_screen();
    if ( $screen->id === "seokey_page_seo-key-wizard" ) {
		// Update from legacy code SEOKEY free vO.3
	    $version = get_option( 'seokey_option_current_version' );
	    if ( false === $version ) {
		    $main_class = SEOKEY_Free::get_instance();
		    $main_class->seokey_plugin_activation();
		    update_option( 'seokey_option_current_version', SEOKEY_VERSION, true );
	    }
        // Current URL
        $url = seokey_helper_url_get_current();
        // Check if user wants to skip wizard
        if ( str_contains( $url, 'skipwizard=yes' ) ) {
	        update_option ( 'seokey-field-cct-cpt', get_post_types( ['public' => true ] ) );
	        update_option ( 'seokey-field-cct-taxo', get_taxonomies( ['public' => true, 'show_ui' => true ] ) );
            // Skip wizard, launch all wings
	        update_option('seokey-gsc-site-disconnected', 'disconnected', true );
            seokey_wizard_end();
            // Redirect user
            wp_safe_redirect( seokey_helper_admin_get_link( 'audit') );
            die;
        }
        // He wants it
        else {
            // We will need our setting API
            $status = seokey_admin_wizard_get_status();
            $all_steps = seokey_admin_wizard_steps();
            end($all_steps);
            if ( $status === 'none' || $status === key( $all_steps ) ) {
                return;
            }
            // Launch settings API function
            add_action( 'seokey_admin_page_wizard_content', 'seokey_admin_page_content' );
        }
    }
}

/* Define wizards settings sections and steps */
add_filter( 'seokey_filter_get_config_sections', 'seokey_wizard_section' );
add_filter( 'seokey_filter_get_config_fields', 'seokey_wizard_step' );

/**
 * Add the first notification notice for the wizard page
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @param array $args List of current SEOKEY Notifications
 * @return array $args Updated list of current SEOKEY Notifications
 */
function seokey_admin_wizard_notice_start( $args ) {
    // TODO Condition sur étapes d'audit déjà réalisées
    $name = get_bloginfo('name');
    $wizard_url = seokey_helper_admin_get_link( 'wizard' );
    $wizard_skip_url = add_query_arg( 'skipwizard', 'yes', $wizard_url );
    $text = '<p>'. esc_html__( 'It takes just a few minutes to configure SEOKEY, improve "%s" SEO and audit your content!', 'seo-key' ) .'</p>';
    $text .= '<a href="' . esc_url( $wizard_url ) . '" class="button button-primary button-hero">'. esc_html__( 'Launch the SEO Wizard', 'seo-key' ) .'</a>';
    $text .= '<a href="' . esc_url( $wizard_skip_url ) . '" class="button button-secondary button-hero seokey-secondary-button-notice">'. esc_html__( 'Ignore and activate', 'seo-key' ) .'</a>';
    $text = sprintf( $text, $name );
    $new_args = array(
        sanitize_title( 'seokey_notice_wizard' ), // Unique ID.
        esc_html_x( 'You are almost done !', 'notification title for the wizard installation','seo-key' ), // The title for this notice.
        $text, // The content for this notice.
        [
            'scope'             => 'global', // Dismiss is per-user instead of global.
            'type'              => 'info', // Can be one of info, success, warning, error.
            'screens_exclude'   => ['seokey_page_seo-key-wizard'],
            'capability'        => seokey_helper_user_get_capability( 'admin' ), // only for theses users and above
            'alt_style'         => false, // alternative style for notice
            'option_prefix'     => 'seokey_option_first_wizard', // Change the user-meta or option prefix.
            'state'             => 'permanent',
        ]
    );
    array_push($args, $new_args );
    return $args;
}

/**
 * Add our fields for this setting page
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   seokey_filter_get_config_fields
 *
 * @notes  Function should always be named "seokey_add_fields__" + {page slug}
 *
 * @param  (array) $fields The actual fields
 * @return (array) $fields The fields with the new one
 **/
function seokey_wizard_step( $fields ) {
    // Define correct ID
    $unique_ID  = 'wizard';
	// Get manuel SEO optimizations
	$fields_title_meta  = seokey_settings_add_seooptimizations_fields( $unique_ID );
	$fields             = ( !empty( $fields_title_meta) ) ? array_merge( $fields, $fields_title_meta ) : $fields;
    // Get title and meta fields
    $new_fields = seokey_settings_add_title_meta( $unique_ID );
    $fields     = ( !empty( $new_fields) ) ? array_merge( $fields, $new_fields ) : $fields;
	// Get content fields
	$new_fields    = seokey_settings_add_contents( $unique_ID );
	$fields        = ( !empty( $new_fields) ) ? array_merge( $fields, $new_fields ) : $fields;
	// Get schema fields
    $new_fields = seokey_settings_add_schema_org( $unique_ID );
	$fields     = ( !empty( $new_fields) ) ? array_merge( $fields, $new_fields ) : $fields;
	// Get automatic optimizations fields
	$new_fields = seokey_settings_add_automatic_opti_hidden_field( $unique_ID );
	$fields     = ( !empty( $new_fields) ) ? array_merge( $fields, $new_fields ) : $fields;
	return $fields;
}

/**
 * Add Automatic settings tab
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook   seokey_filter_get_config_fields
 *
 * @param string $unique_ID Unique section ID
 * @return (array) $fields All fields with the new one
 **/
function seokey_settings_add_automatic_opti_hidden_field( $unique_ID ) {
    $fields[] = [
        // You must use data from seokey_settings_api_get_config_sections()
        'ID'          => $unique_ID,
        // Field Section : current section where this setting will be displayed.
        'section'     => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
        // Field Name (ID)
        'name'        => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'wizardstatus' ),
        // A better description for our setting (optional)
        'desc'        => __( 'Automatic optimization status', 'seo-key' ),
        // Type field used for generating field with correct callback function
        'type'        => 'text',
    ];
    return $fields;
}

/**
 * Add Wizard setting section
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @notes  Function should always be named "seokey_add_sections__" + {page slug}
 * @hook   seokey_filter_get_config_sections
 * @uses   ID       // string, required   // ID of the Section
 * @uses   name     // string, required   // Name of the section
 * @uses   desc     // string             // Description for the section
 * @uses   tabname  // string, required   // Tab name
 * @return mixed (array) $sections The sections with the new one
 */
function seokey_wizard_section( $sections ) {
    // Don't touch this :)
    $unique_ID = 'wizard';
	// Add one group section
	$sections[] = [
		// Section Unique ID
        'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'seooptimizations',
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => esc_html__( 'SEO optimizations', 'seo-key' )
	];
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'schemaorg',
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => _x( 'Who are you?', 'Setting Section tab name', 'seo-key' )
	];
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'cct',
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => _x( 'Contents', 'Setting Section tab name', 'seo-key' )
	];
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'metas',
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => _x( 'Metas and URL', 'Setting Section tab name', 'seo-key' )
	];
    $sections[] = [
        // Section Unique ID
        'ID'      => 'wizard',
        // Name of the section
        'name'    => 'search-console',
        // Tabname. If you want this section in a tabulation navigation, this field must be filled
        'tabname' => __( 'Search Console', 'seo-key' )
    ];
	return $sections;
}

add_filter( 'submenu_file', 'seokey_admin_wizard_hide_menu_page' );
/**
 * Hide the Wizard menu page from SEOKEY main menu
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array $submenu_file List of all submenu pages in submenu_file filter
 */
function seokey_admin_wizard_hide_menu_page( $submenu_file ) {
	// Hide menu when wizard has been finished
	$current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
	if ( 'goodtogo' === $current_wizard ) {

		$hidden_submenus = array(
			'seo-key-wizard' => true,
		);
		// Select another submenu item to highlight (optional).
		global $plugin_page;
		if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
			$submenu_file = 'seo-key-settings';
		}
		// Hide submenu.
		foreach ( $hidden_submenus as $submenu => $unused ) {
			remove_submenu_page( 'seo-key', $submenu );
		}
	}
    return $submenu_file;
}

/**
 * Wizard end function
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_wizard_end(){
	// Define current SEOKEY version
	update_option( 'seokey_version', SEOKEY_VERSION, true );
    // Define the wizard end
    update_option( 'seokey_option_first_wizard_seokey_notice_wizard', 'goodtogo', true );
    // SEOKEY Files creation
    seokey_helper_files( 'create', 'robots' );
	// Sitemap lastmod
	require_once( dirname( __file__ ) . '/modules/sitemap/sitemaps-lastmod.php' );
	$lastmod = new Seokey_Sitemap_Lastmod();
	$lastmod->seokey_sitemap_set_term_lastmod();
	$lastmod->seokey_sitemap_set_author_lastmod();
    // Allow sitemap creation
	update_option( 'seokey_sitemap_creation', 'running', true );
}