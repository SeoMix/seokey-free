<?php
/**
 * Load SEOKEY Admin Settings for content configuration
 *
 * @Loaded  on 'init'
 * @Loaded  on is_admin() condition
 * @Loaded  with plugin configuration file + admin-menus-and-links.php + admin-page-settings.php
 * @loaded from seokey_filter_get_config_fields()
 *
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

/**
 * Add Content settings
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_contents( $unique_ID ) {
	/**
	 * Contents
	 */
	// Get all public, CPTs from WP
	$_builtin = get_post_types( ['_builtin' => true, 'public' => true ], 'objects' );
	// Get all custom CPTs
	$_custom = get_post_types( ['_builtin' => false, 'public' => true ], 'objects' );
	// Merge them
	$all_cpts = apply_filters( 'seokey_filter_settings_add_contents_post_types', array_merge( $_builtin, $_custom ) );
	// Set the values with keys and labels
	$values = wp_list_pluck( $all_cpts, 'label' );
	// Set the default with keys and public bool value
	$default = wp_list_pluck( $all_cpts, 'public' );
	// Remove the false bool value
	$default = array_filter( $default );
	// Remove CPT Attachement //
	unset( $default['attachment'] );
	unset( $values['attachment'] );
	// Cache data
	seokey_helper_cache_data( 'seokey_settings_add_base_sections_fields_posts', wp_list_pluck( $all_cpts, 'label' ) );
	// Visibility for CPTs
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'cct' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'cct', 'cpt' ),
		// Field Title
		'title'   => __( 'Are these content types useful for Google or users?', 'seo-key' ),
		// A better description for our setting (optional)
		'desc'    => __( 'Some contents may not be useful: tell us which ones to hide from Google (noindex).', 'seo-key' ),
        'desc-position' => 'above',
		// Type field used for generating field with correct callback function (optional, default is 'text').
		'type'    => 'multi_checkbox_slide',
		// Set a size
		'size'    => 'size-normal',
		// Send the users settings values
		'values'  => $values,
		// Send the default values
		'default' => $default,
        // Do we need an explanation ?
        'has-explanation' => true,
		'has-sub-explanation' => true,
        // Button values
		'args'    => [
			'label_on'  => _x( 'Show', 'Status of the `public` setting for a custom post type set on true', 'seo-key' ),
			'label_off' => _x( 'Hide', 'Status of the `public` setting for a custom post type set on false', 'seo-key' )
		]
	];
	// Get all public taxonomies from WP
	$_builtin = get_taxonomies( ['_builtin' => true, 'public' => true ], 'objects' );
	// Get all custom taxonomies
	$_custom = get_taxonomies( ['_builtin' => false, 'public' => true ], 'objects' );
	// Merge them
	$all_taxos = array_merge( $_builtin, $_custom );
	// Remove post format taxonomie
	if ( isset ( $all_taxos['post_format'] ) ) {
		unset( $all_taxos['post_format'] );
	}
	// Filtering
	$all_taxos = apply_filters( 'seokey_filter_settings_add_contents_taxonomies', $all_taxos );
	// Set the values with keys and labels
	$values = wp_list_pluck( $all_taxos, 'label' );
	// Set the default with keys and public bool value
	$default = wp_list_pluck( $all_taxos, 'public' );
	// Remove the false bool value
	$default = array_filter( $default );
	// Visibility for Taxos
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'cct' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'cct', 'taxo' ),
		// Field Title
		'title'   => __( 'Are these taxonomies useful for Google or users?', 'seo-key' ),
		// A better description for our setting (optional)
        'desc'    => __( 'Some taxonomies may not be useful. Tell us which ones.', 'seo-key' ),
        'desc-position' => 'above',
		// Type field used for generating field with correct callback function (optional, default is 'text').
		'type'    => 'multi_checkbox_slide',
		// Set a size
		'size'    => 'size-normal',
		// Send the users settings values
		'values'  => $values,
		// Send the default values
		'default' => $default,
        // Do we need an explanation ?
        'has-explanation' => true,
		'has-sub-explanation' => true,
        // Button values
		'args'    => [
			'label_on'  => _x( 'Show', 'Status of the `public` setting for a custom post type set on true', 'seo-key' ),
			'label_off' => _x( 'Hide', 'Status of the `public` setting for a custom post type set on false', 'seo-key' )
		]
	];
	/**
	 * Titles and metas
	 */
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'metas' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'metas', 'category_base' ),
		// Field Title
		'title'   => __( 'URL optimization', 'seo-key' ),
		// The the dedicated <label> for this field
		'label'   => sprintf( __( 'Remove and redirect "/category/" part in categories URL', 'seo-key' ), '<code>&lt;title&gt;</code>' ),
		// A better description for our setting (optional)
		'desc'    => __( 'Categories URLs have a "category" prefix. It is not useful for Google and it makes them harder to read (Ex. /category/category-name/).<br>You should remove them.', 'seo-key' ),
        'desc-position' => 'above',
        // Type field used for generating field with correct callback function
		'type'    => 'checkbox',
	];
	return $fields;
}

add_filter ('seokey_filter_helper_help_messages', 'seokey_settings_add_contents_explanations',10, 2 );
// TODO Comments
function seokey_settings_add_contents_explanations( $value, $messageid ) {
	// Avoid PHP 8.2 errors
	if ( $value === false ) {
		$value = array();
	}
	// Post types
	if ( str_starts_with( $messageid, 'seokey-field-cct-cpt-label-') ) {
		$post_type = str_replace ('seokey-field-cct-cpt-label-', '', $messageid );
		$recent_post = wp_get_recent_posts(array(
			'numberposts' => 1,
			'post_status' => 'publish',
			'post_type' => $post_type
		));
		$labels = get_post_type_object( $post_type );
		$value['h2']    = sprintf( esc_html__( 'Does "%s" content type is useful ?', 'seo-key' ), $labels->labels->name );
		$value['text']  = sprintf( __( 'You can create and manage contents called <strong>"%s"</strong> (it\'s one of your "post types").', 'seo-key'), $labels->labels->name );
		$value['text']  .= '<br><br>';
		$value['text']  .= __( '<strong>What is a post type ?</strong> ', 'seo-key' );
		$value['text']  .= esc_html__( "It is a kind of content. For example, WordPress have two default post types : posts and pages.", 'seo-key' );
		$value['text']  .= '<br><br>';
		if ( !empty( $recent_post ) ) {
			$value['text']  .= sprintf( __( 'Here is an example of content found for this post type: <strong><a href="%s" target="_blank">%s</a></strong>', 'seo-key' ), get_permalink( $recent_post[0]['ID'] ), esc_html( $recent_post[0]['post_title'] ) );
			$value['text']  .= '<br><br>';
			$value['text']  .= __( '<strong>Is it useful for users or for Google ?</strong><br>If your answer is no, select "Hide". If you don\'t know, keep the "Show" option.', 'seo-key' );
		} else {
			$value['text']  .= sprintf( __( 'Actually, you do not have created any "%s".', 'seo-key' ), $labels->labels->name );
			$value['text']  .= '<br><br>';
			$value['text']  .= __( 'If you are not planning on using this content type, <strong>you should hide it from Google</strong>. To do so, select "Hide". If you don\'t know, keep the "Show" option.', 'seo-key' );
		}
		$value['text']  = wp_kses_post( $value['text'] );
	}
	// taxonomies
	if ( str_starts_with( $messageid, 'seokey-field-cct-taxo-label-') ) {
		$taxonomy = str_replace ('seokey-field-cct-taxo-label-', '', $messageid );
		$recent_term = get_terms( $taxonomy, [
				'orderby'   => 'id',
				'order'     => 'DESC',
				'number'    => 1,
			]
		);
		$recent_term = reset( $recent_term );
		$labels = get_taxonomy( $taxonomy );
		$value['h2']    = sprintf( esc_html__( 'Does "%s" taxonomy is useful ?', 'seo-key' ), $labels->labels->name );
		$value['text']  = sprintf( __( 'You can sort contents with the <strong>"%s"</strong> taxonomy.', 'seo-key'), $labels->labels->name );
		$value['text']  .= '<br><br>';
		$value['text']  .= __( '<strong>What is a taxonomy?</strong> ', 'seo-key' );
        $value['text']  .= esc_html__( "It is used to display a list of contents. For example, 'categories' are a taxonomy used to display 'posts'.", 'seo-key' );

		$value['text']  .= '<br><br>';
		if ( !is_wp_error( $recent_term ) && !empty( $recent_term ) ) {
			$value['text']  .= sprintf( __( 'Here is an example of content found for this taxonomy: <strong><a href="%s" target="_blank">%s</a></strong>', 'seo-key' ), get_term_link( $recent_term->term_id ), esc_html( $recent_term->name ) );
		} else {
			$value['text']  .= sprintf( __( 'Actually, you do not have created any "%s".', 'seo-key' ), $labels->labels->name );
		}
		$value['text']  .= '<br><br>';
		$value['text']  .= __( '<strong>Is it useful for users or for Google ?</strong><br>If your answer is no, select "Hide".  If you don\'t know, keep the "Show" option.', 'seo-key' );
		$value['text']  = wp_kses_post( $value['text'] );
	}
	// Autors
	if ( 'seokey-field-cct-pages-label-author' === $messageid ) {
		$value['h2']    = esc_html__( 'Are authors pages useful ?', 'seo-key' );
		$user_query = new WP_User_Query(
			array(
				'number'         => '1',
				'count_total'    => true,
			)
		);
		$user_query = $user_query->get_results();
		$value['text']  = __( 'You can show author pages to Google.', 'seo-key');
		$value['text']  .= '<br><br>';
		if ( !is_wp_error( $user_query ) && !empty( $user_query ) ) {
			$data = get_object_vars( $user_query[0]->data );
			$value['text']  .= sprintf( __( 'Here is an example author URL : <strong><a href="%s" target="_blank">%s</a></strong>', 'seo-key' ),
				get_author_posts_url( $data['ID'] ), $data['display_name'] );
		} else {
			$value['text']  .= sprintf( __( 'Actually, you do no have created any author URL.', 'seo-key' ) );
		}
		$value['text']  .= '<br><br>';
		$value['text']  .= __( '<strong>Is it useful for users or for Google ?</strong><br>If your answer is no, select "Hide".  If you don\'t know, keep the "Show" option.', 'seo-key' );
		$value['text']  = wp_kses_post( $value['text'] );
	}
	// Return final value
	return $value;
}












/**
 * Add Content settings
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_contents_author( $unique_ID ) {
    $values = ['author' => __('Author')];
    // Visibility for special pages/templates
    $fields[] = [
        // You must use data from seokey_settings_api_get_config_sections()
        'ID' => $unique_ID,
        // Field Section : current section where this setting will be displayed.
        'section' => seokey_settings_api_get_page_section($unique_ID, 'cct'),
        // Field Name (ID)
        'name' => seokey_settings_api_get_page_field($unique_ID, 'cct', 'pages'),
        // Field Title
        'title' => __('Are author pages useful for Google or users?', 'seo-key'),
        // A better description for our setting (optional)
        'desc' => __('Author pages can also be on Google. Tell us what to do with them.', 'seo-key'),
        'desc-position' => 'above',
        // Type field used for generating field with correct callback function (optional, default is 'text').
        'type' => 'multi_checkbox_slide',
        // Set a size
        'size' => 'size-normal',
        // Send the users settings values
        'values' => $values,
        // Send the default values
        'default' => ['author' => true],
        // Do we need an explanation ?
        'has-explanation' => true,
        'has-sub-explanation' => true,
        // Button values
        'args' => [
            'label_on' => _x('Show', 'Status of the `public` setting for a custom post type set on true', 'seo-key'),
            'label_off' => _x('Hide', 'Status of the `public` setting for a custom post type set on false', 'seo-key')
        ]
    ];
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID' => 'wizard',
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section($unique_ID, 'cct'),
		// Field Name (ID)
		'name' => seokey_settings_api_get_page_field($unique_ID, 'cct', 'pages'),
		// Field Title
		'title' => __('Are author pages useful for Google or users?', 'seo-key'),
		// A better description for our setting (optional)
		'desc' => __('Author pages can also be on Google. Tell us what to do with them.', 'seo-key'),
		'desc-position' => 'above',
		// Type field used for generating field with correct callback function (optional, default is 'text').
		'type' => 'multi_checkbox_slide',
		// Set a size
		'size' => 'size-normal',
		// Send the users settings values
		'values' => $values,
		// Send the default values
		'default' => ['author' => true],
		// Do we need an explanation ?
		'has-explanation' => true,
		'has-sub-explanation' => true,
		// Button values
		'args' => [
			'label_on' => _x('Show', 'Status of the `public` setting for a custom post type set on true', 'seo-key'),
			'label_off' => _x('Hide', 'Status of the `public` setting for a custom post type set on false', 'seo-key')
		]
	];
    return $fields;
}







add_filter( 'seokey_filter_get_config_fields', 'seokey_settings_add_base_sections_fields_taxonomy', 30 );
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
function seokey_settings_add_base_sections_fields_taxonomy( $fields ) {
	// Define correct ID
	$unique_ID = 'settings';
	// First values
	$title = __( 'Help us understand your different content types', 'seo-key' );
	$explanation = true;
	// get post type list
	$posts_types = seokey_helper_cache_data( 'seokey_settings_add_base_sections_fields_posts' );
	if ( isset( $posts_types['attachment'] ) ){
		unset( $posts_types['attachment'] );
	}
	$count = 0;
	$values = [];
	foreach ( $posts_types as $key => $post_type ) {
		// After first one, change some values
		$count++;
		if ( $count > 1 ) {
			$title = '';
			$explanation = false;
		}
		$field = 'taxonomy-choice-' . $key;
		// Remove bad taxonomies
		$exclude = apply_filters( 'seokey_settings_filter_taxonomy_choice', [ 'post_format' ] );
		$taxonomies = get_object_taxonomies( $key );
		$taxonomies = array_diff( $taxonomies, $exclude );
		// Define $values
		foreach ( $taxonomies as $taxonomy ) {
				$values[$taxonomy] = get_taxonomy($taxonomy)->labels->name;
		}
		if ( !empty( $values ) ) {
            if ( count($values) > 1 ) {
                $type = 'select';
            } else {
                $type = 'select-one';
            }
            $newfields[] = [
                // You must use data from seokey_settings_api_get_config_sections()
                'ID' => $unique_ID,
                // Field Section : current section where this setting will be displayed.
                'section' => seokey_settings_api_get_page_section($unique_ID, 'cct'),
                // Field Name (ID)
                'name' => seokey_settings_api_get_page_field($unique_ID, 'cct', $field),
                // Field Title
                'title' => $title,
                // A better description for our setting (optional)
                'desc' => sprintf(__('What makes it easier to describe or categorize this type of content "%s"?', 'seo-key'), $post_type),
                'desc-position' => 'above',
                // Type field used for generating field with correct callback function
                'type' => $type,
                // Values
                'values' => $values,
                'class' => 'tr-no-margin',
                // Do we need an explanation ?
                'has-explanation' => $explanation,
            ];
			$newfields[] = [
				// You must use data from seokey_settings_api_get_config_sections()
				'ID' => 'wizard',
				// Field Section : current section where this setting will be displayed.
				'section' => seokey_settings_api_get_page_section($unique_ID, 'cct'),
				// Field Name (ID)
				'name' => seokey_settings_api_get_page_field($unique_ID, 'cct', $field),
				// Field Title
				'title' => $title,
				// A better description for our setting (optional)
				'desc' => sprintf(__('What best describes "%s"?', 'seo-key'), $post_type),
				'desc-position' => 'above',
				// Type field used for generating field with correct callback function
				'type' => $type,
				// Values
				'values' => $values,
				'class' => 'tr-no-margin',
				// Do we need an explanation ?
				'has-explanation' => $explanation,
			];
            $fields = ( !empty( $newfields ) ) ? array_merge( $fields, $newfields ) : $fields;
            $newfields = [];
		}
		$values = [];
	}
	// Return all fields
	return $fields;
}


add_filter ( 'seokey_filter_setting_callback_switch_select-one', 'seokey_add_field__selection_one', 10, 6 );
/**
 * Add text instead of <select> when only one value is found for a post type taxonomy select
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @return string HTML text
 **/
function seokey_add_field__selection_one( $args, $type, $option_value, $option_name, $placeholder, $described_by ) {
	$post_type = str_replace('seokey-field-cct-taxonomy-choice-', '', $placeholder);
	$labels = get_post_type_labels(get_post_type_object( $post_type ) );
    return '<p class="select-one-text">' . sprintf(
				__( '<strong>%s</strong> is the only taxonomy for %s. We\'ve selected it for you', 'seo-key' ),
				reset($type['values']),
				$labels->name ) . '</p>';
}

add_action('seokey_action_setting_table_before','seokey_settings_add_base_sections_explain_visibility');
// TODO comment
function seokey_settings_add_base_sections_explain_visibility( $id){
	if ( 'seokey-section-cct' === $id ) {
		echo '<p>' . wp_kses_post( __( "For some of the options below, if you don't know what contents you need to hide, <strong>do not change them</strong>.<br>You can also use each question mark to have more information about them.", "seo-key" ) ) . '</p><br>';
	}
}