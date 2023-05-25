<?php
/**
 * Admin Redirection module : form functions
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     redirections.php
 * @package SEOKEY
 */

/**
 * Admin Redirection module
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     admin-module.php
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

add_action( 'admin_post_Seokey_Redirections_Form_Submit', 'Seokey_Redirections_Form_Submit' );
/**
 * Admin Post function to handle form submission
 */
function Seokey_Redirections_Form_Submit (){
    // Security : check current user role
    seokey_redirection_check_capabilities();
    // Security : check nonce
    check_admin_referer( 'seokey-redirections-form-nojs', 'seokey-redirections-form-nojs-name', TRUE );
    // Everything is good, proceed
    $redirections = Seokey_Redirections_Form::get_instance();
    $redirections->seokey_redirections_submit( $_POST );
}

/**
 * Handle all redirection form process : add, delete and edit a redirection
 */
class Seokey_Redirections_Form {
	/**
	 * Constants
	 */
	const ADMIN_MENU_SLUG   = 'redirections';

	/**
	 * Data send with the redirection form
	 * @var array
	 */
	protected $form_post = [];

	/**
	 * Form values
	 * @var array
	 */
    public $form_fields_values = [];

    /**
	 * Message value
	 * @var array
	 */
	public $message_value = false;

	/**
	 * Data used when saving modifications
	 * @var array
	 */
	protected $datas = [];

	/**
	 * Errs
	 * @var array
	 */
	protected $validate = [];

	/**
	 * Redirection types
	 * @var array
	 */
	static $type_redirection = ['direct', 'regexp'];

	/**
	 * Redirection Status
	 * @var array
	 */
	static $status_redirection = ['301'];

	/**
	 * @var    (object) $instance Singleton
	 * @access public
	 * @static
	 */
	public static $instance = null;
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Init
	 */
	public function init() {
		// Notifications
		add_action( 'admin_notices', [ $this, 'seokey_redirections_notice'] );
        // Actions
		add_action( 'load-seokey_page_seo-key-redirections', [ $this, 'seokey_redirections_data_actions'] );
	}

	/**
	 * Send and register a redirection
	 */
	public function seokey_redirections_submit( $data ) {
        // Clean data
        $this->seokey_redirections_set_secured_form_post( $data );
        // Do we have enough data to add or modify our redirection ?
        if ( $this->seokey_redirections_validate_form_post() ) {
            // Prepare source
            $this->seokey_redirections_data_prepare_source();
            // Does this redirection already exist ?
            if ( $this->seokey_redirections_check_exist() ) {
                // Save redirection
                $this->seokey_redirections_data_save();
            }
        }
        // classic forms : redirect user
        if ( ! empty ( $_POST ) ) {
            // Get referrer URL
            $referer    = wp_get_referer();
            $full_url   = untrailingslashit( esc_url( home_url( $referer ) ) );
            // Redirect user, our job is done here
	        $full_url = add_query_arg( 'notice', (int) 1, $full_url );
            wp_safe_redirect( $full_url );
        }
	}

	/**
	 * Sanitize form data
	 */
	public function seokey_redirections_set_secured_form_post( $data ) {
		// Empty var for now
		$datas    = [];
		// How do we need to clean data ?
		$sanitize = [
			'id'     => "sanitize_key",
			'source' => 'sanitize_text_field',
			'target' => 'esc_url_raw',
			'type'   => "sanitize_text_field",
			'status' => "sanitize_text_field",
		];
		// If we have data
		if ( ! empty( $data ) ) {
			// Check data type
			$enabled_datas_type = apply_filters( 'seokey_filter_redirections_data_types', self::$type_redirection );
			if ( empty( $datas['type'] ) || ! empty( $datas['type'] ) && ! in_array( $datas['type'], $enabled_datas_type ) ) {
				$datas['type'] = current( $enabled_datas_type );
			}
			// Check status
			$enabled_datas_status = apply_filters( 'seokey_filter_redirections_data_status', self::$status_redirection );
			if ( empty( $datas['status'] ) || ! empty( $datas['status'] ) && ! in_array( $datas['status'], $enabled_datas_status ) ) {
				$datas['status'] = current( $enabled_datas_status );
			}
			// Cleaning data
			foreach ( $data as $post_key => $post_value ) {
				$key = sanitize_text_field( $post_key );
				if ( ! empty( $key ) ) {
					$value = null;
					if ( ! empty( $post_value ) ) {
						if ( in_array( $key, array_keys( $sanitize ) ) ) {
							if ( is_array( $sanitize[ $key ] ) ) {
								$sanitize_function = $sanitize[ $key ][ $datas['type'] ];
							} else {
								$sanitize_function = $sanitize[ $key ];
							}
							$value = $sanitize_function( $post_value );
						}
					}
					// Clean data again
					$datas[ $key ] = trim( $value );
				}
			}
		}
		$this->form_post = array_filter( $datas );
	}

	/**
	 * Check data before using it
	 * @return bool
	 */
	public function seokey_redirections_validate_form_post() {
		// We may need to throw an error
		$errors = [];
		// Check source
		if ( empty( $this->form_post['source'] ) ) {
			$errors['source'] = $error = __( 'Fill in the source field', 'seo-key' );
		}
		// Check target
		if ( empty( $this->form_post['target'] ) ) {
			$errors['target'] = $error = __( 'Fill in the target field ', 'seo-key' );
		} else {
			// TODO Fix issue on sub-sub-domaines
			$pattern = "/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)/";
			if ( ! preg_match( $pattern, $this->form_post['target'] ) ) {
				$errors['target'] = $error = __( 'Enter a valid target url', 'seo-key' );
			}
		}
		// Error ?
		if ( ! empty( $errors ) ) {
			// Define error message
			$this->validate = [
				'type'    => 'error',
				'message' => __( 'You have errors', 'seo-key' ),
				'fields'  => $errors
			];
			// Die here
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				wp_send_json_error( $error );
			}
			return false;
		}
        // Keep going !
        return true;
	}

	/**
	 * Check if this redirection is already here
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_redirections_check_exist() {
		// Database object
        global $wpdb;
		// Already an ID ? If we try di change source, check if it not yet in our current redirection list
		if ( ! empty( $this->datas['id'] ) ) {
			// Get current source from this ID
			$source_origin = $wpdb->get_results(
				$wpdb->prepare( "SELECT source FROM {$wpdb->prefix}seokey_redirections WHERE id=%s", $this->datas['id'] ), ARRAY_N
			);
			if ( ! empty( $source_origin ) && $this->datas['source'] === $source_origin[0][0] ) {
                // we are not changing current source
	            return true;
            }
		}
		// Check if source is already here
		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT source FROM {$wpdb->prefix}seokey_redirections WHERE BINARY source=%s", $this->datas['source'] )
		);
		// We found data, the redirection is already here
		if ( ! empty( $results ) ) {
			// Error message
			$this->validate = [
				'type'    => 'error',
				'message' => __( 'The source already exists', 'seo-key' )
			];
			// Die here
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				wp_send_json_error( 'Redirection already here' );
			}
			update_option( 'seokey_redirection_notice', $this->validate, false );
			return false;
		}
		// No results, this redirection does not exist ! We can proceed
		return true;
	}

	/**
	 * Handle data before saving
     *
     * @return void|bool
	 */
	public function seokey_redirections_data_prepare_source() {
        // TODO Fix here is user is trying to add a full permalink source with wrong domain
		// Get data
		$this->datas = $this->form_post;
		// Source URL (remove spaces)
		$source = trim( $this->datas['source'] );
		// Check source type, sanitize data and remove domain if necessary
        $source = seokey_helpers_get_current_domain($source);
        // Check if user is trying to redirect an URL from one of his own domains first
        $domain = seokey_helper_url_extract_domain( $source, true );
        $checkDomain = false;
        foreach ( seokey_helpers_get_available_domains() as $domainAvailable ){
            if( seokey_helper_url_extract_domain( $domainAvailable, true ) === $domain ){
                $checkDomain = true;
                break;
            }
        }
        // If not a good domain
        if( false === $checkDomain ){
            if ( defined('DOING_AJAX') && DOING_AJAX ) {
                wp_send_json_error( __( 'You can\'t redirect an external URL', 'seo-key' ) );
            }
            return false;
        }
		// Return data
		$this->datas['source'] = $source;

	}

	/**
	 * Save redirection
	 *
	 * @param bool $datas
	 * @return bool redirection saving status
	 */
	public function seokey_redirections_data_save( $datas = false ) {
		// Where is my data ?
		$datas = ( ! empty( $datas ) ) ? $datas : $this->datas;
		// Define ID
		$id = ( ! empty( $datas['id'] ) ) ? (int) $datas['id'] : (int) '';
		// Prepare data
		$request_datas = [
			'source' => $datas['source'],
			'target' => $datas['target'],
			'type'   => $datas['type'],
			'status' => $datas['status'],
		];
        // Create full URL
        $request_datas['source'] = seokey_helpers_get_current_domain( $request_datas['source'] );
        global $wpdb;
        // First: check if something has changed
        $checkChanges = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}seokey_redirections WHERE source=%s AND target=%s", $datas['source'] ,$datas['target'] )
        );
        if ( !empty( $checkChanges ) ) {
            if ( defined('DOING_AJAX') && DOING_AJAX ) {
                wp_send_json_error( __( 'Redirection already exists', 'seo-key' ) );
            }
            return false;
        }
        // Second: check if source = target
        if ( $request_datas['target'] === seokey_helpers_get_current_domain( $request_datas['source'] ) ) {
	        if ( defined('DOING_AJAX') && DOING_AJAX ) {
		        wp_send_json_error( __( 'Source and target are identical', 'seo-key' ) );
            }
	        return false;
        }
		// Database Object
		$table = $wpdb->prefix . 'seokey_redirections';
		// If we have ID : it's an update
		if ( ! empty( $id ) ) {
            // If update = reset hits
            $request_datas['hits'] = 0;
            // Source target type status hits
            $format = ['%s', '%s', '%s', '%d', '%d'];
			// Update
			$result = $wpdb->update( $table, $request_datas, ['id' => $id ], $format, ['%d'] );
			// Define success message
			$response = [
				'type'    => 'success',
				'message' => esc_html( 'Redirection changed', 'seo-key' )
			];
			$this->message_value = true;
            update_option( 'seokey_redirection_notice', $response, false );
			// Send message for ajax and non-ajax requests
            $good_or_not = ( 1 === $result ) ? true : false;
			if ( TRUE === $good_or_not ) {
				// Clean bad URL if necessary (404 and automatic redirections)
				$this->seokey_redirections_cleaning( $request_datas['source'] );
				// Send data to ajax call
			    if ( defined('DOING_AJAX') && DOING_AJAX ) {
				    $return = array(
					    'message'       => __( 'Redirection updated', 'seo-key' ),
					    'errorcount'   => $this->seokey_redirections_404_cleaning_count(),
					    'guessedcount' => $this->seokey_redirections_guessed_cleaning_count()
				    );
				    wp_send_json_success( $return );
				}
			}
			// Return result
			return $good_or_not;
		}
		else {
			// Saving a new redirect
			// Date format
            // Source Target Type Status Created_at
			$format = ['%s', '%s', '%s', '%d', '%s'];
            // Save data
			$wpdb->insert( $table, $request_datas, $format );
			// Define success message
			$response = [
				'type'    => 'success',
				'message' => esc_html( 'Redirection added', 'seo-key' )
			];
			// If we have a validation message, we will need to display it
			if ( ! empty( $this->validate ) ) {
				$response = $this->validate;
			}
			update_option( 'seokey_redirection_notice', $response, false );
			// Remove URl if the new redirection is found within the guess permalink list
			$redirections_guess = get_option( 'seokey_option_redirections_guess' );
			if ( false !== $redirections_guess ) {
				$newredirection = $datas['source'];
				foreach ( $redirections_guess as $key => $url ) {
					$from = seokey_helper_url_remove_domain( $url['From'] );
					if ( $newredirection === $from ) {
						unset ( $redirections_guess[ $key ] );
					}
				}
				update_option( 'seokey_option_redirections_guess', $redirections_guess, false );
			}
			// Send message for ajax and non-ajax requests
			$good_or_not = $wpdb->insert_id;
			if ( $good_or_not ) {
				// Clean bad URL if necessary (404 and automatic redirections)
				$this->seokey_redirections_cleaning( $request_datas['source'] );
				// Send data to ajax call
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					$return = array(
						'message'       => __( 'Redirection added', 'seo-key' ),
						'errorcount'   => $this->seokey_redirections_404_cleaning_count(),
						'guessedcount' => $this->seokey_redirections_guessed_cleaning_count()
					);
					wp_send_json_success( $return );
				}
			} else {
				$return = array(
					'message'       => __( 'Error adding this redirection', 'seo-key' ),
				);
				wp_send_json_error( $return );
			}
			// Return result
			return $good_or_not;
		}
	}

    // TODO Comment
	public function seokey_redirections_404_cleaning_count() {
		// Get database object
		global $wpdb;
		$table_name = $wpdb->prefix . 'seokey_redirections_bad';
		$result = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE type = "404"' );
		return $result;
	}
	// TODO Comment
	// TODO factorisation seokey_redirections_404_cleaning_count & seokey_redirections_guessed_cleaning_count
	public function seokey_redirections_guessed_cleaning_count() {
		// Get database object
		global $wpdb;
		$table_name = $wpdb->prefix . 'seokey_redirections_bad';
		$result = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE type = "guessed"' );
		return $result;
	}

	public function seokey_redirections_cleaning( $source ) {
        // Get database object
        global $wpdb;
        $table = $wpdb->prefix . 'seokey_redirections_bad';
        // Delete
		$wpdb->delete( $table, ['source' => $source ] );
        $wpdb->delete( $table, ['source' => str_replace( '&', '&#038;', $source ) ] );
    }

	/**
	 * Redirection actions
	 */
	public function seokey_redirections_data_actions() {
		// Do we want to trigger an action ?
		if ( ! empty( $_GET['type'] ) ) {
			// Check role
			seokey_redirection_check_capabilities();
			// Get ID
			$ID = ( ! empty ( $_GET['id'] ) ) ? (int) $_GET['id'] : '';
			// Valid ID ? Keep going
			if ( is_numeric( $ID ) ) {
				// Security Nonce
				$nonce        = $_GET['_wpnonce'];
				$nonce_action = 'actions-redirection' . $ID;
				// Good to go ?
				if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
					// Action types
					if ( "delete-redirection" === $_GET['type'] ) {
						// Trigger action
						$this->seokey_redirections_data_actions_delete( $ID );
					} elseif ( "edit-redirection" === $_GET['type'] ) {
						// Trigger action
						$this->seokey_redirections_data_actions_edit( $ID );
					}
				}
			}
		}
	}

    public function seokey_redirections_data_actions_delete( $ID, $table = 'seokey_redirections' ){
        // Get database object
        global $wpdb;
        $table = $wpdb->prefix . sanitize_title( $table );
        // Delete
        $seokey_redirections_delete = $wpdb->delete( $table, ['id' => $ID ] );
        // Get response
        if ( $seokey_redirections_delete ) {
	        if ( defined('DOING_AJAX') && DOING_AJAX ) {
		        wp_send_json_success( 'Redirection deleted' );
	        }
            $response = [
                'type'    => 'success',
                'message' => __( 'Redirection deleted', 'seo-key' )
            ];
        } else {
	        if ( defined('DOING_AJAX') && DOING_AJAX ) {
		        wp_send_json_error( 'Error deleting this redirection' );
	        }
            $response = [
                'type'    => 'error',
                'message' => 'Technical error'
            ];
        }
        // Update notice
        if ( ! empty( $response ) ) {
            update_option( 'seokey_redirection_notice', $response, false );
        }
	    // Redirect user
	    // TODO fix hardcoded URL
	    wp_redirect( '/wp-admin/admin.php?page=seo-key-redirections&notice=1' );
    }

    public function seokey_redirections_data_actions_edit( $ID ){
        // Get database object
        global $wpdb;
        $table = $wpdb->prefix . 'seokey_redirections';
        if ( ! empty( $ID ) ) {
            $where = [
                [
                    'condition' => 'id = %d',
                    'value'     => $ID,
                    'single'    => true
                ]
            ];
	        $wp_list_table = new seokey_WP_List_Table_redirections();
            $redirection = $wp_list_table->seokey_redirections_get_items( $where );
            if ( ! empty( $redirection ) ) {
                $this->form_fields_values = $redirection;
            }
        }
	}

	/**
	 * Display notification
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_redirections_notice() {
		// TODO PERF + Notification API
		$getnotice = '';
		if ( isset( $_GET['notice'] ) ) {
			$getnotice = sanitize_key( (int) $_GET['notice'] );
		}
		if ( $getnotice == 1 ) {
			$notice = get_option( 'seokey_redirection_notice' );
			if ( ! empty( $notice ) && $notice !== false ):
				?>
                <div class="notice notice-<?php echo $notice['type']; ?> is-dismissible">
                    <p><?php echo wp_kses( $notice['message'], true ); ?></p>
					<?php if ( ! empty( $notice['fields'] ) ): ?>
                        <ol>
							<?php foreach ( $notice['fields'] as $field => $error ): ?>
                                <li><?php echo wp_kses( $error, true ); ?></li>
							<?php endforeach; ?>
                        </ol>
					<?php endif; ?>
                </div>
				<?php
				update_option( 'seokey_redirection_notice', false, false );
			endif;
		}
	}
}
$redirections = Seokey_Redirections_Form::get_instance();
$redirections->init();

add_action('wp_ajax__seokey_redirections_form_submit', '_seokey_redirections_form_submit_callback');
/**
 * Action wp_ajax for redirection form submit
 */
function _seokey_redirections_form_submit_callback() {
	// Security check
	if ( ! check_ajax_referer( 'seokey-set-redirection', 'security', false ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	$data = array();
	$data['source'] = $_POST["source"];
	$data['target'] = $_POST["target"];
	$data['id'] = ( !empty( $_POST["id"] ) ) ? $_POST["id"] : '';
	if ( ! empty ( $data ) ) {
		if ( ! empty ( $data['target'] ) && ! empty ( $data['source'] ) ) {
			$redirections = Seokey_Redirections_Form::get_instance();
			$redirections->seokey_redirections_submit( $data );
			return;
		} else {
			wp_send_json_error( 'Not enough data in the redirection form' );
		}
	} else {
		wp_send_json_error( 'Not enough data in the redirection form' );
	}
}

add_action('wp_ajax__seokey_redirections_delete', '_seokey_redirections_delete_callback');
/**
 * Action wp_ajax for deleting redirection
 */
function _seokey_redirections_delete_callback() {
	// Security check
	if ( ! check_ajax_referer( 'seokey-set-redirection', 'security', false ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	// Check ID
	$id = (int) $_POST["id"];
	if ( !is_numeric($id)) {
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
		    wp_send_json_error( 'ID error' );
	    }
		return;
	}
    // Delete redirection
	$redirections = Seokey_Redirections_Form::get_instance();
    if ( isset( $_POST["type"] ) ) {
        switch ( $_POST["type"] ) {
            case 'error':
                $redirections->seokey_redirections_data_actions_delete( $id, 'seokey_redirections_bad' );
                break;
        }
    } else {
        $redirections->seokey_redirections_data_actions_delete($id);
    }
}

add_action('wp_ajax__seokey_redirections_edit', '_seokey_redirections_edit_callback');
/**
 * Action wp_ajax for editing redirection
 */
function _seokey_redirections_edit_callback() {
	// Security check
	if ( ! check_ajax_referer( 'seokey-set-redirection', 'security', false ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_send_json_error( 'Security issue' );
		return;
	}
	// Check ID
	$id = (int) $_POST["id"];
	if ( !is_numeric($id)) {
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			wp_send_json_error( 'ID error' );
		}
		return;
	}
	$source = $_POST["source"];
	$target = ( !empty( $_POST["target"] ) ) ? $_POST["target"] : '';
	$content = seokey_redirection_form( $id, $source, $target );
	wp_send_json_success( $content );
}

function seokey_redirection_form( $id, $source, $target ){
    $form = '<form action="' . esc_url( admin_url('admin-post.php') ) . '" method="post" class="seokey-redirections-form">';
	$nonce = wp_nonce_field( 'seokey-redirections-form-nojs', 'seokey-redirections-form-nojs-name', true, false);
	$form .= $nonce;
    $form .='
        <table class="form-table">
            <tbody>
            <tr>
                <td scope="row" class="tdaligncenter">
                    <label class="seokey-arrowbelow" for="source-' . (int) $id . '"><b>'. esc_html__( "From", "seo-key" ) . '</b></label>
                </td>
                <td>
                    <input placeholder="'.esc_attr__('Ex. /slug or https://mywebsite.com/slug', 'seo-key').'" name="source" type="text" id="source-' . (int) $id . '" class="regular-text" required="required"  value="' . esc_url( $source ) . '">
                </td>
            </tr>
            <tr>
                <td scope="row" class="tdaligncenter">
                    <label for="target-' . (int) $id . '"><b>' .esc_html__( "To", "seo-key" ) . '</b></label>
                </td>
                <td>
                    <input placeholder="'.esc_attr__('Ex. https://mywebsite.com', 'seo-key').'" name="target" type="text" id="target-' . (int) $id . '" class="regular-text" required="required"  value="' . esc_url( $target ) . '">
                </td>
            </tr>';
    if ( !empty ($target ) ) {
	    $label = esc_html__( "Update this redirection", "seo-key" );
    } else {
	    $label = esc_html__( "Add this redirection", "seo-key" );
    }
    $form .= '<tr>
                <td colspan="2">
                <input name="id" type="hidden" id="id-'. (int) $id .'" value="'. (int) $id .'">
                <button type="submit" class="button button-primary seokey_redirection_edit_button" value="'. (int) $id .'">' . esc_html__( $label, "seo-key" ) . '</button>
                <button type="submit" class="button button-secondary edit-redirection-cancel">' . esc_html__( "Cancel", "seo-key" ) . '</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>';
    return $form;
}