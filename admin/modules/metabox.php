<?php
/**
 * Load Metaboxes for meta desc and title
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
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

add_action( 'load-post.php',        'seokey_meta_boxes_public_cpts_add' );
add_action( 'load-post-new.php',    'seokey_meta_boxes_public_cpts_add' );
/**
 * Add the correct hooks for the posts forms to add meta boxes.
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   load-post.php
 * @hook   load-post-new.php
 * @global $typenow
 * @return void
 **/
function seokey_meta_boxes_public_cpts_add() {
	global $typenow;
	// Only add the metabox if the post type is public (settings page).
	$cpts = array_flip( seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => true ] ) ) );
	if ( isset( $cpts[ $typenow ] ) ) {
		// Tell SEOKEY we will need a meta metabox
		seokey_helper_cache_data( 'SEOKEY_METABOX', true);
		// Add metabox
		add_action( 'add_meta_boxes_' . $typenow, 'seokey_meta_boxes_add', 30 );
	}
}

add_action( 'admin_init', 'seokey_admin_meta_boxes_public_taxos_add' );
/**
 * Add the correct hooks for the terms forms, when creating and editing. Goal is to add meta boxes
 * ps: admin_init because load-term.php won't trigger create_$tax because of ajax calls.
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @hook   admin_init
 * @see    seokey_admin_meta_boxes_public_taxos_add()
 * @return void
 */
function seokey_admin_meta_boxes_public_taxos_add() {
	if( defined( 'DOING_AJAX' ) ) {
		return ;
	}
	// Only add the metabox if the taxo is public (by our settings).
	$taxos = array_flip( seokey_helper_get_option( 'cct-taxo', get_taxonomies( ['public' => true, 'show_ui' => true ] ) ) );
	foreach ( $taxos as $tax => $dummy ) {
        // Add metaboxes
        add_action( $tax . '_edit_form', 'seokey_meta_boxes_add', 30 );
		add_action( $tax . '_add_form', 'seokey_meta_boxes_add', 30 );
		add_action( 'edit_' . $tax, 'seokey_meta_boxes_save_all', 30, 2 );
		add_action( 'create_' . $tax, 'seokey_meta_boxes_save_all', 30, 2 );
		// User is editing his own profil
		add_action( 'personal_options_update', 'seokey_meta_boxes_save_all', 30, 2 );
	}
}

/**
 * Add a metabox in the post edit page for all public CPTs
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @hook   add_meta_boxes
 * @see    seokey_admin_meta_boxes_public_taxos_add()
 * @param string $term Values can be Typenow or Tax
 */
function seokey_meta_boxes_add( $term ) {
	// Where are we ?
	global $current_screen, $taxnow;
	// Taxonomies
	if ( 'edit-' . $taxnow === $current_screen->id ) {

		add_meta_box(
			'seokey-metatags',
			'<i class="dashicons dashicons-seokey-lock"></i> ' .__( 'SEOKEY - Improve your visibility', 'seo-key' ),
			'seokey_meta_boxes_public_taxos_callback',
			$taxnow,
			'normal',
			'high',
			$term
		);
        add_filter( 'esc_html', 'seokey_meta_boxes_add_filter_taxo_accordion_sections', 10, 2 );
        do_accordion_sections( $taxnow, 'normal', 'high' );
        remove_filter('esc_html', 'seokey_meta_boxes_add_filter_taxo_accordion_sections' );
	}
	// Post
    else {
	    global $typenow;
		add_meta_box(
			'seokey-metabox',
			'<i class="dashicons dashicons-seokey-lock"></i>' . __( 'SEOKEY - Improve your visibility', 'seo-key' ),
			'seokey_meta_boxes_public_cpts_callback',
			$typenow,
			'normal',
			'high'
		);
	}

}

/**
 * Filter do_accordion_sections to allow our dashicon
 *
 * @author Daniel Roch
 * @since  0.0.1
 *
 * @see    seokey_meta_boxes_add()
 */
function seokey_meta_boxes_add_filter_taxo_accordion_sections( $safe_text, $text ){
    if ( $text === '<i class="dashicons dashicons-seokey-lock"></i> ' .__( 'SEOKEY - Improve your visibility', 'seo-key' ) ) {
        $safe_text = '<i class="dashicons dashicons-seokey-lock"></i> ' . esc_html__( 'SEOKEY - Improve your visibility', 'seo-key' );
    }
    return $safe_text;
}

/**
 * Prints 2 fields for metatitle and metadesc + a HTML google preview bloc for taxonomies
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @see    seokey_all_public_cpts_meta_boxes()
 * @param  void $dummy Forget me.
 * @param  array $box Contains the add_meta_box declaration with box with arguments we need
 * @global $taxnow
 **/
function seokey_meta_boxes_public_taxos_callback( $dummy, $box ) {
    echo '<span id="tab-seokey-metas" class="seokey-metabox-tab seokey-metabox-tab-first">';
        echo '<p class="description">'.esc_html_e( 'Tell Google what your content is about:', 'seo-key' ).'</p>';
        echo '<span class="seokey-flex">';
            // Define our variables
            global $taxnow;
            $is_term = false;
            if ( is_object( $box['args'] ) ) {
                // Edit a term
                $term_obj = $box['args'];
                $is_term  = true;
            } elseif ( taxonomy_exists( $box['args'] ) ) {
                // Create a term
                $term_obj = get_taxonomy( $box['args'] );
                $is_term  = false;
            }
            // Get previous values if possible
            $metatitle = '';
            $metadesc  = '';
            if ( $is_term ) {
                $termid = (int) $term_obj->term_id;
                $metatitle = get_term_meta( $termid, 'seokey-metatitle', true );
                $metadesc  = get_term_meta( $termid, 'seokey-metadesc', true );
            }
            wp_nonce_field( 'seokey-metas', '_seokeynonce', false );
            $args = $args2 = ['metatitle' => $metatitle, 'metadesc' => $metadesc, 'term' => $term_obj ];
            seokey_helper_admin_print_meta_fields_html( $args );
            if ( ! $is_term ) {
                global $wp_rewrite;
                $default_title          = esc_html__( '(Default Title)', 'seo-key' );
                $default_description    = esc_html__( '(Default Description)', 'seo-key' );
                $tax_rewrite_rules      = $wp_rewrite->get_extra_permastruct( $taxnow );
                $url                    = home_url( $tax_rewrite_rules );
                $private = false;
            } else {
                $default_title          = seokey_meta_title_value( 'taxonomy', $termid, $args = array(	'name' => $term_obj->name ), true );
                $default_description    = seokey_meta_desc_value( 'taxonomy', $termid, $args = array( 'description' => $term_obj->description ), true );
                $url                    = get_term_link( $term_obj, $taxnow );
                $private                = get_term_meta( $termid, 'seokey-content_visibility' ) ? true : false;
            }
            $title          = $metatitle ? $metatitle : $default_title;
            $description    = $metadesc ? $metadesc : $default_description;
            echo seokey_helper_admin_print_google_preview(
                [
                    'title'           => $title,
                    'def_title'       => $default_title,
                    'description'     => $description,
                    'def_description' => $default_description,
                    'url'             => esc_url( $url ),
                    'private'         => $private,
                ]
            );
        echo '</span>';
    seokey_helper_admin_print_meta_fields_html_visibility( $args2 );
	echo '</span>';
}

/**
 * Prints 2 fields for metatitle and metadesc + a HTML google preview bloc for $post
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @see    seokey_all_public_cpts_meta_boxes()
 * @todo factorisation !!!!
 **/
function seokey_meta_boxes_public_cpts_callback() {
    echo '<div id="seokey-stats">';
        echo '<p class="description has-explanation">';
            esc_html_e( 'SEO performance for this content', 'seo-key' );
            echo seokey_helper_help_messages('metabox-data-source-sc');
        echo '</p>';
        $search = new Seokey_SearchConsole();
        $content = $search->seokey_gsc_metabox_render('post', get_the_ID() );
        echo $content;
        ?>
        <p class="description" id="seokey-seo-goal">
            <?php esc_html_e( 'Your SEO goal', 'seo-key' ); ?>
        </p>
        <p>
            <?php esc_html_e( 'Tell SEOKEY what is the main topic of this content to receive better advice.', 'seo-key' ); ?>
        </p>
        <?php
        $current_keyword = get_post_meta( get_the_ID(), "seokey-main-keyword", true );
        $button_text = ( empty( $current_keyword ) ) ? __( 'Target this keyword or phrase', 'seo-key' ) : __( 'Update keyword or phrase', 'seo-key' );
        ?>
        <input id="seokey_audit_content_main_keyword" type="text" name="seokey_audit_content_main_keyword" value="<?php echo esc_attr( $current_keyword  ); ?>"/>
        <button id ="content_main_keyword_submit" type="submit" class="button button-primary"><?php echo esc_html( $button_text ); ?></button>
        <span id="seokey_audit_content_main_keyword_message" style="display:none"></span>
        <p class="description" id="seokey-seo-optimize">
            <?php esc_html_e( 'Optimize and audit your content', 'seo-key' ); ?>
        </p>
    </div>
    <?php
    $html = '';
    $links      = [
        'seokey-metas' => __( 'Search engine data', 'seo-key' ),
        'seokey-audit' => __( 'Content Audit', 'seo-key' ),
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
    ?>
    <?php
    echo '<nav role="navigation" class="nav-tab-wrapper">' . $html . '</nav>';
    ?>
    <span id="tab-seokey-metas" class="seokey-metabox-tab seokey-metabox-tab-first">
        <p><?php esc_html_e( 'Tell Google what your content is about:', 'seo-key' ); ?></p>
        <span class="seokey-flex">
            <?php
            // Get previous values
            $ID = get_the_ID();
            $metatitle = get_post_meta( $ID, 'seokey-metatitle', true );
            $metadesc  = get_post_meta( $ID, 'seokey-metadesc', true );
            wp_nonce_field( 'seokey-metas', '_seokeynonce', false );
            $args = ['metatitle' => $metatitle, 'metadesc' => $metadesc ];
            seokey_helper_admin_print_meta_fields_html( $args );
            $default_title          = seokey_meta_title_value( 'singular', $ID, $args = array(), true );
            $title                  = $metatitle ? $metatitle : $default_title;
            $default_description    = seokey_meta_desc_value( 'singular', $ID, $args = array(), true );
            $description            = $metadesc ? $metadesc : $default_description;
            $private                = get_post_meta( $ID, 'seokey-content_visibility' ) ? true : false;
            echo seokey_helper_admin_print_google_preview(
                [
                    'title'           => $title,
                    'def_title'       => $default_title,
                    'description'     => $description,
                    'def_description' => $default_description,
                    'url'             => get_permalink(),
                    'private'         => $private,
                ]
            );
        echo '</span>';
        seokey_helper_admin_print_meta_fields_html_visibility( $args );
    echo '</span>';
    echo '<span id="tab-seokey-audit" class="seokey-metabox-tab">';
        $audit = new Seokey_Audit_Content();
        echo $audit->seokey_audit_content_metabox_cpt_callback();
    echo '</span>';
}

add_action( 'show_user_profile', 'seokey_meta_boxes_profile_add' );
add_action( 'edit_user_profile', 'seokey_meta_boxes_profile_add' );
/**
 * Add the correct hooks for the profile forms to add meta boxes.
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @hook   show_user_profile
 * @hook   edit_user_profile
 * @return void
 **/
function seokey_meta_boxes_profile_add() {
    // Author page public ?
	$settings = seokey_helper_get_option( 'cct-pages', '' );
	$settings = is_array( $settings ) ? array_flip( $settings ) : [];
	$allowed  = isset( $settings['author'] ) || ! isset( $settings['i_am_a_dummy_value'] );
    $allowed = ( empty ( $settings ) ) ? true : $allowed;
	if ( $allowed ) { // Author pages are allowed
        // Add metabox
        add_meta_box(
            'seokey-metatags',
            __('SEOKEY - Improve your visibility', 'seo-key'),
            'seokey_meta_boxes_profile_callback',
            'profile',
            'normal',
            'high'
        );
        do_accordion_sections('profile', 'normal', 'default');
    }
}

/**
 * Add a metabox in profile pages
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @see    seokey_meta_boxes_profile_add()
 *
 **/
function seokey_meta_boxes_profile_callback() {
    ?>
    <p class="description"><?php esc_html_e( 'Tell Google what your content is about:', 'seo-key' ); ?></p>
    <span class="seokey-flex">
        <?php
        global $user_id;
        // Get previous values if possible
        $metatitle = get_user_meta( $user_id, 'seokey-metatitle', true );
        $metadesc  = get_user_meta( $user_id, 'seokey-metadesc', true );
        wp_nonce_field( 'seokey-metas', '_seokeynonce', false );
        $args = ['metatitle' => $metatitle, 'metadesc' => $metadesc ];
        seokey_helper_admin_print_meta_fields_html( $args );
        $default_title          = seokey_meta_title_value( 'user', $user_id, $array = array( 'name' => get_the_author_meta( 'display_name', $user_id ) ), true );
        $title                  = $metatitle ? $metatitle : $default_title;
        // try to get the auhor BIO
        $default_description    = seokey_helper_meta_length( get_the_author_meta( 'description', $user_id ), METADESC_COUNTER_MAX );
        // no author bio, fallback to a generic meta desc
        $default_description    = $default_description ? $default_description : sprintf(
            /* translators: 1:User Name 2:Name of the website */
                __( '%s, author on %s.', 'seo-key'), get_the_author_meta( 'display_name', $user_id ), get_bloginfo( 'name' )
            );
        $description            = $metadesc ? $metadesc : $default_description;
        $url                    = get_author_posts_url( $user_id );
        $private                = get_the_author_meta( 'seokey-content_visibility', $user_id ) ? true : false;
        echo seokey_helper_admin_print_google_preview(
            [
                'title'             => $title,
                'def_title'         => $default_title,
                'description'       => $description,
                'def_description'   => $default_description,
                'url'               => $url,
                'private'           => $private,
            ]
        );
    echo '</span>';
    seokey_helper_admin_print_meta_fields_html_visibility( $args );


	seokey_users_profile_form( $user_id );
}

add_action( 'save_post', 'seokey_meta_boxes_save_all', 10, 2 );
add_action( 'profile_update', 'seokey_meta_boxes_save_all', 10, 2 );
/**
 * Save  metatitle, metadesc and content visibility on post save
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @hook   save_post
 * @hook   edit_{$taxonomy}
 * @hook   create_{$taxonomy}
 * @param int $object_id Post ID or term ID
 * @param null $post_or_tt_id
 */
function seokey_meta_boxes_save_all( $object_id, $post_or_tt_id = null ) {
	// Security
	if ( ! isset( $_POST['_seokeynonce'] ) || ! wp_verify_nonce( $_POST['_seokeynonce'], 'seokey-metas' ) ) {
		return;
	}
	if ( ! current_user_can( seokey_helper_user_get_capability ( 'contributor' ) ) ) {
		return;
	}
	if ( is_int( $post_or_tt_id ) ) {
	    // term
		if ( ! current_user_can( seokey_helper_user_get_capability ( 'editor' ) ) ) {
			return;
		}
		$update_object_meta = 'update_term_meta';
		$delete_object_meta = 'delete_term_meta';
	} elseif ( is_null( $post_or_tt_id ) ||  ( ! empty( $post_or_tt_id->roles ) ) ) {
		// author profile
		if ( ! current_user_can( seokey_helper_user_get_capability ( 'editor' ) ) ) {
            // Die if author or contributor tries to change someone else profil
			if ( ! is_null( $post_or_tt_id ) ) {
                return;
            }
		}
		$update_object_meta = 'update_user_meta';
		$delete_object_meta = 'delete_user_meta';
	} else {
	    // post
		if ( ! current_user_can( seokey_helper_user_get_capability ( 'editor' ) ) ) {
			// Die if author or contributor tries to change someone else content
			$content_author = get_post_field( 'post_author', $post_or_tt_id );
			$current_user   = get_current_user_id();
			if ( (int) $content_author !== (int) $current_user ) {
				return;
			}
		}
		$update_object_meta = 'update_post_meta';
		$delete_object_meta = 'delete_post_meta';
	}
	// Always check if data isset: never delete, just update
	if ( isset( $_POST['metatitle'] ) ) {
		$update_object_meta( $object_id, 'seokey-metatitle', sanitize_text_field( $_POST['metatitle'] ) );
	}
	if ( isset( $_POST['metadesc'] ) ) {
		$update_object_meta( $object_id, 'seokey-metadesc', sanitize_text_field( $_POST['metadesc'] ) );
	}
	// Always check the witness, then we will knwo if we delete this value or if we need an update
	if ( isset( $_POST['content_visibility_witness'] ) ) {
		if ( isset( $_POST['content_visibility'] ) ) {
			$update_object_meta( $object_id, 'seokey-content_visibility', 1 );
		} else {
			$delete_object_meta( $object_id, 'seokey-content_visibility' );
		}
	}
}

add_action( 'created_term', 'seokey_meta_boxes_created_term_save_meta', 10, 3 );
/**
 * Save custom data on term creation
 *
 * @author Gauvain Van Ghele, Daniel Roch
 * @since  1.5.2
 *
 * @hook   created_term
 * @param int $term_id Term ID
 * @param int $tt_id Term ID + Taxonomy
 * @param string $taxonomy Taxonmy name
 * @return void
 */
function seokey_meta_boxes_created_term_save_meta(  $term_id, $tt_id, $taxonomy ) {
    // Meta Title
    if( isset( $_POST['metatitle'] ) ){
        add_term_meta( $term_id, 'seokey-metatitle', sanitize_text_field( $_POST['metatitle'] ) );
    }
    // Metdesc
    if( isset( $_POST['metadesc'] ) ) {
        add_term_meta( $term_id, 'seokey-metadesc', sanitize_textarea_field( $_POST['metadesc'] ) );
    }
    // Noindex
	if( isset( $_POST['content_visibility'] ) ) {
		add_term_meta( $term_id, 'seokey-content_visibility', (int) $_POST['content_visibility'] );
	}
}