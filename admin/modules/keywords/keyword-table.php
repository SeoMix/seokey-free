<?php
/**
 * Keywords List Table
 *
 * @Loaded on 'plugins_loaded' + admin
 *
 * @see     keywords.php
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

// Let's go
class seokey_WP_List_Table_keywords extends seokey_WP_List_Table_base {
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
				'singular' => esc_html__( 'keyword', 'seo-key' ),
				'plural'   => esc_html__( 'keywords', 'seo-key' ),
				'ajax'     => true,
				'screen'   => 'seokey_page_seo-key-keywords',
				'perpage'  => 30,
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
			'keyword'     => esc_html__( 'Targeted keyword or phrase', 'seo-key' ),
			'position'    => esc_html__( 'Average position', 'seo-key' ),
			'clicks'      => esc_html__( 'Clicks', 'seo-key' ),
			'impressions' => esc_html__( 'Impressions', 'seo-key' ),
			'content'     => esc_html__( 'Related content', 'seo-key' ),
			'advice'      => esc_html__( 'Next action', 'seo-key' ),
		);
	}

	/**
	 * @return array nonce data
	 */
	function get_nonce() {
		return array(
			'seokey-audit-list-nonce',
			'_seokey_audit_list_nonce',
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
			'keyword'     => [ 'keyword', false ],
			'position'    => [ 'position', false ],
			'clicks'      => [ 'clicks', false ],
			'impressions' => [ 'impressions', false ],
			'advice'      => [ 'whattodo', false ],
			'content'     => [ 'content', false ],
		);
	}

	// Show selected keyword
	public function column_keyword( $item ) {
		return $item['keyword'];
	}

	// Show position for each combination of keyword + content
	public function column_position( $item ) {
		$pos = $item['position'];
		if ( 1000 === $pos ) {
			$pos = esc_html__( '(PRO only)', 'seo-key' );
		}

		return $pos;
	}

	// Show clicks for each combination of keyword + content
	public function column_clicks( $item ) {
		return esc_html__( '(PRO)', 'seo-key' );
	}

	// Show impressions for each combination of keyword + content
	public function column_impressions( $item ) {
		return esc_html__( '(PRO)', 'seo-key' );
	}

	// Show advice for each combination of keyword + content
	public function column_advice( $item ) {
		// Render
		if ( !is_null( $worktodo_data_details = seokey_helper_cache_data( 'worktodo_data_details-' . $item['id'] ) ) ) {
			$render = seokey_helper_suggestion_action( $item['whattodoid'], $item['whattodo'], true,$worktodo_data_details );
		} else {
			$render = seokey_helper_suggestion_action( $item['whattodoid'], $item['whattodo'], true, $item['id'] );
		}
		return $render;
	}

	/**
	 * Item Column content
	 *
	 * @param object $item Row data
	 *
	 * @return string
	 */
	public function column_content( $item ) {
		// TODO later terms, authors
		$url       = esc_url( get_permalink( (int) $item['id'] ) );
		$edit_link = esc_url( get_edit_post_link( (int) $item['id'] ) );
		$name      = $item['content'];
		// Main link
		$link = '<a href="' . $edit_link . '">' . $name . '</a>';
		// Action links
		$actions['edit'] = '<a href="' . $edit_link . ' ">' . _x( 'Edit', 'List table row action', 'seo-key' ) . '</a>';
		$actions['view'] = '<a href="' . $url . ' ">' . _x( 'View', 'List table row action', 'seo-key' ) . '</a>';

		// Return content
		return sprintf( '%1$s %2$s',
			$link,
			$this->row_actions( $actions )
		);
	}

	/**
	 * @Override of prepare_items method
	 */
	function prepare_items() {
		// Items per page
		$per_page = seokey_helper_get_screen_option( 'per_page', 'seokey_keywords_per_page', 20 );
		// Define of column_headers. It contains
		$columns               = $this->get_columns();
		$hidden                = $this->hidden_columns;
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		// Get all redirection data
		$items = $this->seokey_keywords_get_items();
		// Empty var for now
		$table = [];
		// We have data ? Create a correct table
		if ( ! empty( $items ) ) {
			foreach ( $items as $r ) {
                $clicks      = '-';
                $impressions = '-';
                $position = 1000;
				$todo                 = seokey_audit_whattodo( $r->post_id );
				$table[ $r->post_id ] = [
					'id'          => $r->post_id,
					'content'     => esc_html( get_the_title( (int) $r->post_id ) ),
					'keyword'     => $r->meta_value,
					'position'    => $position,
					'clicks'      => $clicks,
					'impressions' => $impressions,
					'whattodo'    => $todo['worktodo'],
					'whattodoid'  => $todo['id'],
				];
			}
		}
		// Sort all this stuff
		seokey_helper_cache_data( 'seokey_helper_usort_reorder', 'impressions' );
		seokey_helper_cache_data( 'seokey_helper_usort_reorder_order', 'DESC' );
		usort( $table, 'seokey_helper_usort_reorder' );
		// Current page
		$current_page = $this->get_pagenum();
		// Define items
		$total_items = count( $table );
		$table       = array_slice( $table, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $table;
		// Pagination call
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
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
	 * @return array|false|object|void
	 */
	public function seokey_keywords_get_items() {
		global $wpdb;
		$table_metas = $wpdb->prefix . 'postmeta';
		// TODO fix Search parameter if user is sending one
		// Searching data ?
//$search = ( ! empty ( $_REQUEST['s'] ) ) ? esc_sql( $_REQUEST['s'] ) : false;
//		$results = $wpdb->get_results('
//			SELECT  *
//			FROM ' . $table_metas . '
//			WHERE meta_key = "seokey-main-keyword"
//		');


		// Searching data ?
		$search  = ( ! empty ( $_REQUEST['s'] ) ) ? esc_sql( $_REQUEST['s'] ) : false;
		$results = ( false !== $search ) ?
			$wpdb->get_results( "SELECT * FROM $table_metas WHERE meta_key = 'seokey-main-keyword' AND meta_value LIKE '%%%$search%%'"
			) :
			$wpdb->get_results( "SELECT * FROM $table_metas WHERE meta_key = 'seokey-main-keyword'"
			);


		// No data, do nothing
		if ( empty( $results ) ) {
			return false;
		}

		// Get data
		return $results;
	}


	/**
	 * @Override of display method
	 */
	public function display() {
//		$this->display_tablenav('top');
		// Parent Display method
		if ( isset ( $_REQUEST['per_page'] ) ) {
			$per_page = (int) $_REQUEST['per_page'];
		} else {
			$per_page = seokey_helper_get_screen_option( 'per_page', 'seokey_keywords_per_page', 20 );
		}
		echo '<input id="per_page" name="per_page" type="hidden" value="' . (int) $per_page . '">';
		echo '<input id="orderby" name="orderby" type="hidden" value="impressions">';
		echo '<input id="order" name="order" type="hidden" value="DESC">';
		parent::display();
	}
}

// Display table Ajax calls
add_action('wp_ajax__seokey_keywords_display_table', '_seokey_keywords_display_table_callback');
/**
 * Action wp_ajax for fetching the first time all table structure
 */
function _seokey_keywords_display_table_callback() {
	// Nonce
	check_ajax_referer('seokey_keywords_table_list', 'security');
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
		wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
	}
	// New table
	$wp_list_table = new seokey_WP_List_Table_keywords();
	// Define data
	$wp_list_table->set_columns( $wp_list_table->get_columns() );
	// Get data
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