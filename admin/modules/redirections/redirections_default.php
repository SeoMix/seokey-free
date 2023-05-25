<?php
/**
 * Admin Redirection module : Core redirection list
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     redirections.php
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
 * Display Redirections table
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_redirections_display_default(){
    ?>
	<form id="seokey_redirection_list" method="get">
		<div id="seokey_redirections"></div>
		<div id="seokey_redirections_no_js">
			<?php
			// Fallback no JS
//			seokey_redirection_nojs();
			?>
		</div>
	</form>
	<?php
}

/**
 * Display Redirections table : NO JS
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @return void WP List Table HTML
 */
function seokey_redirection_nojs() {
	// Security
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		return;
	}
	// Load our class
	$wp_list_table = new seokey_WP_List_Table_redirections();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
	$wp_list_table->prepare_items();
	// Show everything
	$wp_list_table->display();
}

// Let's go
class seokey_WP_List_Table_redirections extends seokey_WP_List_Table_base {
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
				'singular'  => esc_html__( 'redirection', 'seo-key' ),
				'plural'    => esc_html__( 'redirections', 'seo-key' ),
				'ajax'      => true,
				'screen'    => 'seokey_page_seo-key-redirections',
				'perpage'   => 30,
			)
		);
	}

	/**
	 * The array is associative :
	 * keys are slug columns
	 * values are description columns
     *
     * @return array Column names and ID
	 */
	function get_columns() {
		return array(
			//'cb'           => '<input type="checkbox" />',
			'source'       => esc_html__( 'Redirect this URL', 'seo-key' ),
			'target'       => esc_html__( 'To', 'seo-key' ),
			'actions'      => esc_html__( 'Actions', 'seo-key' ),
			'hits'         => esc_html__( 'Hits', 'seo-key' ),
			'hits_last_at' => esc_html__( 'Last hit', 'seo-key' ),
		);
	}

	/**
	 * @return array nonce data
	 */
	function get_nonce() {
		return array(
			'seokey-redirection-list-nonce',
			'_seokey_redirection_list_nonce',
		);
	}

	/**
	 * @return array
	 *
	 * The array is associative :
	 * keys are slug columns
	 * values are array of slug and a boolean that indicates if is sorted yet
	 */
	function get_sortable_columns() {
		return $sortable_columns = array(
			'source'       => ['Source', false ],
			'target'       => ['Target', false ],
			'hits'         => ['Hits', false ],
			'hits_last_at' => ['Hits_last_at', false ],
		);
	}

	/**
	 * Add unique ID to each row (useful for some JS functions)
     *
	 * @param object $item Row data
	 * @return void echo HTML row
	 */
	public function single_row( $item ) {
		echo '<tr id="redirectionrow-' . (int) $item['id'] . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Source Column CB
	 *
	 * @param object $item Row data
	 * @return string
	 */
	public function column_source( $item ) {
        return seokey_redirection_display_column_source($item);
    }

	/**
	 * Target Column CB
	 *
	 * @param object $item Row data
	 *
	 * @return string
	 */
	public function column_target( $item ) {
		return '<a class="targeturl" href="' . esc_url( $item['target'] ) . '">' . esc_html( $item['target'] ) . '</a>';
	}

	/**
	 * Actions Column CB
	 *
	 * @param object $item Row data
	 *
	 * @return string
	 */
	public function column_actions( $item ) {
		// Links and item data
		$itemid = (int) $item['id'];
		$data = [
			'edit-redirection'      => [
				__( 'Edit','seo-key' ), 'button-primary', 'edit-redirection'
			],
			'delete-redirection'      => [
				__( 'Delete','seo-key' ), 'button-primary'
			],
		];
		// Base URL
		$base_url   = admin_url( 'admin.php' );
		if ( !empty( $_GET['page']) ) {
			$base_url   = add_query_arg( 'page', sanitize_title( $_GET['page']),  $base_url );
		}
		// Create all links
		$links  = '<span class="column-actions-wrapper">';
		foreach ( $data as $key => $type ) {
			$url = add_query_arg( 'type', esc_html( $key ), $base_url );
			$url = add_query_arg( 'id', $itemid, $url );
			$url = add_query_arg( '_wpnonce', wp_create_nonce( 'actions-redirection'.$itemid ), $url );
			$links.= '<a href="' . esc_url ( $url ) . '" class="button ' . sanitize_html_class( $type[1] ) . ' ' . sanitize_html_class ( $key ) . '">' . esc_html( $type[0] ) . '</a>';
		}
		$links .= '</span>';
		// return data
		return $links;
	}

	/**
	 * @var array
	 *
	 * Array contains slug columns that you want hidden
	 */
	private $hidden_columns = array();

	/**
	 * Get redirections list with data
	 *
	 * @param array $where SQL clause
	 * @return array|false|object|void
	 */
	public function seokey_redirections_get_items( $where = [] ) {
		// Get database object
		global $wpdb;
		// Define our table
		$table_name = $wpdb->prefix . 'seokey_redirections';
		// Searching data ?
		$search = ( ! empty ( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
		if ( false !== $search ) {
			$do_search = ( $search ) ? "source LIKE %s OR target LIKE %s" : '';
			// Get specific data (we are searching for a specific redirections)
			$request_prepared = $wpdb->prepare( "SELECT * FROM " . $table_name . " WHERE " . $do_search, '%' . $search . '%', '%' . $search . '%' );
			$result = $wpdb->get_results( $request_prepared );
		}
		else {
			// Specific where clause
			if ( ! empty( $where[0]['single'] ) && $where[0]['single'] === true ) {
				// prepare data
				$search_condition   = $where[0]['condition'];
				$search_value       = $where[0]['value'];
				$request_prepared = $wpdb->prepare( "SELECT * FROM " . $table_name . " WHERE " . $search_condition, $search_value );
				$result = $wpdb->get_row( $request_prepared );
			}
			// All redirections
			else {
				$result = $wpdb->get_results( "SELECT * FROM " . $table_name . ' ORDER BY hits_last_at DESC' );
			}
		}
		// Return data
		$result = ( ! empty( $result ) ) ? $result : false;
		return $result;
	}

	/**
	 * @Override of prepare_items method
	 */
	function prepare_items() {
		// Items per page
		if ( isset ( $_REQUEST['per_page'] ) ) {
			$per_page = (int) $_REQUEST['per_page'];
		} else {
			$per_page = seokey_helper_get_screen_option('per_page', 'seokey_redirections_per_page', (int) 20);
		}
		// Define of column_headers. It contains
		$columns  = $this->get_columns();
		$hidden   = $this->hidden_columns;
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		// Get all redirection data
		$redirections = $this->seokey_redirections_get_items();
		// Empty var for now
		$table = [];
		// We have data ? Create a correct table
		if ( ! empty( $redirections ) ) {
			foreach ( $redirections as $r ) {
				$table[ $r->id ] = [
					'id'           => $r->id,
					'source'       => $r->source,
					'target'       => $r->target,
					'hits'         => ( ! empty( $r->hits ) ) ? $r->hits : 0,
					'hits_last_at' => $r->hits_last_at,
				];
			}
		}
		// return data
		$data =  $table;
		// Sort all this stuff
        seokey_helper_cache_data( 'seokey_helper_usort_reorder','hits_last_at' );
		usort( $data, 'seokey_helper_usort_reorder' );
		// Current page
		$current_page = $this->get_pagenum();
		// Define items
		$total_items = count($data);
		$data = array_slice( $data,( ( $current_page-1 ) * $per_page ),$per_page );
		$this->items = $data;
		// Pagination call
		$this->set_pagination_args(
			array(
				'total_items'	=> $total_items,
				'per_page'	    => $per_page,
				'total_pages'	=> ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * @Override of display method
	 */
	function display() {
        // Parent Display method
		if ( isset ( $_REQUEST['per_page'] ) ) {
			$per_page = (int) $_REQUEST['per_page'];
		} else {
			$per_page = seokey_helper_get_screen_option('per_page', 'seokey_redirections_per_page', (int) 20);
		}
        echo '<input id="per_page" name="per_page" type="hidden" value="' . (int) $per_page .'">';
		echo '<input id="orderby" name="orderby" type="hidden" value="hits_last_at">';
		echo '<input id="order" name="order" type="hidden" value="DESC">';

		parent::display();
	}

	/**
	 * @Override ajax_response method for native calls
	 */
	function ajax_response() {
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
}

// First display
add_action('wp_ajax__seokey_redirections_ajax_display_default', '_seokey_redirections_ajax_display_callback');
/**
 * Action wp_ajax for fetching the first time all table structure
 */
function _seokey_redirections_ajax_display_callback() {
	// Nonce
	check_ajax_referer('seokey_redirection_list', 'security');
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
	}
	// New table
	$wp_list_table = new seokey_WP_List_Table_redirections();
	$wp_list_table->prepare_items();
	// capture data
	ob_start();
	$wp_list_table->display();
	$display = ob_get_clean();
	// return json encoded table
	die(
		json_encode(
			array(
				"display" => $display
			)
		)
	);
}

// Update Table
add_action( 'wp_ajax__seokey_redirections_ajax_fetch_history', '_seokey_redirections_ajax_fetch_history_callback' );
/**
 * Action wp_ajax for fetching ajax_response
 */
function _seokey_redirections_ajax_fetch_history_callback() {
	$wp_list_table = new seokey_WP_List_Table_redirections();
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	$wp_list_table->ajax_response();

}
