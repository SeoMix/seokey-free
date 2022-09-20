<?php
/**
 * Load import functions
 *
 * @Loaded on plugins_loaded + is_admin() + capability admin
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
 * Display import form
 *
 * @since  1.2.0
 * @author Daniel Roch
 */
function seokey_admin_import_display( $defaulttext = false ){
	$plugin_list = seokey_admin_import_list();
	if ( $plugin_list ) {
		echo '<h2 id="import-other-seo-plugin">' . esc_html__('Import from other SEO plugins', 'seo-key' ) . ' </h2>';
		if ( count( $plugin_list ) === 1 ) {
			echo '<p id="import-other-seo-plugin-explanation">';
				printf(
					esc_html__( 'One SEO plugin with import functions has been detected. Would you like to import data from %1$s?', 'seo-key' ),
					'<strong>' . reset( $plugin_list ) . '</strong>'
				);
			echo '</p>';
			echo '<span id="seokey_import_value" data-value="' . esc_attr( array_key_first( $plugin_list ) ). '"></span>';
		} else {
			echo '<p>';
				esc_html_e( 'You have several active SEO plugins with import functions. Tell us from which plugin you want to import data.', 'seo-key' );
			echo '</p>';
			$select = '<select id="seokey_import_values">';
			foreach ( $plugin_list as $plugin_key => $plugin_name ) {
				$select .= '<option value="' . $plugin_key . '">' . $plugin_name. '</option>';
			}
			$select .= '</select>';
			echo $select;
		}
		seokey_helper_cache_data('seokey_admin_import_assets_trigger', true );
		// Display button
		echo '<button class="button button-primary button-hero" id="seokey-launch-import">'. esc_html__('Import data', 'seo-key' ) .'</button>';
		if ( 'wizard' === $defaulttext ) {
			echo '<button class="button button-secondary button-hero" id="seokey-launch-import-abort">'. esc_html__('Do not import data', 'seo-key' ) .'</button>';
		}
		echo '<p id="seokey-import-message"></p>';
		seokey_helper_loader( 'seokey-import' );
	} elseif ( true === $defaulttext ) {
		echo '<h2 id="import-title">' . esc_html__('Import from other SEO plugins', 'seo-key' ) . ' </h2>';
		echo '<p>' . esc_html__('No other SEO plugin with import function has been detected.', 'seo-key' ) . ' </p>';
	}
}

/**
 * Return installed plugins with import function
 *
 * @since  1.2.0
 * @author Daniel Roch
 * @return bool|array Plugin list on success, false on error
 */
function seokey_admin_import_list(){
	$other_plugins = New SeokeyCheckOtherExtensions;
	$plugin_list = [];
	foreach ( $other_plugins->plugins_with_import as $key => $plugin ) {
		if ( in_array($plugin, $other_plugins->plugins_installed ) ) {
			$plugin_list[$key] = $plugin;
		}
	}
	switch( count( $plugin_list ) ) {
		case 0:
			return false;
		default:
			return $plugin_list;
	}
}

add_action( 'admin_footer', 'seokey_admin_import_assets' );
/**
 * Enqueue assets (CSS) for import function
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @uses    wp_enqueue_style()
 * @hook    admin_enqueue_scripts
 */
function seokey_admin_import_assets() {
	$needed = seokey_helper_cache_data('seokey_admin_import_assets_trigger' );
	if ( true === $needed ) {
		// Enqueue import JS
		wp_enqueue_script( 'seokey-import', esc_url( SEOKEY_URL_ASSETS . 'js/seokey-import.js' ), array( 'jquery', 'wp-i18n' ), SEOKEY_VERSION, true );
		wp_localize_script( 'seokey-import', 'seokey_data_import',
			[
				'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
				'security'                    => wp_create_nonce( 'seokey_data_import_sec' ),
			]
		);
		wp_set_script_translations( 'seokey-import', 'seo-key', SEOKEY_PATH_ROOT . '/public/assets/languages' );
	}
}

add_action( 'wp_ajax_seokey_import', 'wp_ajax_seokey_import_callback' );
/**
 * Handle ajax call for data import
 *
 * @since  1.2.0
 * @author Daniel Roch
 */
function wp_ajax_seokey_import_callback() {
	// Nonce
	if ( ! wp_verify_nonce( $_GET['security'], 'seokey_data_import_sec' ) ) {
		wp_send_json_error();
		die();
	}
	// User role
	if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
		wp_send_json_error();
		die();
	}
	if ( ! empty ( $_GET['plugin'] ) ) {
		// TODO later: move each import into their own functions and file
		switch ( $_GET['plugin'] ) {
			case "wordpress-seo/wp-seo.php":
				// Get Yoast Data
				$yoast_wpseo_titles = get_option( 'wpseo_titles' );
				$yoast_wpseo        = get_option( 'wpseo' );

				// Category base
				if ( !empty( $yoast_wpseo_titles['stripcategorybase'] ) ) {
					if ( 1 === (int) $yoast_wpseo_titles['stripcategorybase'] ) {
						update_option('seokey-field-metas-category_base', 1, true );
					}
				} else {
					update_option('seokey-field-metas-category_base', 0, true );
				}

				// Google meta HTML
				if ( isset( $yoast_wpseo['googleverify'] ) ) {
					update_option('seokey-field-search-console-searchconsole-google-verification-code', esc_html( $yoast_wpseo['googleverify'] ), true );
				}
				unset($yoast_wpseo);
				
				// Front page metas (if front page is a classic blog page)
				$frontpage_id = get_option( 'page_on_front' );
				if ( empty( $frontpage_id ) ) {
					if ( !empty( $yoast_wpseo_titles['title-home-wpseo'] ) ) {
						$data = seokey_helper_import_parsing( $yoast_wpseo_titles['title-home-wpseo'], 'homepage' );
						update_option( 'seokey-field-metas-metatitle', $data, true );
					}
					if ( !empty( $yoast_wpseo_titles['metadesc-home-wpseo'] ) ) {
						$data = seokey_helper_import_parsing( $yoast_wpseo_titles['metadesc-home-wpseo'], 'homepage' );
						update_option( 'seokey-field-metas-metadesc', $data, true );
					}
				} else {
					// Title
					$data = get_post_meta( $frontpage_id, '_yoast_wpseo_title', true );
					if ( empty( $data ) ) {
						$data = $yoast_wpseo_titles[ 'title-page' ];
					}
					if ( '' != $data ) {
						update_post_meta( $frontpage_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ) ) );
						update_option( 'seokey-field-metas-metatitle', $data, true );
					}
					// Meta description
					$data = get_post_meta( $frontpage_id, '_yoast_wpseo_metadesc', true );
					if ( empty( $data ) ) {
						$data = $yoast_wpseo_titles[ 'metadesc-page' ];
					}
					if ( '' != $data ) {
						update_post_meta( $frontpage_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $frontpage_id, 'page' ), ENT_QUOTES ) ) );
						update_option( 'seokey-field-metas-metadesc', $data, true );
					}
					unset($data);
				}
				
				// Authors pages
				$author_pages = array( 'author' );
				if ( !empty( $yoast_wpseo_titles['disable-author'] ) ) {
					if ( true === $yoast_wpseo_titles['disable-author'] ) {
						$author_pages = array( 'i_am_a_dummy_value' );
					}
				}
				update_option( 'seokey-field-cct-pages', $author_pages, true );
				
				// CPT global settings (show/hide, best taxonomy)
				$_builtin   = get_post_types( ['_builtin' => true, 'public' => true ], 'objects' );
				$_custom    = get_post_types( ['_builtin' => false, 'public' => true ], 'objects' );
				// Merge them
				$all_cpts = array_merge( $_builtin, $_custom );
				unset($_builtin);
				unset($_custom);
				// Set the default vales with keys and public bool value
				$all_cpts = wp_list_pluck( $all_cpts, 'public' );
				// Remove the false bool value and remove the attachment post type
				$all_cpts = array_filter( $all_cpts );
				unset( $all_cpts['attachment'] );
				// Add filter
				$all_cpts = apply_filters( 'seokey_filter_settings_add_contents_post_types', $all_cpts );
				// Iterate
				$all_cpts = array_keys( $all_cpts );
				$cctcpt = $cctcptknown = [ 'i_am_a_dummy_value' ];
				if ( !empty( $all_cpts ) ) {
					foreach ( $all_cpts  as $post_type ) {
						$name = 'noindex-' . $post_type;
						// Tell which one to keep (noindex)
						if ( 1 !== (int) $yoast_wpseo_titles[$name] ) {
							$cctcpt[] = $post_type;
						}
						// Tell SEOKEY knowned post types
						$cctcptknown[] = $post_type;
						// Define main taxonomy for each post type
						$name = 'post_types-' . $post_type . '-maintax';
						if ( !empty( $yoast_wpseo_titles[$name] ) ) {
							update_option('seokey-field-cct-taxonomy-choice-' . $post_type, $yoast_wpseo_titles[$name], true );
						}
					}
					update_option( 'seokey-field-cct-cpt', $cctcpt, true );
				}
				unset( $all_cpts );

				//  Global taxonomies
				$_builtin = get_taxonomies( ['_builtin' => true, 'public' => true ], 'objects' );
				$_custom = get_taxonomies( ['_builtin' => false, 'public' => true ], 'objects' );
				$all_taxos = array_merge( $_builtin, $_custom );
				unset( $_builtin );
				unset( $_custom );
				// Remove post format taxonomie
				if ( isset ( $all_taxos['post_format'] ) ) {
					unset( $all_taxos['post_format'] );
				}
				$ccttaxo = $ccttaxoknown = [ 'i_am_a_dummy_value' ];
				if ( !empty( $all_taxos ) ) {
					foreach ( $all_taxos as $taxonomy ) {
						$name = 'noindex-tax-' . get_object_vars( $taxonomy )['name'];
						// Tell which one to keep (noindex)
						if ( 1 !== (int) $yoast_wpseo_titles[$name] ) {
							$ccttaxo[] = get_object_vars( $taxonomy )['name'];
						}
						// Tell SEOKEY known taxonomies
						$ccttaxoknown[] = get_object_vars( $taxonomy )['name'];
					}
					update_option( 'seokey-field-cct-taxo', $ccttaxo, true );
					$content = [
						'posts'         => $cctcptknown,
						'taxonomies'    => $ccttaxoknown
					];
					update_option( 'seokey_admin_content_watcher_known', $content, TRUE );
				}
				unset( $ccttaxo );
				unset( $cctcpt );
				// Do not unset $all_taxos yet, we will need them later

				// Post types archives
				$args = array(
					'has_archive'   => true,
				);
				$output = 'names';
				$post_types = get_post_types( $args, $output );
				if ( !empty( $post_types ) ) {
					foreach ( $post_types as $post_type ) {
						if ( isset( $yoast_wpseo_titles[ 'title-ptarchive-' . $post_type ] ) ) {
							$data = seokey_helper_import_parsing( $yoast_wpseo_titles[ 'title-ptarchive-' . $post_type ], 'post_type_archive', '', $post_type );
							update_option( 'seokey-metatitle-' . $post_type, sanitize_text_field( $data ), true );
						}
						if ( isset( $yoast_wpseo_titles['metadesc-ptarchive-' . $post_type] ) ) {
							$data = seokey_helper_import_parsing( $yoast_wpseo_titles[ 'metadesc-ptarchive-' . $post_type ], 'post_type_archive', '', $post_type );
							update_option( 'seokey-metadesc-' . $post_type, sanitize_textarea_field( htmlspecialchars_decode( $data, ENT_QUOTES ) ), true );
						}
					}
				}
				// Purge data
				unset( $post_types );
				
				// Iterate $posts
				// we may need to cool down
				global $wpdb;
				$totalcount = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
				$batch      = 150;
				$offset     = 0;
				while ( $offset < $totalcount) {
					$args     = [
						'posts_per_page'            => $batch,
						'post_type'                 => 'any',
						'post_status'               => 'any',
						'no_found_rows'             => true,
						'update_post_term_cache'    => false,
						'ignore_sticky_posts'       => true,
						'offset'                    => $offset,
					];
					$postlist = get_posts( $args );
					if ( $postlist ) {
						foreach ( $postlist as $post ) {
							if ( 'attachment' !== $post->post_type ) {
								// Title
								$data = get_post_meta( $post->ID, '_yoast_wpseo_title', true );
								if ( empty( $data ) ) {
									$data = $yoast_wpseo_titles[ 'title-' . $post->post_type ];
								}
								if ( '' != $data ) {
									update_post_meta( $post->ID, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) ) );
								}
								// Meta description
								$data = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
								if ( empty( $data ) ) {
									$data = $yoast_wpseo_titles[ 'metadesc-' . $post->post_type ];
								}
								if ( '' != $data ) {
									update_post_meta( $post->ID, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ), ENT_QUOTES ) ) );
								}
								// Main keyword
								$data = get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true );
								if ( '' != $data ) {
									update_post_meta( $post->ID, 'seokey-main-keyword', sanitize_text_field( seokey_helper_import_parsing( $data, 'post', $post->ID, $post->post_type ) ) );
								}
								// Noindex
								$data = (int) get_post_meta( $post->ID, '_yoast_wpseo_meta-robots-noindex', true );
								if ( 1 === $data ) {
									update_post_meta( $post->ID, 'seokey-content_visibility', 1 );
								} else {
									delete_post_meta( $post->ID, 'seokey-content_visibility' );
								}
								unset($data);
							}
						}
					}
					$offset += $batch;
					usleep(50000);
				}
				unset($totalcount);
				unset($batch);
				unset($offset);
				unset($args);
				unset($postlist);
				
				// Iterate terms
				$taxonomies = array_keys( $all_taxos );
				$all_tax_metas = get_option( 'wpseo_taxonomy_meta' );
				unset( $all_taxos );
				foreach ( $taxonomies as $taxonomie ) {
					// get terms data
					$taxonomy_meta = '';
					if ( !empty ( $all_tax_metas[$taxonomie] ) ) {
						$taxonomy_meta = $all_tax_metas[$taxonomie];
					}
					$terms = get_terms( array(
						'taxonomy' => $taxonomie,
						'hide_empty' => false,
					) );
					// Iterate
					foreach ( $terms as $term ) {
						// Title
						if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_title'] ) ) {
							$data = $taxonomy_meta[$term->term_id]['wpseo_title'];
						} else {
							$data = $yoast_wpseo_titles[ 'title-tax-' . $term->taxonomy ];
						}
						if ( '' != $data ) {
							update_term_meta( $term->term_id, 'seokey-metatitle', sanitize_text_field( seokey_helper_import_parsing( $data, 'term', $term->term_id ) ) );
						}
						// Meta description
						if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_desc'] ) ) {
							$data = $taxonomy_meta[$term->term_id]['wpseo_desc'];
						} else {
							$data = $yoast_wpseo_titles[ 'metadesc-tax-' . $term->taxonomy ];
						}
						if ( '' != $data ) {
							update_term_meta( $term->term_id, 'seokey-metadesc', sanitize_textarea_field( htmlspecialchars_decode( seokey_helper_import_parsing( $data, 'term', $term->term_id ), ENT_QUOTES ) ) );
						}
						// Noindex
						if ( !empty ( $taxonomy_meta[$term->term_id]['wpseo_noindex'] ) ) {
							$data = $taxonomy_meta[$term->term_id]['wpseo_noindex'];
							if ( 'noindex' === $data ) {
								$data = 1;
							}
						}
						if ( 1 === $data ) {
							update_term_meta( $term->term_id, 'seokey-content_visibility', 1 );
						} else {
							delete_term_meta( $term->term_id, 'seokey-content_visibility' );
						}
					}
				}
				unset( $terms );
				unset( $term );
				unset( $taxonomies );
				
				
				// Iterate users
				$users = get_users();
				foreach ( $users as $key => $user ) {
					// Title
					$data = get_user_meta( $user->ID, 'wpseo_title', true );
					if ( empty( $data ) ) {
						$data = $yoast_wpseo_titles['title-author-wpseo'];
					}
					$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
					update_user_meta( $user->ID, 'seokey-metatitle', esc_html( $data ) );
					// Metadesc
					$data = get_user_meta( $user->ID, 'wpseo_metadesc', true );
					if ( empty( $data ) ) {
						$data = $yoast_wpseo_titles['metadesc-author-wpseo'];
					}
					$data = seokey_helper_import_parsing( $data, 'users', $user->ID );
					update_user_meta( $user->ID, 'seokey-metadesc', esc_html( $data ) );
					// Noindex
					$data = get_user_meta( $user->ID, 'wpseo_noindex_author', true );
					$data = ( "on" === $data ) ? 1 : 0;
					if ( 0 === $data ) {
						delete_user_meta( $user->ID, 'seokey-content_visibility' );
					} else {
						update_user_meta( $user->ID, 'seokey-content_visibility', $data );
					}
					// Purge data
					unset( $users[$key] );
				}
				// Purge data
				unset( $users );

				
				// Schema.org: person
				if ( $yoast_wpseo_titles['company_or_person'] === "person" ) {
					// Name
					if ( !empty( $yoast_wpseo_titles['company_or_person_user_id'] ) ) {
						// Do not delete others correct values
						$previous_values = seokey_helper_get_option( 'schemaorg-schema-person' );
						if ( false !== $previous_values ) {
							$person = $previous_values;
						}
						$person['name'] = get_the_author_meta( 'display_name', $yoast_wpseo_titles['company_or_person_user_id'] );
						update_option( 'seokey-field-schemaorg-schema-person', $person, true );
					}
					// Image
					if ( isset( $yoast_wpseo_titles['person_logo_meta'] ) ) {
						if ( !empty( $yoast_wpseo_titles['person_logo_meta']['url'] ) ) {
							update_option( 'seokey-field-schemaorg-schema-person-image', esc_url( $yoast_wpseo_titles['person_logo_meta']['url'] ), true );
						}
					}
					$social = [
						'twitter',
						'facebook',
						'instagram',
						'linkedin',
						'myspace',
						'pinterest',
						'soundcloud',
						'tumblr',
						'youtube',
						'wikipedia'
					];
					// Do not delete others correct values
					$previous_values = seokey_helper_get_option( 'schemaorg-schema-person-sameas' );
					if ( false !== $previous_values ) {
						$sameas = $previous_values;
					}
					foreach ( $social as $link ) {
						if ( !empty( $value = get_the_author_meta( $link, $yoast_wpseo_titles['company_or_person_user_id'] ) ) ) {
							if ( $link === 'twitter' ) {
								$value = 'https://www.twitter.com/' . $value;
							}
							$sameas[] = esc_url( $value );
						}
					}
					$sameas = array_unique( $sameas );
					update_option( 'seokey-field-schemaorg-schema-person-sameas', $sameas, true );
				}
				// Schema.org: Company
				else {
					// Company Name
					if ( ! empty( $yoast_wpseo_titles['company_name'] ) ) {
						// Do not delete others correct values
						$previous_values = seokey_helper_get_option( 'schemaorg-schema-local_business' );
						if ( false !== $previous_values ) {
							$company = $previous_values;
						}
						$company['name'] = sanitize_text_field( $yoast_wpseo_titles['company_name'] );
						update_option( 'seokey-field-schemaorg-schema-local_business', $company, true );
					}
					// Logo
					if ( ! empty( $yoast_wpseo_titles['company_logo'] ) ) {
						update_option( 'seokey-field-schemaorg-schema-local_business-image', esc_url( $yoast_wpseo_titles['company_logo'] ), true );
					}
				}
				
				// Deactivate plugin
//				if ( 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
//					deactivate_plugins( $_GET['plugin'] );
//					if ( ! is_network_admin() ) {
//						update_option( 'recently_activated', array( $_GET['plugin'] => time() ) + (array) get_option( 'recently_activated' ) );
//					} else {
//						update_site_option( 'recently_activated', array( $_GET['plugin'] => time() ) + (array) get_site_option( 'recently_activated' ) );
//					}
//					// Rewrite rules after plugin deactivation
//					flush_rewrite_rules();
//				}
				
				// Renew sitemaps
				if ( 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
					// Sitemap lastmod
					require_once( dirname( __file__ ) . '/sitemap/sitemaps-lastmod.php' );
					$lastmod = new Seokey_Sitemap_Lastmod();
					$lastmod->seokey_sitemap_set_term_lastmod();
					$lastmod->seokey_sitemap_set_author_lastmod();
					// Allow sitemap creation
					update_option( 'seokey_sitemap_creation', 'running', true );
				}
				
				// The end
				wp_send_json_success( esc_html__( 'Import completed. ', 'seo-key' ) );
				break;
			default:
				wp_send_json_error( esc_html__( 'Incorrect plugin name. ', 'seo-key' ) );
				break;
		}
	}
}

/**
 * Fallback function for Yoast Import %%
 *
 * @return array|string|string[]
 * @author Daniel Roch
 * @since  1.2.0
 *
 * @param string $data data to parse (searching for %% items)
 * @param string $type content tyope (term, post, user...)
 * @param integer $ID ID for this content
 * @param string $cpt Post type (page, post...)
 * @return string Cleaned data
 */
function seokey_helper_import_parsing( $data, $type = '', $ID = 0, $cpt = '' ) {
	// Separator
	$yoast_wpseo = get_option( 'wpseo_titles' );
	$separator   = '-';
	if ( ! empty( $yoast_wpseo['separator'] ) ) {
		$separator_options = [
			'sc-dash'   => '-',
			'sc-ndash'  => '&ndash;',
			'sc-mdash'  => '&mdash;',
			'sc-middot' => '&middot;',
			'sc-bull'   => '&bull;',
			'sc-star'   => '*',
			'sc-smstar' => '&#8902;',
			'sc-pipe'   => '|',
			'sc-tilde'  => '~',
			'sc-laquo'  => '&laquo;',
			'sc-raquo'  => '&raquo;',
			'sc-lt'     => '&lt;',
			'sc-gt'     => '&gt;',
		];
		$separator         = $separator_options[ $yoast_wpseo['separator'] ];
	}
	$replacements = [
		// Global
		'%%sitename%%'              => wp_strip_all_tags( get_bloginfo( 'name' ), true ),
		'%%sitedesc%%'              => wp_strip_all_tags( get_bloginfo( 'description' ) ),
		'%%sep%%'                   => $separator,
		// Always ignore
		'%%searchphrase%%'          => '',
		'%%page%%'                  => '',
		'%%pagetotal%%'             => '',
		'%%pagenumber%%'            => '',
		'%%term404%%'               => '',
		// For posts
		'%%id%%'                    => '',
		'%%date%%'                  => '',
		'%%modified%%'              => '',
		'%%title%%'                 => '',
		'%%parent_title%%'          => '',
		'%%excerpt%%'               => '',
		'%%excerpt_only%%'          => '',
		'%%tag%%'                   => '',
		'%%category%%'              => '',
		'%%primary_category%%'      => '',
		'%%focuskw%%'               => '',
		'%%currentyear%%'           => '',
		'%%currentmonth%%'          => '',
		// For posts & post type archive
		'%%archive_title%%'         => '',
		'%%pt_single%%'             => '',
		'%%pt_plural%%'             => '',
		// For posts and authors
		'%%name%%'                  => '',
		'%%user_description%%'      => '',
		// For terms
		'%%category_description%%'  => '',
		'%%tag_description%%'       => '',
		'%%term_description%%'      => '',
		'%%term_title%%'            => '',
		'%%term_hierarchy%%'        => '',
	];
	// now define correct value according to each data
	switch ( $type ) {
		case 'post':
			$replacements['%%id%%']                 = (int) $ID;
			$replacements['%%date%%']               = get_the_date( '', $ID );
			$replacements['%%modified%%']           = get_the_modified_date( '', $ID );
			$replacements['%%title%%']              = get_the_title( $ID );
			$replacements['%%parent_title%%']       = ( false !== ( $parent_id = wp_get_post_parent_id( $ID ) ) ) ? get_the_title( $parent_id ) : '';
			$excerpt    = get_the_excerpt( $ID );
			if ( empty( $excerpt ) ) {
				$post    = esc_html( strip_tags( do_shortcode( get_post( $ID )->post_content ) ) );
				$excerpt = substr( $post, 0, strrpos( substr( $post, 0, METADESC_COUNTER_MAX ), ' ' ) );
			}
			$replacements['%%excerpt%%']            = $excerpt;
			$replacements['%%excerpt_only%%']       = $excerpt;
			$terms_post_tag                         =  get_the_terms( $ID, 'post_tag' );
			if ( !empty( $terms_post_tag ) ) {
				$replacements['%%tag%%']            = join(', ', wp_list_pluck( get_the_terms( $ID, 'post_tag' ), 'name') );
			}
			$terms_category =  get_the_terms( $ID, 'category' );
			if ( !empty( $terms_category ) ) {
				$replacements['%%category%%']            = join(', ', wp_list_pluck( get_the_terms( $ID, 'category' ), 'name') );
			}
			$replacements['%%primary_category%%']   = get_cat_name( get_post_meta( $ID, '_yoast_wpseo_primary_category', true ) );
			$replacements['%%focuskw%%']            = get_post_meta( $ID, '_yoast_wpseo_focuskw', true );
			$replacements['%%currentyear%%']        = date_i18n( 'Y' );
			$replacements['%%currentmonth%%']       = date_i18n( 'M' );
			
			$author = get_post_field( 'post_author', $ID );
			$replacements['%%name%%']               = get_userdata( $author )->display_name;
			$replacements['%%user_description%%']   = get_user_meta( $author, 'description', true );
			
			$post_type = get_object_vars( get_post_type_object( $cpt ) );
			$replacements['%%archive_title%%']  = $post_type['name'];
			$replacements['%%pt_single%%']      = $post_type['labels']->singular_name;
			$replacements['%%pt_plural%%']      = $post_type['label'];
			break;
		case 'term':
			$term = get_term( $ID );
			$termdescription                            = wp_html_excerpt( htmlspecialchars_decode( wp_strip_all_tags( $term->description ), ENT_QUOTES ), METADESC_COUNTER_MAX );
			$replacements['%%term_description%%']       = $termdescription;
			$replacements['%%tag_description%%']        = $termdescription;
			$replacements['%%category_description%%']   = $termdescription;
			$replacements['%%term_title%%']             = $term->name;
			$replacements['%%term_hierarchy%%']         = '';
			if ( $term->parent !== 0 ) {
				$args = [
					'format'    => 'name',
					'separator' => ' ',
					'link'      => false,
					'inclusive' => true,
				];
				$replacements['%%term_hierarchy%%']         = ' ' . $separator . ' ' . get_term_parents_list( $ID, $term->taxonomy, $args );
			}
			break;
		case 'users':
			// Get user data by user id
			$replacements['%%name%%']               = get_userdata( $ID )->display_name;
			$replacements['%%user_description%%']   = get_user_meta($ID, 'description', true );
			break;
		case 'post_type_archive':
			$post_type = get_object_vars( get_post_type_object( $cpt ) );
			$replacements['%%archive_title%%']  = $post_type['name'];
			$replacements['%%pt_single%%']      = $post_type['labels']->singular_name;
			$replacements['%%pt_plural%%']      = $post_type['label'];
			break;
	}
	// Replace with correct value
	$data = str_replace( array_keys( $replacements ), array_values( $replacements ), $data );
	// Remove double spaces
	$data = str_replace( '  ', ' ', $data );
	return $data;
}