<?php
/**
 * Custom WP_List_Table base
 *
 * @Loaded  on 'init' + role editor
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

// WP_List_Table base
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Let's create a default and functionnal WP List Table
class seokey_WP_List_Table_base extends WP_List_Table {
	/**
	 * Primary column (for order SQL clause)
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const PRIMARY_COLUMN   = '';

	/**
	 * Our columns
	 *
	 * @since 3.1.0
	 * @var array
	 */
	protected $_columns;
	function set_columns( $args ) {
		$this->_columns = $args;
	}

	/**
	 * @return array
	 * The array is associative :
	 * keys are slug columns
	 * values are description columns
	 */
	function get_columns() {
		return array();
	}

	/**
	 * Use this function to get nonce data
	 */
	function get_nonce() {
		return '';
	}

	/**
	 * @Override of constructor
	 * Constructor take 3 parameters:
	 * singular : name of an element in the List Table
	 * plural : name of all of the elements in the List Table
	 * ajax : if List Table supports AJAX set to true
	 */
	function __construct() {
		parent::__construct(
			array(
				'singular'  => esc_html__( 'xxx', 'seo-key' ),
				'plural'    => esc_html__( 'xxx', 'seo-key' ),
				'ajax'      => true,
				'screen'    => 'xxx',
				'perpage'   => 20,
			)
		);
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 *
	 * Method column_default let at your choice the rendering of everyone of column
	 */
	function column_default( $item, $column_name ) {
		$column_name = sanitize_text_field( $column_name );
		return $item[ $column_name ];
	}

	/**
	 * @var array
	 *
	 * Array contains slug columns that you want hidden
	 */
	private $hidden_columns = array(
	);

	/**
	 * @return array
	 *
	 * The array is associative :
	 * keys are slug columns
	 * values are array of slug and a boolean that indicates if is sorted yet
	 */
	function get_sortable_columns() {
		$sortable_columns = [];
		$columns = $this->get_columns();
		// Do not sort Bulk actions
		if ( array_key_exists( 'cb', $columns ) ) {
			unset($columns['cb']);
		}
		foreach ( $columns as $key => $value ) {
			$sortable_columns[$key] = [ $key, false];
		}
		return $sortable_columns;
	}

	/**
	 * @Override of display method
	 */
	function display() {
		if ( isset( $_REQUEST['page'] ) ) { ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
		<?php }
        echo '<span id="search-table-element">';
		    $this->search_box( __( 'search', 'seo-key'), 'search_id' );
		echo '</span>';
		// Parent Display method
		parent::display();
	}

    /**
     * @Override of search method
     */
     function search_box( $text, $input_id, $class = '' ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
        $input_id = $input_id . '-search-input';
        foreach( [ 'orderby', 'order', 'post_mime_type', 'detached' ] as $field ) {
            if ( ! empty( $_REQUEST[ $field ] ) ) {
                echo '<input type="hidden" name="' . $field . '" value="' . esc_attr( $_REQUEST[ $field ] ) . '" />';
            }
        }
        ?>
        <form class="search-box seokey-search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
        </form>
        <?php
    }


	/**
	 * @Override ajax_response method
	 */
	function ajax_response() {
	    // Copy this function but DO NOT FORGET TO ADD OR FIX SECURITY CHECKS (nonces + user rôle)
		if ( ! current_user_can( seokey_helper_user_get_capability( 'contributor' ) ) ) {
			wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
		}
		// Get data
		$this->prepare_items();
		// Extract args
		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );
		// Capture data
		ob_start();
			if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
				$this->display_rows();
			} else {
				$this->display_rows_or_placeholder();
			}
			$rows = ob_get_clean();
		ob_start();
			$this->print_column_headers();
			$headers = ob_get_clean();
		ob_start();
			$this->pagination('top');
			$pagination_top = ob_get_clean();
		ob_start();
			$this->pagination('bottom');
			$pagination_bottom = ob_get_clean();
		// Define response
		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;
		if ( isset( $total_items ) ) {
			$response['total_items_i18n'] = sprintf( _n( '%s item', '%s items', $total_items, 'seo-key' ), number_format_i18n( $total_items ) );
		}
		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}
		// Send JSON encoded data
		die( json_encode( $response ) );
	}

	/**
	 * Column CB
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	public function column_cb( $item ){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label ("plugin")
			/*$2%s*/ (int) $item['id']          // The value of the checkbox should be the record's id
		);
	}
}
