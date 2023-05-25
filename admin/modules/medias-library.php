<?php
/**
 * Improve media library
 *
 * @Loaded on plugins_loaded + is_admin() + capability author
 * @see seokey_plugin_init()
 * @package SEOKEY
 */

/**
 * Security
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}

add_filter( 'parent_file', 'seokey_medias_library_editor_menu_highlight' );
/**
 * Media Library ALT Editor Menu highlight
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook parent_file
 */
function seokey_medias_library_editor_menu_highlight( $file ) {
    // Are we in the ALT editor ?
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        $file = 'seo-key';
    }
    return $file;
}

add_action( 'admin_menu', 'seokey_medias_library_menu_move', 200 );
/**
 * Move ALT editor menu
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @hook admin_menu
 * @global $submenu
 * @see seokey_admin_menus()
 */
function seokey_medias_library_menu_move() {
    global $submenu;
    // Get alt editor menu
    // TODO later find a better way to target correct $submenu['seo-key']
    $alt_editor_menu = $submenu['seo-key'][0];
    // delete it
    unset($submenu['seo-key'][0]);
    // Add it to the right place
    $submenu['seo-key'] = seokey_helper_array_insert_at_position( $submenu['seo-key'], 4, $alt_editor_menu);
}

add_action( 'admin_menu', 'seokey_medias_library_menu', 5 );
/**
 * Media Library ALT Editor Menu
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook admin-menu
 */
function seokey_medias_library_menu() {
    add_submenu_page(
        'seo-key',
        __( 'ALT Editor', 'seo-key' ),
        __( 'ALT Editor', 'seo-key' ),
        seokey_helper_user_get_capability( 'editor' ),
        'upload.php?mode=list&seokeyalteditor=yes',
        '',
        20
    );
}

add_filter('submenu_file', 'seokey_medias_library_menu_highlight');
/**
 * Highlight correct menu
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_medias_library_menu_highlight( $submenu_file ) {
    if ( seokey_helpers_medias_library_is_alt_editor() ) {
        return 'upload.php?mode=list&seokeyalteditor=yes';
    }
    return $submenu_file;
}

add_action( 'admin_init', 'seokey_medias_library_notice' );
/**
 * Notices on Media Library
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook load-upload.php
 */
function seokey_medias_library_notice() {
    // Are we using media library ?
	if ( true !== seokey_helpers_medias_library_is_alt_editor() ) {
		add_filter('seokey_filter_admin_notices_launch', 'seokey_medias_library_notice_content_grid', 1 );
	} else {
        // Avoid errors with third party plugins
		remove_all_filters( 'bulk_actions-upload' );
		add_filter('seokey_filter_admin_notices_launch', 'seokey_medias_library_notice_fix_images', 11);
	}
}

/**
 * Main notice for our ALT Editor
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_medias_library_notice_fix_images( $args ) {
    global $wp_version;
    // WP version is before 6.0
    if ( 0 > version_compare( $wp_version, '6.0.0' ) ) {
        $broken_text = '<div class="notice-flexboxcolumn">';
        $broken_text .= '<h3>'. __( 'WordPress Media library is broken', 'seo-key' ) . '</h3>';
        $broken_text .= '<p>'. __( 'WordPress Media Library allows users to manage their media and update their alternative texts.', 'seo-key' ) . '</p>';
        $broken_text .= '<p>';
        $broken_text .= __( 'But, it <strong>does not natively update alternative texts everywhere</strong>. When you add a media into your content, it is no longer linked to the media library. Changing the ALT in the media library does not change update your media within in your contents.', 'seo-key' );
        $broken_text .= seokey_helper_help_messages( 'alt-editor-media-library' );
        $broken_text .= '</p>';
        $broken_text .= '<p>';
        $broken_text .= __( "<strong>Update to WordPress 6.0 or above to allow SEOKEY to fix this issue</strong>.", 'seo-key' );
        $broken_text .= '</p>';
        $broken_text .= '</div>';
    }
    // WP Version is after 6.0
    else {
        $broken_text = '<div class="notice-flexboxcolumn">';
        $broken_text .= '<h3>'. __( 'SEOKEY fixes the WordPress media library', 'seo-key' ) . '</h3>';
        $broken_text .= '<p>'. __( 'WordPress Media Library allows users to manage their media and update their alternative texts.', 'seo-key' ) . '</p>';
        $broken_text .= '<p>';
        $broken_text .= __( 'But, it <strong>does not natively update alternative texts everywhere</strong>. When you add a media into your content, it is no longer linked to the media library. Changing the ALT in the media library does not change update your media within in your contents.', 'seo-key' );
        $broken_text .= seokey_helper_help_messages( 'alt-editor-media-library-fixed' );
        $broken_text .= '</p>';
        $broken_text .= '<p>';
        $broken_text .= __( "<strong>SEOKEY PRO do it automatically for you</strong>, you have nothing to do.", 'seo-key' );
        $broken_text .= '</p>';
        $broken_text .= '</div>';
    }
    $text = '<div class="flexbox">';
    $text .= '<div class="notice-flexboxcolumn">';
    $text .= '<h3>'. __( 'Why alternative texts are important?', 'seo-key' ) . '</h3>';
    $text .= '<p>';
    $text .= __( 'With an alternative text, Google will understand your images.', 'seo-key' );
    $text .= seokey_helper_help_messages( 'alt-explanations' );
    $text .= '</p>';
    $text .= '<p>'. __( 'It will improve your visibility and accessibility on Search Engines Results.', 'seo-key' ) . '</p>';
    $text .= '</div>';
    $text .= $broken_text;
    $text .= '<div class="notice-flexboxcolumn">';
    $text .= '<h3>'. __( 'How to use our SEO ALT editor ?', 'seo-key' ) . '</h3>';
    $text .= '<p>'. __( 'Use the forms below to update each alternative text. You can use keyboard TAB to switch from one ALT to the next one while updating them.', 'seo-key' ) . '</p>';
    $text .= '<p>'. __( 'We added a filter above the media library to allow quick access to all images without alternative texts..', 'seo-key' ) . '</p>';
    $text .= '</div>';
    $text .= '</div>';
    $new_args = array(
        sanitize_title( 'seokey_notice_media_library_fix_images' ), // Unique ID.
        '',
        $text,
        [
            'scope'         => 'user',       // Dismiss is per-user instead of global.
            'type'          => 'information',    // Make this an information
            'capability'    => seokey_helper_user_get_capability('editor' ), // only for these users and above
            'alt_style'     => false, // alternative style for notice
            'option_prefix' => 'seokey_notice_show_media_library',   // Change the user-meta or option prefix.
            'state'         => 'permanent',
            'class'         => ['notice'],
        ]
    );
    array_push($args, $new_args );
    return $args;
}

add_filter('admin_body_class', 'seokey_medias_library_notice_body_class');
function seokey_medias_library_notice_body_class($classes) {
    // Are we using media library ?
    global $pagenow;
    if ( 'upload.php' === $pagenow && true === seokey_helpers_medias_library_is_alt_editor() ) {
        $classes .= ' seokey-admin-alt-editor';
    }
    // Return all classes
    return $classes;
}

add_filter( 'manage_media_columns', 'seokey_medias_library_columns', SEOKEY_PHP_INT_MAX, 1 );
/**
 * Remove or move other columns in Media library ALT editor
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array $args List of current columns
 */
function seokey_medias_library_columns( $columns ) {
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        // Only our custom columns
        // $list['cb']                         = $columns['cb'];
        $list['seokey_column_media']        = $columns['seokey_column_media'];
        $list['seokey_column_media_alt']    = $columns['seokey_column_media_alt'];
        $list['seokey_column_media_name']   = $columns['seokey_column_media_name'];
        return $list;
    }
    // return all columns
    return $columns;
}

add_filter( 'bulk_actions-upload', 'seokey_medias_library_remove_bulkactions', SEOKEY_PHP_INT_MAX );
// TODO comment
function seokey_medias_library_remove_bulkactions($bulk_actions) {
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        return [];
    }
    return $bulk_actions;
}

add_filter( 'admin_title', 'seokey_medias_library_page_title' );
/**
 * Custom <title> for our ALT editor page
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array $args List of current columns
 */
function seokey_medias_library_page_title( $title ){
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        $title = esc_html__( 'SEOKEY ALT Editor Media Library', 'seo-key');
        $title .= ' &lsaquo; ' . get_bloginfo( 'name' );
    }
    return $title;
}

/**
 * Notice on media library grid view : you are missing something
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array $args List of current notifications
 */
function seokey_medias_library_notice_content_grid( $args ) {
    $url = get_home_url( null, '/wp-admin/upload.php?mode=list&seokeyalteditor=yes' ) ;
    $new_args = array(
        'seokey_notice_media_library_grid', // Unique ID.
        esc_html__('SEOKEY ALT editor is hidden', 'seo-key'), // The title for this notice.
        sprintf(
        /* translators: 1:Plugin name 2:Admin URL to Media List View */
            __( 'You should use SEOKEY ALT Editor to improve the visibility of your images on Google.<br><br><a class="button button-primary" href="%1$s">View ALT Editor</a>', 'seo-key' ),
            esc_url ( $url )
        ),
        [
	        'scope'         => 'user',       // Dismiss is per-user instead of global.
	        'type'          => 'information',    // Make this a warning (orange color).
	        'capability'    => seokey_helper_user_get_capability('editor' ), // only for theses users and above
	        'screens'       => ['upload'],
	        'alt_style'     => false, // alternative style for notice
        ]
    );
    array_push($args, $new_args );
    return $args;
}

/**********************************************************************************************/
/************************************ ONLY ON THE ALT EDITOR **********************************/
/**********************************************************************************************/

if ( true === seokey_helpers_medias_library_is_alt_editor() ) {

    /************************************ ALT Column **********************************/
    add_filter( 'manage_media_columns', 'seokey_medias_library_column', 50 );
    /**
     * Add our ALT column
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook manage_media_columns
     * @param array $posts_columns Current columns
     * @return array Current columns
     */
    function seokey_medias_library_column( $posts_columns ) {
        // Add an ALT column
        $posts_columns['seokey_column_media_name']  = esc_html__( 'Media information', 'seo-key' );
        $posts_columns['seokey_column_media']       = esc_html__( 'Media', 'seo-key' );
        $posts_columns['seokey_column_media_alt']   = esc_html__( 'Media ALT attribute', 'seo-key' );
        // Return all colunms
        return $posts_columns;
    }

    add_filter( 'manage_upload_sortable_columns', 'seokey_medias_library_column_sortable', SEOKEY_PHP_INT_MAX );
    /**
     * Make our ALT column sortable
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook manage_upload_sortable_columns
     * @param array $columns Current columns
     * @return array Current columns
     */
    function seokey_medias_library_column_sortable( $columns ) {
        // Add an ALT column
        $columns['seokey_column_media_alt'] = '_wp_attachment_image_alt';
        $columns['seokey_column_media_name'] = 'title';
        // Return all colunms
        return $columns;
    }

    add_filter( 'request', 'seokey_medias_library_column_sortable_orderby' );
    /**
     * Get the orderby value for our ALT sortable column
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook request
     * @param array $vars Current orderby vars
     * @return array Current orderby vars
     */
    function seokey_medias_library_column_sortable_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && '_wp_attachment_image_alt' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                    'orderby' => 'meta_value',
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => '_wp_attachment_image_alt',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => '_wp_attachment_image_alt',
                            'compare' => 'EXISTS'
                        )
                    )
                )
            );
        }
        return $vars;
    }

    /************************************ ALT Form **********************************/
    add_action( 'manage_media_custom_column', 'seokey_medias_library_column_content_alt_form', 10, 2 );
    /**
     * Get our Data and form for ALT column
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook manage_media_custom_column
     * @param string $column_name Column name
     * @param int $id Media ID
     */
    function seokey_medias_library_column_content_alt_form( $column_name, $id ) {
        if ( 'seokey_column_media_alt' !== $column_name ) {
            return;
        }
        $alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
        seokey_medias_library_alt_form( $alt, $id );
    }

    add_action( 'manage_media_custom_column', 'seokey_medias_library_column_content_name', 10, 2 );
    /**
     * Get our Data and form for ALT column
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook manage_media_custom_column
     * @param string $column_name Column name
     * @param int $id Media ID
     */
    function seokey_medias_library_column_content_name( $column_name, $id ) {
        if ( 'seokey_column_media_name' !== $column_name ) {
            return;
        }
        seokey_medias_library_get_file_infos( $id );
    }

    add_action( 'manage_media_custom_column', 'seokey_medias_library_column_content_file', 10, 2 );
    /**
     * Get our Data and form for ALT column
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook manage_media_custom_column
     * @param string $column_name Column name
     * @param int $id Media ID
     */
    function seokey_medias_library_column_content_file( $column_name, $id ) {
        if ( 'seokey_column_media' !== $column_name ) {
            return;
        }
        seokey_medias_library_get_media( $id );
    }

    // TODO Comment
    function seokey_medias_library_get_media( $id ) {
        $post = get_post( $id );
        list( $mime ) = explode( '/', $post->post_mime_type );

        $title      = _draft_or_post_title();
        $thumb      = wp_get_attachment_image( $post->ID, array( 60, 60 ), true, array( 'alt' => '' ) );

        $class = $thumb ? ' class="has-media-icon"' : '';
        ?>
        <strong<?php echo $class; ?>>
            <?php
            if ( $thumb ) :
                ?>
                <span class="media-icon <?php echo sanitize_html_class( $mime . '-icon' ); ?>"><?php echo $thumb; ?></span>
            <?php
            endif;
            _media_states( $post );
            ?>
        </strong>
        <?php
    }

    // TODO Comment
    function seokey_medias_library_get_file_infos( $id ) {
        $post = get_post( $id );
        list( $mime ) = explode( '/', $post->post_mime_type );
        $title      = _draft_or_post_title();
        $link_start = '';
        $link_end   = '';
        ?>
        <span>
            <?php _e( 'Title in media library:', 'seo-key' ); ?>
        </span>
        <strong>
            <?php
            echo $title;
            _media_states( $post  );
            ?>
        </strong>
        <br>
        <span>
            <?php _e( 'File name:', 'seo-key'  ); ?>
        </span>
        <strong>
            <?php
            $file = get_attached_file( $post->ID );
            echo esc_html( wp_basename( $file ) );
            ?>
        </strong>
        <br>
        <?php seokey_medias_library_get_parent( $post );
    }

    // TODO Comment
    function seokey_medias_library_get_parent( $post ) {
        if ( $post->post_parent > 0 ) {
            $parent = get_post( $post->post_parent );
        } else {
            $parent = false;
        }
        if ( $parent ) {
            ?>
            <span>
                <?php _e( 'This file was uploaded in this content:', 'seo-key'  ); ?>
            </span>
            <?php
            $title       = _draft_or_post_title( $post->post_parent );
            $parent_type = get_post_type_object( $parent->post_type );
            if ( $parent_type && $parent_type->show_ui && current_user_can( 'edit_post', $post->post_parent ) ) {
                ?>
                <strong>
                    <a href="<?php echo get_edit_post_link( $post->post_parent ); ?>">
                        <?php echo $title; ?>
                    </a>
                </strong>
                <?php
            } elseif ( $parent_type && current_user_can( 'read_post', $post->post_parent ) ) {
                ?>
                <strong>
                    <?php echo $title; ?>
                </strong>
                <?php
            }
        }
    }


    /**
     * Add ALT form if necessary
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @param string $alt Current Media ALT
     * @param int $id Current Media ID
     */
    function seokey_medias_library_alt_form( $alt, $id ) {
        // Is this an attachment
        if ( wp_attachment_is_image($id) ) {
            // You are not an editor or admin
            if ( ! current_user_can( seokey_helper_user_get_capability('editor') ) ) {
                // get data about this image
                $author_id      = (int) get_post_field ('post_author', $id);
                $current_user   = (int) get_current_user_id();
                if ( $current_user !== $author_id ) {
                    // It's not your image, die here but show some information to user
                    if ( ! empty ( $alt ) ) {
                        esc_html_e( $alt );
                    } else {
                        esc_html_e( 'No ALT found', 'seo-key' );
                    }
                    echo '<br>';
                    esc_html_e( "This media belongs to another author: you can't change it.", 'seo-key' );
                    return;
                }
            }
            // Display form
            seokey_medias_library_alt_form_content( $alt, $id );
        }
    }

    /**
     * ALT Form content
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @param string $alt Current Media ALT
     * @param int $id Current Media ID
     */
    function seokey_medias_library_alt_form_content( $alt, $id ){
        ?>
        <form method="post">
            <div class="seokey-media-alt" id="<?php echo $id; ?>">
                <label for="seokey_alt_id_<?php echo $id; ?>">
                    <?php _e( 'Edit your image description (ALT text)', 'seo-key');?>
                    <?php echo seokey_helper_help_messages( 'alt-editor-input-label');?>
                </label>

                <span class="input-loader">
                    <input autocomplete="off" placeholder="<?php _e( 'Describe this media', 'seo-key');?>" tabindex="<?php echo wp_unique_id ();?>" type="text" class="regular-ext seokey_alt_input" id="seokey_alt_id_<?php echo $id; ?>" value="<?php echo esc_attr( $alt );?>" data-oldvalue="<?php echo esc_attr( $alt );?>" /><input id="seokey_alt_id_submit_<?php echo $id; ?>" class="seokey-alt-editor-submit" type="submit" value="<?php esc_html_e( 'Submit', 'seo-key' );?>">
                    <span class="alt-spinner"></span>
                    <span class="seokey-media-alt-message" style="display:none;"><?php _e('ALT text saved', 'seo-key');?></span>
                </span>
            </div>
        </form>
        <?php
    }

    add_action( 'admin_enqueue_scripts', 'seokey_medias_library_alt_form_js' );
    /**
     * Add JS for ALT Editor
     *
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @hook admin_enqueue_scripts
     * @param string $hook Current page hook
     */
    function seokey_medias_library_alt_form_js( $hook ) {
        if ( $hook === 'upload.php' && current_user_can( seokey_helper_user_get_capability( 'author' ) ) ) {
            wp_enqueue_script( 'seokey-js-media-library-alt', SEOKEY_URL_ASSETS . 'js/seokey-media-library-alt.js', array( 'jquery' ), SEOKEY_VERSION, true );
            $args = array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'ajax_nonce'    => wp_create_nonce( 'seokey-js-media-library-alt-nonce' ),
            );
            wp_localize_script( 'seokey-js-media-library-alt', 'adminAjax', $args );
        }
    }
}




add_action( 'wp_ajax_seokey_medias_library_alt_form_update' , 'seokey_medias_library_alt_form_update' );
/**
 * Ajax call for ALT editor forms
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook wp_ajax_seokey_medias_library_alt_form_update
 */

function seokey_medias_library_alt_form_update() {
    // Security check
    if ( ! check_ajax_referer( 'seokey-js-media-library-alt-nonce', 'security', false ) ||
        ! current_user_can( seokey_helper_user_get_capability ('author' ) ) ) {
        wp_send_json_error( 'Security Issue' );
        return;
    }
    // Data
    $media_post_id  = absint ( $_POST['post_id'] );
    // Check if this is your media (if you are not an editor or above)
    if ( ! current_user_can( seokey_helper_user_get_capability('editor') ) ) {
        $author_id    = (int) get_post_field( 'post_author', $media_post_id );
        $current_user = (int) get_current_user_id();
        if ( $current_user !== $author_id ) {
            // This is not your media : die here
            wp_send_json_error( 'This is not your content' );
            return;
        }
    }
    // Do we have data ?
    $media_alt_text = wp_strip_all_tags( $_POST['alt_text'] );
    $data = sanitize_text_field( $media_alt_text );
    if ( empty ( $data) ) {
        // empty ALT, delete meta
        delete_post_meta( $media_post_id, '_wp_attachment_image_alt' );
    } else {
        // Update ALT text
        update_post_meta($media_post_id, '_wp_attachment_image_alt', $data);
    }
    wp_send_json_success();
}




/************************************ Alt filter (dropdown **********************************/
add_action( 'restrict_manage_posts', 'seokey_medias_library_filter_alt_images', 1 );
/**
 * Create ALT Media library filters
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @hook restrict_manage_posts
 */
function seokey_medias_library_filter_alt_images(){
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        seokey_medias_library_filter_alt_images_select( array(
            'show_option_all'   => __( 'All medias (with or without ALT)', 'seo-key' ),
            'selected'          => sanitize_title( get_query_var( 'seokey-alt-filter', 0 ) ),
            'name'              => 'seokey-alt-filter'
        ));
    }
}

add_action( 'restrict_manage_posts',  'seokey_medias_library_clean_otheractions', 2 );
/**
 * Disable all other plugins actions on media editor
 *
 * @since 0.0.1
 * @author Daniel Roch
 * @return void
 */
function seokey_medias_library_clean_otheractions() {
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        remove_all_actions( 'restrict_manage_posts' );
    }
}






/**
 * ALT Media library filters select
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param array|string $args Current select arguments
 */
function seokey_medias_library_filter_alt_images_select( $args = '' ) {
    $defaults = array(
        'show_option_all'         => '',
        'selected'                => 0,
        'name'                    => 'seokey-alt-filter',
    );
    $parsed_args        = wp_parse_args( $args, $defaults );
    $show_option_all    = esc_html( $parsed_args['show_option_all'] );
    $name               = esc_attr( $parsed_args['name'] );
    $output = '';
    $output .= '<select autocomplete="off" name="' . $name . '">';
    $values = [
        'without_alt'   => __( 'Images without ALT', 'seo-key'),
        'with_alt'      => __( 'Images with ALT', 'seo-key')
    ];
    if ( $show_option_all ) {
        $output .= "\t<option value='0'>$show_option_all</option>\n";
    }
    $currentfilter = '';
    if ( isset( $_GET['seokey-alt-filter'] ) ) {
        $currentfilter         = sanitize_text_field( $_GET['seokey-alt-filter'] );
    }
    foreach ( (array) $values as $key => $value ) {
        $key        = esc_attr( $key );
        $_selected  = selected( $key, $currentfilter, false );
        $output .= "\t<option value='$key' $_selected>" . esc_html( $value ) . "</option>\n";
    }
    $output .= '</select>';
    echo $output;
}

add_action('pre_get_posts', 'seokey_medias_library_filter_alt_images_request_filter');
/**
 * Filter Media Library according to our own filters
 *
 * @param object $query Current Admin Query
 * @author  Daniel Roch
 *
 * @hook pre_get_posts
 * @since   0.0.1
 */
function seokey_medias_library_filter_alt_images_request_filter( $query ) {
    // Are we using the media library ?
    if ($GLOBALS['pagenow'] !== 'upload.php' || !is_admin()) {
        return;
    }
    // Fix compatibility with some other plugins
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
    $screen = seokey_helper_get_current_screen();
    if (empty($screen) || ($screen->id !== 'upload')) {
        return;
    }
    // Are we trying to filter media according to ALT data ?
    if (isset($_GET['seokey-alt-filter'])) {
        $param = sanitize_text_field($_GET['seokey-alt-filter']);
        // All images with an ALT
        if ('with_alt' === $param) {
            $query->query_vars['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_wp_attachment_image_alt',
                    'value' => array(''),
                    'compare' => 'NOT IN'
                )
            );
            $query->query_vars['post_mime_type'] = "image";
        } // All images without ALT
        elseif ('without_alt' === $param) {
            $query->query_vars['meta_query'] = array(
                array(
                    'key' => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS'
                ),
            );
            $query->query_vars['post_mime_type'] = "image";
        } // ALl images
        elseif ('0' === $param) {
            $query->query_vars['post_mime_type'] = "image";
        }
    }
}

add_filter ( 'disable_months_dropdown', 'seokey_medias_library_clean_dates' );
/**
 * Disable date dropdown
 *
 * @author  Daniel Roch
 * @hook disable_months_dropdown
 * @since   0.0.1
 */
function seokey_medias_library_clean_dates(){
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        return true;
    }
    return false;
}

add_filter( 'bulk_actions-upload', 'seokey_medias_library_clean_bulkactions' );
/**
 * Disable bulk actions
 *
 * @author  Daniel Roch
 * @hook bulk_actions-upload (bulk_actions-{SCREEN->ID}
 * @since   0.0.1
 */
function seokey_medias_library_clean_bulkactions($actions) {
    if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
        return '';
    }
    return $actions;
}