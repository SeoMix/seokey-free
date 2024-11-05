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
class seokey_WP_List_Table_contents extends seokey_WP_List_Table_base {
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
			'content'     => esc_html__( 'Content', 'seo-key' ),
			'clicks'      => esc_html__( 'Clicks', 'seo-key' ),
			'impressions' => esc_html__( 'Impressions', 'seo-key' ),
			'keyword'     => esc_html__( 'Targeted keyword', 'seo-key' ),
			'keywordpos'  => esc_html__( 'Average position', 'seo-key' ),
			'advice'      => esc_html__( 'Advice for this keyword', 'seo-key' ),
			'keywords'    => esc_html__( 'Important known keywords', 'seo-key' ),
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
			'content'     => [ 'content', false ],
			'keyword'     => [ 'keyword', false ],
			'advice'      => [ 'whattodo', false ],
		);
	}


	/**
	 * Prints column headers, accounting for hidden and sortable columns.
	 * Edited for SEOKEY to use DESC default sorting for numeric sortable columns
	 *
	 * @since 1.9.0
	 *
	 * @param bool $with_id Whether to set the ID attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
			                     . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}
		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );
			$order_text     = '';
			if ( in_array( $column_key, $hidden, true ) ) {
				$class[] = 'hidden';
			}
			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ), true ) ) {
				$class[] = 'num';
			}
			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}
			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];
				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = strtolower( $desc_first );
					if ( ! in_array( $order, array( 'desc', 'asc' ), true ) ) {
						// Here we are working default SEOKEY sorting
						if ( 'numeric' == $sortable[$column_key][1] ) {
							$order = $desc_first ? 'desc' : 'desc';
						} else {
							$order = $desc_first ? 'desc' : 'asc';
						}
					}
					$class[] = 'sortable';
					$class[] = 'desc' === $order ? 'asc' : 'desc';
				}
				$column_display_name = sprintf(
					'<a href="%1$s">' .
					'<span>%2$s</span>' .
					'<span class="sorting-indicators">' .
					'<span class="sorting-indicator asc" aria-hidden="true"></span>' .
					'<span class="sorting-indicator desc" aria-hidden="true"></span>' .
					'</span>' .
					'%3$s' .
					'</a>',
					esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ),
					$column_display_name,
					$order_text
				);
			}
			$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';
			if ( ! empty( $class ) ) {
				$class = "class='" . implode( ' ', $class ) . "'";
			}
			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}

	/**
	 * Add keyword data range and filters for users
	 *
	 * @since 1.9.0
	 *
	 * @param string $which defines tablenav to enhance
	 */
	protected function extra_tablenav( $which ) {
        if ( $which === 'top' ) {
            echo '<div class="alignleft actions">';
				// Contents without targeted keyword
		        $checked = false;
		        if ( isset($_REQUEST['filter2']) && $_REQUEST['filter2'] == 1 ) {$checked = true;}
				echo '<input type="checkbox" class="seokey-filter" id="filter2" name="filter2" ' . checked(1, $checked, false) . ' />';
	            echo '<label class="seokey-label-filter" for="filter2" >' . esc_html__('Contents without targeted keywords', 'seo-key') . '</label>';
	            // Contents with targeted keyword
		        $checked = false;
		        if ( isset($_REQUEST['filter3']) && $_REQUEST['filter3'] == 1 ) {$checked = true;}
		        echo '<input type="checkbox" class="seokey-filter" id="filter3" name="filter3" ' . checked(1, $checked, false) . ' />';
		        echo '<label class="seokey-label-filter" for="filter3" >' . esc_html__('Contents with targeted keywords', 'seo-key') . '</label>';
            echo '</div>';
        }
	}

	// Show selected keyword
	public function column_keyword( $item ) {
		return esc_html( $item['keyword'] );
	}

	// Show keyword position
	public function column_keywordpos( $item ) {
		return esc_html__( '(PRO)', 'seo-key' );
	}

	// Show known keywords
	public function column_keywords( $item ) {
		return esc_html__( '(PRO)', 'seo-key' );
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
	 * Add unique ID to each row (useful for some JS functions)
	 *
	 * @param object $item Row data
	 * @return void echo HTML row
	 */
	public function single_row( $item ) {
		$class = '';
        $nofollow = get_post_meta( (int) $item['id'], 'seokey-content_visibility', true );
		if ( '1' === $nofollow ) {
            $class = 'nofollow';
		}
		echo '<tr class="' . $class . '">';
		    $this->single_row_columns( $item );
		echo '</tr>';
	}
	
 
 
	/**
	 * @Override of prepare_items method
	 */
	function prepare_items() {
		// Items per page
		$per_page = seokey_helper_get_screen_option( 'per_page', 'seokey_keywords_per_page', 25 );
		// Define of column_headers. It contains
		$columns               = $this->get_columns();
		$hidden                = $this->hidden_columns;
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		// Get all content data
		$items = $this->seokey_keywords_get_items();
		// Empty var for now
		$table = [];
		// We have data ? Create a correct table
		if ( ! empty( $items ) ) {
			foreach ( $items as $r ) {
				$todo                 = seokey_audit_whattodo( $r->ID );
				$table[ $r->ID ] = [
					'id'          => $r->ID,
					'content'     => esc_html( get_the_title( (int) $r->ID ) ),
					'keyword'     => esc_html( $r->keyword ),
					'whattodo'    => $todo['worktodo'],
					'whattodoid'  => $todo['id'],
				];
			}
		}
		// Sort all this stuff
		seokey_helper_cache_data( 'seokey_helper_usort_reorder', 'content' );
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
        // Filter Post types
		$post_types = seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => true ] ) );
		$post_types_list = implode( "','", array_map( 'esc_sql', $post_types ) );
		// Define the SQL query: all post published without password and indexable, with clics and impression data
        $sql = "SELECT 
			    {$wpdb->prefix}posts.ID,
			    {$wpdb->prefix}posts.post_title,
			    {$wpdb->prefix}posts.post_type,
			    {$wpdb->prefix}posts.post_password,
				keyword_meta.meta_value AS keyword
			FROM {$wpdb->prefix}posts
			LEFT JOIN {$wpdb->prefix}postmeta AS keyword_meta
			ON {$wpdb->prefix}posts.ID = keyword_meta.post_id
			AND keyword_meta.meta_key = 'seokey-main-keyword'
			WHERE {$wpdb->prefix}posts.post_status = 'publish' 
			AND {$wpdb->prefix}posts.post_password = ''
			AND {$wpdb->prefix}posts.post_type IN ('$post_types_list')";
		// Search data ?
		$search  = ( ! empty ( $_REQUEST['s'] ) ) ? esc_sql( $_REQUEST['s'] ) : false;
		if ( false !== $search ) {
            $sql .= " AND {$wpdb->prefix}posts.post_title LIKE '%$search%'";
		}
		// Keyword data ?
		$keyword  = ( ! empty ( $_REQUEST['filter2'] ) ) ? esc_sql( $_REQUEST['filter2'] ) : false;
		if ( false !== $keyword ) {
			$sql .= " AND (keyword_meta.meta_value IS NULL
        OR keyword_meta.meta_value = '')";
		}
		// Keyword data ?
		$keyword  = ( ! empty ( $_REQUEST['filter3'] ) ) ? esc_sql( $_REQUEST['filter3'] ) : false;
		if ( false !== $keyword ) {
			$sql .= " AND (keyword_meta.meta_value IS NOT NULL
        OR keyword_meta.meta_value != '')";
		}
        $results = $wpdb->get_results($sql);
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
		// Parent Display method
		if ( isset ( $_REQUEST['per_page'] ) ) {
			$per_page = (int) $_REQUEST['per_page'];
		} else {
			$per_page = seokey_helper_get_screen_option( 'per_page', 'seokey_keywords_per_page', 20 );
		}
		echo '<input id="per_page" name="per_page" type="hidden" value="' . (int) $per_page . '">';
		echo '<input id="orderby" name="orderby" type="hidden" value="content">';
		echo '<input id="order" name="order" type="hidden" value="ASC">';
		parent::display();
	}
}