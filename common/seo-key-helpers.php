<?php
/**
 * Load every SEOKEY common functions and helpers
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 *
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

// Msssages helpers
require SEOKEY_PATH_COMMON . 'seo-key-helpers-help-messages.php';

// Dev helpers functions
require SEOKEY_PATH_COMMON . 'seo-key-helpers-dev.php';

// Singleton CLASS
require SEOKEY_PATH_COMMON . 'class-singleton.php';

// Countries iso
require SEOKEY_PATH_COMMON . 'seo-key-helpers-data-countryiso.php';

// Metas helpers functions
require SEOKEY_PATH_COMMON . 'seo-key-helpers-metas.php';

// Robots.txt helpers functions
require SEOKEY_PATH_COMMON . 'seo-key-helpers-robotstxt.php';

// Googlebot functions
require SEOKEY_PATH_COMMON . 'seo-key-helpers-googlebot.php';

// WP Background Processing Class
require SEOKEY_PATH_COMMON . 'wp_background_processing.php';

/**
 * Return a SEOKEY option
 *
 * No need of a filter here, the WP one is enough
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @param string $option_name Concatenation of "pagename-sectionname-fieldname"
 * @param bool $default_value
 * @return bool|mixed|void (variant)
 */
function seokey_helper_get_option( $option_name, $default_value = false ) {
	$default_value = get_option( 'seokey-field-' . $option_name, $default_value );
	return $default_value;
}

/**
 * Find if current page is local host
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @param array $whitelist Local Host list
 * @param bool $default_value
 * @return bool true if Local host
 */
function seokey_helper_isLocalhost( $whitelist = ['127.0.0.1', '::1'] ) {
	$remote_addr = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	return in_array( $remote_addr, $whitelist );
}

/**
 * Create folder if not exists & parent folder if not exist
 *
 * @author Gauvain
 * @since  1.6.0
 *
 */
function seokey_helper_create_folder( $path , $indexFile = false ){
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $filesystem = seokey_helper_filesystem();
    $parent_path = dirname( $path );
	if( is_null( $filesystem ) ) {
		return;
	} else {
		if ( ! $filesystem->exists( $parent_path ) ) {
			if ( $filesystem->mkdir( $parent_path, 0755 ) ) {
				// add index.php
				if ( true === $indexFile ) {
					seokey_helper_create_index_file( $parent_path );
				}
			} else {
				seokey_helper_create_folder( $parent_path, $indexFile );
			}
		}
		if ( ! $filesystem->exists( $path ) ) {
			if ( $filesystem->mkdir( $path, 0755 ) ) {
				if ( true === $indexFile ) {
					seokey_helper_create_index_file( $path );
				}
			} else {
				seokey_dev_write_log( 'Error creating folder : ' . $path );
			}
		}
	}
}


/**
 * Delete folder & all files inside
 *
 * @param string $path
 * @author Gauvain Van Ghele
 * @since  1.6.0
 *
 */
function seokey_helper_delete_folder( $path ){
    $filesystem = seokey_helper_filesystem();
    if( is_null( $filesystem ) ){
        seokey_dev_write_log('Object filesystem is Null while deleting folder : '. $path );
    }else {
        if ($filesystem->exists($path)) {
            $filesystem->delete($path, true);
        }
    }
    return;
}

/**
 * Create empty index.php in a folder
 *
 * @param string $directory
 * @author Gauvain
 * @since  1.6.0
 *
 */
function seokey_helper_create_index_file( $directory ){
    $filesystem = seokey_helper_filesystem();
    if( is_null( $filesystem ) ){
        seokey_dev_write_log('Object filesystem is Null while creating index file in : '. $directory );
    }else {
        $content = $filesystem->get_contents(SEOKEY_PATH_COMMON . 'index.phps');
        $file = $directory . "/index.php";

        // We check if directory is here
        if ( ! $filesystem->is_dir( $directory ) ) {
            $filesystem->mkdir( $directory );
        }
        // Don't do anything if directory is still not here or file exists
        if ( $filesystem->exists( $file ) || ! $filesystem->is_dir( $directory ) ) {
            return false;
        }
        // Create our new file
        return $filesystem->put_contents($file, utf8_decode($content));
    }
}

/**
 * Create file if necessary
 *
 * @notes: seokey_helper_require_file( 'module_slug', 'path', 'role', ['setting-name' => 'value'] );
 * "module_slug" is the file name (whitout .php) we want to include
 * "Path" is realpath to file, default to SEOKEY_PATH_PUBLIC
 * "Role" should use values from seokey_helper_user_get_capability(), default to everyone ('admin')
 * "setting-name" will be loaded using seokey_helper_get_option() => it allows us to include file only if an option has a specific value.
 * "setting-name" value can start with "!" to reverse the effect, the array can contains any condition number
 *
 * @since  0.0.1
 * @author Julio Potier, Daniel Roch
 *
 * @param string $module_slug Which module (filename without .php) you want to include
 * @param string $path Path of the module (default to Public-path/modules)
 * @param string $role Which role need this module ? Default to admin
 * @param array $conditions Do we need specific option value to trigger this module ?
 * @return void
 *
 */
function seokey_helper_require_file( $module_slug, $path = ( SEOKEY_PATH_PUBLIC . 'modules/' ), $role = 'admin', $conditions = [] ) {
	// Security : do not allow $path to move in other directories
	if ( true == seokey_helper_isLocalhost() ) {
		$path = str_replace( '..', '', $path );
	} else {
		$path = esc_url( str_replace( '..', '', $path ) );
	}
	// File is not here, abort
	if ( ! file_exists( $path . $module_slug . '.php' ) ) {
		seokey_dev_error( '', '', $path . $module_slug . '.php');
		return;
	}
	// Do not allow require before our verifications (default value)
	$allowed = false;
	// User is allowed ?
	if ( 'everyone' === $role || current_user_can( seokey_helper_user_get_capability( $role ) ) ) {
		// If there is no condition, always require the module
        if ( empty( $conditions ) ) {
			$allowed = true;
		} else {
			// Check each condition
			foreach ( $conditions as $key => $_value ) {
				// Remove the possible "!" from the value
				$value = str_replace( '!', '', $_value );
				// If "!" was present, "reserve" the effect of the condition
				$reverse = $_value !== $value;
				// If the condition is not ok
				if ( get_option( $key ) !== $value ) {
					// Do not allow (or do it with reverse (^=))
					$allowed = $reverse ^= false;
					break;
				} else {
					// Allow (or do not do it with reverse (^=))
					$allowed = $reverse ^= true;
				}
			}
		}
	}
	// Finally allowed, require the module
	if ( $allowed ) {
	    // Require file
	    $url = $path . $module_slug . '.php';
		require_once( $url );
	}
}

/**
 * Create and return a SEOKEY admin link
 *
 * @notes: Useful function to get an admin bar link or an admin page link
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @param string $page
 * @return string Admin page URL
 */
function seokey_helper_admin_get_link( $page = '', $base = 'admin.php?page=' ) {
	// If $page is not defined, it's our main menu page so we will just use our plugin slug
	// otherwise, we define the correct slug for this specific menu
	if ( 'admin.php?page=' === $base ) {
		$slug = ! empty( $page ) ? SEOKEY_SLUG . '-' . $page : SEOKEY_SLUG;
	} else {
		$slug = $page;
	}
	// Return admin URL with our parameter
	return esc_url( admin_url( $base . $slug ) );
}

/**
 * Get Current user URL
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see     https://wordpress.org/plugins/sf-move-login/ (some code here is inspired from the "Move Login" WordPress plugin : it's a plugin worth checking)
 * @see     is_ssl()
 *
 * @return string User current URL
 */
function seokey_helper_url_get_current( $port = true, $cache = true ) {
	// Caching
	$data = seokey_helper_cache_data ('seokey_helper_url_get_current' );
	if ( NULL === $data || false === $cache ) {
		if ( false === $port ) {
			$port = '';
		} else {
			// Get port Data if necessary
			$portused = isset( $_SERVER['SERVER_PORT'] ) ? (int) $_SERVER['SERVER_PORT'] : '';
			$port = ( 80 !== $portused && 443 !== $portused ) ? ( ':' . $portused ) : '';
		}
		// Get Request URI
		$uri = ! empty( $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] ) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : '';
		$uri = empty( $uri ) ? $_SERVER['REQUEST_URI'] : '';
		$domain = $_SERVER['HTTP_HOST'];
		if ( true === str_contains( $_SERVER['HTTP_HOST'], ':' ) ) {
			$domain = strstr( $domain, ':', true );
		}
		// Get final URL
		$currenturl = esc_url( 'http' . ( is_ssl() ? 's' : '' ) . '://' . $domain . $port . $uri );
		// Do some cache
		if ( true === $cache ) {
			seokey_helper_cache_data ('seokey_helper_url_get_current', $currenturl  );
		}
		// return final URL
		return $currenturl;
	}
	return $data;
}

/**
 * Helper function : does this begins with ?
 *
 * @param string $string
 * @param string $check string to check
 *
 * @return bool (string) true or false
 * @author  Daniel Roch
 *
 * @since   1.0
 */
if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( $haystack, $needle ) {
		return strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
	}
}

/**
 * str_contains
 * Polyfill for PHP version before 8
 * based on original work from the PHP Laravel framework
 */
if ( !function_exists( 'str_contains' ) )  {
	function str_contains( $haystack, $needle ) {
		return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
	}
}

/**
 * Helper function : dos this end with ?
 *
 * @since   1.0
 * @author  Daniel Roch
 *
 * @return bool true or false
 */
if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with($haystack, $needle) {
		return substr( $haystack, -strlen( $needle ) ) === (string)
			$needle;
	}
}

/**
 * Returns a page setting slug without the SEOKEY part
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @param string $dirty The slug page
 * @return string The setting slug without the SEOKEY_SLUG one
 **/
function seokey_helper_url_get_clean_plugin_slug( $dirty ) {
	return str_replace( SEOKEY_SLUG . '-', '', $dirty );
}

/**
 * Check if current URL is a sitemap or not
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @return bool true or false
 **/
function seokey_helper_is_sitemap() {
	// Home URL
	$home = trailingslashit( apply_filters( 'seokey_filter_home_url', home_url() ) );
	// Core Sitemap URL start
	$homesitemap    = $home . 'wp-sitemap';
	$customsitemaps = $home . 'sitemap';
	// Get current URL
	$current_url = seokey_helper_url_get_current();
	// Does current URL begins with wp-sitemap ?
	if ( true === str_starts_with( $current_url, $homesitemap ) || true === str_starts_with( $current_url, $customsitemaps ) ) {
		// Does it ends with .xml
		if ( true === str_ends_with( $current_url, '.xml' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Get SQL Collation
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @global $wpdb
 * @return string the actual WordPress collation for SQL table creation
 **/
function seokey_helper_sql_collation() {
	// Get database global
	global $wpdb;
	// Find and define SQL collation
	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}
	return $collate;
}

/**
 * Return the actual pagination
 *
 * @author Julio Potier
 * @since  0.0.1
 *
 * @return int The current pagination number
 */
function seokey_helper_get_paged() {
	// Get raw pagination data
	global $paged, $page;
	$pagination = max( $paged, $page );
	// Not a paginated URL => pagination is 1
	if ( $pagination < 1 ) {
		$pagination = 1;
	}
	// Return data
	return (int) $pagination;
}

/**
 * Return paginated URL
 *
 * @author Daniel ROCH
 * @since  1.6.0
 *
 * @param string $url Base URL
 * @return string $url Paginated URL
 */
function seokey_helper_get_paginated_url( $url, $type = 'term' ) {
	// Get pagination variables
	$pagenum = seokey_helper_get_paged();
	// Do we have pagination ?
	if ( $pagenum > 1 ) {
		// Ensure URl has a trailingslash
		$base = trailingslashit( $url );
		// For terms, get pagination_base rewrite structure (no need for paginated posts)
		if ( 'term' === $type ) {
			global $wp_rewrite;
			$pagination_base = $wp_rewrite->pagination_base;
			$base .= $pagination_base . '/';
		}
		$url = $base . $pagenum . '/';
	}
	// Return cleaned URL
	return esc_url( $url );
}

/**
 * Create a file for our plugin
 *
 * @author  Daniel Roch
 * @since 0.0.1
 *
 * @param string $action Actions for this function : create file (default) or delete it
 * @param string $what Name of the file
 * @return bool|void True on success, false on failure, void if security issues
 */
function seokey_helper_files( $action = 'create', $what = '' ) {
    // security
    if ( !current_user_can( seokey_helper_user_get_capability('admin') ) ) {
        return;
    }
	// Which file ?
	switch ( $what ) {
		case 'muplugin':
			// Mu-plugin Directory
			$directory = WPMU_PLUGIN_DIR;
			// Mu-plugin file URL
			$file = $directory . "/seokey-free-muplugin.php";
			// Get our futur content
			$content = file_get_contents( SEOKEY_PATH_COMMON . 'seo-key-helpers-muplugin.phps' );
			// What to do next
			$deleteold       = true;
			$createdirectory = true;
			break;
        case 'mupluginjs':
            // Mu-plugin Directory
            $directory = WPMU_PLUGIN_DIR;
            // Mu-plugin file URL
            $file = $directory . "/seokey-free-muplugin.js";
            // Get our futur content
            $content = file_get_contents( SEOKEY_PATH_COMMON . 'seo-key-helpers-mupluginjs.phps' );
            // What to do next
            $deleteold       = true;
            $createdirectory = true;
            break;
        case 'muplugincss':
            // Mu-plugin Directory
            $directory = WPMU_PLUGIN_DIR;
            // Mu-plugin file URL
            $file = $directory . "/seokey-free-muplugin.css";
            // Get our futur content
            $content = file_get_contents( SEOKEY_PATH_COMMON . 'seo-key-helpers-muplugincss.phps' );
            // What to do next
            $deleteold       = true;
            $createdirectory = true;
            break;
		case 'robots':
			// Directory
			$directory = ABSPATH;
			// Robots.txt file URL
			$file = $directory . "robots.txt";
			// Get our content
			$content = seokey_robots_txt_content();
			// What to do
			$deleteold       = 'checkfirst';
			$createdirectory = false;
			break;
		case 'robotsforce':
			// Directory
			$directory = ABSPATH;
			// Robots.txt file URL
			$file = $directory . "robots.txt";
			break;
		default:
			return;
	}
	// Get useful files functions
	$filesystem = seokey_helper_filesystem();
	// What do we need to do ?
	switch ( $action ) {
		case 'create':
			// File already here ?
			if ( file_exists( $file ) ) {
				// Delete it if necessary
				if ( true === $deleteold ) {
					$filesystem->delete( $file );
				}
				// Already here and this is our own file ? Delete old file.
				elseif ( 'checkfirst' === $deleteold ) {
					// Check current content
					$currentcontent    = file_get_contents( $file );
					$check_start = "# BEGIN SEOKEY Robots.txt file : DO NOT EDIT our rules";
					$check_end = "# END SEOKEY Robots.txt file (add your custom rules below)";
					if ( true == str_starts_with( $currentcontent, $check_start ) ) {
						// Does it ends with .xml
						if ( true === str_ends_with( $currentcontent, $check_end ) ) {
							$filesystem->delete( $file );
						}
					}
					// Delete it only if it is our file
					if ( seokey_robots_txt_content() === $currentcontent ) {
						$filesystem->delete( $file );
					}
				}
			}
			// We check if directory is here
			if ( ! file_exists( $directory ) && true === $createdirectory ) {
				$filesystem->mkdir( $directory );
			}
			// Don't do anything if directory is still not here or file exists
			if ( file_exists( $file ) || ! file_exists( $directory ) ) {
				return FALSE;
			}
			// Create our new file
			return $filesystem->put_contents( $file, utf8_decode( $content ) );
		case 'delete':
			// Delete old file !
			if ( file_exists( $file ) ) {
			    // Robots.txt specific case: only delete if user has not modified it
                if ( 'robots' === $what ) {
                    // Check current content
                    $currentcontent    = file_get_contents( $file );
                    if ( true === str_starts_with( $currentcontent, '# BEGIN SEOKEY Robots.txt file' ) &&
                         true === str_ends_with( $currentcontent, '# END SEOKEY Robots.txt file (add your custom rules below)' ) ) {
                        $filesystem->delete( $file );
                    }
                } elseif ( 'robotsforce' === $what ) {
					$filesystem->delete( $file );
				} else {
                    // For all other files: delete them!
                    $filesystem->delete( $file );
                }
			}
			break;
		default:
			break;
	}
}

/**
 * Get filesystem object
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @return void|WP_Filesystem_Direct $wp_filesystem object
 */
function seokey_helper_filesystem() {
	// security
	if ( ! current_user_can( seokey_helper_user_get_capability( 'admin' ) ) ) {
		return;
	}
	// Define our variable
	static $filesystem;
	// Already defined, end here
	if ( $filesystem ) {
		return $filesystem;
	}
	// require core files
	require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
	// Launch  Filesystem class
	$filesystem = new WP_Filesystem_Direct( new StdClass() ); // WPCS: override ok.
	// Set the permission constants if not already set.
	if ( ! defined( 'FS_CHMOD_DIR' ) ) {
		define( 'FS_CHMOD_DIR', ( @fileperms( ABSPATH ) & 0777 | 0755 ) );
	}
	if ( ! defined( 'FS_CHMOD_FILE' ) ) {
		define( 'FS_CHMOD_FILE', ( @fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
	}
	return $filesystem;
}

/**
 * Is this specific post private ?
 *
 * @author Daniel Roch
 * @since 0.0.1
 *
 * @return bool (bool) $status True if private, false if not
 */
function seokey_helper_post_is_private() {
	// Default to false : "this content is not private"
	$status = false;
	// Site is public, keep going !
	if ( true === get_option( 'blog_public' ) ) {
		// Post type globally private ?
		$global_checked = seokey_helper_is_global_checked( 'posts', get_post_type() );
		if ( true === $global_checked ) {
			return true;
		}
		// This specific $post is private ?
		if ( true === get_post_meta( get_the_ID(), 'seokey-content_visibility', true ) ) {
			return true;
		}
		// Is this post being published ? (post metas are not yet available when first publishing a $post, that's why we need to get our own cache value)
		if ( true === seokey_helper_cache_data( '_seokey_ping_temporary_data' ) ) {
			return true;
		}
	}
	// Return post status : private or not
	return $status;
}

/**
 * Delete starting or ending slashes in an URL
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @param string $url Current URl to strip slashes from
 * @param string $position Where do we need to remove slashes ? Default to "end", can be "start"
 * @return string
 */
function seokey_helper_url_remove_slashes( $url, $position = 'end' ) {
	if ( 'start' === $position || 'both' === $position ) {
		$url = ltrim( $url, '/' );
	}
	if ( 'end' === $position || 'both' === $position ) {
		$url = rtrim( $url, '/' );
	}
	return $url;
}

/**
 * Fallbacks for allow_url_fopen = 0
 */
function seokey_helpers_get_headers( $url ){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $headers = curl_exec($ch);
    curl_close($ch);
    $data = [];
    $headers = explode(PHP_EOL, $headers);
    foreach ($headers as $row) {
        $parts = explode(':', $row);
        if (count($parts) === 2) {
            $data[trim($parts[0])] = trim($parts[1]);
        }
    }
    return $data;
}

/**
 * strpos with an array
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @param string $haystack string to check
 * @param array $needle values used to check
 * @param integer $offset offset value for strpos function
 * @return string default to false, true if $haystack found in $needle array
 */
function seokey_helper_strpos_array( $haystack, $needle, $offset=0 ) {
	// Our data needs to be an array
    if ( ! is_array( $needle ) ) {
    	$needle = array( $needle );
   	}
    // Let's search
    foreach ( $needle as $query ) {
        if( false !== strpos( $haystack, $query, $offset ) ) {
        	// Return true for first needle found
	        return true;
	    }
    }
    // Return false if not found
    return false;
}

/**
 * array_key_first
 * Polyfill for php version before 7.3.0
 */
if ( ! function_exists( 'array_key_first') ) {
	function array_key_first( array $arr ) {
		foreach( $arr as $key => $unused ) {
			return $key;
		}
		return NULL;
	}
}

/**
 * Extract content from $post, without html, scripts or CSS, and using excerpt as fallback
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @param int $id
 * @return string Post content
 */
function seokey_helper_post_content_extract( $id = 0 ) {
	// get ID and define our content variable
	$content = '';
	$id      = (int) $id;
	// We have an ID? let's find our content
	if ( $id > 0 ) {
		$mypost = get_post( $id );
	} else {
		// No ID, we will try to find it anyway
		global $post;
		$mypost = $post;
	}
	if ( $mypost ) {
		// Get our default content
		$content = wp_strip_all_tags( apply_filters( 'the_content', $mypost->post_content ) );
		// Use excerpt if no content
		if ( ! $content ) {
			$content = get_the_excerpt( $mypost );
		}
	}
	return $content;
}

/**
 * Remove domain from URL : only returns paths and args
 *
 * @since   0.0.1
 * @author  Leo Fontin / Julio Potier
 *
 * @param string $url url to remove domain from
 * @return String URI
 */
function seokey_helper_url_remove_domain( $url ) {
	$parse_url = parse_url( $url );
	$url = ! empty( $parse_url['path'] ) ? $parse_url['path'] : '';
	$url .= ! empty( $parse_url['query'] ) ? '?' . $parse_url['query'] : '';
	$url .= ! empty( $parse_url['fragment'] ) ? '#' . $parse_url['fragment'] : '';
	return $url ?: '/';
}

/**
 * Get domain from string
 *
 * @since   0.0.1
 * @author  Daniel ROCH
 *
 * @param string $url string to get domain from
 * @return String domain and subdomain
 */
function seokey_helper_url_extract_domain( $url, $sub = false ) {
	$parse_url  = parse_url( $url );
	$host       = $parse_url['host'];
	preg_match( '/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i', $host, $subdomain );
	$host       = ( empty( $subdomain ) ) ? $host : str_replace( $subdomain[0], '', $host );
	$subdomain  = ( empty( $subdomain ) ) ? '' : $subdomain[0];
	$host       = ( true === $sub ) ? $subdomain . $host : $host;
	return $host;
}

/**
 * Get current Screen option
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @param string $type Option name
 * @param string $key Screen option key
 * @param string $default Default value if user has not set an option yet
 * @return string|bool|void|array $data Option value
 */
function seokey_helper_get_screen_option( $type, $key, $default ) {
    if ( ! isset( $type ) || ! isset( $key ) ) {
	    return null;
    }
    // All this for pagination purposes
    $user = get_current_user_id();
    // retrieve the value of the option stored for the current user
    $data = (int) get_user_meta( $user, $key, true );
    // If no data
    if ( empty ( $data) || $data < 1 ) {
        // Get the current admin screen
        $screen = seokey_helper_get_current_screen();
        // Fallback for ajax calls
        if ( is_null ( $screen ) ) {
            $data = $default;
        } else {
            // Retrieve the option value for this screen, or fallback to default
            $data = (!is_null($screen->get_option($type, $key))) ? $screen->get_option($type, $key) : (int)$default;
        }
    }
    return $data;
}

/**
 * Usort Reorder fallback
 *
 * @since   0.0.1
 * @author  Daniel Roch
 *
 * @note User seokey_helper_cache_data seokey_helper_usort_reorder to set the orderby method
 *
 * @param string $a
 * @param string $b
 * @return array|int sorted array
 */
function seokey_helper_usort_reorder( $a, $b ) {
    $orderby    = ( ! empty( $_REQUEST['orderby'] ) )   ? esc_html( strtolower( $_REQUEST['orderby'] ) ):   seokey_helper_cache_data( 'seokey_helper_usort_reorder');
    $order      = ( ! empty( $_REQUEST['order'] ) )     ? esc_html( $_REQUEST['order'] ) :                  seokey_helper_cache_data( 'seokey_helper_usort_reorder_order');
	$result     = strnatcmp( $a[ $orderby ], $b[ $orderby ] );
    return ( 'ASC' === strtoupper( $order ) ) ? $result : -$result;
}


/**
 * Check if current admin page is our post type archive menu
 *
 * @since   0.0.1
 * @author  Daniel Roch, Julio Potier
 *
 * @return bool|int true if currently on a post type archive menu
 */
function seokey_helpers_admin_is_post_type_archive() {
    // Are we on a custom post type archive page
    global $typenow;
    // Get the post type object
    $typenow_object = get_post_type_object( $typenow );
	// Check if current post type have an archive
	if ( isset( $typenow_object->has_archive ) ) {
    	$post_type_archive = $typenow_object->has_archive;
		// Filter for specific post types
		$post_type_archive = apply_filters( 'seokey_filter_helpers_admin_is_post_type_archive', $typenow_object );
		// return value
		return $post_type_archive;
    }
	return false;
}

/* Helper function */
function seokey_helpers_medias_library_is_alt_editor(){
	// Are we using media library ?
	global $pagenow;
	if ( 'upload.php' === $pagenow ) {
		$alteditor  = ( isset( $_GET['seokeyalteditor'] ) ) ? sanitize_title( $_GET['seokeyalteditor'] ) : FALSE;
		$mode       = ( isset( $_GET['mode'] ) ) ? sanitize_title($_GET['mode']) : '';
		// ?mode=list
		if ( 'grid' === $mode ) {
			return false;
		} else {
			if ( 'yes' === $alteditor ) {
				return true;
			}
			// Get current user option
			$meta = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
			// No ?mode, user meta is set to list
			if ( 'grid' === $meta ) {
				return false;
			}
		}
		// ALt editor parameter
		if ( 'yes' === $alteditor ) {
			return true;
		}
		// Custom filter use
		$altfilter = ( isset( $_GET['seokey-alt-filter'] ) ) ? 'yes' : FALSE;
		if ( 'yes' === $altfilter ) {
			return true;
		}
	}
    return false;
}

// TODO Comments
function seokey_helper_loader( $id = '', $class=' ') {
echo '<div id="' . sanitize_html_class( $id ) . '-loader" class="' . sanitize_html_class( $class ) . 'seokey-loader">
      <div class="seokey-spinner"></div>
    </div>
    ';
}

// TODO comments
function seokey_helper_suggestion_action( $id, $text, $placement = true, $data = '' ) {
	$render = '<div class="seokey-whattodo-text has-explanation ' . $id . '">' . $text . seokey_helper_help_messages( $id, $placement, $data ) . '</div>';
    return $render;
}

/**
 * Get cleaned rewrites rules
 *
 * @see https://wordpress.org/plugins/rewrite-rules-inspector/ for their useful code ($rewrite_rules_by_source)
 *
 * @since 0.0.1
 * @author  Daniel Roch
 */
function seokey_helper_parse_request_get_rules(){
	$cleaned_rules_by_source = seokey_helper_cache_data( 'seokey_cache_helper_parse_request_get_rules' );
	if ( null === $cleaned_rules_by_source ) {
		// TODO Cache (transient, CRON or someting else)
		global $wp_rewrite;
		// Track down which rewrite rules are associated with which methods by breaking it down.
		$rewrite_rules_by_source         = array();
		$rewrite_rules_by_source['post'] = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->permalink_structure, EP_PERMALINK );
		//	$rewrite_rules_by_source['date']     = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->get_date_permastruct(), EP_DATE );
		$rewrite_rules_by_source['root']     = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->root . '/', EP_ROOT );
		$rewrite_rules_by_source['comments'] = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->root . $wp_rewrite->comments_base, EP_COMMENTS, true, true, true, false );
		$rewrite_rules_by_source['search']   = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->get_search_permastruct(), EP_SEARCH );
		$rewrite_rules_by_source['author']   = $wp_rewrite->generate_rewrite_rules( $wp_rewrite->get_author_permastruct(), EP_AUTHORS );
		$rewrite_rules_by_source['page']     = $wp_rewrite->page_rewrite_rules();
		// Extra permastructs including tags, categories, etc.
		foreach ( $wp_rewrite->extra_permastructs as $permastructname => $permastruct ) {
			if ( is_array( $permastruct ) ) {
				$rewrite_rules_by_source[ $permastructname ] = $wp_rewrite->generate_rewrite_rules( $permastruct['struct'], $permastruct['ep_mask'], $permastruct['paged'], $permastruct['feed'], $permastruct['forcomments'], $permastruct['walk_dirs'], $permastruct['endpoints'] );
			} else {
				$rewrite_rules_by_source[ $permastructname ] = $wp_rewrite->generate_rewrite_rules( $permastruct, EP_NONE );
			}
		}
		// Unset useless rewrites rules
		if ( ! empty ( $rewrite_rules_by_source['post_format'] ) ) {
			unset( $rewrite_rules_by_source['post_format'] );
		}
		// Clean rules for better use
		$cleaned_rules_by_source = [];
		foreach ( $rewrite_rules_by_source as $key => $source ) {
			foreach ( $source as $subkey => $value ) {
				$cleaned_rules_by_source[ $key ][] = $subkey;
			}
		}
		seokey_helper_cache_data( 'seokey_cache_helper_parse_request_get_rules', $cleaned_rules_by_source );
	}
	return $cleaned_rules_by_source;
}

/**
 * Find URL type
 *
 * @since 0.0.1
 * @author  Daniel Roch
 */
function seokey_helper_parse_request_find_type( $url = 'none' ) {
	// Default values and data
	$result = 'none';
	global $wp_rewrite;
	$rewrite = $wp_rewrite->wp_rewrite_rules();
	// Get rules by type
	$cleaned_rules_by_source = seokey_helper_parse_request_get_rules();
	// Find URL $type
	foreach ( (array) $rewrite as $match => $query ) {
		if ( preg_match( "#^$match#", $url, $matches ) ||
		     preg_match( "#^$match#", urldecode( $url ), $matches ) ) {
			// Iterate on each types
			$types = ['post', 'page', 'author', 'root', 'comments', 'search', 'date'];
			foreach ( $types as $type ) {
				if ( in_array( $match, $cleaned_rules_by_source[$type] ) ) {
					$result = $type;
					break;
				}
			}
		}
	}
	// return $type
	return sanitize_title( $result );
}

/**
 * Cache data
 *
 * @usage
 * (set)        seokey_helper_cache_data( 'the_key', 'the_data' );
 * (get)        $foo = seokey_helper_cache_data( 'the_key' );
 * (delete)     seokey_helper_cache_data( 'the_key', null );
 *
 * @since 0.0.1
 * @author  Julio Potier (Secupress power)
 */
function seokey_helper_cache_data( $key ) {
	static $data = array();
	$func_get_args = func_get_args();
	if ( array_key_exists( 1, $func_get_args ) ) {
		if ( null === $func_get_args[1] ) {
			unset( $data[ $key ] );
		} else {
			$data[ $key ] = $func_get_args[1];
		}
	}
	return isset( $data[ $key ] ) ? $data[ $key ] : null;
}

/**
 * Helper function : tell if this content is private or not
 *
 * @author Daniel Roch
 * @since  0.0.1
 */
function seokey_helper_is_global_checked( $type = 'posts', $data = 'none' ) {
	// Get known post types
	$known = get_option('seokey_admin_content_watcher_known');
	switch( $type ) {
		case 'posts':
			$global_checked = ! isset( array_flip( seokey_helper_get_option( 'cct-cpt', [] ) )[ $data ] );
			// noindexed ?
			if ( true === $global_checked ) {
				// check ifwe have known data
				if ( isset ( $known['posts'] ) ) {
					// This content type is unkown, remove noindex
					if ( ! isset( $known['posts'][$data] ) ) {
						$global_checked = FALSE;
					}
				}
			}
			return $global_checked;
		case 'taxonomies':
			$global_checked = ! isset( array_flip( seokey_helper_get_option( 'cct-taxo', [] ) )[ $data ] );
			// noindexed ?
			if ( true === $global_checked ) {
				// check ifwe have known data
				if ( isset ( $known['taxonomies'] ) ) {
					// This content type is unkown, remove noindex
					if ( ! isset( $known['taxonomies'][$data] ) ) {
						$global_checked = false;
					}
				}
			}
			return $global_checked;
	}
	// Default to false
	return false;
}

/**
 * Helper function : better count for emojis
 *
 * @author Julio Potier
 * @since  0.0.1
 */
function seokey_helper_strlen( $string ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $string ) : strlen( $string );
}

/**
 * Helper function: safe seokey_helper_get_current_screen
 *
 * @author Daniel Roch
 * @since  0.0.1
 */
function seokey_helper_get_current_screen() {
	if ( !function_exists( 'get_current_screen' ) ) {
		require_once ABSPATH . '/wp-admin/includes/screen.php';
	}
	return get_current_screen();
}

/**
 * Helper function: move one item array at desired position
 *
 * @author Daniel Roch
 * @since  0.0.1
 */
function seokey_helper_array_insert_at_position( $array, $index, $val ) {
	$size = count($array);
	if ( !is_int($index) || $index < 0 || $index > $size ) {
		return -1;
	} else{
		$tomerge   = array_slice( $array, 0, $index );
		$tomerge[] = $val;
		return array_merge( $tomerge, array_slice( $array, $index, $size ) );
	}
}


// TODO comment
function seokey_helpers_is_admin_pages() {
	if ( ! is_admin() ) {
		return FALSE;
	}
	$current_screen = seokey_helper_get_current_screen();
	// Our admin pages
	if ( ! is_null( $current_screen ) ) {
		if ( str_starts_with( $current_screen->base, 'seokey_page_seo-key-' ) ) {
			return TRUE;
		}
	}
	// Main page
	if ( ! is_null( $current_screen ) ) {
		if ( $current_screen->base === 'toplevel_page_seo-key' ) {
			return TRUE;
		}
	}
	// ALT editor
	if ( true === seokey_helpers_medias_library_is_alt_editor() ) {
		return true;
	}
	// Post type archive menu
	if ( true === seokey_helpers_admin_is_post_type_archive() ) {
		return true;
	}
	return false;
}

//TODO comments
function seokey_helpers_redirections_is_redirect_editor(){
	$current_screen = seokey_helper_get_current_screen();
	if ( is_null ( $current_screen ) ) {
		if ( !empty ( $_GET['page'] ) ) {
			if ( 'seo-key-redirections' === $_GET['page'] ) {
				return true;
			}
		}
	}
	elseif ( $current_screen->base === 'seokey_page_seo-key-redirections' ) {
		return true;
	}
	return false;
}

//TODO comments
function seokey_helpers_is_free() {
    if ( "SEOKEY" === SEOKEY_NAME ) {
        return true;
    }
    return false;
}

/**
 * Helper function: Create cache helper to know if multilingual plugin is active
 *
 * @author Gauvain Van Ghele
 * @since  1.6
 */
function seokey_helpers_get_languages() {
	$lang           = 'single';
    $plugins        = get_option( 'active_plugins' );
    $pluginscheck   = [
        'wpmlpro'       => 'sitepress-multilingual-cms/sitepress.php',
        'weglotpro'     => 'weglot-pro/weglot-pro.php',
        'polylangpro'   => 'polylang-pro/polylang.php',
        'weglot'        => 'weglot/weglot.php',
        'polylang'      => 'polylang/polylang.php',
    ];
    // TODO Comment hook
    $pluginscheck =  apply_filters( 'seokey_filter_get_languages', $pluginscheck );
    foreach ( $pluginscheck as $pluginName => $plugin ) {
        if ( in_array( $plugin, $plugins ) ) {
	        $lang = $pluginName;
            break;
        }
    }
	seokey_helper_cache_data('languages', $lang );
}

/**
 * Check if url is valid and return a full url with domain.
 *
 * @param string $link ( optionnal )
 *
 * @return ( string ) with full url
 * @author  Gauvain Van Ghele
 *
 * @since   1.5.X
 */
function seokey_helpers_get_current_domain( $link = null ){
    // Check if this is a valid url and add domain if not.
    if( wp_http_validate_url( $link ) ) {
        return $link;
    } else {
        // Check if my array is here
        if ( seokey_helper_cache_data('languages') ) {
            foreach ( seokey_helper_cache_data('languages')['lang'] as $lang ) {
                // If multilingual plugin installed
                if ( isset( seokey_helper_cache_data('languages')['plugin'] ) ) {
                    // Check for plugin ( only polylang for now )
                    switch ( seokey_helper_cache_data('languages')['plugin'] ) {
                        case 'polylang':
                        case 'polylangpro':
                            if ( function_exists('pll_current_language' ) ) {
                                if ( $lang['iso2'] == pll_current_language() ) {
                                    $domain = $lang['domain'];
                                    break;
                                }
                            }
                            break;
                        case 'wpmlpro':
                            if ( defined(ICL_LANGUAGE_CODE ) ) {
                                if ( $lang['iso2'] == ICL_LANGUAGE_CODE ) {
                                    $domain = $lang['domain'];
                                    break;
                                }
                            }
                            break;
                        // TODO Weglot
                        default:
                            break;
                    }
                } else {
                    // If not multilingual
                    if ( $lang['locale'] == get_locale() ) {
                        $domain = $lang['domain'];
                        break;
                    }
                }
            }
        }
        $domain = isset( $domaine ) ? $domaine : home_url();
        // Check if first slash is here
        if ( !is_null( $link ) ) {
            if ( substr( $link, 0, 1 ) !== '/' ) {
                $link = '/' . $link;
            }
        }
        return $domain . $link;
    }
}

// TODO Comment
function seokey_helpers_get_base_url( $link = null ) {
    $base_url = get_option('siteurl');
    // Check if first slash is here
    if ( !is_null($link) ){
        if ( substr( $link, 0, 1 ) !== '/' ) {
            $link = '/' . $link;
        }
        $base_url = $base_url.$link;
    }
    return esc_url( $base_url );
}

// TODO Comment
function seokey_helpers_get_available_domains(){
    if ( seokey_helper_cache_data('languages') ) {
        foreach ( seokey_helper_cache_data('languages')['lang'] as $lang ) {
            $domains[] = $lang['domain'];
        }
    } else {
        $domains[] = home_url();
    }
    return $domains;
}

/**
 * Get base url, depends on single site / multi / domain type
 *
 * @param string $relative (optional)
 * $relative is only for xsl : need short path
 *
 * @return ( string ) with url
 * @author  Gauvain Van Ghele
 *
 * @since   1.5.X
 */
function seokey_helpers_get_sitemap_base_url( $langIso3 = null, $relative = false ){
	// Get upload path
	$uploads_path = seokey_helpers_get_short_upload_dir( $relative );
	// Define path
	if ( true === $relative ) {
		$url = $uploads_path . '/seokey/sitemaps/';
	} else {
		if ( !is_null( $langIso3 ) ) {
			switch ( seokey_helper_cache_data('languages' )['site']['domain_type'] ){
				case 'subdomain':
				case 'domain':
					// Avoid double '//' with ltrim / rtrim in case of subfolder
					$url = rtrim( seokey_helper_cache_data('languages' )['lang'][$langIso3]['domain'],"/")."/" . ltrim( $uploads_path,"/" ) . '/seokey/sitemaps/';
					break;
				case 'suffix':
				case 'single':
				default:
					if ( str_starts_with( $uploads_path, home_url() ) ) {
						$url = $uploads_path . '/seokey/sitemaps/';
					} else {
						if ( str_starts_with( $uploads_path, WP_CONTENT_URL ) ) {
							$url = $uploads_path . '/seokey/sitemaps/';
						} else {
							$url = home_url( $uploads_path . '/seokey/sitemaps/');
						}
					}
					break;
			}
		} else {
			$url = $uploads_path . '/seokey/sitemaps/';
		}
	}
	return $url;
}


/**
 * Get short upload dir
 *
 * @param string $relative ( optionnal )
 * $relative is only for WordPress if in subfolder : add this folder before wp-content
 *
 * @author  Gauvain Van Ghele
 *
 * @since   1.6.2
 */
function seokey_helpers_get_short_upload_dir( $relative = false ){
	// Get data from home option
	$parts = parse_url( get_option('home') );
	// Get short URL for "uploads" folder
	$exploded_url = explode( untrailingslashit( site_url() ), wp_upload_dir()['baseurl'] );
	if ( count( $exploded_url ) > 1 ) {
		$uploads_path = $exploded_url[1];
	} else {
		// Get URL for "wp-content" folder
		$content_url = content_url();
		// Rebuild wp-content/uploads
		$uploads_path = str_replace( untrailingslashit( site_url() ), '', $content_url ) . '/uploads';
	}
	// If Wordpress is in a subfolder and url is short : Get subfolder/upload_path
	if ( isset( $parts['path'] ) && $parts['path'] != '/' && true === $relative ) {
		$uploads_path = "/". rtrim( ltrim( $parts['path'],"/" ),"/" ) . $uploads_path;
	}
	// return data
	return $uploads_path;
}

/**
 * Check if we are curently on a sitemaps page
 *
 * @return  boolean
 * @author  Arthur Leveque
 *
 * @since   1.8.1
 */
function seokey_helpers_is_sitemaps_page(){
	$sitemaps_url = seokey_helpers_get_sitemap_base_url(); // get SeoKey's sitemaps URL to compare
	// Check if in current requested URL, we have the SeoKey's sitemaps URL
	return str_contains( esc_url( seokey_helper_url_get_current() ), $sitemaps_url );
}

// TODO Comment
function seokey_helpers_get_parent_key( $array, $search_value, $key ) {
	foreach ( $array as $parent_key => $child_array ) {
		if ( isset( $child_array[$key]) && $child_array[$key] == $search_value ) {
			return $parent_key;
		}
	}
	return false;
}

/**
 * Clean and escape HTML
 *
 * @param string $html HTML code to clean
 * @author  Daniel Roch
 * @since   1.6.6
 */
function seokey_helpers_data_clean_escaped_html( $html ) {
	// New lines => spaces
	$html = preg_replace( '/(\r|\n)/', ' ', $html );
	// <br> => spaces.
	$html = preg_replace( '/<br(\s*)?\/?>/i', ' ', $html );
	// <li> => •
	$html = preg_replace( '/(<li[^>]*>)/i', '$1• ', $html );
	// Replace closing tags with a space after it (useful for removing tags
	$html = preg_replace( '/<\/(p|div|h\d)>/i', '</$1> ', $html );
	// Strip tags
	$html = wp_strip_all_tags( $html );
	// Replace multiple spaces with one (created wih our wp_strip_all_tags)
	$html = preg_replace( '!\s+!', ' ', $html );
	// Escape (you can never be too careful)
	$html = esc_html( $html );
	// Return trimmed data
	return trim( $html );
}

/**
 * Get all arrays in multidimensional array where a key equals a value and send them to a new array
 *
 * @param array $array array to do our research
 * @param string $key_to_find name of the key we need
 * @param mixed $value_for_key value for the key we need
 * @param array $result_array array that will be alimented through the process
 * @author  Arthur Leveque
 * @since   1.8.0
 */
function seokey_helpers_get_subarrays_with_key( $array, $key_to_find, $value_for_key, &$result_array ) {
	// Explore the current array
	foreach ( $array as $key => $value ) {
		// If our current element is an array, do a recursive call
		if ( is_array( $value ) ) {
			seokey_helpers_get_subarrays_with_key( $value, $key_to_find, $value_for_key, $result_array );
		} else {
			// If this element is not an array, check the key and the value
			if ( $key == $key_to_find && $value == $value_for_key ) {
				// If this is what we want, add it to the final array
				array_push( $result_array, $array );
			}
		}
	}
}