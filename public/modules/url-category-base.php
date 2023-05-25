<?php
/**
 * Remove /category/ from URL
 *
 * @Loaded on plugins_loaded
 * @see seokey_plugin_init()
 * @see public-modules.php
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

add_action( 'admin_init', 'seokey_permalinks_category_base_flush_init', 1000 );
/**
 * Check option to trigger a flush on next init, and create correct sitemap
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @hook updated_option
 */
function seokey_permalinks_category_base_flush_init() {
    // TODO basculer en CRON pour de meilleures performances
	if ( get_option( 'seokey_option_flush_rewrite' ) ) {
	    // Flushing rewrites rules
		add_action( 'shutdown', 'flush_rewrite_rules' );
		delete_option( 'seokey_option_flush_rewrite' );
		flush_rewrite_rules();
		// Let's create a clean category sitemap
        if ( class_exists( 'Seokey_Sitemap_Render' ) ) {
            $sitemaps = Seokey_Sitemap_Render::get_instance();
            $sitemaps->seokey_sitemap_init( 'term', 'category' );
            $sitemaps->seokey_sitemap_init( 'post', 'post' );
        }
	}
}

/**
 * Save an option that triggers a flush on the next init. when specific option is updated
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @hook updated_option
 */
add_action( 'added_option', 'seokey_settings_api_flush_rewrite_added_option', 10, 2 );
function seokey_settings_api_flush_rewrite_added_option( $option_name, $value ){
	if ( $option_name === "seokey-field-metas-category_base" ) {
		update_option( 'seokey_option_flush_rewrite', 1, true );
	}
}

/**
 * Save an option that triggers a flush on the next init. when specific option is added
 *
 * @since 0.0.1
 * @author Daniel Roch
 *
 * @hook updated_option
 */
add_action( 'updated_option', 'seokey_settings_api_flush_rewrite_updated_option', 10, 3 );
function seokey_settings_api_flush_rewrite_updated_option( $option_name, $old_value, $value ){
	if ( $option_name === "seokey-field-metas-category_base" ) {
		update_option( 'seokey_option_flush_rewrite', 1, true );
	}
}

// Only if option is activated
if ( seokey_helper_get_option( 'metas-category_base') ) {
	/**
	 * Flush rewrites rules each tim a category is created, updated or deleted
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook created_category
	 * @hook delete_category
	 * @hook edited_category
	 */
	add_action( 'created_category', 'flush_rewrite_rules' );
	add_action( 'deleted_category', 'flush_rewrite_rules' );
	add_action( 'edited_category',  'flush_rewrite_rules' );

	add_filter( 'category_rewrite_rules', 'seokey_permalinks_category_base_rewrite_rules' );
	/**
	 * New category rewrites rules
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook category_rewrite_rules
	 * @credits : WordPress VIP
	 * @param array $rules Current Rewrites Rules
	 * @return array New Rewrites Rules
	 */
	function seokey_permalinks_category_base_rewrite_rules( $rules ) {
		// WPML compatibility
		if ( class_exists( 'Sitepress' ) ) {
            global $sitepress;
            remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            add_filter('terms_clauses', array($sitepress, 'terms_clauses'), 10, 4);
        }
		// Get categories
		$categories = get_categories( array( 'hide_empty' => false ) );
		if ( is_array( $categories ) && ! empty( $categories ) ) {
			// Redefined slugs
			$slugs = array();
			foreach ( $categories as $category ) {
				if ( is_object( $category ) && ! is_wp_error( $category ) ) {
					// Get slug for parent category
					if ( 0 == $category->category_parent ) {
						// Get slug
						$newslug = $category->slug;
						$rules['(' . $newslug . ')(/page/(\d+))?/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
					} // Get slug for child categories
					else {
						$newslug = trim( get_category_parents( $category->term_id, false, '/', true ), '/' );
						$rules['(' . $newslug . ')(/page/(\d+))?/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
					}
				}
			}
		}
		// Return rules
		return $rules;
	}

	add_filter( 'term_link', 'seokey_permalinks_category_base_term_link', SEOKEY_PHP_INT_MAX, 3 );
	/**
	 * Change term links for categories
	 *
	 * @param string $termlink Current term link
	 * @param object $term Current term object
	 * @param string $taxonomy Current taxonomy
	 *
	 * @author  Daniel Roch
	 *
	 * @hook term_link
	 * @since   0.0.1
	 */
	function seokey_permalinks_category_base_term_link( $termlink, $term, $taxonomy ) {
		// Only for categories
		if ( $taxonomy == 'category' ) {
			// Get current configuration
			$category_base = get_option( 'category_base' );
			if ( '' == $category_base ) {
				$category_base = 'category';
			}
			// TODO later: move these function into third-party folder
			// WPML compatibility
            if ( class_exists( 'Sitepress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
                $args = array('element_id' => $term->term_id, 'element_type' => 'category' );
                $taxonomy_language_code = apply_filters( 'wpml_element_language_code', null, $args );
                $category_base = apply_filters('wpml_translate_single_string', 'category', 'WordPress', 'URL category tax slug', $taxonomy_language_code);
            }
            // Polylang compatibility
            if ( function_exists( 'pll_translate_string' ) ) {
                $category_base = pll_translate_string( $category_base, pll_get_term_language($term->term_id) );
            }
			// Replace category base
			$termlink = preg_replace( '`' . preg_quote( trailingslashit( $category_base ), '`' ) . '`u', '', $termlink, 1 );
		}
        // return link
		return $termlink;
	}

	add_action( 'template_redirect', 'seokey_permalinks_category_base_redirect', 4 );
	/**
	 * Redirect old categories URL to new ones
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @hook template_redirect
	 */
	function seokey_permalinks_category_base_redirect() {
		// Do we have category data ?
		global $wp_query;
		$category_or_not = ( isset( $wp_query->query['category_name'] ) ) ? $wp_query->query['category_name'] : null;
		// No category data, die here (seokey_permalinks_default_category_base_redirect will handle some of the issues)
		if ( is_null( $category_or_not ) ) {
			return;
		}
		// It's a category : lets' find out if we are using correct URL (without base slug)
		$category_base_option   = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		$current_url            = seokey_helper_url_get_current();
		$category_base_url      = trailingslashit( home_url( $category_base_option) );
		// Bad URL, we may need a 301
		if ( str_starts_with( $current_url, $category_base_url ) ) {
			// We found it : redirect !
			wp_redirect( get_category_link( get_category_by_slug( basename( $category_or_not ) ) ), 301 );
			exit();
		}
	}
}

add_action( 'template_redirect', 'seokey_permalinks_default_category_base_redirect', 5 );
/**
 * Redirect old /category/ slugs if user has selected a new base slug
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @hook template_redirect
 */
function seokey_permalinks_default_category_base_redirect() {
    // Compare current URL & the URL we want
    seokey_permalinks_redirect_category_if_wrong_url();
	// Only on error pages
	if ( is_404() ) {
		$category_base = get_option( 'category_base' );
		// User has changed the category base slug for categories
		if ( '' !== $category_base ) {
			// Get current URL and default categories base URL
			$current_url = seokey_helper_url_get_current( false, false );
			if ( str_starts_with( $current_url, ( home_url( 'category/' ) ) ) ) {
				// Let's find the correct category
				$current_cat_slug = basename( $current_url );
				$category = get_category_by_slug( $current_cat_slug );
				// No category, we won't do anything
				if ( false === $category ) {
					return;
				}
				wp_redirect( get_category_link( $category ), 301 );
				exit();
			}
		}
	}
}

/**
 * Compare current URL & URL we want ( without / category )
 * Redirect if not the same
 *
 * @author  Gauvain Van Ghele
 * @since   1.6.0
 *
 */
function seokey_permalinks_redirect_category_if_wrong_url(){
	// When no 404 is triggered but this not the rigth category URL
    if ( is_category() ) {
        $category_link  = get_category_link( get_queried_object()->term_id );
        $category_url   = parse_url( $category_link );
        $current_url    = parse_url( seokey_helper_url_get_current( false, false ) );
        $path1          = explode('/', trim($category_url['path'], '/'));
        $path2          = explode('/', trim($current_url['path'], '/'));
        if ( $path1[0] !== $path2[0] ) {
            // If URl does not match, redirect
            wp_redirect( $category_link, 301 );
            die;
        }
    }
}