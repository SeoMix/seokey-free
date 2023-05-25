<?php
/**
 * Load Post type archive menu
 *
 * @Loaded on plugins_loaded + is_admin() + capability editor
 * @see seokey_plugin_init()
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

add_action( 'admin_menu', 'seokey_admin_menus_post_type_archive' );
/**
 * Add a "Archive page" menu for each public CPT with active post type archive (has_archive = true in the register_post_type function)
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @hook   admin_menu
 * @see    add_submenu_page()
 * @return void
 */
function seokey_admin_menus_post_type_archive() {
	// Get all public, CPTs from WP
	$_builtin = get_post_types( ['_builtin' => true, 'public' => true, 'has_archive' => true ], 'objects' );
	// Get all custom CPTs
	$_custom = get_post_types( ['_builtin' => false, 'public' => true, 'has_archive' => true ], 'objects' );
	// Merge them
	$all_cpts = array_merge( $_builtin, $_custom );
	// No one ? Kill here
	if ( empty ( $all_cpts ) ) {
		return;
	}
	// Iterate the menu creation
	foreach ( $all_cpts as $key => $post_type_object ) {
	    $key = sanitize_title( $key );
		// Default slug for a CPT
		$slug = 'edit.php?post_type=' . $key;
		// Native "post" type is different
		if ( 'post' === $key ) {
			$slug = 'edit.php';
		}
		// Native "media" is, but not not available by default, just in case someone hacked it
		if ( 'attachement' === $key ) {
			$slug = 'upload.php';
		}
		// Add the menu finally
		add_submenu_page( $slug, esc_html__( 'Archive', 'seo-key' ), esc_html__( 'Archive page', 'seo-key' ), $post_type_object->cap->edit_published_posts, 'seo-key-archive_' . $key, 'seokey_post_type_archive_content' );
	}
}

//TODO COmments
function seokey_post_type_archive_metabox(){
    $html = '';
    $links      = [
        'seokey-metas' => __( 'Search engine data', 'seo-key' ),
    ];
    $i = 0;
    foreach ( $links as $key => $type ) {
        if ( $i === 0 ) {
            $class = 'nav-tab nav-tab-active';
        } else {
            $class = 'nav-tab';
        }
        $i++;
        $html.= '<a id="' . sanitize_html_class( $key ) . '" href="#' . sanitize_html_class( $key ) . '" class="' . $class . '">' . $type . '</a>';
    }
    echo '<nav role="navigation" class="nav-tab-wrapper">' . $html . '</nav>';
    ?>
    <span id="tab-seokey-metas" class="seokey-metabox-tab seokey-metabox-tab-first">
        <p><?php esc_html_e( 'Tell Google what your content is about:', 'seo-key' ); ?></p>
        <span class="seokey-flex">
            <?php
            global $typenow;
            // Get the post type object
            $typenow_object = get_post_type_object( $typenow );
            // Get the front archive link
            $get_post_type_archive_link = esc_url( get_post_type_archive_link( $typenow ) );
            // Get previous values
            $metatitle = get_option( 'seokey-metatitle-' . $typenow );
            $metadesc  = get_option( 'seokey-metadesc-' . $typenow );
            wp_nonce_field( 'seokey-metas', '_seokeynonce', false );
            $args = ['metatitle' => $metatitle, 'metadesc' => $metadesc, 'typenow' => $typenow ];
            seokey_helper_admin_print_meta_fields_html( $args );
    
            $default_title       = seokey_meta_title_value(
                    'post_type_archive',
                    0,
                    $args = array(
                        'name'  => $typenow_object->name,
                        'label' => $typenow_object->label,
                    ),
                    true
            );
            $default_description = seokey_meta_desc_value(
                'post_type_archive',
                0,
                $args = array(
                    'name'          => $typenow_object->name,
                    'label'         => $typenow_object->label,
                    'description'   => $typenow_object->description,
                ),
                true
            );
            $title          = $metatitle ? $metatitle : $default_title;
            $description    = $metadesc ? $metadesc : $default_description;
            echo seokey_helper_admin_print_google_preview( [
                    'title'             => $title,
                    'def_title'         => $default_title,
                    'description'       => $description,
                    'def_description'   => $default_description,
                    'url'               => esc_url( $get_post_type_archive_link ),
                    'private'           => get_option( 'seokey-content_visibility-' . $typenow ) ? true : false,
            ] );
        echo '</span>';
        seokey_helper_admin_print_meta_fields_html_visibility( $args );
        submit_button( null, 'primary', 'submit', false );
    echo '</span>';
    return $html;
}

/**
 * Callback for the menu page content
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @see    seokey_admin_menus_post_type_archive()
 * @global $typenow
 * @return void the HTML DOM with forms
 */
function seokey_post_type_archive_content() {
    // Do not show anything if user is not an editor or admin
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
	    return;
	}
    global $typenow;
	// Get the post type object
	$typenow_object = get_post_type_object( $typenow );
	// Get the front archive link

    // TODO Multilingual
	$get_post_type_archive_link = esc_url( get_post_type_archive_link( $typenow ) );

	// Get metas previous values
	settings_errors( 'seokey', true );
	// HTML output
	?>
    <div class="wrap" id="seokey-metabox">
        <h1 class="wp-heading-inline"><?php printf( esc_html_x( '%s: Archive Page SEO Settings', 'H1 page title', 'seo-key' ), $typenow_object->label ); ?></h1>
        <div id="edit-slug-box">
            <strong><?php esc_html_e( 'View post type archive page: ' ); ?></strong>
            <span id="sample-permalink"><a href="<?php echo esc_url( $get_post_type_archive_link ); ?>"><?php echo esc_html( $get_post_type_archive_link ); ?></a></span>
        </div>
        <div id="poststuff">
            <!--// Custom desc and title box //-->
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'meta-infos-' . $typenow ); ?>
                <input type="hidden" name="action" value="seokey-archive-meta-infos">
                <input type="hidden" name="typenow" value="<?php echo esc_attr( $typenow ); ?>">
                <div id="post-body" class="metabox-holder columns-1">
                    <?php
                        seokey_post_type_archive_metabox();
                    ?>
                </div>
            </form>
            <!--// End Custom desc and title box //-->
        </div>
    </div>
	<?php
}

add_filter( 'admin_body_class', 'seokey_admin_archives_pages_body_class' );
function seokey_admin_archives_pages_body_class( $classes ) {
	$screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the dashboard page ?
	if ( true === str_contains( $current_page, 'page_seo-key-archive' ) ) {
        $classes .= ' seokey_archives_pages';
        seokey_helper_cache_data ('seokey_metabox_post_type_archive', true );
	}
	return $classes;
}

add_action( 'admin_post_seokey-archive-meta-infos', 'seokey_post_type_archive_content_cb' );
/**
 * admin-post callback to save the meta infos from a CPT archive menu
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @see    seokey_post_type_archive_content()
 *
 * @hook   admin_post_seokey-archive-meta-infos
 * @return void on referer
 */
function seokey_post_type_archive_content_cb() {
	// If we don't have the required infos, die with a message
	if ( ! isset( $_POST['_wpnonce'], $_POST['metatitle'], $_POST['metadesc'], $_POST['typenow'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'meta-infos-' . $_POST['typenow'] ) ) {
		wp_die( __( 'An error has occurred.', 'seo-key' ) . '<br><em>' . __FUNCTION__ . '#' . __LINE__ . '</em>' );
	}
	if ( ! current_user_can( seokey_helper_user_get_capability( 'editor' ) ) ) {
	    wp_die( __( 'An error has occurred.', 'seo-key' ) . '<br><em>' . __FUNCTION__ . '#' . __LINE__ . '</em>' );
    }
	// Create a clean array to sanitize our contents
	$_CLEAN                       = [];
	$_CLEAN['typenow']            = get_post_type_object( $_POST['typenow'] ) ? sanitize_key( $_POST['typenow'] ) : false;
	$_CLEAN['metatitle']          = sanitize_text_field( $_POST['metatitle'] );
	$_CLEAN['metadesc']           = sanitize_text_field( $_POST['metadesc'] );
	$_CLEAN['content_visibility'] = sanitize_text_field( $_POST['content_visibility'] );
	// If we don't have a correct CPT, die with a message
	if ( ! $_CLEAN['typenow'] ) {
		wp_die( __( 'An error has occurred.', 'seo-key' ) . '<br><em>' . __FUNCTION__ . '#' . __LINE__ . '</em>' );
	}
	$typenow = sanitize_title($_POST['typenow']);
	// Update the meta infos
	update_option( 'seokey-metatitle-' . $typenow,          $_CLEAN['metatitle'], false);
	update_option( 'seokey-metadesc-' . $typenow,           $_CLEAN['metadesc'], false );
	update_option( 'seokey-content_visibility-' . $typenow, $_CLEAN['content_visibility'], false );
	// Add the "setting message" to get a notice in administation
	add_settings_error( 'seokey', 'updated', esc_html_x( 'SEO Settings updated.', 'update message in an admin notice', 'seo-key' ), 'updated' );
	// Manually do it because we're not using the settings API here
	set_transient( 'settings_errors', get_settings_errors(), 30 );
	// Safe redirect on referer
	wp_safe_redirect( add_query_arg( 'settings-updated', 301, wp_get_referer() ) );
	// End function
	die();
}