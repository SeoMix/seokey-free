<?php
/**
 * Ajouter des date de modificati ondes terms et des auteurs
 *
 * @since   0.0.1
 * @author  Léo Fontin
 *
 * @see seokey_plugin_init()
 * @see public-modules.php
 * @package SEOKEY
 *
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}


class Seokey_Sitemap_Lastmod {
	/**
	 * Seokey_Sitemap constructor.
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function __construct() {}
	
    /**
     * Get init lastmod for all terms
     */
    public function seokey_sitemap_set_term_lastmod() {
        // Get all terms
        $terms = get_terms( [
            'hide_empty' => false
        ] );
        if ( ! empty( $terms ) ) {
            foreach ( $terms as $term ) {
                // Get current meta lastmod
                $lastmod = get_term_meta( $term->term_id, 'seokey-sitemap-lastmod', true );
                if ( empty( $lastmod ) ) {
                    // Get last post attached to this term
                    $post = new WP_Query( [
                        'post_type'      => 'any',
                        'posts_per_page' => '1',
                        'orderby'        => 'modified',
                        'order'          => 'DESC',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => $term->taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $term->term_id
                            )
                        )
                    ] );
                    // Good to god ? =>update lastmod
                    if ( ! empty( $post->posts[0]->post_modified ) ) {
                        $modified = date( 'Y-m-d H:i:s', strtotime( $post->posts[0]->post_modified ) );
                    } else {
                        $modified = current_time('Y-m-d H:i:s');
                    }
                    update_term_meta( $term->term_id, 'seokey-sitemap-lastmod', $modified );
                }
            }
        }
    }

    /**
     *  Get init lastmod for all users
     */
    public function seokey_sitemap_set_author_lastmod() {
        // Get all users
        $users = get_users();
        if ( ! empty( $users ) ) {
            foreach ( $users as $user ) {
                // Get current meta
                $lastmod = get_user_meta( $user->ID, 'seokey-sitemap-lastmod', true );
                // Get last post from this user
                if ( empty( $lastmod ) ) {
                    $post = new WP_Query( [
                        'post_type'      => 'any',
                        'posts_per_page' => '1',
                        'orderby'        => 'modified',
                        'order'          => 'DESC',
                        'author'         => $user->ID
                    ] );
                    // Good to god ? =>update lastmod
                    if ( ! empty( $post->posts[0]->post_modified ) ) {
                        $modified = date( 'Y-m-d H:i:s', strtotime( $post->posts[0]->post_modified ) );
                        update_user_meta( $user->ID, 'seokey-sitemap-lastmod', $modified );
                    }
                }
            }
        }
    }


    /**
     * Seokey Last Mod listeners
     *
     * @since   0.0.1
     * @author  Léo Fontin
     */
    public function watch_lastmod() {
        // While editing any post : update terms lastmod if terms have changed
        add_action( 'set_object_terms',     [ $this, 'seokey_sitemap_lastmod_update_term_on_post_update'], 10, 6 );
        // While editing any post, update author lastmod
        add_action( 'save_post',            [ $this, 'seokey_sitemap_lastmod_update_author_on_post_update'], 10, 2 );
        // Update term lastmod on term creation or update
        add_action( 'saved_term',           [ $this, 'seokey_sitemap_lastmod_update_term'], 10, 3 );
        add_action( 'edited_term',          [ $this, 'seokey_sitemap_lastmod_update_term'], 10, 3 );
        // Update user lastmod on user creation or update
        add_action( 'insert_user_meta',     [ $this, 'seokey_sitemap_lastmod_update_author'], 10, 3 );
        // Update terms and users lastmod on post trash
        add_action( 'wp_trash_post',        [ $this, 'seokey_sitemap_lastmod_update_trashed_content'], 10, 1 );
    }

    /**
     * Update Terms trashed content
     *
     * @param $post_id
     */
    public function seokey_sitemap_lastmod_update_trashed_content( $post_id ) {
        $post       = get_post( $post_id );
        $taxonomies = get_object_taxonomies( $post );
        if ( ! empty( $taxonomies ) ) {
            foreach ( $taxonomies as $taxo ) {
                // Get terms related to this post
                $terms = wp_get_post_terms( $post->ID, $taxo );
                if ( ! empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        update_term_meta( $term->term_id, 'seokey-sitemap-lastmod', date( 'Y-m-d H:i:s', time() ) );
	                    $sitemaps = Seokey_Sitemap_Render::get_instance();
	                    $sitemaps->seokey_sitemap_init( 'term', $taxo );
	                    $sitemaps->seokey_sitemap_init( 'author' );
                    }
                }
            }
        }
    }

    /**
     * Update User on any modification (edit, add)
     *
     * @param $post_id
     */
    public function seokey_sitemap_lastmod_update_author( $meta, $user, $update ) {
        $meta['seokey-sitemap-lastmod'] = date( 'Y-m-d H:i:s', time() );
        return $meta;
    }

    /**
     * Update term lastmod on term creation or update
     *
     * @param $post_id
     */
    public function seokey_sitemap_lastmod_update_term( $term_id, $tt_id, $taxonomy ) {
        update_term_meta( $term_id, 'seokey-sitemap-lastmod', date( 'Y-m-d H:i:s', time() ) );
        $sitemaps = Seokey_Sitemap_Render::get_instance();
        $sitemaps->seokey_sitemap_init( 'term', $taxonomy );
    }

    /**
     * Update user lastmod on post update
     *
     * @param $post_id
     */
    public function seokey_sitemap_lastmod_update_author_on_post_update( $post_id, $post ) {
        if ( ! empty( $post ) ) {
            update_user_meta( $post->post_author, 'seokey-sitemap-lastmod', date( 'Y-m-d H:i:s', time() ) );
	        $sitemaps = Seokey_Sitemap_Render::get_instance();
	        $sitemaps->seokey_sitemap_init( 'author' );
        }
    }

    /**
     * Update terms when contents are updated (ex. a user is editing a post and he removes a category term)
     *
     * @param $post_id
     */
    public function seokey_sitemap_lastmod_update_term_on_post_update( $post_id, $terms_id_start, $c, $taxonomy, $e, $terms_id_end ) {
        // Get all public taxonomies ( then remove dummy value)
        $allowed_taxos = seokey_helper_get_option( 'cct-taxo', get_taxonomies( ['public' => true, 'show_ui' => true ] ) );
        unset($allowed_taxos[0]);
        // Check if we are updating a term from a public taxonomy
        if ( in_array( $taxonomy, $allowed_taxos ) ) {
            // Check added terms
            $added = array_diff($terms_id_start, $terms_id_end);
            foreach ($added as $term) {
                update_term_meta( $term, 'seokey-sitemap-lastmod', date( 'Y-m-d H:i:s', time() ) );
	            $sitemaps = Seokey_Sitemap_Render::get_instance();
	            $sitemaps->seokey_sitemap_init( 'term', $taxonomy );
            }
            // Check removed terms
            $removed = array_diff($terms_id_end, $terms_id_start);
            foreach ($removed as $term) {
                update_term_meta( $term, 'seokey-sitemap-lastmod', date( 'Y-m-d H:i:s', time() ) );
	            $sitemaps = Seokey_Sitemap_Render::get_instance();
	            $sitemaps->seokey_sitemap_init( 'term', $taxonomy );
            }
        }
    }
}