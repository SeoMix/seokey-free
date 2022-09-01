<?php
/**
 * Load SEOKEY Admin Settings for homepage title and description
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
 * Add Title and meta settings if homepage is post listing
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_title_meta( $unique_ID ) {
    $fields = [];
    $fields[] = [
        // You must use data from seokey_settings_api_get_config_sections()
        'ID'          => $unique_ID,
        // Field Section : current section where this setting will be displayed.
        'section'     => seokey_settings_api_get_page_section( $unique_ID, 'metas' ),
        // Field Name (ID)
        'name'        => seokey_settings_api_get_page_field( $unique_ID, 'metas', 'metatitle' ),
        // Field Title
        'title'       => __( 'Homepage information for Google', 'seo-key' ),
        // A better description for our setting (optional)
        'desc'        => sprintf( __( 'It will tell Google what your website is about (%s HTML tag).', 'seo-key' ), '<code>&lt;title&gt;</code>' ),
        'desc-position' => 'above',
        // Type field used for generating field with correct callback function
        'type'        => 'text',
        'placeholder' => get_bloginfo( 'name' ),
        // Do we need an explanation ?
        'has-explanation' => true,
    ];
    $fields[] = [
        // You must use data from seokey_settings_api_get_config_sections()
        'ID'          => $unique_ID,
        // Field Section : current section where this setting will be displayed.
        'section'     => seokey_settings_api_get_page_section( $unique_ID, 'metas' ),
        // Field Name (ID)
        'name'        => seokey_settings_api_get_page_field( $unique_ID, 'metas', 'metadesc' ),
        // A better description for our setting (optional)
        'desc'        => sprintf( __( "Google may show it on search result pages to describe your homepage (%s HTML tag).", 'seo-key' ), '<code>&lt;meta name="description"&gt;</code>' ),
        'desc-position' => 'above',
        // Type field used for generating field with correct callback function
        'type'        => 'textarea',
        'placeholder' => get_bloginfo( 'description' ),
    ];
	return $fields;
}


add_action( 'seokey_action_setting_field_after_field', 'seokey_settings_add_title_meta_edit_link', 10 ,1 );
/**
 * Add link to static front-ppage edit when wizard has ended
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_title_meta_edit_link( $data ) {
	// Only for oou metadesc field
	if ( 'seokey-field-metas-metadesc' === $data['id'] ) {
		$front_option   = get_option( 'page_on_front' );
		$home_is_page   = ( $front_option > 0 ) ? $front_option : false;
		$current_wizard = get_option( 'seokey_option_first_wizard_seokey_notice_wizard' );
		// Only if we are using a static page and if wizard has ended
		if ( $front_option > 0 && 'goodtogo' === $current_wizard ) {
			$name = get_the_title( (int) $home_is_page );
			echo '<p>' . esc_html__('You can also edit directly your homepage here: ', 'seo-key');
			echo '<a target="_blank" class="button button-secondary" href="' . get_edit_post_link( $home_is_page ) . '">';
			printf( esc_html__( 'Edit "%1$s" content.', 'seo-key' ), $name );
			echo '</a>';
			echo '</p>';
		}
	}
}