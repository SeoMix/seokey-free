<?php
/**
 * Load SEOKEY metabox helper functions
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

/**
 * Print a google preview bloc
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @param  (array) $args
 * @return string HTML Google preview
**/
function seokey_helper_admin_print_google_preview( $args ) {
    
    $args = wp_parse_args( $args,
        [
            'title'                 => 'MISSING TITLE',
            'def_title'             => 'MISSING DEFAULT TITLE',
            'url'                   => 'MISSING URL',
            'description'           => 'MISSING DESCRIPTION',
            'def_description'       => 'MISSING DEFAULT DESCRIPTION',
            'private'               => 'MISSING NOINDEX DATA',
        ]
    );
    $classes = "";
    if ( true === $args["private"] ) {
        $classes = "seokey-googlepreview-private";
    }
	$html = '<span id="seokey-google-preview" class="'. $classes . ' ">';
	    $html .= '<p class="description">' . esc_html__( 'Google Preview', 'seo-key' ) . '</p>';
        $html .= '<div id="seokey-googlepreview-wrapper">
                    <span id="seokey-googlepreview">
                        <div id="seokey-googlepreview-url" data-original="' .  esc_attr( $args["url"] ) . '">
                            ' .  esc_url( $args["url"] ) . '
                        </div>
                        <div id="seokey-googlepreview-title" data-original="' . esc_attr( $args["def_title"] ) . '">
                            ' .  esc_html( $args["title"] ) . '
                        </div>
                        <div id="seokey-googlepreview-desc" data-original="' .  esc_attr( $args["def_description"] ) . '">
                            ' .  wp_kses_post( $args["description"] ) . '
                        </div>
                    </span>
                </div>';
	$html .= '</span>';
    return $html;
}

/**
 * Will print 2 input fields for metatitle and metadescription
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @param  (array) $args Contains 2 keys "metatitle" and "metadesc" to prevent this function to get it itself
 **/
function seokey_helper_admin_print_meta_fields_html( $args ) {
	?>
    <span id="meta-tags-inputs">
        <div>
            <label class="seokey-label has-explanation" for="metatitle">
                <?php esc_html_e( 'Title', 'seo-key' ); ?>
                <?php echo seokey_helper_help_messages( 'metabox-data-metatitle' ); ?>
                <span id="seokey-metatitle-counter"></span>
            </label>
            <input autocomplete="off" id="metatitle" name="metatitle" title="<?php printf( esc_html__( '%d to %d chars', 'seo-key' ), METATITLE_COUNTER_MIN, METATITLE_COUNTER_MAX ); ?>" class="large-text" value="<?php echo esc_attr( $args['metatitle'] ); ?>">
        </div>
        <div>
            <label class="seokey-label has-explanation" for="meta-tags-inputs-textarea">
                <?php esc_html_e( 'Description', 'seo-key' ); ?>
                <?php echo seokey_helper_help_messages( 'metabox-data-metadesc' );?>
                <span id="seokey-metadesc-counter"></span>
            </label>
            <textarea autocomplete="off" title="<?php printf( esc_html__( '%d to %d chars', 'seo-key' ), METADESC_COUNTER_MIN, METADESC_COUNTER_MAX ); ?>" name="metadesc" id="meta-tags-inputs-textarea"><?php echo esc_textarea( $args['metadesc'] ); ?></textarea>
        </div>
    </span>
    <?php
}

// TODO Comments
function seokey_helper_admin_print_meta_fields_html_visibility( $args ) {
	// Get indexation data
	$site_checked = (bool) get_option( 'blog_public' );
	// Get current URL data
	global $typenow, $taxnow, $user_id, $pagenow;
	// Default value : all can be indexed
	$global_checked = false;
	// User has defined it's content
	if ( ! empty( seokey_helper_get_option( 'cct-cpt', [] ) ) )    {
        // Get global visibility data
        if ( ! empty( $taxnow ) ) {
            // Editing a term
	        $global_checked = seokey_helper_is_global_checked( 'taxonomies', $taxnow );
        } elseif ( ! empty( $typenow ) && post_type_exists( $typenow ) ) {
            // Editing a post
	        $global_checked = seokey_helper_is_global_checked( 'posts', $typenow );
        } elseif ( $pagenow === "user-edit.php" || $pagenow === "profile.php" ) {
            // Editing a user
            $page = seokey_helper_get_option( 'cct-pages', [] );
            if ( ! empty( $page ) && ! in_array( 'author', $page ) ) {
                $global_checked = true;
            }
        }
    }
	// Content type can be indexed, let's check this one
	if ( true !== $global_checked ) {
        // Get individual content indexation status
        $local_checked = false;
        if ( isset( $args['term'] ) && ! $args['term'] instanceof WP_Taxonomy ) {
            // Editing a term
            $local_checked = (bool) get_term_meta( $args['term']->term_id, 'seokey-content_visibility', true );
        } elseif ( ! empty( $typenow ) && post_type_exists( $typenow ) ) {
            $post_id = get_the_ID();
            if ( $post_id !== false ) {
                // Editing a post
                $local_checked = (bool) get_post_meta( get_the_ID(), 'seokey-content_visibility', true );
            } else {
                // Editing a post type archive
                $local_checked = (bool) get_option( 'seokey-content_visibility-' . $typenow );
            }
        } elseif ( ! empty( $user_id ) ) {
            // Editing a user
            $local_checked = get_user_meta( $user_id, 'seokey-content_visibility', true );
        }
    }
    ?>
    <div class="seokey-label has-explanation">
        <?php esc_html_e( 'Search engine visibility', 'seo-key' ); ?>
        <?php echo seokey_helper_help_messages( 'metabox-data-noindex' );?>
    </div>
    <label>
        <?php
        if ( ! $site_checked ) { ?>
            <input type="checkbox" autocomplete="off" name="content_visibility" value="1" checked="checked" readonly="readonly" disabled="disabled">
            <?php printf( esc_html__( 'Search engines are discouraged from indexing this site (%sChange this%s)', 'seo-key' ), '<a href="' . admin_url( 'options-reading.php' ) . '">', '</a>' ); ?> <em>(noindex)</em>
            <?php
        } elseif ( $global_checked ) { ?>
            <input type="checkbox" autocomplete="off" name="content_visibility" value="1" checked="checked" readonly="readonly" disabled="disabled">
            <?php
            if ( isset( $args['term'] ) ) {
                printf( esc_html__( 'This taxonomy is hidden (%sChange this%s)', 'seo-key' ), '<a href="' . esc_url( admin_url( 'admin.php?page=seo-key-settings#contents' ) ) . '">', '</a>' ); ?> <em>(noindex)</em>
                <?php
            } else {
                printf( esc_html__( 'This post type is hidden (%sChange this%s)', 'seo-key' ), '<a href="' . esc_url( admin_url( 'admin.php?page=seo-key-settings#contents' ) ). '">', '</a>' ); ?> <em>(noindex)</em>
                <?php
            }
        } else { ?>
            <input type="checkbox" autocomplete="off" name="content_visibility" value="1" <?php checked( $local_checked ); ?>>
            <?php
            if ( true == seokey_helper_cache_data ('seokey_metabox_post_type_archive' ) ) {
                printf( esc_html__( 'Hide this %s post type archive, I don\'t want to see it on Google', 'seo-key' ), strtolower( get_post_type_object( $typenow )->labels->singular_name ) ); ?> <em>(noindex)</em>
                <?php
            } elseif ( isset( $args['term'] ) ) {
                printf( esc_html__( 'Hide this %s, I don\'t want to see it on Google', 'seo-key' ), sprintf( '%s %s', strtolower( get_taxonomy_labels( get_taxonomy( $taxnow ) )->singular_name ), esc_html__( 'archive' ) ) ); ?> <em>(noindex)</em>
                <?php
            } elseif( !empty( $typenow ) ) {
                printf( esc_html__( 'Hide this %s, I don\'t want to see it on Google', 'seo-key' ), strtolower( get_post_type_object( $typenow )->labels->singular_name ) ); ?> <em>(noindex)</em>
                <?php
            } elseif( !empty( $user_id ) ) {
                esc_html_e( 'Hide this author, I don\'t want to see it on Google', 'seo-key' ); ?> <em>(noindex)</em>
                <?php
            }
        } ?>
    </label>
    <input type="hidden" autocomplete="off" name="content_visibility_witness" value="1">
    <?php
}