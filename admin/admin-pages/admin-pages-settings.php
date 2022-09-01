<?php
/**
 * Load SEOKEY Admin pages functions
 *
 * @Loaded  on 'init'
 * @Loaded  on is_admin() condition
 * @Loaded  with plugin configuration file + admin-menus-and-links.php
 *
 * - Trigger SEOKEY admin page "contents"
 *
 * @see     seokey_settings_api_get_config_sections()
 * @see     seokey_settings_api_get_config_fields()
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

// TODO Comments
if ( seokey_helpers_is_free() ) {
	add_action('seokey_action_setting_table_after', 'seokey_settings_free_text', 25, 1);
	// TODO Comments
	function seokey_settings_free_text( $data ) {
		if ( "seokey-section-licence" === $data ) {
			echo '<h2>' . __( 'Upgrade now to improve your SEO!', 'seo-key') . '</h2>';
			echo '<p>' . __( "We give you all the keys to succeed:", "seo-key" ) . '</p>';
			echo '<ul>';
				echo '<li>' . __( "A full audit module", "seo-key" ) . '</li>';
				echo '<li>' . __( "Individual advice for each content: what should I do next?", "seo-key" ) . '</li>';
				echo '<li>' . __( "Easily connect your Search Console and get more SEO data", "seo-key" ) . '</li>';
				echo '<li>' . __( "See and fix Google 404 and WordPress automatic redirections", "seo-key" ) . '</li>';
			echo '</ul>';
			echo '<p>' . __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY Premium</a>", 'seo-key' ) . '</p>';
			
		}
	}
}

/**
 * Import fields settings for  "Title & Meta"
 */
seokey_helper_require_file( 'admin-settings-fields-title-meta',	SEOKEY_PATH_ADMIN . 'admin-pages/settings/', 'everyone' );

/**
 * Import fields settings for "Schema.org"
 */
seokey_helper_require_file( 'admin-settings-fields-schema-org',	SEOKEY_PATH_ADMIN . 'admin-pages/settings/', 'everyone' );

/**
 * Import fields settings for "Contents"
 */
seokey_helper_require_file( 'admin-settings-fields-content',	SEOKEY_PATH_ADMIN . 'admin-pages/settings/', 'everyone' );

// TODO Comment
add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_settings_title', 20 );
function seokey_admin_page_settings_title(){
    $screen       = seokey_helper_get_current_screen();
    $current_page = $screen->base;
    // Are we in the dashboard page ?
    if ( $current_page === 'seokey_page_seo-key-settings' ) {
	    // Display content when wizard has been finished
	    $current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
	    if ( 'goodtogo' === $current_wizard ) {
            echo '<div class="seokey-wrapper-limit">';
		        seokey_admin_page_content();
            echo '</div>';
	    }
    }
}

add_filter( 'seokey_filter_get_config_sections', 'seokey_settings_add_base_sections' );
/**
 * Add our sections for this setting page
 *
 * @hook   seokey_filter_get_config_sections
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @notes  Do not remove sections here!
 * @notes  Function should always be named "seokey_add_sections__" + {page slug}
 *
 * @uses   ID       // string, required   // ID of the Section
 * @uses   name     // string, required   // Name of the section
 * @uses   desc     // string             // Description for the section
 * @uses   tabname  // string, required   // Tab name
 * @return mixed (array) $sections The sections with the new one
 */
function seokey_settings_add_base_sections( $sections ) {
	// Don't touch this :)
	$unique_ID = 'settings';
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'schemaorg',
		// Description for this section (optional text to display below H2)
		'desc'    => _x( 'These information will help Google to display data about you.', 'Setting Section explanation - <p>', 'seo-key' ),
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => _x( 'Who are you?', 'Setting Section tab name', 'seo-key' )
	];
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'cct',
		// Description for this section (optional text to display below H2)
		'desc'    => __( 'Some of your contents may not be browsable publicly, tell us which ones.', 'seo-key' ),
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => __( 'Contents', 'seo-key' )
	];
	// Add one group section
	$sections[] = [
		// Section Unique ID
		'ID'      => $unique_ID,
		// Name of the section
		'name'    => 'metas',
		// Description for this section (optional text to display below H2)
		'desc'    => __( 'Some of your contents may not be browsable publicly, tell us which ones.', 'seo-key' ),
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => __( 'Metas and URL', 'seo-key' )
	];
	return $sections;
}

add_filter( 'seokey_filter_get_config_fields', 'seokey_settings_add_base_sections_fields' );
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
function seokey_settings_add_base_sections_fields( $fields ) {
	// Define correct ID
	$unique_ID = 'settings';
	// Get title and meta fields
	$fields_title_meta  = seokey_settings_add_title_meta( $unique_ID );
	$fields             = ( !empty( $fields_title_meta) ) ? array_merge( $fields, $fields_title_meta ) : $fields;
	// Get schema.org fields
	$fields_schema_org  = seokey_settings_add_schema_org( $unique_ID );
	$fields             = ( !empty( $fields_schema_org) ) ? array_merge( $fields, $fields_schema_org ) : $fields;
	// Get content fields
	$fields_contents    = seokey_settings_add_contents( $unique_ID );
	$fields             = ( !empty( $fields_contents) ) ? array_merge( $fields, $fields_contents ) : $fields;
	// Return all fields
	return $fields;
}

add_filter( 'seokey_filter_get_config_fields', 'seokey_settings_add_base_sections_fields_author', SEOKEY_PHP_INT_MAX );
/**
 * Add our author fields for this setting page
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
function seokey_settings_add_base_sections_fields_author( $fields ) {
    // Define correct ID
    $unique_ID = 'settings';
    // Get title and meta fields
    // Get content fields
    $fields_contents    = seokey_settings_add_contents_author( $unique_ID );
    $fields             = ( !empty( $fields_contents) ) ? array_merge( $fields, $fields_contents ) : $fields;
    // Return all fields
    return $fields;
}


/********************************************************************** SEO Optimizations **********************************************************************/

add_filter( 'seokey_filter_get_config_sections', 'seokey_settings_automatic_optimizations_tab', SEOKEY_PHP_INT_MAX );
/**
° Automatic optimization tab
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_filter_get_config_sections
° @param array $tabs actual tabs
° @return array New tab array
 */
function seokey_settings_automatic_optimizations_tab( $tabs ){
    // Add one group section
    $tabs[] = [
        // Section Unique ID
        'ID'      => 'settings',
        // Name of the section
        'name'    => 'seooptimizations',
        // Tabname. If you want this section in a tabulation navigation, this field must be filled
        'tabname' => esc_html__( 'SEO optimizations', 'seo-key' )
    ];
    return $tabs;
}

// TODO Comment
add_filter( 'seokey_action_setting_sections_after', 'seokey_settings_automatic_optimizations_tab_content', SEOKEY_PHP_INT_MAX );
function seokey_settings_automatic_optimizations_tab_content( $id_section ) {
    if ( $id_section === 'seokey-section-seooptimizations' ) {
	    seokey_automatic_optimizations_top();
    }
}

// TODO Comment
add_filter( 'seokey-settings-api-after-option-seokey-field-seooptimizations-wizardstatus', 'seokey_settings_automatic_optimizations_tab_content_list', SEOKEY_PHP_INT_MAX );
function seokey_settings_automatic_optimizations_tab_content_list() {
	seokey_automatic_optimizations();
}

add_filter( 'seokey_filter_get_config_fields', 'seokey_settings_add_seooptimizations_sections_fields' );
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
function seokey_settings_add_seooptimizations_sections_fields( $fields ) {
	// Define correct ID
	$unique_ID = 'settings';
	// Get manuel SEO optimizations
	$fields_title_meta  = seokey_settings_add_seooptimizations_fields( $unique_ID );
	$fields             = ( !empty( $fields_title_meta) ) ? array_merge( $fields, $fields_title_meta ) : $fields;
	// Return all fields
	return $fields;
}

/**
 * Add Title and meta settings if homepage is post listing
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_seooptimizations_fields( $unique_ID ) {
	$fields   = [];
	// No pagination on author pages
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'           => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
		// Field Name (ID)
		'name'              => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'pagination-authors' ),
		// Field Title and description
		'title'             => __( 'SEO optimizations', 'seo-key' ),
		'desc'              => __( 'Some SEO optimizations need manual choices depending on your theme and plugins. Select which optimization to activate.', 'seo-key' ),
		'desc-position'     => 'above',
		'label'             => __( 'No pagination on author pages', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'              => 'checkbox',
		'placeholder'       => esc_html__( 'Username', 'seo-key' ),
		'has-sub-explanation'   => true,
		'default'               => true,
		'class'                 => 'hidedefault',
	];
	// No paginated comments
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'           => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
		// Field Name (ID)
		'name'              => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'pagination-comments' ),
		// Field Title and description
		'label'             => __( 'No pagination with comments', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'              => 'checkbox',
		'placeholder'       => esc_html__( 'Username', 'seo-key' ),
		'has-sub-explanation'   => true,
		'default'               => true,
		'class'                 => 'hidedefault',
	];
	// No reply to comments
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'           => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
		// Field Name (ID)
		'name'              => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'replylinks' ),
		// Field Title and description
		'label'             => __( 'No "reply to" links for each comment', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'              => 'checkbox',
		'placeholder'       => esc_html__( 'Username', 'seo-key' ),
		'has-sub-explanation'   => true,
		'default'               => true,
		'class'                 => 'hidedefault',
	];
	// Hide noindexed content from main loops
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'           => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
		// Field Name (ID)
		'name'              => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'hide-noindexed' ),
		// Field Title and description
		'label'             => __( 'Hide noindexed content from main loops', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'                  => 'checkbox',
		'placeholder'           => esc_html__( 'Username', 'seo-key' ),
		'has-sub-explanation'   => true,
		'default'               => true,
		'class'                 => 'hidedefault',
	];
	// Deactivate and redirect secondary RSS feeds
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                    => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'               => seokey_settings_api_get_page_section( $unique_ID, 'seooptimizations' ),
		// Field Name (ID)
		'name'                  => seokey_settings_api_get_page_field( $unique_ID, 'seooptimizations', 'rss-secondary' ),
		// Field Title and description
		'label'                 => __( 'Deactivate and redirect secondary RSS feeds to the main feed', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'                  => 'checkbox',
		'placeholder'           => esc_html__( 'Username', 'seo-key' ),
		'has-sub-explanation'   => true,
		'default'               => true,
		'class'                 => 'hidedefault',
	];
	return $fields;
}

/********************************************************************** TOOLS **********************************************************************/
add_filter( 'seokey_filter_get_config_sections', 'seokey_settings_tools_tab', SEOKEY_PHP_INT_MAX );
/**
° Tools tab
 *
° @author  Daniel Roch° SeoMix
° @since  0.0.1
 *
° @hook seokey_filter_get_config_sections
° @param array $tabs actual tabs
° @return array New tab array
 */
function seokey_settings_tools_tab( $tabs ){
    // Add one group section
    $tabs[] = [
        // Section Unique ID
        'ID'      => 'settings',
        // Name of the section
        'name'    => 'tools',
        // Description for this section (optional text to display below H2)
        'desc'    => __( 'You may need some tools to correctly use SEOKEY', 'seo-key' ),
        // Tabname. If you want this section in a tabulation navigation, this field must be filled
        'tabname' => __( 'Tools', 'seo-key' )
    ];
	$tabs[] = [
		// Section Unique ID
		'ID'      => 'settings',
		// Name of the section
		'name'    => 'licence',
		// Description for this section (optional text to display below H2)
		'desc'    => __( 'You may need some tools to correctly use SEOKEY', 'seo-key' ),
		// Tabname. If you want this section in a tabulation navigation, this field must be filled
		'tabname' => __( 'Licence & import', 'seo-key' )
	];
    return $tabs;
}

add_filter( 'seokey_filter_get_config_fields', 'seokey_settings_add_tools_sections_fields', 2 );
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
function seokey_settings_add_tools_sections_fields( $fields ) {
    // Define correct ID
    $unique_ID = 'settings';
    // Get title and meta fields
    $fields_title_meta  = seokey_settings_add_tools_fields( $unique_ID );
    $fields             = ( !empty( $fields_title_meta) ) ? array_merge( $fields, $fields_title_meta ) : $fields;
        // Return all fields
    return $fields;
}

/**
 * Add htpassdata
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_tools_fields( $unique_ID ) {
	$fields   = [];
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'                => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'           => seokey_settings_api_get_page_section( $unique_ID, 'tools' ),
		// Field Name (ID)
		'name'              => seokey_settings_api_get_page_field( $unique_ID, 'tools', 'htpasslogin' ),
		// Type field used for generating field with correct callback function
		'type'              => 'text',
		'placeholder'       => esc_html__( 'Username', 'seo-key' ),
		'title'             => esc_html__( 'Htaccess/htpasswd protection', 'seo-key' ),
		'has-explanation'   => true,
	];
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'          => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'     => seokey_settings_api_get_page_section( $unique_ID, 'tools' ),
		// Field Name (ID)
		'name'        => seokey_settings_api_get_page_field( $unique_ID, 'tools', 'htpasspass' ),
		// Type field used for generating field with correct callback function
		'type'        => 'password',
		'placeholder' => esc_html__( 'Password', 'seo-key' ),
	];
	return $fields;
}

add_action('seokey_action_setting_sections_before', 'seokey_settings_breadcrumbs_text', 20, 1);
// TODO comments
function seokey_settings_breadcrumbs_text( $data ){
	if ( "seokey-section-tools" === $data ) {
		$code = 'if ( function_exists( "seokey_breacrumbs_print" ) ) {
        echo seokey_breacrumbs_print();
}';
		echo '<h2 class="setting-description has-explanation" >';
		_e( 'Breadcrumbs', 'seo-key' ) ;
		echo seokey_helper_help_messages( 'breadcrumb-add');
		echo '</h2>';
		echo '<p>'. __( 'If you want to display a breadcrumb to Google and users, use the <strong>[seokey_breadcrumbs]</strong> shortcode or copy and paste the code below into your theme.', 'seo-key' ) . '</p>';
		echo '<pre><code class="customcode" id="copy-breadcrumb">';
            echo htmlspecialchars( $code );
		echo '</code></pre>';
        echo "<button class='seokey-button button-secondary' id='copy-breadcrumb-button'>" . __( 'Copy code', 'seo-key') . "</button>";
        echo "<span id='copy-breadcrumb-message'>" .__( 'Copied', 'seo-key') ."</span>";
	}
}

add_action('seokey_action_setting_field_before_field', 'seokey_settings_add_htpass_desc_nouse', 20, 1);
// TODO comments
function seokey_settings_add_htpass_desc_nouse( $field ){
	if ( "seokey-field-tools-htpasslogin" === $field['id'] ) {
		echo '<p>' . __( 'It seems your website is not password protected or that your actual credentials are already correct.<br>Check the box below to display htaccess/htpasswd credentials anyway.', 'seo-key' ) . '</p>';
		echo '<input autocomplete="off" id="showhtpass" name="showhtpass" type="checkbox">';
		echo '<label for="showhtpass">' . esc_html__( 'Show credentials', 'seo-key' ) . '</label><br>';
	}
}

add_action('seokey_action_setting_table_after', 'seokey_settings_import_text', 30, 1);
// TODO Comments
function seokey_settings_import_text( $data ) {
	if ( "seokey-section-licence" === $data ) {
		seokey_admin_import_display( true );
	}
}