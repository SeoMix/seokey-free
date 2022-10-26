<?php
/**
 * Load SEOKEY Admin Settings for schema.org
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
 * Add schema.org settings
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $unique_ID ID of current settings
 * @return array Fields to add
 */
function seokey_settings_add_schema_org( $unique_ID ) {
	/**
	 * Who are you?
	 */
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'context' ),
		// Field Title
		'title'         => __( 'What is your website about?', 'seo-key' ),
		'desc'          => __( 'Tell Search Engine more about you to improve your visibility: who are you?', 'seo-key' ),
		'desc-position' => 'above',
        // Type field used for generating field with correct callback function
		'type'    => 'select',
		// Options for the choices
		'values'  => [
			'0'              => __( 'I don’t want to display this information on Google', 'seo-key' ),
			'person'         => _x( 'A person', 'context from schema.org', 'seo-key' ),
			'local_business' => _x( 'A business, organization or association', 'context from schema.org', 'seo-key' ),
		],
		// Set a default value
		'default' => '0',
        // Do we need an explanation ?
        'has-explanation' => true,
	];
	/**
	 * Person
	 */
	$dashicon = '<span class="dashicons dashicons-admin-users"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-person' ),
		// Field Title
		'title'   => $dashicon . __( 'Personal information', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'    => 'schema-jsonld-person',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends' => ['field' => 'context', 'value' => ['person'] ]
	];
    $dashicon = '<span class="dashicons dashicons-format-image"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-person-image' ),
		// Field Title
		'title'   => $dashicon . __( 'Photo or avatar', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'    => 'image',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends' => ['field' => 'context', 'value' => ['person'] ]
	];
    $dashicon = '<span class="dashicons dashicons-networking"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'       => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'  => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'     => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-person-sameas' ),
		// Field Title
		'title'    => $dashicon . __( 'Social media profiles', 'seo-key' ),
		// Field Title
		'placeholder'    => esc_html_x( 'Ex. https://www.facebook.com/seokey.wp/', 'Placeholder', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'     => 'url',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends'  => ['field' => 'context', 'value' => ['person'] ],
		// This field can be duplicated
		'repeater' => true
	];
	/**
	 * Local Business
	 */
	$dashicon = '<span class="dashicons dashicons-cart"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-local_business' ),
		// Field Title
		'title'   => $dashicon . __( 'Business information', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'    => 'schema-jsonld-local_business',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends' => ['field' => 'context', 'value' => ['local_business'] ]
	];
    $dashicon = '<span class="dashicons dashicons-format-image"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'      => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section' => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'    => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-local_business-image' ),
		// Field Title
		'title'   => $dashicon . __( 'Logo*', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'    => 'image',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends' => ['field' => 'context', 'value' => ['local_business'] ],
		// Required field
		'args'    => ['required' => true ]
	];
	$dashicon = '<span class="dashicons dashicons-admin-home"></span> ';
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'       => $unique_ID,
		// Field Section : current section where this setting will be displayed.
		'section'  => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'     => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-local_business-is-store' ),
		// Field Title
		'title'    => $dashicon . __( 'Local Business', 'seo-key' ),
		// Type field used for generating field with correct callback function
		'type'     => 'schema-jsonld-local_business-is-store',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends'  => ['field' => 'context', 'value' => ['local_business'] ],
		// This field can be duplicated
		'repeater' => true
	];
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'       => $unique_ID,
		// Custom ID form field row
		'field_id' => 'local-business-opening-hours-specification',
		// Field Section : current section where this setting will be displayed.
		'section'  => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'     => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-local_business-pricing' ),
		// Field Title
		'title'    => '',
		// Type field used for generating field with correct callback function
		'type'     => 'schema-jsonld-local_business-pricing',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends'  => ['field' => 'local_business-is-store', 'value' => [ true ] ]
	];
	$fields[] = [
		// You must use data from seokey_settings_api_get_config_sections()
		'ID'       => $unique_ID,
		// Custom ID form field row
		'field_id' => 'local-business-opening-hours-specification',
		// Field Section : current section where this setting will be displayed.
		'section'  => seokey_settings_api_get_page_section( $unique_ID, 'schemaorg' ),
		// Field Name (ID)
		'name'     => seokey_settings_api_get_page_field( $unique_ID, 'schemaorg', 'schema-local_business-openingHoursSpecification' ),
		// Field Title
		'title'    => '',
		// Type field used for generating field with correct callback function
		'type'     => 'schema-jsonld-local_business-openingHoursSpecification',
		// this field will be displayed when the field 'context' (see above) will be checked on 'local_business'
		'depends'  => ['field' => 'local_business-is-store', 'value' => [ true ] ],
		// This field can be duplicated
		'repeater' => true
	];
	return $fields;
}


// SCHEMA
add_filter( 'seokey_filter_setting_callback_switch_schema-jsonld-person', 'seokey_add_field__schema_jsonld_person', 10, 5 );
/**
 * Add the "person" fields block
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   seokey_filter_setting_callback_switch_ . schema-jsonld-person
 *
 * @param seokey_filter_setting_callback_switch_ filter
 *
 * @return (string) $contents The DOM content for these fields
 **/
function seokey_add_field__schema_jsonld_person( $html, $args, $type, $option_value, $option_name ) {
	// Default values (always empty)
	$default = [
		'name'          => '',
		'alternateName' => '',
		'url'           => '',
		'jobTitle'      => '',
		'telephone'     => '',
		'worksFor'      => '',
		'image'         => '',
		'birthdate'     => '',
	];
	// Get the user one
	$option_value = wp_parse_args( $option_value, $default );
	// Do not display anything now
	ob_start();
	// Start the HTML content ?>
    <section class="settings-flex">
        <div>
            <?php _e( 'Name', 'seo-key' ); ?>*<br>
            <input required type="text" class="regular-text" name="<?php echo $option_name; ?>[name]" value="<?php echo esc_attr( $option_value['name'] ); ?>" placeholder="<?php _ex( 'e.g. John', 'placeholder', 'seo-key' ); ?>"/>
        </div>
        <div>
            <?php _e( 'Birthdate', 'seo-key' ); ?><br>
            <input type="date" class="regular-text" name="<?php echo $option_name; ?>[birthdate]" value="<?php echo esc_attr( $option_value['birthdate'] ); ?>"/>
        </div>
        <div>
            <?php _e( "Job title or role", 'seo-key' ); ?><br>
            <input type="text" class="regular-text" name="<?php echo $option_name; ?>[jobTitle]" value="<?php echo esc_attr( $option_value['jobTitle'] ); ?>" placeholder="<?php _ex( 'ex. SEO Consultant, Farmer', 'placeholder', 'seo-key' ); ?>"/>
        </div>
        <div>
            <?php _e( 'Company', 'seo-key' ); ?><br>
            <input type="text" class="regular-text" name="<?php echo $option_name; ?>[worksFor]" value="<?php echo esc_attr( $option_value['worksFor'] ); ?>" placeholder="<?php _ex( 'ex. Nike, Google', 'placeholder', 'seo-key' ); ?>"/>
        </div>
    </section>
	<?php
	// Get the HTML contents
	$contents = ob_get_contents();
	// Clean the buffer
	ob_end_clean();
	return $contents;
}

add_filter( 'seokey_filter_setting_callback_switch_schema-jsonld-local_business', 'seokey_add_field__schema_jsonld_local_business', 10, 5 );
/**
 * Add the "business" fields block
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   seokey_filter_setting_callback_switch_ . schema-jsonld-local_business
 * @param see seokey_filter_setting_callback_switch_ filter
 * @return (string) $contents The DOM content for these fields
 **/
function seokey_add_field__schema_jsonld_local_business( $html, $args, $type, $option_value, $option_name ) {
	// Default values (always empty)
	$default = [
		'name'              => '',
		'url'               => '',
        'streetAddress'     => '',
        'addressLocality'   => '',
        'addressCountry'    => '',
        'postalCode'        => '',
		'telephone'         => ''
	];
	// Get the user one
	$option_value = wp_parse_args( $option_value, $default );
	// Do not display anything now
	ob_start();
	// Start the HTML content
	?>
    <div>
        <?php _e( 'Name', 'seo-key' ); ?>*<br>
        <input required class="regular-text" type="text" name="<?php echo $option_name; ?>[name]" value="<?php echo esc_attr( $option_value['name'] ); ?>" placeholder="<?php _ex( 'Ex. SEOKEY, Nike', 'placeholder', 'seo-key' ); ?>" />
    </div>

    <h2 class="setting-description"><span class="dashicons dashicons-email-alt"></span><?php esc_html_e( "Contact details", "seo-key" );?></h2>
    <div>
		<?php _e( 'Address', 'seo-key' ); ?>*<br>
        <input required class="regular-text regular-text-large" type="text" name="<?php echo $option_name; ?>[streetAddress]" value="<?php echo esc_attr( $option_value['streetAddress'] ); ?>" placeholder="<?php _ex( 'e.g.1357 Blane Street', 'placeholder', 'seo-key' ); ?>"/>
    </div>
    <section class="settings-flex settings-flex-shrink">
        <div>
		    <?php _e( 'Postcode', 'seo-key' ); ?>*<br>
            <input required type="text" name="<?php echo $option_name; ?>[postalCode]" value="<?php echo esc_attr( $option_value['postalCode'] ); ?>" placeholder="<?php _ex( 'e.g. 44400', 'placeholder', 'seo-key' ); ?>"/>
        </div>
        <div>
            <?php _e( 'City', 'seo-key' ); ?>*<br>
            <input required class="regular-text" type="text" name="<?php echo $option_name; ?>[addressLocality]" value="<?php echo esc_attr( $option_value['addressLocality'] ); ?>" placeholder="<?php _ex( 'e.g. Paris, New York', 'placeholder', 'seo-key' ); ?>"/>
        </div>
        <div>
		    <?php _e( 'Country', 'seo-key' ); ?><br>
            <input class="regular-text" type="text" name="<?php echo $option_name; ?>[addressCountry]" value="<?php echo esc_attr( $option_value['addressCountry'] ); ?>" placeholder="<?php _ex( 'e.g. france', 'placeholder', 'seo-key' ); ?>"/>
        </div>
    </section>
    <div>
        <?php _e( 'Phone number', 'seo-key' ); ?>*<br>
        <input required pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" class="regular-text" type="tel" name="<?php echo $option_name; ?>[telephone]" value="<?php echo esc_attr( $option_value['telephone'] ); ?>" placeholder="<?php _ex( 'e.g. +33240590935', 'placeholder', 'seo-key' ); ?>"/>
    </div>
	<?php
	// Get the HTML contents
	$contents = ob_get_contents();
	// Clean the buffer
	ob_end_clean();
	return $contents;
}


add_filter( 'seokey_filter_setting_callback_switch_schema-jsonld-local_business-is-store', 'seokey_add_field__schema_jsonld_local_business_is_store', 10, 5 );
// TODO Add comment
function seokey_add_field__schema_jsonld_local_business_is_store( $html, $args, $type, $option_values, $option_name ) {
	// Do not display anything now
	ob_start();
	// Start the HTML content
	?>
	<div>
		<input data-depends-on="schemaorg-context-local_business" type="checkbox" <?php checked( 1, $option_values ); ?> name="<?php echo $option_name; ?>" value="1" class="seokey-toggle-display-button" data-seokey-toggle-display="local-business-opening-hours-specification"/>
		<label class="conditional-checkbox" for="<?php echo $option_name; ?>"><?php _e( 'Is it open to the public?', 'seo-key' ); ?></label>
	</div>
	<?php
	// Get the HTML contents
	$contents = ob_get_contents();
	// Clean the buffer
	ob_end_clean();
	return $contents;
}



add_filter( 'seokey_filter_setting_callback_switch_schema-jsonld-local_business-pricing', 'seokey_add_field__schema_jsonld_local_business_pricing', 10, 5 );
// TODO Add comment
function seokey_add_field__schema_jsonld_local_business_pricing( $html, $args, $type, $option_value, $option_name ) {
	// Default values (always empty)
    // TODO vérifier si utile de les avoir tous
	$default = [
		'name'              => '',
		'pricerangemin'     => '',
		'pricerangemax'     => '',
		'url'               => '',
		'streetAddress'     => '',
		'addressLocality'   => '',
		'addressCountry'    => '',
		'postalCode'        => '',
		'telephone'         => ''
	];
	// Get the user one
	$option_value = wp_parse_args( $option_value, $default );
	// Do not display anything now
	ob_start();
	// Start the HTML content
    // TODO Fix min max price when whe change one or another value
	?>
    <div class="seokey-pricing">
        <p class="setting-description setting-description-small"><?php _ex( 'Price range', 'minimum price from a store', 'seo-key' ); ?></p>
        <span><?php _e( 'Min.:', 'seo-key' ); ?></span>

        <input type="number" min="0" id="min-amount-seokey" class="text" name="<?php echo $option_name; ?>[pricerangemin]" value="<?php echo esc_attr( $option_value['pricerangemin'] ); ?>" placeholder="<?php _ex( 'e.g. 500', 'placeholder "From price min" range', 'seo-key' ); ?>"/>
        <br>
        <span><?php _e( 'Max.:', 'seo-key' ); ?></span>
        <input type="number" min="0" id="max-amount-seokey" class="text" name="<?php echo $option_name; ?>[pricerangemax]" value="<?php echo esc_attr( $option_value['pricerangemax'] ); ?>" placeholder="<?php _ex( 'e.g. 9000', 'placeholder "To price max" range', 'seo-key' ); ?>"/>
    </div>
	<?php
	// Get the HTML contents
	$contents = ob_get_contents();
	// Clean the buffer
	ob_end_clean();
	return $contents;
}


add_filter( 'seokey_filter_setting_callback_switch_schema-jsonld-local_business-openingHoursSpecification', 'seokey_settings_add_local_business_openingHoursSpecification', 10, 5 );
/**
 * Add the "business-openingHoursSpecification" fields block
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   seokey_filter_setting_callback_switch_ . schema-jsonld-local_business-openingHoursSpecification
 * @param see seokey_filter_setting_callback_switch_ filter
 * @return (string) $contents The DOM content for these fields
 **/
function seokey_settings_add_local_business_openingHoursSpecification( $html, $args, $type, $option_values, $option_name ) {
	// Could be empty, so create an array with 1 empty value instead
	if ( empty( $option_values ) || 'Array' === $option_values ) {
		$option_values = [''];
	}
	// $i used for the repeater, in the input name
	$i = 0;
	// Main css class for the repeater
	$class = 'seokey-repeater-container';
	// Since this block of fields is a repeater, interate
	foreach ( $option_values as $key => $option_value ) {
		// Default values (always empty)
		$default = [
			'dayOfWeek'       => [],
			'opens'           => '',
			'closes'          => '',
		];
		// Get the user one
		$option_value = wp_parse_args( $option_value, $default );
		// Do not display anything now
		ob_start();
		// Start the HTML content
		?>
		<div id="<?php echo $option_name . $i; ?>" class="<?php echo $class; ?>" data-uniqid="<?php echo $option_name; ?>">
            <p class="setting-description setting-description-small"><?php
                $option_name_id = $option_name . '[' . $i . ']';
                $input_name = $option_name_id.'[dayOfWeek]';
                $days       = [ 17 => 'Monday', 18 => 'Tuesday', 19 => 'Wednesday', 20 => 'Thursday', 21 => 'Friday', 22 => 'Saturday', 23 => 'Sunday']; // Do not translate.
            _ex( 'Opening Hours', 'opening hours for a local business', 'seo-key' );
                ?></p>
            <input type="text" class="demi-text timepickerme" value="<?php echo esc_attr( $option_value['opens'] ); ?>" name="<?php echo $option_name_id . '[opens]'; ?>"
                   placeholder="<?php echo esc_attr_x( '8:15 am (from)', 'when does the local business opens', 'seo-key' ); ?>">

            <input type="text" class="demi-text timepickerme" value="<?php echo esc_attr( $option_value['closes'] ); ?>" name="<?php echo $option_name_id . '[closes]'; ?>"
                   placeholder="<?php echo esc_attr_x( '6:00 pm (to)', 'when does the local business closes', 'seo-key' ); ?>">
            <?php if ( $i >= 1 ) {
                echo '<span class="seokey-repeater-button-del dashicons dashicons-dismiss"></span>';
            }?>
            <section class="settings-flex">
                <?php foreach ( $days as $num => $day ) {
                    ?>
                    <div>
                        <input type="checkbox" <?php checked( in_array( $day, $option_value['dayOfWeek'] ) ); ?> name="<?php echo $input_name . '[]'; ?>" value="<?php echo $day; ?>">
                        <label><?php echo date_i18n( 'l', strtotime( '12/' . $num . '/1979' ) ); ?></label>
                    </div>
                <?php } ?>
            </section>
        </div>
		<?php
		$class = 'seokey-repeater-container-cloned';
		$i ++;
	}
	// Get the HTML contents
	$contents = ob_get_contents();
	// Clean the buffer
	ob_end_clean();
	return $contents;
}

add_action('seokey_action_setting_field_after_title', 'seokey_settings_add_schema_org_person_social_links', 10, 1);
// TODO comments
function seokey_settings_add_schema_org_person_social_links( $data ){
    if ( "seokey-field-schemaorg-schema-person-sameas" === $data['id'] ) {
	    echo '<td>'. esc_html__( 'If you have social media profiles, add them below ', 'seo-key' ) . '</td>';
    }
}