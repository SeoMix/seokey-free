<?php
/**
 * Third party: Woocommerce
 *
 * @Loaded on plugins_loaded + wizard done
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

// TODO filter seokey_admin_content_watcher

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	add_action ( 'template_redirect', 'seokey_thirdparty_woocommerce_check' );
	// Check if bad taxonomies is displayed + remove default meta robots
	function seokey_thirdparty_woocommerce_check(){
		if ( is_front_page() ) {
			if ( !empty( $_GET ) ) {
				if ( !empty( $_GET['product_type'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['product_visibility'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['product_shipping_class'] ) ) {
					seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
				}
				if ( !empty( $_GET['taxonomy'] ) ) {
					if ( str_starts_with( $_GET['taxonomy'], 'pa_' ) ) {
						seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check', 1 );
					}
				}
				
			}
		}
		if ( 1 === seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check' ) ) {
			add_filter( 'wp_robots', 'seokey_meta_robot_noindex_checker_force', ( SEOKEY_PHP_INT_MAX + 10 ) );
			remove_action( 'wp_head', 'wp_robots' );
		}
	}
	
	
	add_action( 'seokey_action_head', 'seokey_thirdparty_woocommerce_head', 5 );
	// If bad taxo => no canonical tag
	function seokey_thirdparty_woocommerce_head(){
		if ( 1 === seokey_helper_cache_data( 'seokey_thirdparty_woocommerce_check' ) ) {
			remove_action( 'seokey_action_head', 'seokey_head_meta_canonical' );
		}
	}

	add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_woocommerce_sitemaps' );
	// Exclude fake taxonomies from sitemaps
	function seokey_thirdparty_woocommerce_sitemaps($excluded) {
		$excluded['taxo'][] = 'product_type';
		$excluded['taxo'][] = 'product_visibility';
		$excluded['taxo'][] = 'product_shipping_class';
		$weirdtaxos      = wc_get_attribute_taxonomies();
		foreach ( $weirdtaxos as $taxo ) {
			$name = wc_attribute_taxonomy_name( $taxo->attribute_name );
			$excluded['taxo'][] = $name;
		}
		return $excluded;
	}
	
	add_filter( 'seokey_filter_settings_add_contents_taxonomies', 'seokey_thirdparty_woocommerce_settings', 500 );
	// Exclude fake taxonomies from settings
	function seokey_thirdparty_woocommerce_settings($default){
		unset($default['product_type']);
		unset($default['product_visibility']);
		unset($default['product_shipping_class']);
		$weirdtaxos = wc_get_attribute_taxonomies();
		foreach ( $weirdtaxos as $taxo ) {
			$name = wc_attribute_taxonomy_name( $taxo->attribute_name );
			unset($default[$name]);
		}
		return $default;
	}

    add_filter( 'seokey_filter_admin_bar_post_type_archive_has_archive', 'seokey_thirdparty_woocommerce_admin_bar_post_type_archive' );
    // Add shop post type archive link in admin bar ($has_archive should have been true, but woocommerce decided to do another thing...
    function seokey_thirdparty_woocommerce_admin_bar_post_type_archive( $has_archive ) {
        // TODO multilingue
        if ( function_exists( 'wc_get_page_id' ) ) {
            $shop_page_slug = get_post_field( 'post_name', wc_get_page_id( 'shop' ) );
            if ( $shop_page_slug === $has_archive ) {
                $has_archive = true;
            }
        }
        return $has_archive;
    }
		
	add_filter('option_seokey-content_visibility-product', 'seokey_thirdparty_woocommerce_shop_noindex_synchronisation');
	/**
	 * Sync data (noindex) between shop page and product archive page
	 *
	 * @param mixed $pre_option unfiltered option value for noindex Product archive page
	 * @author  Daniel Roch
	 *
	 * @since   1.6.0
	 */
	function seokey_thirdparty_woocommerce_shop_noindex_synchronisation( $pre_option ) {
		if ( function_exists( 'wc_get_page_id' ) ) {
			$shop_page_slug = wc_get_page_id( 'shop' );
		}
		if ( !empty ( $shop_page_slug ) && $shop_page_slug > 0 ) {
			return get_post_meta( $shop_page_slug, 'seokey-content_visibility', true );
		}
		return $pre_option;
	}
	    add_filter('option_seokey-metatitle-product', 'seokey_thirdparty_woocommerce_shop_title_synchronisation');
    /**
     * Sync data (meta title) between shop page and product archive page
     *
     * @param mixed $pre_option unfiltered option value for Product archive page title
     * @author  Daniel Roch
     *
     * @since   1.6.0
     */
    function seokey_thirdparty_woocommerce_shop_title_synchronisation( $pre_option ) {
        if ( function_exists( 'wc_get_page_id' ) ) {
            $shop_page_slug = wc_get_page_id( 'shop' );
        }
        if ( !empty ( $shop_page_slug ) && $shop_page_slug > 0 ) {
            return get_post_meta( $shop_page_slug, 'seokey-metatitle', true );
        }
        return $pre_option;
    }

    add_filter('option_seokey-metadesc-product', 'seokey_thirdparty_woocommerce_shop_metadesc_synchronisation');
    /**
     * Sync data (meta desc) between shop page and product archive page
     *
     * @param mixed $pre_option unfiltered option value for Product archive page meta description
     * @author  Daniel Roch
     *
     * @since   1.6.0
     */
    function seokey_thirdparty_woocommerce_shop_metadesc_synchronisation( $pre_option ) {
        if ( function_exists( 'wc_get_page_id' ) ) {
            $shop_page_slug = wc_get_page_id( 'shop' );
        }
        if ( !empty ( $shop_page_slug ) && $shop_page_slug > 0 ) {
            return get_post_meta( $shop_page_slug, 'seokey-metadesc', true );
        }
        return $pre_option;
    }

    add_action('updated_option', 'seokey_thirdparty_woocommerce_static_synchronisation_update', 10, 3);
    /**
     * Sync meta title, noindex and meta desc for homepage: sync data on updated option
     *
     * @param string $option option to sync
     * @param mixed $old_value option value before update
     * @param mixed $value option value updated
     * @author  Daniel Roch
     *
     * @since   1.0.0
     */
    function seokey_thirdparty_woocommerce_static_synchronisation_update( $option, $old_value, $value ) {
        seokey_thirdparty_woocommerce_static_synchronisation_update_callback( $option, $value );
    }

    add_action('added_option', 'seokey_thirdparty_woocommerce_static_synchronisation_add', 10, 2);
    /**
     * Sync meta title, noinex and meta desc for homepage: sync data on added option
     *
     * @param string $option option to sync
     * @param mixed $value option value added
     * @since   1.0.0
     * @author  Daniel Roch
     *
     */
    function seokey_thirdparty_woocommerce_static_synchronisation_add( $option, $value ) {
        seokey_thirdparty_woocommerce_static_synchronisation_update_callback( $option, $value );
    }

    /**
     * Sync meta title, noindex and meta desc for homepage
     *
     * @param string $option option to sync
     * @param mixed $value option value added/updated
     * @since   1.6.0
     * @author  Daniel Roch
     *
     */
    function seokey_thirdparty_woocommerce_static_synchronisation_update_callback( $option, $value ) {
		// Only for our Product archive page options ("woocommerce shop page")
	    if ( 'seokey-metatitle-product' === $option || 'seokey-metadesc-product' === $option || 'seokey-content_visibility-product' === $option ) {
		    // Find our shop page ID
			if ( function_exists( 'wc_get_page_id' ) ) {
			    $shop_page_slug = wc_get_page_id( 'shop' );
		    }
			// Update if necessary
		    if ( ! empty ( $shop_page_slug ) && $shop_page_slug > 0 ) {
			    if ( 'seokey-metatitle-product' === $option ) {
				    update_post_meta( $shop_page_slug, 'seokey-metatitle', $value );
			    }
			    if ( 'seokey-metadesc-product' === $option ) {
				    update_post_meta( $shop_page_slug, 'seokey-metadesc', $value );
			    }
			    if ( 'seokey-content_visibility-product' === $option ) {
				    update_post_meta( $shop_page_slug, 'seokey-content_visibility', $value );
			    }
		    }
	    }
    }
	
	add_filter ( 'seokey_filter_helper_audit_content_data', 'seokey_thirdparty_woocommerce_audit_content', 10, 2 );
	/**
	 * Add Woocommerce values to content audit
	 *
	 * @param string $content content of the post
	 * @param mixed $post post values
	 * @since   1.6.0
	 * @author  Daniel Roch
	 *
	 */
	function seokey_thirdparty_woocommerce_audit_content( $content, $post ){
		if ( 'product' === $post->post_type ) {
			// Add short description to content
			$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
			$content           = ( ! empty( $short_description ) ) ? $content . ' ' . $short_description : $content;
		}
		return $content;
	}

	if ( is_plugin_active( 'woocommerce-request-a-quote/class-addify-request-for-quote.php' ) ) {
		add_filter( 'seokey_filter_helpers_admin_is_post_type_archive', 'seokey_thirdparty_request_a_quote_fix' );
		/**
		 * Fix for "Request a quote for Woocommerce", prevent the menu to be hidden 
		 *
		 * @param object $typenow_object
		 * @since   1.7.3
		 * @author  Arthur Leveque
		 *
		 */
		function seokey_thirdparty_request_a_quote_fix( $typenow_object ) {
			//  Check if current page is "addify_quote" post type admin archive page
			if ( $typenow_object->name === "addify_quote" ) {
				return false;
			}
			return $typenow_object->has_archive;
		}
	}
}