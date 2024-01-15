<?php
/**
 * Audit contents
 *
 * @Loaded on 'admin_init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

add_action( 'admin_enqueue_scripts', 'seokey_enqueue_admin_metabox_metas_audit' );
/**
 * Enqueue assets (CSS) for Option Reading Menu
 *
 * @author  Daniel Roch
 *
 * @uses    wp_enqueue_style()
 *
 * @hook    admin_enqueue_scripts
 *
 * @since   0.0.1
 */
function seokey_enqueue_admin_metabox_metas_audit( ) {
    // Only load if we need a metabox
    $goforit = seokey_helper_cache_data('SEOKEY_METABOX' );
    if ( $goforit === TRUE ) {
        // JS for all admin pages
	    wp_enqueue_script( 'wp-i18n' );
        wp_enqueue_script( 'seokey-metabox-audit', SEOKEY_URL_ASSETS . 'js/build/seokey-audit-content.js', array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION, TRUE );
        $args = array(
            // Ajax URL
            'ajaxUrl'               => admin_url( 'admin-ajax.php' ),
            // Security nonce
            'security'              => wp_create_nonce( 'seokey-metabox-audit-sec' ),
        );
        wp_localize_script( 'seokey-metabox-audit', 'seokey_audit_content', $args );
	    wp_set_script_translations( 'seokey-metabox-audit', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
    }
}

add_action('wp_ajax__seokey_audit_save_keyword', 'Seokey_audit_save_keyword_ajax');
function Seokey_audit_save_keyword_ajax(){
    // Nonce
    check_ajax_referer('seokey_audit_content_metabox', 'security');
    // User role
    if (!current_user_can(seokey_helper_user_get_capability('contributor'))) {
        wp_die(__('Failed security check', 'seo-key'), SEOKEY_NAME, 403);
    }
    $audit = new Seokey_Audit_Content();
    $post_id = (int) $_GET['id'];
    if ( $post_id > 0 ) {
        $post = get_post( $post_id );
        if ( $post ) {
            $audit->seokey_audit_content_save_main_keyword( $post_id, $post );
        } else {
            wp_send_json_error("No valid post");
        }
     } else{
        wp_send_json_error("No valid post");
    }
}

add_action( 'save_post', 'seokey_save_keyword_on_post_save', 10, 3 );
/**
 * Save SEOKEY keyword on currently saved post
 *
 * @author  Arthur Leveque
 * @since   1.8.1
 */
function seokey_save_keyword_on_post_save( $post_id, $post, $update ) {
    if ( !is_admin() ) {
        return;
    }
    if ( !current_user_can( seokey_helper_user_get_capability( 'contributor' ) ) ) {
        return;
    }
    // Do nothing if it's not an editor and this is not your post
    $current_user = wp_get_current_user();
    if( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) && $post->post_author !== $current_user->ID ) {
        return;
    }
    // Exclude adding keyword to secupress logs
    if ( $post_id > 0 && $post->post_type !== 'secupress_log_action' ) {
        $keyword = ( isset( $_POST['seokey_audit_content_main_keyword'] ) ) ? sanitize_text_field( $_POST['seokey_audit_content_main_keyword'] ) : '';
        // Do we have a keyword ?
        if ( !empty ( $keyword ) ) {
            // Update post meta
	        if ( ! add_post_meta( $post_id, 'seokey-main-keyword', $keyword, true ) ) {
		        update_post_meta ( $post_id, 'seokey-main-keyword', $keyword );
	        }
            // We need to add our keyword in our specific table
            // Get Database Object
            global $wpdb;
            $table = $wpdb->prefix . 'seokey_keywords';
            // only delete old values if we are updating and not creating the current post
            if ( $update ) {
                $wpdb->delete( $table, array(
                    'content_id' => $post_id
                ), array( '%d' ) );
            }
            // Prepare data in order to add this keyword to our list
            $post_url      = get_permalink( $post_id );
            $request_datas = [
                'keyword'      => $keyword,
                'content_url'  => $post_url,
                'content_type' => $post->post_type,
                'content_id'   => $post_id,
            ];
            // Date format
            $format = ['%s', '%s', '%s', '%d'];
            // Save data
            $wpdb->insert( $table, $request_datas, $format );
        }
    } 
}

class Seokey_Audit_Content {
	/**
	 * Seokey_Audit_Content constructor.
	 */
	public function __construct() {
		// Create metabox
		add_action( 'load-post.php',        [ $this, 'seokey_audit_meta_boxes'] );
		add_action( 'load-post-new.php',    [ $this, 'seokey_audit_meta_boxes'] );
		// Saving Post
		global $pagenow;
		if ( 'edit.php' === $pagenow ) {
			add_action( 'save_post', [ $this, 'seokey_audit_content_save_main_keyword'], 9, 2 );
		}
	}

	public function seokey_audit_meta_boxes() {
		global $typenow;
		// Only add the metabox if the post type is public (settings page).
		$cpts = array_flip( seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => TRUE ] ) ) );
		if ( isset( $cpts[ $typenow ] ) ) {
			// Tell SEOKEY we will need a meta metabox
			seokey_helper_cache_data( 'SEOKEY_METABOX', true);
		}
	}

	/**
	 * Metabox post page callback
	 */
	public function seokey_audit_content_metabox_cpt_callback() {
		include dirname(__FILE__) . '/parts/metabox.php';
	}

	/**
	 * Save main keyword from post
	 */
	public function seokey_audit_content_save_main_keyword( $post_id, $post ) {
		if ( 'auto-draft' == $post->post_status ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
        if ( !is_admin() ) {
            return;
        }
		if ( !current_user_can( seokey_helper_user_get_capability( 'contributor' ) ) ) {
			return;
		}
        // Die now if it's not and editor and this is not your post
        $current_user = wp_get_current_user();
        if( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) && $post->post_author !== $current_user->ID ) {
            wp_send_json_error( 'Not your post' );
            die;
        }
        $keyword            = ( isset( $_GET['seokey_audit_content_main_keyword'] ) ) ? sanitize_text_field( $_GET['seokey_audit_content_main_keyword'] ) : '';
        $whattodo           = seokey_audit_whattodo( $post_id, $keyword );
		if ( !is_null( $worktodo_data_details = seokey_helper_cache_data( 'worktodo_data_details-' . $post_id ) ) ) {
			$updatesuggestion = seokey_helper_suggestion_action( $whattodo['id'], $whattodo['worktodo'], false, $worktodo_data_details );
		} else {
			$updatesuggestion = seokey_helper_suggestion_action( $whattodo['id'], $whattodo['worktodo'], false, $post_id );
		}
        // Do we have a keyword ?
        if ( !empty ( $keyword ) ) {
            // Update post meta
	        if ( ! add_post_meta( $post_id, 'seokey-main-keyword', $keyword, true ) ) {
		        update_post_meta ( $post_id, 'seokey-main-keyword', $keyword );
	        }
            // We need to add our keyword in our specific table
            // Get Database Object
            global $wpdb;
            $table = $wpdb->prefix . 'seokey_keywords';
            // delete old values
            $wpdb->delete( $table, array(
                'content_id' => $post->ID
            ), array( '%d' ) );
            // Prepare data in order to add this keyword to our list
            $post_url      = get_permalink( $post->ID );
            $request_datas = [
                'keyword'      => $keyword,
                'content_url'  => $post_url,
                'content_type' => $post->post_type,
                'content_id'   => $post->ID,
            ];
            // Date format
            $format = ['%s', '%s', '%s', '%d'];
            // Save data
            $wpdb->insert( $table, $request_datas, $format );
            if ( defined('DOING_AJAX') && DOING_AJAX ) {
                wp_send_json_success( $updatesuggestion );
            }
        } else {
            delete_post_meta( $post_id, 'seokey-main-keyword' );
            // TODO Remove keyword from table
            if ( defined('DOING_AJAX') && DOING_AJAX ) {
                wp_send_json_success( $updatesuggestion );
            }
        }

	}
}

add_action('wp_ajax_seokey_audit_content_check', '_seokey_audit_content_check_callback');
/**
 * Action wp_ajax for audit stop&go
 */
function _seokey_audit_content_check_callback() {
    // Nonce
    check_ajax_referer('seokey-metabox-audit-sec', 'security');
    // User role
    if ( !current_user_can( seokey_helper_user_get_capability( 'contributor' ) ) ) {
        wp_die( __( 'Failed security check', 'seo-key'), SEOKEY_NAME, 403 );
    }
    // Get data
    $datas          = $_POST['datas'];
	// Prevent any audit without an ID
	if ( empty($datas["id"] )) {
		wp_send_json_success( 'no data to audit' );
		die;
	} else {
		$id = (int) $datas["id"];
	}
    $url        = seokey_helper_url_remove_slashes( seokey_helper_url_remove_domain($datas["permalink"]), 'both' );
    $excerpt    = ( empty ( $datas["excerpt"] ) ) ? '' : $datas["excerpt"];
	$date =  ( !empty( $datas["date"] ) ) ? $datas["date"] : get_the_date( 'c', $datas["id"] );
    // We do not want the <p> tag for excerpts
    remove_filter( 'the_excerpt', 'wpautop' );
    $item[ $id ]    = [
        'content'       => apply_filters( 'the_content',    stripslashes( $datas["content"] ) ),
        'title'         => apply_filters( 'the_title',      $datas["title"], $id ),
        'excerpt'       => apply_filters( 'the_excerpt',    $excerpt ),
        'metadesc'      => sanitize_text_field($datas["metadesc"]),
        'slug'          => $url,
        'date'          => $date,
        'keyword'       => $datas["keyword"],
        'author'        => ( !empty( $datas["author"] ) )? $datas["author"] : get_post_field( 'post_author', $id ),
        'id'            => $id,
    ];
    // add again the <p> tag filter to excerpts
    add_filter( 'the_excerpt', 'wpautop' );
    // We will store errors here
    $errors = '';
	$error_list = [];
    // Messages for all available tasks
    $messages       = seokey_audit_get_task_messages_content();
    $submessages    = seokey_audit_get_task_messages_content_subpriority();
	// load tasks
	$task_list = seokey_audit_task_list_content();
    // Prepare Queue for content issues
    if ( !empty ( $task_list ) ) {
		// Tell WP we are running an audit while editing on specific content
		seokey_helper_cache_data('audit_single_running', true );
        foreach ( $task_list as $type => $task_list_details ) {
				foreach ( $task_list_details as $task ) {
					$task      = 'content||' . $type . '||' . $task;
					$details   = explode( '||', $task );
					$task_name = sanitize_title( $details[0] . '_' . $details[2] );
					$file      = SEOKEY_PATH_ADMIN . 'modules/audit/tasks/' . $task_name . '.php';
					if ( file_exists( $file ) ) {
						seokey_helper_require_file( $task_name, SEOKEY_PATH_ADMIN . 'modules/audit/tasks/', 'contributor' );
						$class = 'Seokey_Audit_Tasks_' . $task_name;
						if ( class_exists( $class ) ) {
							// Run task now
							$run = new $class( $item );
							if ( ! empty( $run->tasks_status ) ) {
								$raw_data     = array_shift( $run->tasks_status );
								$current_task = $raw_data['task'];
								// Get message for classic task
								if ( empty( $raw_data['sub_priority'] ) ) {
									$current_priority = substr( $raw_data['priority'], 0, 1 );
									$error            = $messages[ $current_task ][ $current_priority ];
	                            }
	                            // Get message for subpriority task
								else {
									$error        = $submessages[ $raw_data['sub_priority'] ];
									$current_task = $raw_data['sub_priority'];
								}
								// Do we have data to use ?
								if ( ! empty( $raw_data['datas'] ) ) {
									$error = wp_kses_post( vsprintf( $error, $raw_data['datas'] ) );
								}
								$error_list[ $current_task ] = $error;

						}
					}
				}
			}
        }
    }
    // Do we have SEO issues ?
	if ( !empty ( $error_list ) ) {
		// Get score for all task type
		$audit_tasks_issues_score = seokey_audit_get_task_score();
		$audit_tasks_issues_score_clean = wp_list_pluck( $audit_tasks_issues_score, 'global' );
		// Sort task scoring by priority
		arsort( $audit_tasks_issues_score_clean, SORT_NUMERIC );
		$errors_sorted = array_replace( $audit_tasks_issues_score_clean, $error_list );
		// Sort our current content errors
        $audit_tasks_issues_score = wp_list_pluck( $audit_tasks_issues_score, 'type' );
		$count = 0;
		foreach ( $errors_sorted as $key => $issue ) {
			$count = $count + (int) $issue;
            // TODO terms and authors
            $type           = "post";
			if ( !is_numeric( $issue ) ) {
	                $class = "task issue";
	                $action = 'discard';
	                $action_name = _x('Ignore', 'Audit List table row actions', 'seo-key');
                if ( current_user_can( seokey_helper_user_get_capability( 'editor') ) ) {
                    $discard_action = ' <button disabled data-sub_priority="' . esc_attr( $key ) . '" data-task="' . esc_attr( $key ) . '" data-type="' . $type . '" data-useraction="' . $action . '" data-item="' . $id . '" class="issue-' . $action . ' button button-small button-secondary">' . $action_name . '</button>';
                } else {
                    $discard_action = '';
                }
                $class .= ' has-explanation issue-'. $key . ' issue-'. $audit_tasks_issues_score[ $key ];
                $help   = seokey_helper_help_messages( 'audit-task-' . $key , true );
                $errors .= '<li data-type="content"  class="' . $class . '"><span>' . $issue . '</span>' . $discard_action . $help . '</li>';
			}
		}
	}
	$errors .= '<div id="audit-metabox-go-pro">';
		$errors .= '<span>' . esc_html__( 'Want more data? Go PRO!', 'seo-key' ) . '</span>';
		$errors .= __( "<a class='button button-primary button-hero' target='_blank' href='https://www.seo-key.com/pricing/'>Buy SEOKEY Premium</a>", 'seo-key' );
	$errors .= '</div>';
	// return HTML data to display on content audit
    wp_send_json_success( $errors );
}