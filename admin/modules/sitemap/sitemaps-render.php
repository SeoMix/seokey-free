<?php
/**
 * Sitemap generation
 *
 * @Loaded on plugins_loaded + is_admin() + capability author
 * @see seokey_plugin_init()
 * @see modules/sitemap/sitemaps.php
 * @package SEOKEY

/**
 * Security
 *
 * Prevent direct access to this file
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You lost the key...' );
}

class Seokey_Sitemap_Render {
    /**
     * Define only one instance of our class
     * @since   0.0.1
     * @author  Daniel Roch
     *
     * @var (array) $instance Singleton
     * @access private
     * @static
     */
    public static $instance = null;

	/**
	 * Post array
	 * @var array
	 */
	protected $posts = [];

	/**
	 * All generated files
	 * @var array
	 */
	protected $generated_files = [];

	/**
	 * LastMod Data
	 * @var array
	 */
	protected $lastmod = [];

	/**
	 * Exclude specific items
	 * @var array
	 */
	public $excluded = [
		'cpt'  => [
			'i_am_a_dummy_value',
			'attachement',
			'revision',
			'wp_block',
			'wp_template',
			'wp_template_part',
			'wp-navigation',
			'wp_global_styles',
			'custom_css',
			'customize_changeset',
			'nav_menu_item',
			'user_request',
			'oembed_cache',
			'ct-content-block', // Content block ???
			'ct_content_block', // Content block ???
			'download', // EDD
			'wpcf7-contact-form', //CF7
		],
		'taxo' => [
			'i_am_a_dummy_value',
			'post_format',
			'nav_menu',
			'nav_menu_item',
			'wp_theme',
			'wp_template_part_area',
			'post_translations',
		],
	];

	// Get filtered excluded
	public function get_excluded() {
		return apply_filters( "seokey_filter_sitemap_sender_excluded", $this->excluded );
	}

	// Unserializing instances of this class is forbidden.
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Seokey_Sitemap constructor.
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function __construct() {
		// Create sitemap on each post modification
		add_action( 'save_post',                [ $this, 'seokey_sitemap_init_posts'], 12, 3 );
		add_action( 'transition_post_status',   [ $this, 'seokey_sitemap_init_posts_transition'], 12, 3 );

        // Create sitemap on taxonomy modification
		$taxos = get_taxonomies( ['public' => true, 'show_ui' => true, 'lang' => 'all' ] );
		foreach ( $taxos as $tax ) {
			add_action( 'edit_' . $tax,         [ $this, 'seokey_sitemap_init_terms'], 12, 2 );
			add_action( 'create_' . $tax,       [ $this, 'seokey_sitemap_init_terms'], 12, 2 );
		}
        // Create sitemap on each term deletion
		add_action( 'delete_term',              [ $this, 'seokey_sitemap_init_terms_delete',  ], 200, 3 );
		// Update options
		add_action( 'updated_option',           [ $this, 'seokey_sitemap_updated_option'] );
		add_action( 'added_option',             [ $this, 'seokey_sitemap_updated_option'] );
        // User update
        add_action( 'profile_update',           [ $this, 'seokey_sitemap_init_authors'] );
        add_action( 'deleted_user',             [ $this, 'seokey_sitemap_init_authors'] );
        add_action( 'user_register',            [ $this, 'seokey_sitemap_init_authors'] );
        // TODO Later : improve generation : only delete/update/create necessary sitemaps
        // TODO Later : improve author generation : only if user role needs a sitemap refresh
	}

     /**
	 * Seokey_Sitemap Trigger specific sitemap creation or deletion
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 */
	public function seokey_sitemap_init_posts( $post, $b, $c ) {
		// Do no trigger sitemap on unpublished posts
		if ( 'publish' !== get_post_status( $post ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$post_type  = get_post_type( $post );
		$allowed    = get_option('seokey-field-cct-cpt');
		$allowed    = ( is_array( $allowed ) ) ? $allowed : array( $allowed );
		if ( in_array( $post_type, $allowed ) && 'running' !== get_option('seokey_sitemap_creation') ) {
			$this->seokey_sitemap_init('post', $post_type );
			update_option( 'seokey_sitemap_creation', 'running', true );
		}
	}
	public function seokey_sitemap_init_posts_transition( $to, $from, $post_object ) {
		if ( 'publish' === $from && $from !== $to && 'running' !== get_option('seokey_sitemap_creation') ) {
			$data = get_object_vars( $post_object );
			$this->seokey_sitemap_init('post', $data['post_type'] );
			update_option( 'seokey_sitemap_creation', 'running', true );
		}
	}
	public function seokey_sitemap_init_terms( $a, $taxonomy ) {
		$this->seokey_sitemap_init( 'term', $taxonomy );
	}
	public function seokey_sitemap_init_terms_delete( $a, $b, $taxonomy ){
		$this->seokey_sitemap_init( 'term', $taxonomy );
	}
	public function seokey_sitemap_init_authors(){
		$this->seokey_sitemap_init( 'author' );
	}

	/**
	 * Check if one of our content option has been changed
	 *
	 * @param $option_name
	 */
	public function seokey_sitemap_updated_option( $option_name ) {
	    // SEOKEY options related to contents
		$options = [
			'seokey-field-cct-cpt',
			'seokey-field-cct-taxo',
			'seokey-field-cct-pages'
		];
		// One of these option has changed : init sitemap !
		if ( in_array( $option_name, $options ) ) {
			$this->seokey_sitemap_delete_old();
			// Allow sitemap background creation
			update_option( 'seokey_sitemap_creation', 'running', true );
		}
	}

    /**
     * Trigger sitemap creation for all items
     *
     * @since   0.0.1
     * @author  Léo Fontin
     */
    public function seokey_sitemap_init( $type = 'all', $data = '' ) {
		// Create sitemap for each language
        foreach ( seokey_helper_cache_data('languages')['lang'] as $lang => $v ) {
            switch ( $type) {
                case 'all':
                    // Generate sitemaps for all post
                    $this->seokey_sitemap_generate_all_post_type( $lang, $data );
                    // Generate sitemaps for all taxo
                    $this->seokey_sitemap_generate_all_taxo( $lang, $data );
                    // Author sitemap generation
                    $this->seokey_sitemap_generate_all_author( $lang );
                    break;
                case 'post':
                    // Generate sitemaps for all posts
                    $this->seokey_sitemap_generate_all_post_type( $lang, $data );
                    break;
                case 'term':
                    // Generate sitemaps for all taxo
                    $this->seokey_sitemap_generate_all_taxo( $lang, $data );
                    break;
                case 'author':
                    // Author sitemap generation
                    $this->seokey_sitemap_generate_all_author( $lang );
                    break;
            }
            // Index sitemap generation
            $this->seokey_sitemap_generate_index( $type, $lang );
        }
    }

	/**
	 * seokey_sitemap_initialisateur pour générer les sitemap des posts type
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_generate_all_post_type( $lang, $data = '' ) {
		// Get all post types
		$post_types = ( !empty( $data ) ) ? array( $data ) : $this->seokey_sitemap_get_post_types(  $lang );
		$allowed = get_option('seokey-field-cct-cpt');
		// Generate each sitemap
		foreach ( $post_types as $type ) {
			if ( in_array( $type, $allowed ) ){
				$this->seokey_sitemap_generate_by_post_type( $lang, $type );
            }
        }
	}

	/**
	 * Returns all public post types
	 *
	 * @return bool|string[]|WP_Post_Type[]
	 * @author  Léo Fontin
	 *
	 * @since   0.0.1
	 */
	public function seokey_sitemap_get_post_types( $lang ) {
	    // Get website configuration
		$post_types = seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => true, 'lang' => $lang ] ) );
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $k => $cpt ) {
				if ( in_array( $cpt, $this->get_excluded()['cpt'] ) ) {
					unset( $post_types[ $k ] );
				}
			}
		}
		// Return public post types
		return $post_types;
	}

	/**
	 * Récupère et traite les données d'un type de post
	 *
	 * @param string $post_type
	 *
	 * @author  Léo Fontin
	 *
	 * @since   0.0.1
	 */
	public function seokey_sitemap_generate_by_post_type( $lang, $post_type = 'posts' ) {
		// Get allo posts from a post type
        $posts = $this->seokey_sitemap_get_posts( $post_type, $lang );
		// Add information to our global variable
		$this->seokey_sitemap_set_posts( $posts, $post_type );
		// Clean data
		$posts_datas = [];
		// Add homepage for page post type if necessary
		if ( $post_type === 'page' ) {
			$homepage = $this->seokey_sitemap_get_homepage( $lang );
			if ( $homepage !== false ) {
				$posts_datas[] = $homepage;
			}
		}
		// Add post type archive link if necessary
		$post_type_data = get_post_types( array( 'name' => $post_type ), 'objects' );
		if ( is_object( $post_type_data ) ) {
			$post_type_data = get_object_vars( reset( $post_type_data ) );
		}
		$archive = ( !empty( $post_type_data['has_archive'] ) ) ? $post_type_data['has_archive'] : 0;
		if ( 1 === (int) $archive ) {
			$posts_datas[] = [
				'loc'     => user_trailingslashit( home_url( $post_type_data['query_var'] ) ),
				'lastmod' => esc_xml( $this->seokey_sitemap_get_formated_current_time() ),
			];
		}
        // Do we have posts ?
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				// Get post last modification date
				$date = get_the_modified_date( 'Y-m-d H:i:s', $post->ID );
				// Get post images
				$images = $this->seokey_sitemap_get_images( $post, 'post' );
                // Final data
				$posts_datas[] = [
					'loc'     => get_the_permalink( $post->ID ),
					'lastmod' => esc_xml( $date ),
					'images'  => $images
				];
			}
            // Create XML sitemap code
            $post_type = str_replace( '_','-', $post_type );
            // No attachment sitemap, just to be sure...
            if ( $post_type !== 'attachment' ) {
                $code = $this->seokey_sitemap_get_code( $posts_datas, $lang, 'url' );
                // Create final file
                $this->seokey_sitemap_output( $code, 'sitemap-' . $post_type . '-'.$lang.'.xml', $lang );
            }
		}
	}

	/**
	 * Get all public post from post type
	 *
	 * @param $post_type
	 *
	 * @return array|bool
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_posts( $post_type, $lang ) {
		// Post array
		$posts = [];
		// Get all posts from post type
		$posts_datas = new WP_query( [
			'post_type'         => $post_type,
			'posts_per_page'    => "-1",
			'post_status'       => ['publish'],
            'orderby'           => 'modified',
            'order'             => 'DESC',
            'lang'              => seokey_helper_cache_data('languages')['lang'][$lang]['iso2'],
		] );
        // Do we have posts ?
		if ( ! empty( $posts_datas->posts ) ) {
			foreach ( $posts_datas->posts as $post ) {
				// Is it a private post ?
				$visibility = get_post_meta( $post->ID, 'seokey-content_visibility', true );
				if ( $visibility !== '1' ) {
					$posts[] = $post;
				}
			}
		}
		return ( ! empty( $posts ) ) ? $posts : false;
	}

	/**
	 * Ajoute les posts d'un post type à la variable globale
	 *
	 * @param $posts
	 * @param $type
	 */
	public function seokey_sitemap_set_posts( $posts, $type ) {
		$this->posts[ $type ] = $posts;
	}

	/**
	 * Add homepage to sitemap page when front_page is set to last posts
	 *
	 * @since   1.6.0
	 * @author  Gauvain Van Ghele, Daniel Roch
	 * @return array|bool array with homepage data, or boolean false
	 */
	public function seokey_sitemap_get_homepage( $lang ) {
		// Are we using a page for our homepage
		$homepage_type = get_option( 'show_on_front' );
		// Homepage is set to last posts
		if ( $homepage_type === 'posts' ) {
			$lang_data = seokey_helper_cache_data('languages');
			if ( !empty ( $lang_data['lang'][$lang]['domain'] ) ) {
				return [
					'loc'     => user_trailingslashit( $lang_data['lang'][$lang]['domain'] ),
					'lastmod' => current_time( 'Y-m-d H:i:s' )
				];
			}
		}
		return false;
	}

	/**
	 * Get all images from content
	 *
	 * @param $post
	 *
	 * @return array
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_images( $content, $type ) {
		// Image list
		$images = [];
        switch ( $type ) {
            case 'post':
                // Get main image
                $thumbnail = $this->seokey_sitemap_get_post_thumbnail( $content );
				$exclude = '';
                if ( !empty( $thumbnail ) ) {
                    $images[] = $thumbnail;
	                $exclude = $thumbnail['loc'];
                }
                // Get images from content
                // TODO later add filter here
                $post_images = $this->seokey_sitemap_get_post_content_images( $content->post_content, $exclude );
                if ( !empty( $post_images ) ) {
                    $images = array_merge( $images, $post_images );
                }
                break;
            case 'term':
                // Get images from taxonomy description
                // TODO later add filter here
                $post_images = $this->seokey_sitemap_get_post_content_images( $content->description );
                if ( !empty( $post_images ) ) {
                    $images = array_merge( $images, $post_images );
                }
                break;
            case 'author':
                $gravatar = get_avatar_url( $content->ID );
                if ( false !== $gravatar ) {
	                $title = sprintf( _x( "%s gravatar", 'User Name Gravatar', 'seo-key' ), $content->display_name );
                    $images[] = [
                        'loc'       => $gravatar,
                        'title'   => iconv( 'utf-8', 'latin1', utf8_encode( $title ) ),
                    ];
                }
                break;
		}
		return $images;
	}

	/**
	 * Get main image data
	 *
	 * @param $post
	 *
	 * @return array|bool|void
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_post_thumbnail( $post ) {
		// Thumbnail
		$thumb_id = get_post_thumbnail_id( $post );
		if ( ! empty( $thumb_id ) ) {
			$url = get_the_post_thumbnail_url( $post );
			// Image title
			$title = get_the_title( $thumb_id );
			$title = html_entity_decode( $title );
			$title = ( ! empty( $title ) ) ? $title : '';
			// Image caption
			$caption_meta = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$caption      = ( ! empty( $caption_meta ) ) ? $caption_meta : '';
			return [
				'loc'     => $this->seokey_sitemap_images_clean_URL( $url ),
				'title'   => iconv( 'utf-8', 'latin1', utf8_encode( $title ) ),
				'caption' => iconv( 'utf-8', 'latin1', utf8_encode( $caption ) ),
			];
		} else {
			return '';
		}
	}

	/**
	 * Récupère les images et leurs infos dans le contenu du post
	 *
	 * @param $post
	 *
	 * @return array|bool|void
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_post_content_images( $post, $exclude = '' ) {
		// Continue only if we have content
		if ( empty( $post ) ) {
			return;
		}
		$images = [];
		// Parse HTML
		$dom = new DOMDocument();
		// Let's ignore errors
		libxml_use_internal_errors( true );
		// Load DOM
		$dom->loadHTML( $post );
		libxml_clear_errors();
		$xpath = new DOMXpath( $dom );
        //  Do we have images ?
		$elements = $xpath->query( '//img' );
		if ( ! is_null( $elements ) ) {
			foreach ( $elements as $image ) {
				// Image URL
				$loc = utf8_decode( $image->getAttribute( 'src' ) );
				if ( str_starts_with( $loc, 'http:' ) ) {
					if ( true === wp_is_using_https() ) {
						$loc = str_replace( 'http:', 'https:', $loc );
					}
				}
				if ( esc_url( $exclude ) !== $loc ) {
					// Image name
					$title = $image->getAttribute( 'title' );
					$title = html_entity_decode( $title );
					if ( empty( $title ) ) {
						$title = explode( '/', $loc );
						$title = array_pop( $title );
					}
					// Image ALT
					$alt     = $image->getAttribute( 'alt' );
					$caption = ( ! empty( $alt ) ) ? $alt : '';
					// Ajout de l'image au tableau final //
					$images[] = [
						'loc'     => $loc,
						'title'   => iconv( 'utf-8', 'latin1', utf8_encode( $title ) ),
						'caption' => iconv( 'utf-8', 'latin1', utf8_encode( $caption ) ),
					];
				}
			}
		}

		return ( ! empty( $images ) ) ? $images : false;

	}

	/**
	 * seokey_sitemap_init de la génération des tous les sitemap des taxos
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_generate_all_taxo( $lang, $data = '' ) {
		// Get all taxonomies or defined taxonomies*
		$taxos = ( !empty( $data ) ) ? array( $data ) : $this->seokey_sitemap_get_taxos( $lang );
		$allowed = get_option('seokey-field-cct-taxo');
		// Create each taxonomy sitemap
		if ( ! empty( $taxos ) ) {
			foreach ( $taxos as $taxo ) {
				if ( in_array( $taxo, $allowed ) ) {
                    $this->seokey_sitemap_generate_by_taxo( $taxo, $lang );
				}
			}
		}
	}

	/**
	 * Return all public taxonomies
	 *
	 * @return bool|mixed|void
	 */
	public function seokey_sitemap_get_taxos( $lang ) {

		// Get all public taxonomies
		$taxos = seokey_helper_get_option( 'cct-taxo', get_taxonomies( ['public' => true, 'show_ui' => true , 'lang' => $lang] ) );
		if ( ! empty( $taxos ) ) {
			foreach ( $taxos as $k => $tax ) {
				if ( in_array( $tax, $this->get_excluded()['taxo'] ) ) {
					unset( $taxos[ $k ] );
				}
			}
		}
		return ( ! empty( $taxos ) ) ? $taxos : false;
	}

	/**
	 * Create each sitemap taxonomy
	 *
	 * @param $taxo
	 *
	 * @author  Léo Fontin
	 * @since   0.0.1
	 */
	public function seokey_sitemap_generate_by_taxo( $taxo, $lang ) {
		// Get all non empty terms from this taxonomy
		$terms = $this->seokey_sitemap_get_taxo_terms( $taxo, $lang );
		if ( ! empty( $terms ) ) {
			// Final array
			$terms_datas = [];
			foreach ( $terms as $term ) {
                // Check if term is public
				$visibility = get_term_meta( $term->term_id, 'seokey-content_visibility', true );
				if ( $visibility !== '1' ) {
					// Get images
					$images = $this->seokey_sitemap_get_images( $term, 'term' );
					// Last mod
                    $default_last_mod =  $this->seokey_sitemap_get_formated_current_time();
                    $lastmod_post = get_term_meta( $term->term_id, 'seokey-sitemap-lastmod', true );
                    $lastmod = ( ! empty( $lastmod_post ) ) ? $lastmod_post : $default_last_mod;
                    // Final Data
					$terms_datas[] = [
						'loc'     => get_term_link( $term->term_id ),
						'lastmod' => $lastmod,
						'images'  => $images
					];
				}
			}
            $file_name = str_replace( '_', '-', $taxo );
            // No attachment sitemap, just to be sure...
            if ( $file_name != 'attachment' ){
                // Final sitemap code
                $code = $this->seokey_sitemap_get_code( $terms_datas, $lang, 'url' );
                // Create file
                $this->seokey_sitemap_output( $code, 'sitemap-' . $file_name . '-'.$lang.'.xml' , $lang );
            }
		}
        else {
            // No terms for this taxonomy, delete this file
            $file_name  = 'sitemap-' . $taxo . '.xml';
            $file_name  = str_replace( '_', '-', $file_name );
            $path       = ABSPATH . $file_name;
            if ( file_exists( $path ) ) {
                unlink( $path );
            }
        }
	}

	/**
	 * Retourne toutes les taxo publiques
	 *
	 * @param $taxo
	 *
	 * @return array|bool|int|WP_Error
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_taxo_terms( $taxo, $lang ) {
		$terms = get_terms( [
			'taxonomy'   => $taxo,
            'lang'       => seokey_helper_cache_data('languages')['lang'][$lang]['iso2'],
			'hide_empty' => false,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'seokey-sitemap-lastmod',
                    'type' => 'DATE',
                ],
                [
                    'key' => 'seokey-sitemap-lastmod',
                    'value'     => '0',
                    'compare'   => 'NOT EXISTS',
                ],
            ],
		] );
		// Return data
		return ( empty( $terms->errors ) ) ? $terms : false;
	}

	/**
	 * Génère le sitemap des auteurs
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_generate_all_author( $lang ) {
		// Get global users visibilty
		$pages = seokey_helper_get_option( 'cct-pages', [] );
		if ( ! empty( $pages ) && in_array( 'author', $pages ) ) {
			// Get all users
			$users = $this->seokey_sitemap_get_authors( $lang );
			$users_datas = [];
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
                    $images = $this->seokey_sitemap_get_images( $user, 'author' );
                    // Delete prefix domain Url : we need good domain prefix for each language
                    $author_posts_url_short = str_replace( home_url(), '', get_author_posts_url( $user->ID ) );
					$users_datas[] = [
						'loc'     => seokey_helper_cache_data('languages')['lang'][$lang]['domain'] . $author_posts_url_short,
						'lastmod' => get_user_meta( $user->ID, 'seokey-sitemap-lastmod', true ),
                        'images'  => $images
					];
				}
			}
			if ( ! empty( $users_datas ) ) {
				// Create sitemap file
				$code = $this->seokey_sitemap_get_code( $users_datas, $lang, 'url' );
				$this->seokey_sitemap_output( $code, 'sitemap-author-'.$lang.'.xml' ,$lang );
			}
		}
	}

	/**
	 * Get all auhtors
	 *
	 * @return array|bool
	 * @author  Léo Fontin
	 *
	 * @since   0.0.1
	 */
	public function seokey_sitemap_get_authors( $lang ) {
        // Get all published post from $lang
        $args = array(
            'post_type'      => 'post', // replace with your post type
            'post_status'    => 'publish', // only retrieve published posts
            'lang'           => seokey_helper_cache_data('languages')['lang'][$lang]['iso2'],

        );
        $query      = new WP_Query( $args );
        $author_ids = array();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $author_ids[] = get_the_author_meta( 'ID' );
            }
        }
        wp_reset_postdata();
		unset( $query );
        // Retrieve the users with the given author IDs
        $users_datas    = get_users( array( 'include' => $author_ids ) );
        $users          = [];
		if ( ! empty( $users_datas ) ) {
			foreach ( $users_datas as $user ) {
				// If user is a least a contributor
				// And he has at least publish one post
				if ( $user->has_cap( 'edit_posts' ) && count_user_posts( $user->ID ) >= 1 ) {
				    // Check if this is a private author
				    if ( 1 != get_user_meta( $user->ID, 'seokey-content_visibility', true ) ) {
                        $users[] = $user;
                    }
				}
			}
		}
        unset( $users_datas );
		return ( ! empty( $users ) ) ? $users : false;
	}

	/**
	 * Génère le site map index
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_generate_index( $type, $lang ) {
        // Delete OLD file
		if ( 'all' === $type ) {
			$this->seokey_sitemap_delete_old();
		}
		// Start defining our futur sitemap list (needed to delete all of them if needs be)
		$current_list = get_option('seokey_option_sitemap_list');
		if ( false === $current_list ) {
			$current_list= [];
		}
		// Get all files
		$files = $this->seokey_sitemap_get_files( $lang );
		$default_last_mod =  $this->seokey_sitemap_get_formated_current_time();
		$index_datas = [];
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				// DO not include Index in the Index
				if ( $file['type'] !== 'index' ) {
					$lastmod = ( ! empty( $file['date'] ) ) ? $file['date'] : $default_last_mod;
					$index_datas[] = [
						'loc'     => $file['url'],
						'lastmod' => $lastmod
					];
					$current_list[] = $file['file'];
				}
			}
		}
		$current_list[] = 'sitemap-index-'.$lang.'.xml';
		update_option ( 'seokey_option_sitemap_list', array_unique( $current_list ), false );
		// Generate Index code and generate Sitemap
		$code = $this->seokey_sitemap_get_code( $index_datas, $lang, 'index' );
		$this->seokey_sitemap_output( $code, 'sitemap-index-'.$lang.'.xml', $lang );
		// create a new robots.txt file
		seokey_helper_files( 'delete', 'robots' );
		seokey_helper_files( 'create', 'robots' );
	}

	/**
	 * Get current formate date
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_get_formated_current_time( $date = '' ) {
		$immutable_date = date_create_immutable_from_format( 'Y-m-d H:i:s', current_time('Y-m-d H:i:s'), new DateTimeZone( 'UTC' ) );
		$format = DATE_W3C;
		return $immutable_date->format( $format );
	}


	/**
	 * Get sitemap list with relative data
	 *
	 * @return array
	 * @author  Léo Fontin
	 *
	 * @since   0.0.1
	 */
	public function seokey_sitemap_get_files( $lang = null ) {
        if ( is_null ( $lang ) ){
            $check = -4;
            $substr = ".xml";
        } else {
            $check = -7;
            $substr = $lang.".xml";
        }
        // Is it a sitemap ?
        $pattern = '#^sitemap-([a-zA-Z0-9-]+).xml$#';
        $files = [];
		// Create sitemap Folder if necessary
		seokey_helper_create_folder( SEOKEY_SITEMAPS_PATH, true );
        // let's find current file
        if ( $handle = opendir( SEOKEY_SITEMAPS_PATH  ) ) {
            while ( false !== ( $file = readdir( $handle ) ) ) {
                // THis is a sitemap
               // if ( preg_match( $pattern, $file )  ) {
                if ( preg_match( $pattern, $file ) && substr( $file, $check ) == $substr ) {
                    // Path
                    $path = SEOKEY_SITEMAPS_PATH . $file;
                    if ( file_exists( $path ) ) {
                        // URL
                        // TODO GOV Test sur Seokey
                        $url = seokey_helpers_get_sitemap_base_url( $lang ) . $file;
                        // Get file data
                        //preg_match_all( $pattern, $file, $match, PREG_PATTERN_ORDER );
                        $types = explode( "-", $file );
                        $type = $types[1];
                        $date = filectime( $path );
                        $date = date( 'Y-m-d H:i:s', $date );
                        $date = get_date_from_gmt( $date, 'Y-m-d H:i:s' );
                        $files[] = [
                            'path' => $path,
                            'url'  => $url,
                            'file' => $file,
                            'type' => $type,
                            'date' => $date
                        ];
                    }
                }
            }
            closedir( $handle );
        }
		return $files;
	}

	/**
	 * Get XML header and footer
	 *
	 * @param $type
	 *
	 * @return array|void
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_get_sitemap_base( $type, $lang ) {
		// Define encoding and style
		$doctype = '<?xml version="1.0" encoding="UTF-8"?>';
		$doctype .= '<?xml-stylesheet type="text/xsl" href="' . seokey_helpers_get_sitemap_base_url( $lang, true ).'sitemap-seokey-render-'.$lang.'.xsl' .'"?>';
		// Data for images
		$images  = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd"';
		switch ( $type ) {
			case 'url':
				return [
					'header' => $doctype . "\n" . '<urlset ' . $images . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n",
					'footer' => '</urlset>' . "\n",
					'balise' => 'url'
				];
				break;
			case 'index':
				return [
					'header' => $doctype . "\n" . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n",
					'footer' => '</sitemapindex>' . "\n",
					'balise' => 'sitemap'
				];
				break;
		}
	}

	/**
	 * Generate Sitemap Code
	 *
	 * @param        $datas
	 * @param string $type
	 *
	 * @return mixed|string
	 * @author  Léo Fontin
	 *
	 * @since   0.0.1
	 */
	public function seokey_sitemap_get_code( $datas, $lang, $type = 'url' ) {
		// Get sitemap codebase
		$base = $this->seokey_sitemap_get_sitemap_base( $type, $lang );
		// Sitemap header
		$sitemap = $base['header'];
		// Foreach sitemap item
		if ( ! empty( $datas ) ) {
			foreach ( $datas as $data ) {
				$sitemap .= "\t" . '<' . $base['balise'] . '>' . "\n";
				if ( ! empty( esc_url( $data['loc'] ) ) ) {
					$sitemap .= "\t\t" . '<loc>' . esc_url( $data['loc'] ) . '</loc>' . "\n";
				}
				if ( ! empty( $data['lastmod'] ) ) {
					$sitemap .= "\t\t" . '<lastmod>' . str_replace(' ', 'T', $data['lastmod'] ) . '+00:00</lastmod>' . "\n";
				}
				// Images
				if ( ! empty( $data['images'] ) ) {
					foreach ( $data['images'] as $image ) {
						$imageloc = $this->seokey_sitemap_images_clean_URL( $image['loc'] );
						if ( ! empty( $imageloc ) ) {
							$sitemap .= "\t\t" . '<image:image>' . "\n";
							$sitemap .= "\t\t\t" . '<image:loc>' . $imageloc . '</image:loc>' . "\n";
							$imagetitle = esc_xml( $image['title'] );
							if ( ! empty( $imagetitle ) ) {
								$sitemap .= "\t\t\t" . '<image:title><![CDATA[' . $imagetitle . ']]></image:title>' . "\n";
							}
							if ( !empty( $image['caption'] ) ) {
								$imagecaption = esc_xml( $image['caption'] );
								if ( ! empty( $imagecaption ) ) {
									$sitemap .= "\t\t\t" . '<image:caption><![CDATA[' . $imagecaption . ']]></image:caption>' . "\n";
								}
							}
							$sitemap .= "\t\t" . '</image:image>' . "\n";
						}
					}
				}
				$sitemap .= "\t" . '</' . $base['balise'] . '>' . "\n";
			}
		}
		// Sitemap end
		$sitemap .= $base['footer'];
		$sitemap .= '<!-- Sitemap generated by SeoKey -->';
        // Return data
		return $sitemap;
	}

	/**
	 * Create all necessary files
	 *
	 * @param $code
	 * @param $file_name
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 *
	 */
	public function seokey_sitemap_output( $code, $file_name, $lang ) {
		$this->seokey_sitemap_output_xsl_file( $file_name, $lang );
		$this->seokey_sitemap_output_file( $code, $file_name );
        // Flush rewrites rules
        flush_rewrite_rules();
		// TODO Later fallback with rewrite rules
        // $this->seokey_sitemap_output_bdd( $code, $file_name );
	}


	/**
	 * Output final files
	 *
	 * @param $code
	 * @param $file_name
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_output_file( $code, $file_name ) {
		// Create sitemap Folder if necessary
		seokey_helper_create_folder( SEOKEY_SITEMAPS_PATH, true );
		// Path
		$file = SEOKEY_SITEMAPS_PATH . $file_name;
		// Open file (create it if not here)
		$file_open = @fopen( $file, 'w' );
		// Insert data
		@fwrite( $file_open, $code );
		// Close file
		@fclose( $file_open );
		// Add file to sitemap file list
		$this->seokey_sitemap_set_generated_files( $file_name );
	}

	/**
	 * Create XSL file (allow user to have a "beautiful" sitemap
	 *
	 * @param $code
	 * @param $file_name
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_output_xsl_file( $file_name, $lang ) {
		// Create sitemap Folder if necessary
		seokey_helper_create_folder( SEOKEY_SITEMAPS_PATH, true );
		// TODO use WordPress file class
		// Path
        $file = SEOKEY_SITEMAPS_PATH . 'sitemap-seokey-render-'.$lang.'.xsl';
        // Open file (create it if not here
        $file_open = @fopen( $file, 'w' );
        // Insert data
        require_once( dirname( __file__ ) . '/sitemaps-xsl.php' );
        $code = seokey_sitemap_output_xsl_file_content( $lang );
        @fwrite( $file_open, $code );
        // Close file
        @fclose( $file_open );
        // Add file to sitemap file list
        $this->seokey_sitemap_set_generated_files( $file_name );
	}

	/**
	 * Add each sitemap as an option for future rewrite rule
	 *
	 * @param $code
	 * @param $file_name
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_output_bdd( $code, $file_name ) {
		// Enregistrement des sites map en option //
		$file_name   = explode( '.', $file_name );
		$file_name   = $file_name[0];
		$option_name = 'seokey_' . str_replace( '-', '_', $file_name );
		update_option( $option_name, $code, false );
		// Création des réécriture d'url //
		// add_rewrite_rule( $file_name . '\.xml$', 'index.php?seokey-sitemap=' . $file_name, 'top' );
		// flush_rewrite_rules( false );
	}

	/**
	 * Delete unused sitemaps
	 *
	 * @since   0.0.1
	 * @author  Léo Fontin
	 */
	public function seokey_sitemap_delete_old() {
        // Get files list
		$files = $this->seokey_sitemap_get_files();
		// If file isn't in our sitemap list, delete it
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				if ( ! in_array( $file['file'], $this->generated_files ) ) {
					if ( file_exists( $file['path'] ) ) {
						unlink( $file['path'] );
					}
				}
			}
		}
	}

	/**
	 * Add generated files to array
	 *
	 * @param $file_name
	 *
	 * @author  Léo Fontin
	 * @since   0.0.1
	 */
	public function seokey_sitemap_set_generated_files( $file_name ) {
		$this->generated_files[] = $file_name;
	}

	// TODO Comment
	public function seokey_sitemap_images_clean_URL( $url ) {
		$url = htmlspecialchars( $url, ENT_COMPAT, 'UTF-8', false );
		$url = iconv( 'utf-8', 'latin1', utf8_encode( $url ) );
		$url = esc_url( $url );
		$url = str_replace( '&#038;', '&amp;', $url );
		return $url;
	}
}

add_filter('mod_rewrite_rules', 'seokey_sitemap_output_htaccess' );
/**
 * Add X-robots header noindex to physical xml files
 *
 * @since   0.0.1
 * @author  Léo Fontin
 */
function seokey_sitemap_output_htaccess( $rules ) {
    $new_rules = <<<EOD

# BEGIN SEOKEY
<IfModule mod_headers.c>
<files sitemap*.xml>
Header set X-Robots-Tag "noindex,follow"
</files>
</IfModule>
# END SEOKEY

EOD;
    return $rules . $new_rules;
}
