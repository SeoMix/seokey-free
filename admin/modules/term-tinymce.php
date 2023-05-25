<?php
/**
 * Add a TinyMCE editor to all terms
 *
 * @Loaded on plugins_loaded + is_admin() + capability contributor
 * @see seokey_plugin_init()
 * @note See "Visual Term Description Editor" plugin : https://fr.wordpress.org/plugins/visual-term-description-editor/
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

add_action( 'init', 'seokey_admin_term_tinymce_init' );
/**
 * Init TinyMCE editor for all terms
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_term_tinymce_init() {
    // TODO FIX : mauvais hook + séparer parties admin/font + uniquement pour les éditeurs ???
    // Only for publish_posts capability user
    if ( current_user_can( seokey_helper_user_get_capability( 'editor') ) ) {
        // Allow clean HTML
        remove_filter( 'pre_term_description',   'wp_filter_kses' );
        remove_filter( 'term_description',       'wp_kses_data' );
        if ( ! current_user_can( 'unfiltered_html' ) ) {
            add_filter( 'pre_term_description',  'wp_kses_post' );
            add_filter( 'term_description',      'wp_kses_post' );
        }
        // Oembed compatibility
        if ( isset( $GLOBALS['wp_embed'] ) ) {
            add_filter( 'term_description', array($GLOBALS['wp_embed'], 'run_shortcode' ), 8);
            add_filter( 'term_description', array($GLOBALS['wp_embed'], 'autoembed' ), 8);
        }
        // This editor needs to work as a classic editor
        add_filter('term_description', 'wptexturize');
        add_filter('term_description', 'convert_smilies');
        add_filter('term_description', 'convert_chars');
        add_filter('term_description', 'wpautop');
        // front office only filters
        if ( !is_admin() ) {
            add_filter('term_description', 'shortcode_unautop');
            add_filter('term_description', 'do_shortcode', 11);
            // No need to continue
            return;
        }
        // add editor for each taxonomy
        $taxonomies = get_taxonomies('', 'names');
        foreach ( $taxonomies as $taxonomy ) {
            add_action( $taxonomy . '_edit_form_fields', 'seokey_admin_term_tinymce_render', 1, 2 );
            add_action( $taxonomy . '_add_form_fields',  'seokey_admin_term_tinymce_render', 1, 2 );
        }
    }
}





add_action( 'admin_enqueue_scripts', 'seokey_admin_term_tinymce_js' );
/**
 * Add usefull JS and CSS for term Tinymce editor
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_term_tinymce_js() {
    // Only when adding or editing a term
    $current_screen = seokey_helper_get_current_screen();
    if ( $current_screen->base === 'edit-tags' || $current_screen->base === 'term' ) {
	    seokey_helper_cache_data( 'SEOKEY_METABOX', true);
        wp_enqueue_script( 'seokey-term-tinymce-js', SEOKEY_URL_ASSETS . 'js/seokey-term-tinymce.js', array( 'jquery' ), SEOKEY_VERSION );
        wp_enqueue_style( 'seokey-term-tinymce-css', SEOKEY_URL_ASSETS . 'css/seokey-term-tinymce.css', '', SEOKEY_VERSION );
    }
}

/**
 * Render term Tinymce editor
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_term_tinymce_render( $tag, $taxonomy = '' ) {
    // Editor data
    $tag_description = ( is_object($tag) ) ? htmlspecialchars_decode( $tag->description ): '';
    $settings = array(
        'textarea_name' => 'description',
        'textarea_rows' => ( is_object($tag) ) ? 10 : 7,
        'editor_class'  => 'i18n-multilingual',
    );
    // rendering html
    $current_action = current_action();
    if( str_contains( $current_action, 'edit_form_fields' ) ): ?>
        <tr class="form-field term-description-wrap term-description-wrap-show sk-term-description-tinymce">
            <th scope="row">
                <label for="description"><?php _e( 'Description' ); ?></label>
            </th>
            <td>
                <?php wp_editor( $tag_description, 'html-tag-description', $settings ); ?>
                <p class="description"><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
            </td>
        </tr>
    <?php endif; if( str_contains( $current_action, 'add_form_fields' ) ): ?>
        <div class="form-field term-description-wrap term-description-wrap-show sk-term-description-tinymce">
            <label for="tag-description"><?php esc_html_e( 'Description' ); ?></label>
            <?php wp_editor( '', 'html-tag-description', $settings ); ?>
            <p><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
        </div>
    <?php endif;

}

add_action( 'current_screen', 'seokey_admin_term_tinymce_init_admin', SEOKEY_PHP_INT_MAX );
/**
 * Add usefull filters when viewing terms listing in admin pages
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_term_tinymce_init_admin() {
    // When viewing term list
    global $current_screen;
    if ( $current_screen->base === 'edit-tags') {
        add_filter('term_description', 'shortcode_unautop');
        add_filter('term_description', 'do_shortcode', 11);
    }
}
