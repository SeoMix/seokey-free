<?php
/**
 * SEOKEY Settings CLASS
 *
 * @Loaded on plugins_loaded + is_admin() + capability administrator
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

/**
 * CLASS SEOKEY settings API main menu
 *
 * Inspired from :
 * @see     https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
 * @see     https://github.com/tareq1988/wordpress-settings-api-class/blob/master/src/class.settings-api.php
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 */
class SeoKeySettingsAPI {
	/**
	 * @var    (object) $instance Singleton
	 * @access public
	 * @static
	 */
	public static $instance = null;
	/**
	 * @var    (array) $settings_pages : settings pages list
	 * @access protected
	 */
	protected $settings_pages = array();
	/**
	 * @var    (array) $settings_sections : settings sections list
	 * @access protected
	 */
	protected $settings_sections = array();
	/**
	 * @var    (array) $settings_fields : settings list (list of database options)
	 * @access protected
	 */
	protected $settings_fields = array();
	/**
	 * @var $tabsdata : navigation tab data
	 * @access protected
	 */
	protected $tabsdata = array();
	/**
	 * Construct SEOKEY class
	 * Avoid launching concurrent objects
	 *
	 * @since   0.0.1
	 * @author  Vincent Blée
	 *
	 */
	// Don't do anything while constructing class
	public function __construct() {
	}

	// Get only one instance of our stuff
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// Unserializing instances of this class is forbidden.
	public function __wakeup() {
	}

	// Cloning of this class is forbidden.
	private function __clone() {
	}

	/**
	 * Define setting pages
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * Params can use theses values :
	 * - name (page name)
	 * - page (Admin screen ID)
	 * - group (setting groupe ID)
	 *
	 * @param array $pages Array of pages
	 * @return SeoKeySettingsAPI  $settings_pages Array of pages
	 */
	public function set_pages( array $pages ) {
		$this->settings_pages = $pages;
		return $this;
	}

	/**
	 * Allow third party plugin or theme to add their own pages
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 * @see      set_pages function
	 *
	 * @param    array $page for one page
	 * @return   SeoKeySettingsAPI  $settings_pages Array for one page
	 */
	public function add_page( array $page ) {
		$this->settings_pages[] = $page;
		return $this;
	}

	/**
	 * Define setting sections
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * Params can use these values
	 * - name (section name)
	 * - title (section H2)
	 * - desc (section description)
	 * - page (relative page)
	 * - tabname (tab name - empty if none)
	 *
	 * @param array $sections
	 * @return SeoKeySettingsAPI Array of sections
	 */
	public function set_sections( $sections ) {
		$this->settings_sections = $sections;
		return $this;
	}

	/**
	 * Allow third party plugin or theme to add their own sections
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * @param array $section
	 * @return SeoKeySettingsAPI ($this) $this Array of sections
	 */
	public function add_section( $section ) {
		$this->settings_sections[] = $section;
		return $this;
	}

	/**
	 * Define setting fields
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * Params can use these values
	 * - name (field name)
	 * - title (field H2)
	 * - desc (field description)
	 * - type (field type)
	 * - validation (optional sanitization function)
	 * - placeholder (optional placeholder)
	 * - size (optional size)
	 * - section (field section name)
	 *
	 * @param array $fields
	 * @return SeoKeySettingsAPI ($this) $this Array of fields
	 */
	public function set_fields( $fields ) {
		$this->settings_fields = $fields;
		return $this;
	}

	/**
	 * Allow third party plugin or theme to add a setting field
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 * @see      set_fields function
	 *
	 * @param    (string) $field Array of fields
	 * @return   ($this) $this Array of fields
	 */
	public function add_field( array $field ) {
		$this->settings_fields[] = $field;
		return $this;
	}
	
	// Callbacks for sanitizations
	public function seokey_sanitize_callback_field__sanitize_text_field( $value ) {
		$callback = 'SeoKeySettingsAPI::sanitize_text_field';
		return $this->seokey_sanitize_callback_field_parsing( $value, $callback );
	}
	public function seokey_sanitize_callback_field__sanitize_url( $value ) {
		$callback = 'SeoKeySettingsAPI::sanitize_url';
		return $this->seokey_sanitize_callback_field_parsing( $value, $callback );
	}
	public function seokey_sanitize_callback_field__sanitize_date( $value ) {
		$callback = 'SeoKeySettingsAPI::sanitize_date';
		return $this->seokey_sanitize_callback_field_parsing( $value, $callback );
	}
	public function seokey_sanitize_callback_field__sanitize_search_console_code( $value ) {
        // Clean HTML if user has past the complete verification code
        if ( strpos( $value, 'content=' ) ) {
            preg_match('`content=([\'"])?([^\'"> ]+)(?:\1|[ />])`', $value, $match );
            if ( isset($match[2] ) ) {
                $value = $match[2];
            }
            unset($match);
        }
        // First cleaning
        $value = sanitize_text_field( $value );
        // Now check if we only have characters and numbers
        if ( $value !== '' ) {
            $regex   = '`^[A-Za-z0-9_-]+$`';
            if ( preg_match( $regex, $value ) ) {
                $verification_code = $value;
            }
        }
        // return cleaned data
        return $value;
	}
	
	// Parse each value
	public function seokey_sanitize_callback_field_parsing( $value, $callback ) {
		// Value is an array of value => trigger specific function
		if ( is_array( $value ) ) {
			$value = $this->seokey_sanitize_callback_field_parsing_array( $value );
		}
		// Value is not an array : trigger now the sanitize callback (default to sanitize_text_field)
		else {
			call_user_func( $callback, $value );
		}
		return $value;
	}
	
	// Parse array values
	public function seokey_sanitize_callback_field_parsing_array( $value ) {
		// iterate
		foreach ( $value as $key => $item ) {
		    // TODO Improve here
		    if ( is_array( ( $item ) ) ) {
                array_map( 'sanitize_text_field', $value );
            } else {
                // Clean values according to type
                switch ( $key ) {
					// classic cases
					case 'url':
					case 'image':
						$value[$key] = $this->sanitize_url( $item );
						break;
					case 'date':
					case 'birthdate':
						$value[$key] = $this->sanitize_date( $item );
						break;
//	                case 'checkbox':
//		                $value[$key] = $this->sanitize_checkbox( $item );
//		                break;
					default:
						$value[$key] = $this->sanitize_text_field( $item );
						break;
				}
			}
		}
		// return cleaned data
		return $value;
	}

	// Default sanitization for all fields
	public function sanitize_text_field( $value = '' ) {
		return sanitize_text_field( $value );
	}
	// URL sanitization
	public function sanitize_url( $value = '' ) {
		return esc_url_raw( $value );
	}
//	public function sanitize_checkbox( $value = '' ) {
//		return (int) $value;
//	}
	// Date sanitization
	public function sanitize_date( $value = '' ) {
		// first cleaning
		$value = sanitize_text_field( $value );
		$value = preg_replace( "([^0-9-])", "", $value );
		return $value;
	}
	
	/**
	 * Let's register our settings and sections
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 */
	public function seokey_config_register_setting() {
		// Trigger 'seokey_config_data_checking' action to check if current admin page needs some tabs (it will allow us to add all necessary JS and CSS)
		add_action( 'current_screen', array( $this, 'seokey_config_data_checking' ), 1 );
		// enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'seokey_settings_api_forms_assets' ), 10 );
		// We need to register each field, then we need to define field rendering
		foreach ( $this->settings_fields as $value ) {
			if ( ! $value ) {
				continue;
			}
			// Get field name
			$fieldname = sanitize_title( $value['name'] );
			// Get group name
			$groupname = seokey_settings_api_get_page_group( $value['ID'] );
			// Callback
			$type = $value['type'];
			//specific cases
			if ( 'seokey-field-search-console-searchconsole-google-verification-code' === $value['name'] ) {
				$callback = 'seokey_sanitize_callback_field__sanitize_search_console_code';
			} else {
				switch ( $type ) {
					case 'url':
					case 'image':
						$callback = 'seokey_sanitize_callback_field__sanitize_url';
						break;
					case 'date':
						$callback = 'seokey_sanitize_callback_field__sanitize_date';
						break;
					default:
						$callback = 'seokey_sanitize_callback_field__sanitize_text_field';
				}
			}
			// Register field in Database
			register_setting(
				$groupname,   // Needed to generate all field from the same group (and the same page)
				$fieldname,   // Name of the field being registered (same as add_option)
				['sanitize_callback' => [ $this, $callback ] ] // Sanitize this field
			);
		}
	}

	/**
	 * Check data and trigger useful functions if necessary (tabs, file uploads)
	 *
	 * - Check if this page needs to have tabs, and define data and enqueue files if necessary
	 * - Check if this page needs to have upload settings, and enqueue files if necessary
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * Could use $current_screen param (WP_Screen object)
	 */
	public function seokey_config_data_checking() {
		// Declare tab array
		$tabs = [];
		// Get our tab names
		foreach ( $this->settings_sections as $section ) {
			// if we have a tab value
			if ( isset( $section['tabname'] ) ) {
				// define tab values
				$tabs[] = [
					'name'         => sanitize_text_field( $section['tabname'] ),
					'sectiontitle' => sanitize_title( $section['tabname'] ),
					'page'         => seokey_helper_admin_get_page_screen_base( $section['ID'] ),
					'url'          => isset( $section['url'] ) ? $section['url'] : null
				];
			}
		}
		// Define tabs data for later use
		$this->tabsdata = $tabs;
	}

	/**
	 * Add necessary scripts and css for tab navigation or file uploads
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 */
	public function seokey_settings_api_forms_assets() {
		// Where are we ?
		$screen = seokey_helper_get_current_screen();
		// No screen ? Are you kidding to me ?
		if ( empty( $screen ) ) {
			return;
		}
		$screen = $screen->base;
		// Get config data
		$settings_sections = $this->settings_sections;
		$settings_sections_ID = wp_list_pluck( $settings_sections, 'ID' );
		$settings_sections_ID = array_map( 'seokey_helper_admin_get_page_screen_base', $settings_sections_ID);
		// Are we in a page with a settings form ? If no, return
		if ( ! in_array( $screen, $settings_sections_ID, true ) ) {
			return;
		}
		// JS for settings and tabs
		wp_enqueue_script( 'seokey-admin-settings',         SEOKEY_URL_ASSETS . 'js/settings.js',       ['jquery', 'wp-i18n'], SEOKEY_VERSION, true );
		wp_enqueue_script( 'seokey-admin-settings-tabs',    SEOKEY_URL_ASSETS . 'js/settings-tabs.js',  ['jquery', 'wp-i18n'], SEOKEY_VERSION, true );
		wp_set_script_translations( 'seokey-admin-settings-tabs', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
	}

	public function seokey_config_register_setting_sections_and_fields() {
		// We need to register each field, then we need to define field rendering
		foreach ( $this->settings_fields as $value ) {
			if ( ! $value ) {
				continue;
			}
			// Get field name
			$fieldname = sanitize_title( $value['name'] );
			// Get page data
			$page = seokey_helper_admin_get_page_screen_base( $value['ID'] );
			// Get group name
			$groupname = sanitize_title( $value['section'] );
			// Are we able to register and render this setting ?
			if ( ! empty ( $groupname ) && ! empty ( $fieldname ) ) {
				// ====================> RENDER SETTINGS
				// Define field size for later HTML rendering
				if ( 'number' === $value['type'] ) {
					$size = 'small';
				} else {
					$size = isset( $value['size'] ) ? sanitize_html_class( $value['size'] ) : 'regular';
				}
				// Values data handling
				$values = empty( $value['values'] ) ? '' : $value['values'];
				if ( ! empty ( $value['values'] ) ) {
					if ( is_array( $value['values'] ) ) {
						// Remove empty values
						$values = array_filter( $values, 'seokey_helper_strlen' );
					}
				}
				// Generate useful arguments for generating each field
				$args = array(
					// Name
					'name'          => $fieldname,
					// custom id for field row
					'field_id'      => isset( $value['field_id'] ) ? wp_kses_post( $value['field_id'] ) : '',
					// Label
					'label'         => isset( $value['label'] ) ? wp_kses_post( $value['label'] ) : '',
					// Description
					'desc'          => isset( $value['desc'] ) ? wp_kses_post( $value['desc'] ) : '',
                    // desc_position
                    'desc-position' => isset( $value['desc-position'] ) ? sanitize_html_class( $value['desc-position'] ) : '',
					// Optionnal Placeholder
					'placeholder'   => isset( $value['placeholder'] ) ? esc_attr( $value['placeholder'] ) : '',
					// Field size (used for css class)
					'size'          => $size,
					// Get type field
					'type'          => isset( $value['type'] ) ? $value['type'] : 'text',
					// Values for some fields (select, radio...)
					'values'        => $values,
					// The default value if set
					'default'       => isset( $value['default'] ) ? $value['default'] : null,
					// A set of bonus args when a field needs something specitif for it, do not sanitize!
					'args'          => isset( $value['args'] ) && is_array( $value['args'] ) ? $value['args'] : [],
					// A set conditions to display a field
					'depends'       => isset( $value['depends'] ) ? $value['depends'] : [],
					// TRUE will add a button to clone the element (JS only)
					'repeater'      => isset( $value['repeater'] ) ? $value['repeater'] : false,
					// Class
					'class'      => isset( $value['class'] ) ? $value['class'] : '',
                    // Do we need explanation ?
                    'has-explanation'       => isset( $value['has-explanation'] ) ? $value['has-explanation'] : false,
					'has-sub-explanation'   => isset( $value['has-sub-explanation'] ) ? $value['has-sub-explanation'] : false,
				);
				// Get setting title
				$title = isset( $value['title'] ) ? wp_kses_post( $value['title'] ) : '';
				// Get field section
				$section = sanitize_title( $value['section'] );
				// Function useful to be able to render this field
				add_settings_field(
				// Id of the field
					$fieldname,
					// Title of this Field (Label)
					$title,
					// Generate this field with this callback
					array( $this, 'seokey_config_field_callback' ),
					// Needed to generate all field from the same page (and the same group)
					$page,
					// Where this field is supposed to appear ? (field section)
					$section,
					// We need more information buddy
					$args
				);
				// End setting condition
			}
			// End setting registration and rendering
		}
		// Create each section for all our fields
		foreach ( $this->settings_sections as $section ) {
			// Is there a description for an optional callback ? (return false if $value[desc] does not exist)
			$callback = ! empty( $section['desc'] ) ? array( $this, 'seokey_settings_api_section_callback' ) : '';
			// Let's add our section
			add_settings_section(
			// Name of the section
				seokey_settings_api_get_page_section( $section['ID'], $section['name'] ),
				// Title of the section
				sanitize_text_field( $section['tabname'] ),
				// Section callback (in order to generate some text)
				$callback,
				// Section page
				seokey_helper_admin_get_page_screen_base( $section['ID'] )
			);
		}
	}

	/**
	 * Callback for generating each setting field
	 *
	 * @since    0.0.1
	 * @author   Daniel Roch
	 *
	 * @param    (string) $args Array of arguments in order to generate fields
	 */
	public function seokey_config_field_callback( $args ) {
		$defaults     = [
			'name'        => '',     // name of the field, will be concat with our prefix
			'field_id'    => '',     // custom id for field row
			'placeholder' => '',     // The field placeholder
			'class'       => '',     // CSS class for this field
			'label'       => '',     // Label of the field
			'desc'        => '',     // Field description
			'type'        => '',     // Type of field
			'default'     => '',     // Default value(s)
			'size'        => '',     // Used in some field that can be sized
			'values'      => [],     // User values for some fields like radios, multi checkboxes
			'depends'     => [],     // Used to hide fields when a condition is not present (JS only)
			'repeater'    => [],     // Used to repeat a field (JS only)
			'args'        => [],     // Bonus arguments, don't sanitize early
		];
		$args         = wp_parse_args( $args, $defaults );
		$option_name  = $args['name'];
		$option_value = get_option( $option_name );
		$placeholder  = esc_attr( $args['placeholder'] );
		$html         = '';
		$class        = sanitize_html_class( $args['class'] );
		$type         = $args['type'];
		$label        = ! empty( $args['label'] ) ? $args['label'] : '';
		$label        = apply_filters( 'seokey_filter_setting_label_value', $label, $option_name);
		// "Aria Described By" variable
		$described_by      = '';
		$described_by_name = '';
		// If there is a complementary description field, add a aria-describedby attribute
		if ( ! empty ( $args['desc'] ) ) {
			// ID for our "Aria Described By"
			$described_by_name = $option_name . '_desc';
			$described_by      = ' aria-describedby="' . $described_by_name . '"';
		}
		// Render setting according to every available type
		switch ( $type ) {
			case 'checkbox':
				// Get an empty get option return value
				$option_value = get_option( $option_name, null );
                if ( is_null( $option_value ) ) {
                    $option_value = ! is_null( $args['default'] ) ? $args['default'] : "";
                }
				$checked = checked( $option_value, 1, false );
				// Render checkbox
				$html = sprintf( '<input autocomplete="off" type="checkbox" class="%1$s %5$s" name="%2$s" id="%2$s" value="1" %3$s%4$s/>',
					$type,
					$option_name,
					$checked,
					$described_by,
					$class
				);
				if ( $label ) {
					if ( true === $args['has-sub-explanation'] ) {
						$explanation = seokey_helper_help_messages( $option_name );
						$html .= ' <label class="has-explanation" for="' . $option_name . '">' . $label . $explanation . '</label>';
					} else {
						$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
					}
				}
				break;
			case 'checkbox_slide':
				$checked = checked( $option_value, '1', false );
				$size    = isset( $args['size'] ) ? $args['size'] : '';
				// Render checkbox
				$html = sprintf( '<div class="onoffswitch-container">
                        <div class="onoffswitch %6$s">
                            <input autocomplete="off" type="checkbox" class="onoffswitch-checkbox %1$s %7$s" id="%2$s" name="%2$s" value="1" %3$s%4$s/>
                            <label class="onoffswitch-label" for="%2$s">
                                <span class="onoffswitch-inner" data-label-on="On" data-label-off="Off"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                            <span>%5$s</span>
                        </div></div>',
					$type,
					$option_name,
					$checked,
					$described_by,
					$args['desc'],
					sanitize_html_class( $size ),
					$class
				);
				break;
			case 'multi_checkbox':
				$html         = '';
				$default      = ! is_null( $args['default'] ) ? $args['default'] : null;
				$option_value = is_array( $option_value ) && ! empty( $option_value ) ? array_flip( $option_value ) : $default;
				foreach ( $args['values'] as $key => $label ) {
					$checked = checked( is_array( $option_value ) && isset( $option_value[ (string) $key ] ), true, false );
					// Render HTML
					$html .= sprintf( '<input autocomplete="off" type="checkbox" class="%1$s %7$s" id="%2$s-%3$s" name="%2$s[]" value="%3$s" %4$s%5$s/>  <label for="%2$s-%3$s">%6$s</label>',
							$type,
							$option_name,
							$key,
							$checked,
							$described_by,
							esc_html( $label ),
							$class
					         ) . '<br>';
				}
				break;
			case 'multi_checkbox_slide':
				// Get default values
				$default = ! is_null( $args['default'] ) ? $args['default'] : null;
				// Get user settings values
				$option_value = is_array( $option_value ) && ! empty( $option_value ) ? array_flip( $option_value ) : $default;
				// Start out container
				$html .= '<section class="seokey-switch-toggle">';
				// Add a hidden value to prevent option to be empty
				$html .= '<input type="hidden" name="' . $option_name . '[]" value="i_am_a_dummy_value">';
				wp_localize_script( 'seokey-admin-settings-tabs', 'seokey_tabs', $args['args'] );
				$toggle = 'even';
				// Handle each option value
				foreach ( $args['values'] as $key => $label ) {
					// Do we have to check it?
					$checked            = checked( is_array( $option_value ) && isset( $option_value[ (string) $key ] ), true, false );
					$explanation_name   = $option_name . '-label-' . $key;
					$subexplanation     = ( true === $args['has-sub-explanation'] ) ? seokey_helper_help_messages( $explanation_name ) : '';
					$toggle = ( $toggle === "odd" ) ? "even" : "odd";
					$class = 'seokey-onoffswitch-label';
					$class .= ( true === $args['has-sub-explanation'] ) ? ' has-explanation ' . $toggle : '';
					// Render checkbox
					$html .= sprintf( '
					<section class="seokey-switch-toggle-item">
                        <section class="seokey-button-check" id="seokey-button-check">
						 	<input type="checkbox" autocomplete="off" class="seokey-checkbox %1$s" id="%2$s-%5$s" name="%2$s[]" value="%5$s" %3$s%4$s/>
						 	<span class="seokey-switch-btn" data-label-on="%7$s" data-label-off="%8$s"></span>
							<span class="seokey-layer"></span>
                        </section>
                        <label class="%10$s" for="%2$s-%5$s">%6$s%9$s</label>
                        </section>',
						$type,
						$option_name,
						$checked,
						$described_by,
						$key,
						$label,
						esc_attr( $args['args']['label_on'] ),
						esc_attr( $args['args']['label_off'] ),
						$subexplanation,
						$class
					);
				}
				$html .= '</section>';
				break;
			case 'radio_slide':
				$div_group = true;
			case 'radio':
				// Display each radio button
				foreach ( $args['values'] as $key => $label ) {
					if ( isset( $div_group ) ) {
						$html .= '<div class="seokey_radio_slide">';
					}
					$default      = ! is_null( $args['default'] ) ? $args['default'] : null;
					$option_value = $option_value ? $option_value : $default;
					$checked      = checked( ! is_null( $option_value ) && $option_value === (string) $key, true, false );
					// Render HTML
					$html .= sprintf( '<input autocomplete="off" type="radio" class="%1$s %7$s" name="%2$s" id="%2$s-%3$s" data-depends-on="%2$s-%3$s" data-depends-off="%2$s" value="%3$s" %4$s%5$s/> <label for="%2$s-%3$s">%6$s</label>',
						$type,
						$option_name,
						$key,
						$checked,
						$described_by,
						esc_html( $label ),
						$class
					);
					if ( isset( $div_group ) ) {
						$html .= '</div>';
					} else {
						$html .= '<br />';
					}
				}
				break;
			case 'select':
				if ( isset( $args['depends'] ) ) {
					$class .= ' depends';
				}
				// Render first part of the select
				$html = sprintf( '<select autocomplete="off" class="select %3$s" id="%1$s" name="%1$s" %2$s>',
					$option_name,
					$described_by,
					$class
				);
				// For each value
				foreach ( $args['values'] as $key => $val ) {
					// Which value needs to be selected ?
					$selected = selected( $option_value, $key, false );
					// Render each value
					$html .= sprintf( '<option value="%1$s" %2$s data-depends-on="%4$s-%1$s" data-depends-off="%4$s">%3$s</option>',
						$key,
						$selected,
						esc_html( $val ),
						$option_name
					);
				}
				// End select
				$html .= '</select>';
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
				}
				break;
			case 'multiselect':
				$size = isset( $args['size'] ) && (int) $args['size'] > 0 ? (int) $args['size'] : 3;
				// Render first part of the select
				$html = sprintf( '<select autocomplete="off" multiple="multiple" class="multi-select %4$s" size="%3$d" id="%1$s" name="%1$s[]" %2$s>',
					$option_name,
					$described_by,
					$size,
					$class
				);
				// For each value
				foreach ( $args['values'] as $key => $val ) {
					// Which value needs to be selected ?
					$selected = selected( $option_value, sanitize_title( $val ), false );
					// Render each value
					$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>',
						sanitize_title( $val ),
						$selected,
						esc_html( $val )
					);
				}
				// End select
				$html .= '</select>';
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
				}
				break;
			case 'textarea':
				// Render textarea
				$html = sprintf( '<textarea rows="5" cols="46" class="%1$s-textarea %6$s" id="%2$s" name="%2$s" placeholder="%3$s" %4$s>%5$s</textarea>',
					$args['size'],
					$option_name,
					$placeholder,
					$described_by,
					esc_textarea( $option_value ),
					$class
				);
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
				}
				break;
			case 'date':
				// Render date
				$html = sprintf( '<input autocomplete="off" type="%1$s" class="%2$s-text %7$s" id="%3$s" name="%3$s" value="%4$s" placeholder="%5$s" %6$s/>',
					$type,
					$args['size'],
					$option_name,
					esc_attr( $option_value ),
					$placeholder,
					$described_by,
					$class
				);
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
				}
				// Enqueue JS and CSS for date field
				wp_enqueue_style( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'jquery-ui-core');
				wp_enqueue_script( 'jquery-ui-datepicker');
				wp_enqueue_script( 'seokey-admin-settings-date', SEOKEY_URL_ASSETS . 'js/settings-date.js','', SEOKEY_VERSION, true );
				break;
			case 'tinymce':
				// Render textarea
				ob_start();
				wp_editor( $option_value, $option_name, ['teeny' => true, 'media_buttons' => false ] );
				$html = ob_get_contents();
				ob_end_clean();
				break;
			case 'url':
			case 'text':
			case 'password':
			case 'email':
			case 'number':
				// If this is a repeater
				if ( isset( $args['repeater'] ) && true === $args['repeater'] ) {
					// Set the values instead od value
					$option_values = $option_value;
					// If not an array or empty, create an array woth an empty value
					if ( empty( $option_values ) || ! is_array( $option_values ) ) {
						$option_values = [''];
					}
					// Iterate each values now
					foreach ( $option_values as $array_key => $option_value ) {
						// Set the pointer at the begining of the array
						reset( $option_values );
						if ( $array_key === key( $option_values ) ) {
							// If this is the first iteration, we need the main container
							$html .= '<div class="seokey-repeater-container" data-uniqid="0">';
                            $end = '</div>';
						} else {
							// Or we need a cloned one
							$html .= '<div class="seokey-repeater-container-cloned">';
							$end = '<span class="seokey-repeater-button-del dashicons dashicons-dismiss"></span></div>';
						}
						// Render input content
						$html .= sprintf( '<input autocomplete="off" type="%1$s" class="%2$s-text %7$s" id="%3$s" name="%3$s%8$s" value="%4$s" placeholder="%5$s" %6$s/>',
							$type,
							$args['size'],
							$option_name,
							esc_attr( $option_value ),
							$placeholder,
							$described_by,
							$class,
							$array_key === key( $option_values ) ? '' : '[]'
						);
						$html .= $end;
					}
				} else {
					// Render single input content
					$html .= sprintf( '<input autocomplete="off" type="%1$s" class="%2$s-text %7$s" id="%3$s" name="%3$s" value="%4$s" placeholder="%5$s" %6$s/>',
						$type,
						$args['size'],
						$option_name,
						esc_attr( $option_value ),
						$placeholder,
						$described_by,
						$class
					);
				}
				// If a label is set, add it now
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '">' . $label . '</label>';
				}
				break;
			case 'image':
				
				$html = '';
				// If a label is set, add it now
				if ( $label ) {
					$html .= ' <label for="' . $option_name . '-upload">' . $label . '</label>';
				}

				$required = '';
				if (!empty($args['args']['required']) && $args['args']['required'] === true) {
					$required = 'required';
				}

				// Render hidden input content
				$html .= sprintf( '<input autocomplete="off" type="text" class="hidden seokey-settings-upload-data" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s" %4$s %5$s/>',
					$option_name,
					esc_attr( $option_value ),
					$placeholder,
					$required,
					$described_by
				);
				// Upload button
				$upload_text = esc_attr__( 'Upload', 'seo-key' );
				// Check if valid URL
				if ( !empty( esc_url( $option_value ) ) ) {
					$upload_text = esc_attr__( 'Change image', 'seo-key' );
					$remove_class= ' seokey-remove-button-show';
				} else {
					$remove_class= '';
				}
				$html .= '<input data-optionname="' . $option_name . '" id="' . $option_name . '-upload" class="button button-small seokey-settings-upload-button" type="button" value="' . $upload_text . '" />';
				// Remove button
				$html .= ' <input class="button button-small seokey-settings-upload-remove-button' . $remove_class . '" type="button" value="' . esc_html__( 'Remove image', 'seo-key' ) . '" />';
				// Add image preview for image type
				$html .= '<p class="hide-if-js">' . __( 'Preview', 'seo-key' ) . '<br>';
				$html .= sprintf( '<img class="seokey-img-preview" id="%1$s-preview" src="%2$s" alt="%3$s">',
					$option_name,
					$option_value,
					esc_attr__('Logo or photo', 'seo-key')
				);
				$html .= '</p>';
				// Enqueue the needed JS scripts
				wp_enqueue_media();
				wp_enqueue_script( 'seokey-admin-settings-medias', SEOKEY_URL_ASSETS . 'js/settings-media.js', ['wp-i18n'], SEOKEY_VERSION );
				wp_set_script_translations( 'seokey-admin-settings-medias', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
				break;
			case 'html':
				// Print raw HTML content!
				$html .= isset( $args['desc'] ) ? $args['desc'] : seokey_dev_error( __FUNCTION__, __LINE__, 'missing "desc" param for "html" type' );
				break;
			default:
				/**
				 * Filter Callback field to allow custom fields
				 *
				 * @since  0.0.1
				 * @author Julio Potier
				 *
				 * @param  (string) $html Text field rendering
				 * @param  (string) $args Text field rendering arguments
				 * @param  (string) $type            Field type
				 * @param  (string) $option_value    Option Value
				 * @param  (string) $option_name     Option name
				 * @param  (string) $placeholder     Field Placeholder
				 * @param  (string) $described_by    Field Described by ID (for ARIA)
				 */
				$html = apply_filters( 'seokey_filter_setting_callback_switch', $html, $args, $type, $option_value, $option_name, $placeholder, $described_by );
				/**
				 * Filter Callback field to allow custom fields
				 *
				 * @since  0.0.1
				 * @author Julio Potier
				 *
				 * @param  (string) $html Text field rendering
				 * @param  (string) $args Text field rendering arguments
				 * @param  (string) $type            Field type
				 * @param  (string) $option_value    Option Value
				 * @param  (string) $option_name     Option name
				 * @param  (string) $placeholder     Field Placeholder
				 * @param  (string) $described_by    Field Described by ID (for ARIA)
				 */
				$html = apply_filters( 'seokey_filter_setting_callback_switch_' . $type, $html, $args, $type, $option_value, $option_name, $placeholder, $described_by );
				// Nothing to print? DIE!
				if ( ! $html ) {
					seokey_dev_error( __FUNCTION__, __LINE__, "`$type` bad field type?" ); // Do not translate.
				}
				break;
			// End switch
		}
		// Nothing to print? DIE!
		if ( empty( $html ) ) {
			seokey_dev_error( __FUNCTION__, __LINE__, "`$type` bad args?" ); // Do not translate.
		}
		// Check if there is a complementary description field before adding the aria-describedby paragraph
		if ( 'html' !== $type && ! empty ( $args['desc'] ) ) {
			// Add contextual explanation
			$desc = '<p class="description seokey-description" id="' . $described_by_name . '">' . wp_kses_post( $args['desc'] ) . '</p>';
            $html = ( $args['desc-position'] === 'above' ) ? $desc.$html : $html.$desc;
		}
		/**
		 * Filter final callback rendering
		 *
		 * @since 0.0.1
		 *
		 * @param (string) $html Text field rendering
		 * @param (string) $args Text field rendering arguments
		 */
		echo apply_filters( 'seokey_filter_setting_section_callback', $html, $args );
        do_action ('seokey-settings-api-after-option-' . $option_name );
	}
	
	/**
	 * Render each setting section
	 *
	 * @since  0.0.1
	 * @author Daniel Roch
	 *
	 * @param string $page Page slug for this form
	 * @param $section
	 * @global $wp_settings_sections , $wp_settings_fields
	 */
	public function seokey_do_settings_fields( $page, $section ) {
		global $wp_settings_fields;
		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}
		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
			$class = SEOKEY_SLUG;
			$data  = '';
			if ( ! empty( $field['args']['class'] ) ) {
				$class .= ' '.sanitize_html_class( $field['args']['class'] );
			}
			if ( ! empty( $field['args']['depends'] ) ) {
				$_sectemp = str_replace( 'seokey-section-', '', $section ) . '-';
				$class    .= ' hide-if-js';
				$data     .= ' data-depends-on="';
				foreach ( $field['args']['depends']['value'] as $value ) {
					$data .= $_sectemp . sanitize_html_class( $field['args']['depends']['field'] ) . '-' . sanitize_html_class( $value );
				}
				$data .= '"';
				$data .= ' data-depends-off="' . $_sectemp . sanitize_html_class( $field['args']['depends']['field'] ) . '"';
			}
			if ( ! empty( $field['args']['field_id'] ) ) {
				$data .= ' data-field-id="' . esc_attr( $field['args']['field_id'] ) . '"';
			}
			$field_id       = esc_attr( $field['id'] );
            $thclass        = '';
            $explanation    = ( !empty( $field['args']['has-explanation'] ) ) ? $field['args']['has-explanation'] : '';
            if ( true === $explanation ) {
                $explanation = seokey_helper_help_messages( 'settings-api-title-' . sanitize_title( $field['id'] ) );
                $thclass = "has-explanation";
            }
			do_action( 'seokey_action_' . $field_id . '_before');
            echo "<tr id='{$field_id}-tr' class='{$class}' {$data}>";
				echo '<th scope="row" class="' . $thclass. '">';
					do_action( 'seokey_action_setting_field_before_title', $field );
					echo '<span class="thspan">';
                        echo $field['title'];
                        echo $explanation;
					echo '</span>';
                    do_action( 'seokey_action_setting_field_after_title', $field );
                echo '</th>';
				echo '<td>';
					do_action( 'seokey_action_setting_field_before_field', $field );
					echo '<span class="tdspan">';
						call_user_func( $field['callback'], $field['args'] );
					echo '</span>';
                    do_action( 'seokey_action_setting_field_after_field', $field );
				echo '</td>';
			echo '</tr>';
		}
	}

	public function seokey_settings_api_section_display( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
		// If there is no sections for this page,do nothing
		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}
		// Foreach section to generate
		foreach ( (array) $wp_settings_sections[ $page ] as $id_section => $section ) {
			// Generate a fieldset for a common setting section
			echo '<fieldset class="seokey-fieldset seokey-fieldsetsetting hide-if-js" id="tab-' . sanitize_title( $section['title'] ) . '">';
			// Add an optional callback for a <p> description
			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}
			// Hook in order to add content or data after a setting section
			do_action( 'seokey_action_setting_table_before', $id_section );
			// Render our stuff
			echo '<table class="form-table">';
			// Hook in order to add content or data before a setting section
			do_action( 'seokey_action_setting_sections_before', $id_section );
			// Render settings
			$this->seokey_do_settings_fields( sanitize_title( $page ), sanitize_key( $section['id'] ) );
			// Hook in order to add content or data after a setting section
			do_action( 'seokey_action_setting_sections_after', $id_section );
			echo '</table>';
			// Hook in order to add content or data after a setting section
			do_action( 'seokey_action_setting_table_after', $id_section );
			// End our fieldset
			echo '</fieldset>';
		}
	}

	/**
	 * Add description to a setting section
	 *
	 * @since  0.0.1
	 * @author Daniel Roch
	 *
	 * @param  (string) $args Array of argument callback - $args contain id, name and callback name function
	 */
	public function seokey_settings_api_section_callback( $args ) {
		// Keep the only array we need
		$id = $args['id'];
		foreach ( $this->settings_sections as $section ) {
			if ( $id !== sprintf( 'seokey-section-%s-%s', $section['ID'], $section['name'] ) ) {
				continue;
			}
            // desc-position
			// Render description
			$render = '<p class="seokey-psetting">' . wp_kses_post( $section['desc'] ) . '</p>';
			// Display
			echo $render;
		}
	}

	/**
	 * Generate setting form
	 *
	 * @since  0.0.1
	 * @author Daniel Roch
	 *
	 */
	public function seokey_settings_api_forms() {
		$screen            = seokey_helper_get_current_screen();
		$current_page      = $screen->base;
		$data              = wp_list_filter( $this->settings_pages, array( 'page' => $current_page ) );
		$current_page_data = array_shift( $data );
		// If current page is not supposed to have a setting API
		if ( is_null( $current_page_data ) ) {
			trigger_error( esc_html__( 'No settings available for this page', 'seo-key' ), E_USER_NOTICE );
			echo "Bad configuration file for Setting API"; // Do not translate, debug for dev only
		} elseif ( in_array( $current_page, $current_page_data ) ) {
			// display settings messages
			settings_errors();
			// Display navigation tabs if necessary
			$this->seokey_settings_api_forms_tabs();
			$form_id = 'seokey-form-settings';
			$nonce   = wp_create_nonce( $form_id );
			echo '<form class="seokey-form seokey-form-' . str_replace( 'seokey_', '', $current_page_data['group'] ) . '" id="' . $form_id . '" action="options.php" method="post" data-ajax-nonce="' . esc_attr( $nonce ) . '">';
			/**
			 * Add the possibility to display something at the top of the form
			 *
			 * @param (array) $current_page_data Page and Group data information
			 */
			do_action( 'seokey_action_setting_form_before', $current_page_data );
			seokey_helper_loader( 'seokey');
			// Render all section fields for this page
			// -> Use $current_page because it will always matches ;)
			$this->seokey_settings_api_section_display( $current_page );
			// Render corresponding setting fields (nonce and other fun stuff)
			settings_fields( sanitize_title( $current_page_data['group'] ) );
			/**
			 * Add the possibility to automatically add a submit button or not
			 *
			 * @param (bool) $with_submit_button
			 */
			$with_submit_button = apply_filters( 'seokey_filter_settings_form_with_submit_button', true );
			if ( $with_submit_button ) {
				// You can set a custom title here
				if ( is_string( $with_submit_button ) ) {
					$title = esc_html__( $with_submit_button );
				} else {
					// of the default title will be used
					$title = esc_html__( 'Save all changes', 'seo-key' );
				}
				echo '<span class="seokey-form-save-section">';
                    /**
                     * Add the possibility to display something before form button
                     *
                     * @param (array) $current_page_data Page and Group data information
                     */
                    do_action( 'seokey_action_setting_form_button_before', $current_page_data );
                    // If you need to customize more this button, use the seokey_action_end_settings_form hook instead
                    submit_button( $title, ['button-primary', 'button-hero'], 'submit', true );
                    /**
                     * Add the possibility to display something after form button
                     *
                     * @param (array) $current_page_data Page and Group data information
                     */
                    do_action( 'seokey_action_setting_form_button_after', $current_page_data );
                echo '</span>';
			}
			echo '</form>';
            /**
             * Add the possibility to display something at the top of the form
             *
             * @param (array) $current_page_data Page and Group data information
             */
            do_action( 'seokey_action_setting_form_after', $current_page_data );
			// end form
		}
	}

	/**
	 * Display tabs just before our setting form
	 *
	 * @since  0.0.1
	 * @author Daniel Roch
	 *
	 */
	public function seokey_settings_api_forms_tabs() {
		global $current_user;
		$render = '';
		$tabs   = $this->tabsdata;
		// Begin rendering Tab Navigation
		$render       .= '<nav role="navigation" class="nav-tab-wrapper hide-if-no-js">';
		$active_class = false;
		do_action( "seokey_action_setting_form_before_all_tabs" );
		// Foreach tab
		foreach ( $tabs as $index => $currenttab ) {
			if ( seokey_helper_admin_get_page_screen_base( seokey_helper_url_get_clean_plugin_slug( $_GET['page'] ) ) !== $currenttab['page'] ) {
				continue;
			}
			// Get current tab slug
			$tabslug        = sanitize_title( $currenttab['name'] );
			$parameter_page = sanitize_title( $_GET['page'] );
			$taburl         = isset( $currenttab['url'] ) ? esc_url( $currenttab['url'] ) : '?page=' . $parameter_page . '#' . $tabslug;
			// Render each tab
			$user_last_tab = get_user_meta( $current_user->ID, 'seokey-settings-tab', true );
			$active_class  = ( false === $active_class && ! isset( $user_last_tab[ $parameter_page ] ) ) || ( isset( $user_last_tab[ $parameter_page ] ) && $user_last_tab[ $parameter_page ] === $tabslug ) ? ' nav-tab-active' : '';
			$render        .= '<a id="' . sanitize_title( $currenttab['sectiontitle'] ) . '" href="' . $taburl . '" class="nav-tab' . $active_class . '">' . $currenttab['name'] . '</a>';
			/**
			 * Allow an action at the end of a tab navigation
			 *
			 * @since 0.0.1
			 *
			 * @param (array) $currenttab Current tab infos
			 */
			do_action( "seokey_action_setting_form_after_tab", $currenttab );
		}
		// render end tab navigation
		$render .= '</nav>';
		/**
		 * Filter Tab navigation rendering
		 *
		 * @since 0.0.1
		 *
		 * @param (string) $render Final rendering of tab navigation
		 * @param (string) $tabs All the tabs for info
		 */
		echo apply_filters( 'seokey_filter_settings_api_forms_tabs', $render, $tabs );
	}
	// End class
}
