<?php
/**
 * Create schema.org scripts
 *
 * @Loaded on plugins_loaded
 * @excluded from admin pages
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

add_filter( 'post_class', 'seokey_schema_hentry_remove' );
/**
 * Remove bad hentry from post_class
 *
 * @param $classes array An array of post classes.
 * @return $classes array An array of post classes wihin hentry.
 */
function seokey_schema_hentry_remove( $classes ) {
	$classes = array_diff( $classes, array( 'hentry' ) );
	return $classes;
}

/**
 * Schema.org core class
 */
class seokey_Schema_Org {
	/**
	 * @var    (object) $instance Singleton
	 * @access public
	 * @static
	 */
	public static $instance = null;

	// Get only one instance
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construct
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function __construct() {
		// Display Schema.org data with our footer action
		add_action( 'seokey_action_footer', [ $this, 'seokey_schema_org_settings'], SEOKEY_PHP_INT_MAX );
	}

	/**
	 * Create each
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_schema_org_settings() {
		// Récupération de l'option context du schema
		$settings_option = seokey_helper_get_option( 'schemaorg-context' );
		if ( ! empty( $settings_option ) ) {
			switch ( $settings_option ) {
				case 'person':
					// Do not add twice "person" markup on user pages
					if ( !is_author() ) {
						// Global user markup
						$this->seokey_schema_org_person();
					}
					break;
				case 'local_business':
					 $this->seokey_schema_org_local_business();
					break;
			}
		}
		if ( is_author() ) {
			// Individual user markup
			$this->seokey_schema_org_user_person();
		}
		// Breadcrumbs
		$this->seokey_schema_org_breadcrumbs();
		// Potential actions
        $this->seokey_schema_org_actions();
	}


	/**
	 * Create schema.org person data (for whole website)
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_schema_org_person() {
		// Get data
		$person_options     = seokey_helper_get_option( 'schemaorg-schema-person' );
		$image_option       = seokey_helper_get_option( 'schemaorg-schema-person-image' );
		$birthdate_option   = seokey_helper_get_option( 'schemaorg-schema-person-birthdate' );
		$sameas_option      = seokey_helper_get_option( 'schemaorg-schema-person-sameas' );
		// Data type
		$datas['@type'] = 'Person';
		// Name
		if ( ! empty( $person_options['name'] ) ) {
			$datas['name'] = esc_html( $person_options['name'] );
		}
		// URL
        $datas['url'] = home_url();
		// Image
		if ( ! empty( $image_option ) ) {
			$datas['image'] = esc_url( $image_option );
		}
		// Birthdate
		if ( ! empty( $birthdate_option ) ) {
			$datas['birthdate'] = esc_html( $birthdate_option );
		}
		// Company
		if ( ! empty( $person_options['worksFor'] ) ) {
			$datas['worksFor'] = [
				'@type' => 'Organization',
				'name'  => esc_html( $person_options['worksFor'] )
			];
		}
		// Jobtitle
		if ( ! empty( $person_options['jobTitle'] ) ) {
			$datas['jobTitle'] = esc_html( $person_options['jobTitle'] );
		}
		// Others websites
		if ( ! empty( $sameas_option ) ) {
			$datas['sameas'] = [];
			foreach ( $sameas_option as $url ) {
				$url_escaped = esc_url( $url );
				array_push($datas['sameas'], $url_escaped );
			}
		}
		$this->seokey_schema_org_display( $datas );
	}


	/**
	 * Create schema.org person data (for each user)
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_schema_org_user_person() {
		// Get user ID and raw datas
		$user_id = get_query_var('author');
		$user_raw_data      = get_userdata( $user_id );
		$user_raw_meta_data = get_user_meta( $user_id );
		// Data type
		$datas['@type'] = 'Person';
		// Name
		$datas['name'] = esc_html( $user_raw_data->display_name );
		// Family name and GivenName
		if ( ! empty( $user_raw_meta_data->last_name ) ) {
			$datas['familyName'] = esc_html( $user_raw_meta_data->last_name );
		}
		if ( ! empty( $user_raw_meta_data->first_name ) ) {
			$datas['givenName'] = esc_html( $user_raw_meta_data->first_name );
		}
		// URL
		if ( ! empty( $user_raw_data->user_url ) ) {
			$datas['url'] = esc_url( $user_raw_data->user_url );
		}
		// TODO Gender
		// Image
		$avatar_url = get_avatar_url( $user_id );
		if ( false !== $avatar_url ) {
			$datas['image'] = esc_url( $avatar_url );
		}
		// Works for
		$seo_user_metas = ( isset ( $user_raw_meta_data['seokey_usermetas'][0] ) ) ? unserialize( $user_raw_meta_data['seokey_usermetas'][0] ) : '';
		if ( '' !== $seo_user_metas ) {
			if ( ! empty( $seo_user_metas['company'] ) ) {
				$datas['worksFor'] = [
					'@type' => 'Organization',
					'name'  => esc_html( $seo_user_metas['company'] )
				];
			}
		}
		//Birthdate
		if ( ! empty( $seo_user_metas['birthdate'] ) ) {
			$datas['birthdate'] = esc_html( $seo_user_metas['birthdate'] );
		}
		// Jobtitle
		if ( ! empty( $seo_user_metas['jobTitle'] ) ) {
			$datas['jobTitle'] = esc_html( $seo_user_metas['jobTitle'] );
		}
		// TODO Others websites
		$this->seokey_schema_org_display( $datas );
	}

	/**
	 * Créé le schema pour un type Local Business
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 */
	public function seokey_schema_org_local_business() {
		$local_business_options     = seokey_helper_get_option( 'schemaorg-schema-local_business' );
		$local_business_is_store    = seokey_helper_get_option( 'schemaorg-schema-local_business-is-store' );
		$image_option               = seokey_helper_get_option( 'schemaorg-schema-local_business-image' );
		$type = 'Organization';
		if ( $local_business_is_store == 1 ) {
			$type = 'LocalBusiness';
		}
		$datas = [
			'@type' => $type
		];
		if ( ! empty( $local_business_options['name'] ) ) {
			$datas['name'] = esc_html( $local_business_options['name'] );
		}
        $datas['url'] = home_url();
		if ( ! empty( $image_option ) ) {
			$datas['logo'] = esc_url( $image_option );
		}
		if ( ! empty( $image_option ) ) {
			$datas['image'] = esc_url( $image_option );
		}
		$datas['address'] = [
			'@type' => 'PostalAddress'
		];
		if ( ! empty( $local_business_options['streetAddress'] ) ) {
			$datas['address']['streetAddress'] = esc_html( $local_business_options['streetAddress'] );
		}
		if ( ! empty( $local_business_options['addressLocality'] ) ) {
			$datas['address']['addressLocality'] = esc_html( $local_business_options['addressLocality'] );
		}
		if ( ! empty( $local_business_options['postalCode'] ) ) {
			$datas['address']['postalCode'] = esc_html( $local_business_options['postalCode'] );
		}
		if ( ! empty( $local_business_options['addressCountry'] ) ) {
			$datas['address']['addressCountry'] = esc_html( $local_business_options['addressCountry'] );
		}
		if ( ! empty( $local_business_options['telephone'] ) ) {
			$datas['telephone'] = esc_html( $local_business_options['telephone'] );
		}
		if ( $local_business_is_store == 1 ) {
			$local_business_pricing     = seokey_helper_get_option( 'schemaorg-schema-local_business-pricing' );
			if ( ! empty( $local_business_pricing['pricerangemin'] ) || ! empty( $local_business_pricing['pricerangeax'] ) ) {
				$datas['priceRange'] = (int) $local_business_pricing['pricerangemin'] . '-' . $local_business_pricing['pricerangemax'] . '€';
			}
			$opening_hours = seokey_helper_get_option( 'schemaorg-schema-local_business-openinghoursspecification' );
			if ( ! empty( $opening_hours ) ) {
				foreach ( $opening_hours as $k => $opening_hour ) {
					if ( ! empty( $opening_hour['dayOfWeek'] ) ) {
					    // Get our days
                        $days = array();
						foreach ( $opening_hour['dayOfWeek'] as $day ) {
                            $days[] = substr( esc_html( $day ), 0, 2 );
                        }
                        $days = implode( ',', $days );
						$datas['openingHoursSpecification'][ $k ] = [
							'@type'     => 'OpeningHoursSpecification',
							'dayOfWeek' => $days,
						];
						// Add hours
						if ( !empty( $opening_hour['opens'] ) ) {
                            $datas['openingHoursSpecification'][ $k ]['opens'] = esc_xml( $opening_hour['opens'] );
						}
						if ( !empty( $opening_hour['closes'] ) ) {
							$datas['openingHoursSpecification'][ $k ]['closes'] = esc_xml( $opening_hour['closes'] );
						}
					}
				}
			}
		}
		// TODO Others websites
		$this->seokey_schema_org_display( $datas );
	}

	// TODO Comments
	public function seokey_schema_org_breadcrumbs() {
		// Define base data
		$breadcrumb     = seokey_breacrumbs_data();
		$datas['@type'] = 'BreadcrumbList';
		$datas['@name'] = get_bloginfo( 'name' );
		// Iterate
		if ( ! empty( $breadcrumb ) ) {
			$datas['itemListElement'] = [];
			foreach ( $breadcrumb as $item ) {
				$itemtoadd = [
					'@type'     => 'ListItem',
					'position'  => $item['position'],
					'item'      => [
						'@type' => 'Thing',
						'@id'   => esc_url( $item['url'] ),
						'url'   => esc_url( $item['url'] ),
						'name'  => esc_html( $item['name'] ),
					],
				];
				array_push( $datas['itemListElement'], $itemtoadd );
			}
		}
		$this->seokey_schema_org_display( $datas );
	}
	
	
	/**
	 * Add SearchAction schema.org
	 *
	 * @since   0.0.1
	 * @author  Daniel Roch
	 *
	 * @param $datas
	 */
	public function seokey_schema_org_actions() {
		$datas['@type'] = 'WebSite';
		$datas['@url'] = home_url();
		$datas['@potentialAction'] = [
			'@type'     => 'SearchAction',
			'target'    => [
				'@type'     => 'EntryPoint',
				'urlTemplate' => home_url( '?s=' ) . '{search_term_string}',
			],
			'query-input' => "required name=search_term_string"
		];
		$this->seokey_schema_org_display( $datas );
	}
	
	/**
	 * Add data to footer
	 *
	 * @since   0.0.1
	 * @author  Leo Fontin
	 *
	 * @param $datas
	 */
	public function seokey_schema_org_display( $datas ) {
		// No data, abord
		if ( empty( $datas ) ) {
			return;
		}
		// Add context if empty
		if ( empty( $datas['@context'] ) ) {
			$datas = array_merge( [ "@context" => "https://schema.org/" ], $datas );
		}
		// Security
		// TODO garder ?
		//array_walk_recursive( $datas, function ( &$item ) {
		//	$item = esc_html( $item );
		//} );
		$json = json_encode( $datas );
		// Display data
		echo '<script type="application/ld+json">'."\n";
			echo $json;
		echo "\n".'</script>'."\n";
	}
}

// Launch class
$schema = seokey_schema_org::get_instance();