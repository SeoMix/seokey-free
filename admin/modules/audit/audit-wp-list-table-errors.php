<?php
/**
 * Admin WP List Table: display audit data
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     audit.php
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
 * Display Audit table (no js)
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @return void WP List Table HTML
 */
function seokey_audit_nojs( $task ) {
    // Security
    if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
        return;
    }
    // Load our class
    $wp_list_table = new seokey_WP_List_Table_audit_errors();
    // Define data
    $wp_list_table->set_columns( $wp_list_table->get_columns() );
    // We need a specific $task
    $wp_list_table->set_task( $task );
    // Get data
    $wp_list_table->prepare_items();
    // Show everything
    $wp_list_table->display();
}

// Let's go
class seokey_WP_List_Table_audit_errors extends seokey_WP_List_Table_base {

    /**
     * @var    string $task : $task to get
     * @access protected
     */
    private $currenttask = '';

    /**
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
                'singular'  => esc_html__( 'audit', 'seo-key' ),
                'plural'    => esc_html__( 'audits', 'seo-key' ),
                'ajax'      => true,
                'screen'    => 'seokey_page_seo-key-audit',
                'perpage'   => 30,
            )
        );
	
	    add_action( 'seokey_audit_free_tfoot', 'seokey_audit_free_tr_message' );
	    function seokey_audit_free_tr_message(){
		    $text = esc_html__( 'Want more data? Go PRO!', 'seo-key' );
		    $text .= __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY Premium</a>", 'seo-key' );
		    echo '<tr id="audit-tables-tfoot">
				<td>' . $text . '</td>
			</tr>';
	    }
    }

    /**
     * Define $task to use if needed
     *
     * @since    0.0.1
     * @author   Daniel Roch
     */
    public function set_task( $task ) {
        $this->currenttask = $task;
        return $this;
    }

    /**
     * The array is associative :
     * keys are slug columns
     * values are description columns
     *
     * @return array Column names and ID
     */
    function get_columns( $type_global = 'post' ) {
        if ( 'global' == $type_global ) {
            $array = array(
                //'cb'           => '<input type="checkbox" />',
                'priority' => esc_html__('Priority', 'seo-key'),
                'item_type' => esc_html__('Issue type', 'seo-key'),
                'name' => esc_html__('Issue', 'seo-key'),
                'datas' => esc_html__('Details', 'seo-key'),
                'actions' => esc_html__('Actions', 'seo-key'),
            );
        } else {
            $array = array(
                'priority' => esc_html__('Priority', 'seo-key'),
                'item_type' => esc_html__('Content type', 'seo-key'),
                'name' => esc_html__('Content title', 'seo-key'),
                'datas' => esc_html__('Issue', 'seo-key'),
                'actions' => esc_html__('Actions', 'seo-key'),
            );
        }
        return $array;
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
            'name'      => ['name', false ],
            'datas'     => ['datas', false],
            'priority'  => ['priority', false],
            'item_type' => ['item_type', false],
        );
    }

    /**
     * Add unique ID to each row (useful for some JS functions)
     *
     * @param object $item Row data
     * @return void echo HTML row
     */
    public function single_row( $item ) {
	    // Define if item has been modified since last audit
        $class = 'seokey-issue issue';
        if ( true === $item['modified'] ) {
            $class .= ' item_updated';
        }
        $class .= ' issue'. (int) $item['priority'];
        echo '<tr class="' . $class . '" id="auditrow-' . (int) $item['item_id'] . '">';
            $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * Item Column name
     *
     * @param object $item Row data
     * @return string
     */
	public function column_name( $item ) {
		// Get item name
		$name = ( !empty( esc_html( $item['name'] ) ) ) ? esc_html ( $item['name'] ) : esc_html__( '[NO TITLE]', 'seo-key');
		// Get data
		switch ( $item['item_type_global'] ) {
			case 'post':
				$url        = esc_url( get_permalink( (int) $item['item_id'] ) );
				$edit_link  = esc_url( get_edit_post_link( $item['item_id'] ) );
				break;
//			case 'term':
//				$url        = esc_url( get_term_link( (int) $item['item_id']) );
//				$edit_link  = esc_url( get_edit_post_link( $item['item_id'] ) );
//				break;
			case 'attachment':
				$url        = false;
				$edit_link  = false;
				break;
			case 'global':
				$url        = false;
				$edit_link  = false;
				break;
            case 'author':
                $url        = esc_url( get_author_posts_url( (int) $item['item_id'] ) );
                $edit_link  = esc_url( get_edit_user_link( (int) $item['item_id'] ) );
                break;
			default:
				break;
		}
		// Action links
		if ( false !== $edit_link ) {
			$actions['edit'] = '<a href="' . $edit_link . ' ">' . _x('Edit', 'List table row action', 'seo-key') . '</a>';
		}
		if ( false !== $url ) {
			$actions['view'] = '<a href="' . $url . ' ">' . _x('View', 'List table row action', 'seo-key') . '</a>';
		}
		if ( !empty ($actions) ) {
			// Return the title contents.
			return sprintf('%1$s %2$s',
				'<strong><a href="' . $edit_link . '">' . $name . '</a></strong>',
				$this->row_actions($actions)
			);
		}
		// Return the title contents.
		return $name;
	}

    /**
     * Content type Column
     *
     * @param object $item Row data
     * @return string
     */
    public function column_type_item( $item ) {
        return esc_html_x( $item['item_type'], 'content type in audit tables', 'seo-key' );
    }

    /**
     * Content type Column
     *
     * @param object $item Row data
     * @return string|void
     */
    public function column_actions( $item ) {
        if ( !current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
            return;
        }
        // Get data
        $action         = 'discard';
        $action_name    = _x( 'Ignore (PRO only)', 'Audit List table row actions', 'seo-key');
        switch ( $item['item_type_global'] ) {
            case 'post':
                $type           = "post";
                $id             = (int) $item['item_id'];
                break;
	        case 'attachment':
		        $type           = "attachment";
		        $id             = (int) $item['id'];
		        $url            = get_admin_url('', 'upload.php?mode=list&seokeyalteditor=yes');
		        $actions['alt-editor'] = '<a class="button button-primary" href="' . $url . ' ">' . _x( 'Go to ALT Editor', 'List table row action', 'seo-key' ) . '</a>';
		        // False = no option
		        break;
            case 'author':
                $type           = "author";
                $id             = (int) $item['id'];
                break;
            case 'global':
                $type           = "global";
                $id             = (int) $item['id'];
                break;
        }
        $subpriority = ( !empty( $item['sub_priority'] ) ) ? ' data-sub_priority="'. esc_attr( $item['sub_priority'] ) . '"' : '';
        $actions[$action] = '<button disabled ' . $subpriority . ' data-type="' . $type . '" data-useraction="' . $action . '" data-item="' . $id . '" class="issue-' . $action . ' button button-secondary">' . $action_name . '</button>';
        // Rendering
        $render = '';
        if ( !empty ( $actions ) ) {
            foreach ( $actions as $button ) {
                $render .= $button;
            }
        }
        // Return data
        return $render;
    }

    /**
     * Message Column CB
     *
     * @param object $item Row data
     * @return string
     */
    public function column_datas( $item ) {
        // Get basic messages
	    $render ='';
        // Modified message
	    if ( true === $item['modified'] ) {
	        $render     .= '<strong>' . esc_html__( 'Content modified since last audit: relaunch audit to renew data.', 'seo-key' ) . '</strong>';
		    $render     .= '<br>';
	    }
        // Sub-priority task
        if ( !empty ( $item['sub_priority'] ) ) {
            $task = $item['sub_priority'];
            $message = seokey_audit_get_sub_task_messages( $item['sub_priority'] );
            $datas = unserialize ( $item['datas'] );
            $datas = ( !is_array( $datas ) ) ? array( $datas ) : $datas;
            $render     .= vsprintf( $message, $datas );
        }
        // Classic task
        else {
            $task = $this->currenttask;
            $message = seokey_audit_get_task_messages( $this->currenttask );
			if ( empty( $item['datas'] ) ) {
				$render = $message[ $item['priority'] ];
			} else {
				$render .= vsprintf( $message[ $item['priority'] ], unserialize( $item['datas'] ) );
			}
        }
        $render = wp_kses_post( $render );
        if ( seokey_helper_has_help_messages( $task ) ) {
            $render = '<span>' . $render;
            $render .= seokey_helper_help_messages('audit-task-details-' . $task, true);
            $render .= '</span>';
        }
        return $render;
    }

    /**
     * @Override of prepare_items method
     */
    function prepare_items() {
        // Items per page
        if ( isset ( $_REQUEST['per_page'] ) ) {
            $per_page = (int) $_REQUEST['per_page'];
        } else {
            $per_page = seokey_helper_get_screen_option('per_page', 'seokey_audit_per_page', (int) 20);
        }
        $type_global = 'post';
        if ( isset ( $_REQUEST['item_type_global'] ) ) {
            $type_global = ( !empty( $_REQUEST['item_type_global'] ) ) ? sanitize_title( $_REQUEST['item_type_global'] ) : 'post';
        }
        // Define of column_headers. It contains
        $columns  = $this->get_columns( $type_global );
        $hidden   = $this->hidden_columns;
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        // Get all redirection data
        $items = $this->seokey_audit_get_items();
	    // Last audit date
	    $last_audit = (int) get_option( 'seokey_audit_global_last_update' );
        // Empty var for now
        $table = [];
        // We have data ? Create a correct table
        if ( ! empty( $items ) ) {
            foreach ( $items as $r ) {
	            // Tell us if this content has been modified since last audit
	            $updated = false;
	            // TODO auteurs + terms + autre
	            $current_item_date = (int) get_post_modified_time( 'U', false, (int) $r->item_id, false );
	            if ( $current_item_date > $last_audit ) {
		            $updated = true;
	            }
                // Content nicename
                // TODO authors & category
                $post_type_obj      = get_post_type_object( $r->item_type );
	            if ( is_null( $post_type_obj ) ) {
                    $content_nicename = $r->item_type;
                } else {
                    $content_nicename = $post_type_obj->labels->singular_name;
                }
                // Final data
                $table[ $r->id ] = [
                    'id'                    => $r->id,
                    'item_id'               => $r->item_id,
                    'name'                  => $r->item_name,
                    'datas'                 => $r->datas,
                    'priority'              => (int) $r->priority,
                    'sub_priority'          => $r->sub_priority,
                    'order'                 => (int) substr( $r->priority, 0, 1),
                    'audit_type'            => $r->audit_type,
                    'item_type_global'      => $r->item_type_global,
                    'item_type'             => $content_nicename,
                    'modified'              => $updated,
                ];
            }
        }
        // return data
        $data =  $table;
        // Sort all this stuff
        seokey_helper_cache_data( 'seokey_helper_usort_reorder','priority' );
        seokey_helper_cache_data( 'seokey_helper_usort_reorder_order','ASC' );
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
    public function seokey_audit_get_items() {
        // Get $task
        $task = sanitize_title( $this->currenttask );
        // Get database object
        global $wpdb;
        // Define our table
        $table_name = $wpdb->prefix . 'seokey_audit';
        // Searching data ?
        $search = ( ! empty ( $_REQUEST['s'] ) ) ? esc_sql( $_REQUEST['s'] ) : false;
		// TODO factorisation
        if ( false !== $search ) {
            // Get specific data (we are searching for a specific redirections)
            if ( !empty ($task ) ) {
                $result = $wpdb->get_results( "SELECT * FROM " . $table_name . ' WHERE task = "'.$task.'" AND item_name LIKE "%%%' . $search . '%%" ORDER BY priority ASC' );
            } else {
                $result = $wpdb->get_results( "SELECT * FROM " . $table_name . ' WHERE item_name LIKE "%%%' . $search . '%%" ORDER BY priority ASC' );
            }
        }
        else {
            if ( !empty ($task ) ) {
                $result = $wpdb->get_results( "SELECT * FROM " . $table_name . ' WHERE task = "'.$task.'" ORDER BY priority ASC' );
            } else {
                $result = $wpdb->get_results( "SELECT * FROM " . $table_name . ' ORDER BY priority ASC' );
            }
        }
        // Return data
        $result = ( ! empty( $result ) ) ? $result : false;
        return $result;
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
}

// First display
add_action('wp_ajax__seokey_audit_display_table', '_seokey_audit_display_table_callback');
/**
 * Action wp_ajax for fetching the first time all table structure
 */
function _seokey_audit_display_table_callback() {
    // Nonce
    check_ajax_referer('seokey_audit_table_list', 'security');
    // User role
    if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
        wp_die( __( 'Failed security check', 'seo-key' ), SEOKEY_NAME, 403 );
    }
        // Do we need a specific task ?
    $task = ( !empty( $_REQUEST['task'] ) ) ? sanitize_title( $_REQUEST['task'] ) : '';
    // New table
    $wp_list_table = new seokey_WP_List_Table_audit_errors();
    // Define columns
    $type_global = ( !empty( $_REQUEST['item_type_global'] ) ) ? sanitize_title( $_REQUEST['item_type_global'] ) : '';
    $columns = $wp_list_table->get_columns( $type_global );
    $wp_list_table->set_columns( $columns );
    // We need a specific $task
    $wp_list_table->set_task( $task );
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
