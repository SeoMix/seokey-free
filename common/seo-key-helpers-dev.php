<?php
/**
 * Load Dev functions
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 * @see     seo-key-helpers.php
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

/**
 * Displays an error message
 *
 * @since  0.0.1
 * @author Julio Potier
 *
 * @param string $function You have to use __FUNCTION__ or __METHOD__
 * @param string $line You have to use __LINE__
 * @param string $message A custom message to understand the issue
 * @return void The detailed message not translated to understand the dev issue
 **/
function seokey_dev_error( $function, $line, $message ) {
	$error = sprintf( 'SEOKEY DEV ERROR: %s()#%s `%s`', $function, $line,	$message );
    error_log( $error );
    die( $error );
}

/**
 * Write logs in the debug log file
 *
 * @since  0.0.1
 * @author Daniel Roch
 *
 * @param string|array|object $log Data to write
 * @return void
 **/
if ( !function_exists ( 'seokey_dev_write_log' ) ) {
	function seokey_dev_write_log( $log ) {
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( gettype( $log ) .': ' . strval( $log ) );
			}
		}
	}
}

/**
 * Pretty print
 *
 * @see : https://github.com/psaikali/seokey-devtools
 *
 * @param mixed $arr
 * @param boolean $admin
 * @param boolean $echo
 * @return void
 */
function seokey_dev_pretty_print( $arr, $admin = false, $echo = true ) {
	$output = '';
	$id = "debug-pp-" . rand( 0, 1000 );
	$extra_class = ( $admin ) ? 'admin' : '';
	if ( $admin && current_user_can( 'manage_options' ) ) {
		$output .= "<a class='debug-pp-link-debug' href='#" . $id . "'>debug</a>";
		$output .= "<a class='debug-pp-link-close' href='#'>x</a>";
	}
	if ( ( $admin && current_user_can( 'manage_options' ) ) || ! $admin ) {
		$output .= "<pre style='text-align:left;' class='seokey-debug-pp " . $extra_class . "' id='" . $id . "'><code>";
		$output .= '<strong>' . gettype( $arr ) .':</strong> ' . print_r( $arr, true );
		$output .= "</code></pre>";
	}
	if ( ! did_action( 'seokey_dev_debug_load_styles' ) ) {
		do_action( 'seokey_dev_debug_load_styles' );
	}
	if ( ! $echo ) {
        return $output;
    }
	echo $output;
}

/**
 * Load styles for the pretty print debug boxes
 *
 * @see : https://github.com/psaikali/seokey-devtools
 *
 * @return void
 */
function debug_load_styles() {
	?>
	<style>
		pre.seokey-debug-pp {
			transition: .5s all ease-out;
			background: #c9eef6;
			padding:1em;
			margin:1em;
			position:relative;
			border-radius:4px;
			overflow-x:scroll;
			text-align:left;
			line-height: 1.4;
			font-size: 16px;
			border:3px solid #bbdde4;
            z-index: 20;
		}
		pre.seokey-debug-pp code {
			white-space: inherit;
			font-family:'PT Mono';
			font-weight:500;
		}
	</style>
	<?php
}
add_action( 'seokey_dev_debug_load_styles', __NAMESPACE__ . '\\debug_load_styles' );

/**
 * Inspect/list functions called on hooks containing specific term
 *
 * @see : https://github.com/psaikali/seokey-devtools
 *
 * @param array Array of terms that the hook should contain
 * @return void
 */
function seokey_dev_hook_detail( $terms = ['wp_'] ) {
	global $wp_filter;
	$related_hooks = [];
	$total         = 0;
	if ( ! is_array( $terms ) ) {
		$terms = [ $terms ];
	}
	foreach ( $wp_filter as $key => $val ) {
		if ( string_contains_all_words( $key, $terms ) ) {
			foreach ( $val->callbacks as $priority ) {
				foreach ( $priority as $callback ) {
					foreach ( $callback as $function => $function_data ) {
						if ( $function !== 'function' ) {
							continue;
						}
						if ( is_array( $function_data ) ) {
							$method = $function_data[1];

							if ( is_string( $function_data[0] ) ) {
								$classname = $function_data[0];
							} else {
								$classname = get_class( $function_data[0] );
							}
							if ( method_exists( $function_data[0], $method ) ) {
								$reflection    = new \ReflectionMethod( $classname, $method );
								$function_name = $classname . '->' . $method;
								$related_hooks[ $key ][] = sprintf( '<strong>%1$s</strong> in <em>%2$s</em> <small>L%3$d</small>', $function_name, str_replace( ABSPATH, '', $reflection->getFileName() ), $reflection->getStartLine() );
							} else {
								$function_name = $classname . '->' . $method;
								$related_hooks[ $key ][] = sprintf( '<strong>%1$s</strong> (method not found)', $function_name );
							}
						} else {
							try {
								$reflection = new \ReflectionFunction( $function_data );
							} catch (\ReflectionException $e) {
								continue;
							}
							if ( $function_data instanceof \Closure ) {
								$related_hooks[ $key ][] = sprintf( 'closure in <em>%1$s</em> <small>L%2$d</small>', str_replace( ABSPATH, '', $reflection->getFileName() ), $reflection->getStartLine() );
							} else {
								$related_hooks[ $key ][] = sprintf( '<strong>%3$s</strong> in <em>%1$s</em> <small>L%2$d</small>', str_replace( ABSPATH, '', $reflection->getFileName() ), $reflection->getStartLine(), $function_data );
							}
						}
						$total++;
					}
				}
			}
		}
	}
	$related_hooks['total'] = $total;
    seokey_dev_pretty_print($related_hooks);
}

/*
 * Check if a string contains ALL words from an array
 *
 * @see : https://github.com/psaikali/seokey-devtools
 *
 * @param array $array Array of strings
 * @return boolean
 */
function string_contains_all_words( $string, $array ) {
	$missed = false;
	foreach ( $array as $word ) {
		if ( strpos( $string, $word ) !== false ) {
			continue;
		} else {
			$missed = true;
			break;
		}
	}
	return ! $missed;
}

/**
 * esc_xml
 * Polyfill for WordPress version before 5.5.0
 */
if ( !function_exists('esc_xml') ) {
	function esc_xml( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
 
    $cdata_regex = '\<\!\[CDATA\[.*?\]\]\>';
    $regex       = <<<EOF
/
    (?=.*?{$cdata_regex})                 # lookahead that will match anything followed by a CDATA Section
    (?<non_cdata_followed_by_cdata>(.*?)) # the "anything" matched by the lookahead
    (?<cdata>({$cdata_regex}))            # the CDATA Section matched by the lookahead
 
|                                         # alternative
 
    (?<non_cdata>(.*))                    # non-CDATA Section
/sx
EOF;
 
        $safe_text = (string) preg_replace_callback(
            $regex,
            static function( $matches ) {
                if ( ! $matches[0] ) {
                    return '';
                }

                if ( ! empty( $matches['non_cdata'] ) ) {
                    // escape HTML entities in the non-CDATA Section.
                    return _wp_specialchars( $matches['non_cdata'], ENT_XML1 );
                }

                // Return the CDATA Section unchanged, escape HTML entities in the rest.
                return _wp_specialchars( $matches['non_cdata_followed_by_cdata'], ENT_XML1 ) . $matches['cdata'];
            },
            $safe_text
        );

        /**
         * Filters a string cleaned and escaped for output in XML.
         *
         * Text passed to esc_xml() is stripped of invalid or special characters
         * before output. HTML named character references are converted to their
         * equivalent code points.
         *
         * @since 5.5.0
         *
         * @param string $safe_text The text after it has been escaped.
         * @param string $text      The text prior to being escaped.
         */
        return apply_filters( 'esc_xml', $safe_text, $text );
    }
}

/**
 * wp_unique_id
 * Polyfill for WordPress version before 5.0.3
 */
if ( !function_exists('wp_unique_id') ) {
	function wp_unique_id( $prefix = '' ) {
		static $id_counter = 0;
		return $prefix . (string) ++$id_counter;
	}
}