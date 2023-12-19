<?php
/**
 * Third party: Plugin ACF
 *
 * @Loaded on plugins_loaded + wizard done
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

if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) || is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
    // Check if acf_render_field_setting() exists to prevent any errors for adding field settings
    if ( function_exists( 'acf_render_field_setting' ) ) {
        add_action( 'init', 'seokey_acf_render_field_settings' );
        /**
         * Define allowed ACF fields to add the SeoKey audit option and add them to ACF
         *
         * @since   1.8.0
         * @author  Arthur Leveque
         *
         */
        function seokey_acf_render_field_settings () {
            // Set allowed fields for audit
            $allowedTypes = [
                'text',
                'textarea',
                'email',
                'url',
                'wysiwyg',
                'image',
                'gallery'
            ];
            // Filter if we need more (or less) allowed ACF fields
            $allowedTypes = apply_filters( 'seokey_filter_acf_allowed_types', $allowedTypes );
            // Only add the SeoKey setting to the allowed fields
            foreach ( $allowedTypes as $allowedType ) {
                add_action( 'acf/render_field_settings/type=' . $allowedType, 'seokey_acf_add_is_auditable_setting' );
            }
        }
    }
    /**
     * Add the "seokey_is_auditable" setting for all allowed ACF fields
     *
     * @since   1.8.0
     * @author  Arthur Leveque
     *
     */
    function seokey_acf_add_is_auditable_setting( $field ) {
        acf_render_field_setting( $field, array(
	        'label'        => __( 'Audit this field with SEOKEY', 'seo-key' ),
	        'instructions' => __( 'Default ACF fields are not included in SEOKEY audits. If this field is important and displayed within your content, activate this setting.', 'seo-key' ),
	        'name'         => 'seokey_is_auditable',
            'type'         => 'true_false',
            'ui'           => 1,
        ), true ); // If adding a setting globally, you MUST pass true as the third parameter!
    }

    /**
     * Use the php function array_key_exists() in a multidimensional array to get values of group and repeater type fields in a new array 
     *
     * @param array $values value of the repeater or group type field
     * @param array $field field that we need (must be a child of a repeater or a group)
     * @param array $acf_fields value for the key we need
     * @author  Arthur Leveque
     * @since   1.8.0
     */
    function seokey_acf_extract_values_from_group_repeater( $values, $field, &$acf_fields ) {
        // Is in current array?
        if ( array_key_exists( $field['name'], $values ) && !empty( $values[$field['name']] ) ) {
            $field['value'] = $values[$field['name']];
            // Add the value to the field and push it to the final array
            array_push( $acf_fields, $field );
        }
        // Check arrays contained in this array
        foreach ( $values as $value ) {
            // If our current element is an array, do a recursive call
            if ( is_array( $value ) ) {
                seokey_acf_extract_values_from_group_repeater( $value, $field, $acf_fields );
            }
        }
        return false;
    }

    add_filter( 'seokey_filter_helper_audit_content_data', 'seokey_acf_add_fields_to_content_audit', 10, 2 );
	/**
	 * Add ACF fields to content audit
	 *
	 * @param string $content Content of the post
	 * @param object $post Post data
	 * @since   1.8.0
	 * @author  Arthur Leveque
	 *
	 */
	function seokey_acf_add_fields_to_content_audit( $content, $post ){
        if ( function_exists( 'get_field_objects' ) ) {
            $field_objects = get_field_objects( $post->ID ); // Get all ACF fields from post
            $acf_fields = array();
            // Only do the group and repeater extraction if we have the correct functions !
            if ( function_exists( 'have_rows' ) && function_exists( 'the_row' ) && function_exists( 'get_row' ) ) {
                // Check if we have something in $field_objects or else it will fail
                if ( $field_objects ) {
                    // Extract all auditable fields from current fields
                    $all_auditable_fields = array();
                    seokey_helpers_get_subarrays_with_key( $field_objects, 'seokey_is_auditable', 1, $all_auditable_fields );
                    // Values from $all_auditable_fields who comes from repeaters and groups are empty ! We need to give them the correct values
                    foreach ( $field_objects as $field_object ) {
                        // Check for desired field types, add everything else
                        switch ( $field_object['type'] ) {
                            case 'group':
                            case 'repeater':
                                // Check if we have any data in the field
                                if ( have_rows( $field_object['key'] ) ) {
                                    $values = $field_object['value']; // Get the field values.
                                    // Go through all our auditable fields
                                    foreach ( $all_auditable_fields as $auditable_field ) {
                                        // Add the fields to $acf_fields with their correct values !
                                        seokey_acf_extract_values_from_group_repeater( $values, $auditable_field, $acf_fields );
                                    }
                                }
                                break;
                            default:
                                array_push( $acf_fields, $field_object );
                        }
                    }
                }
            } else {
                // If the functions does not work, do the original way
                $acf_fields = $field_objects;
            }
            // Abort if there is no ACF fields
            if ( !empty( $acf_fields ) ) { 
                $content_to_add = ''; // Prepare a var to add ACF fields values
                foreach ( $acf_fields as $acf_field ) {
                    // Abort if the "seokey_is_auditable" option is not here or if it is not active
                    if ( !isset( $acf_field['seokey_is_auditable'] ) || $acf_field['seokey_is_auditable'] !== 1 ) {
                        continue;
                    }
                    // Abort if the field is empty
                    if ( empty( $acf_field['value'] ) ) {
                        continue;
                    }
                    // We had different content depending of the field type
                    switch ( $acf_field['type'] ) {
                        case 'url' :
                            // Add "-" in the link content to avoid some audit tasks like keywords in content
                            $content_to_add .= ' <a href="' . esc_url( $acf_field['value'] ) . '">ACF-link-added-with-SeoKey</a>';
                            break;
                        case 'image' :
                            switch ( $acf_field['return_format'] ) {
                                case 'array' :
                                    $content_to_add .= ' <img src="' . esc_url( $acf_field['value']['url'] ) . '" alt="' . esc_attr( $acf_field['value']['alt'] ) . '">';
                                    break;
                                case 'url' :
                                    $content_to_add .= ' <img src="' . esc_url( $acf_field['value'] ) . '" alt="ACF-img-added-with-SeoKey">';
                                    break;
                                case 'id' :
                                    $content_to_add .= ' ' . wp_get_attachment_image( (int) $acf_field['value'] );
                                    break;
                            }      
                            break;
                        case 'gallery' :
                            foreach ( $acf_field['value'] as $img ) {
                                switch ( $acf_field['return_format'] ) {
                                    case 'array' :
                                        $content_to_add .= ' <img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $img['alt'] ) . '">';
                                        break;
                                    case 'url' :
                                        $content_to_add .= ' <img src="' . esc_url( $img ) . '" alt="ACF-img-added-with-SeoKey">';
                                        break;
                                    case 'id' :
                                        $content_to_add .= ' ' . wp_get_attachment_image( (int) $img );
                                        break;
                                }     
                            }
                            break;
                        default :
                            $content_to_add .= ' ' . esc_html( $acf_field['value'] );
                    }
                    // Filter for other fields entered in the filter "seokey_filter_acf_allowed_types" 
                    $content_to_add = apply_filters( 'seokey_filter_acf_content_to_audit', $content_to_add, $acf_field );
                }
                // Add our stringified ACF fields to the content
                $content = $content . $content_to_add;
            }
        }
        return $content;
    }

    add_filter( 'acf/prepare_field', 'seokey_acf_add_class_to_auditable_field' );
    /**
	 * Add css class to ACF fields for single content audit purpose
	 *
	 * @param array ACF field
	 * @since   1.8.0
	 * @author  Arthur Leveque
	 *
	 */
    function seokey_acf_add_class_to_auditable_field( $field ) {
        // Exit early if field is not auditable
        if ( !isset( $field['seokey_is_auditable'] ) || $field['seokey_is_auditable'] !== 1 ) {
            return $field;
        }
        // Add the "seokey-is-auditable" css class to the field for the single content audit with JS
        $field['wrapper']['class'] .= ' seokey-is-auditable';
        return $field;
    }
}