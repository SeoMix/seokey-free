<?php
/**
 * Audit functions helpers
 *
 * @Loaded on 'init' & role editor
 *
 * @see     audit.php
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Get results for all issue or for a specific global type (content, technical, etc.)
 *
 * @return array|false Audit results
 */
function seokey_audit_get_results_by_task( $type_issue = '' ) {
    // Connect to Database and get audit data
    global $wpdb;
    $task_type = ( $type_issue === '' ) ? '' : ' WHERE audit_type = "' . esc_sql( $type_issue ) . '"';
    $sql = 'SELECT task, count(*), audit_type, item_type_global FROM ' . $wpdb->base_prefix . '%1$s' . $task_type . ' GROUP BY task';
    $results = $wpdb->get_results( $wpdb->prepare( $sql, 'seokey_audit' ), ARRAY_A );
    return $results;
}

/**
 * Get results for all issue or for a specific global type (content, technical, etc.)
 *
 * @return array|false Audit results
 */
function seokey_audit_get_results_by_type( $type_issue = '' ) {
    // Connect to Database and get audit data
    global $wpdb;
    $task_type = ( $type_issue === '' ) ? '' : ' WHERE audit_type = "' . esc_sql( $type_issue ) . '"';
    $sql = 'SELECT audit_type, count(*) FROM ' . $wpdb->base_prefix . '%1$s' . $task_type . ' WHERE priority NOT IN ( 4 ) GROUP BY task';
    $results = $wpdb->get_results( $wpdb->prepare( $sql, 'seokey_audit' ), ARRAY_A );
    return $results;
}

/**
 * Return audit messages from task if there is a sbupriority
 *
 * @param  bool  $task
 *
 * @return mixed|void
 */
function seokey_audit_get_sub_task_messages( $task = false ) {
	// Task not here, do nothing
	if ( $task === false ) {
		return;
	}
	// Get available messages
	$messages = seokey_audit_get_task_messages_content_subpriority();
	// Return each message for this task
	if ( isset( $messages[ $task ] ) ) {
		// Message array is available, return data
		return $messages[ $task ];
	}
	// We will need a more specific message
	return false;
}

/**
 * Return audit messages from task
 *
 * @param  bool  $task
 *
 * @return mixed|void
 */
function seokey_audit_get_task_messages( $task = false ) {
	// Task not here, do nothing
	if ( $task === false ) {
		return;
	}
	// Get available messages
	$messages = seokey_audit_get_task_messages_content();
	// Return each message for this task
	if ( isset( $messages[ $task ] ) ) {
	    // Message array is available, return data
		return $messages[ $task ];
	}
	// We will need a more specific message
	return 'false';
}

// TODO COMMENT
// TODO MOVE
function seokey_helper_get_meta_values( $key = '' ) {
	global $wpdb;
	if( empty( $key ) ) {
		return '';
	}
	$r = $wpdb->get_col( $wpdb->prepare( "
        SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s
    ", $key ) );
	foreach ( $r as $key => $result ) {
		$r[$key] = strtolower($result);
	}
	return $r;
}

/**
 * Tell user what he needs to do
 *
 * @param  int $count
 *
 * @return bool|array
 */
function seokey_audit_whattodo( $id = 0, $keyword = false ) {
	$message = [];
	// No ID, abort
	if ( $id === 0 ) {
		return false;
	}
	// Keyword target
	$keyword = ( false === $keyword ) ? get_post_meta( $id, "seokey-main-keyword", true ) : $keyword;
	if ( empty( $keyword ) ) {
		$message['worktodo'] = __( "Select Keyword", "seo-key" );
		$message['id']       = "worktodo_nokeyword";
	} else {
		// Wait ?
		$current_time   = current_time ('timestamp' );
		$date           = get_post_timestamp( $id ) + MONTH_IN_SECONDS ;
		// Recent post
		if ( $date >= $current_time ) {
			$message['worktodo'] = __( "Wait", "seo-key" );
			$message['id']       = "worktodo_wait_30";
		} else {
			// Get keyword data
			$updated_date   = get_post_modified_time( 'U', true, $id );
			$diff = (int) ( ( $current_time - $updated_date ) / DAY_IN_SECONDS );
			// Post recently updated
			if ( $diff < 14 ) {
				$message['worktodo'] = __( "Wait", "seo-key" );
				$message['id']       = "worktodo_wait_7";
			}
			// No keyword data (SEO is bad) => update
			else {
				$message['worktodo'] = __( "Optimize", "seo-key" );
				$message['id']       = "worktodo";
			}
		}
	}
	// return data
	return $message;
}

/**
 * Return DOMDocument of the given content
 *
 * @param  string  $content
 * @param  boolean  $striplashes  if you need to add striplashes
 *
 * @return object
 */
function seokey_audit_get_domdocument( $content = '', $striplashes = false ) {
	$contentToDOM = $striplashes ? stripslashes( $content ) : $content;
	$dom          = new DOMDocument();
	// Prevent errors if the content is empty
	if ( !empty( $contentToDOM ) ) {
		$dom->loadHTML( $contentToDOM,
			LIBXML_HTML_NOIMPLIED |      # Make sure no extra BODY
			LIBXML_HTML_NODEFDTD |              # or DOCTYPE is created
			LIBXML_NOERROR |                    # Suppress any errors
			LIBXML_NOWARNING                    # or warnings about prefixes.
		);
	}
	return $dom;
}

/**
 * Return a string in lowercase without special chars and accents, ideal for string comparison
 *
 * @param  string  $content
 * @param  string  $language language code needed for cleaning stopwords on multinlingual sites (fr, en...)
 *
 * @return string
 */
function seokey_audit_clean_string( $content = '', $language = '' ) {
	$cleaned_string = str_replace( '\’', '', $content );// Better for english text
	$cleaned_string = remove_accents( $cleaned_string );
	$cleaned_string = strtolower( $cleaned_string );
	$cleaned_string = seokey_audit_clean_stopwords( $cleaned_string, $language );
	return $cleaned_string;
}

/**
 * substract all stop words from a string in a selected language
 *
 * @param  string  $content
 * @param  string  $language must be de 2 first letters of the locale (for exemple en for english, fr for french...)
 *
 * @return string
 */
function seokey_audit_clean_stopwords( $content = '', $language = '' ) {
	// If we didn't specified any language, take the site language
	if ( $language === '' ) {
		$language = substr( get_locale(), 0, 2 );
	}
	// Now that we have a language, check the stop words for this language
	if ( $language ) {
		$stopwords = []; // Prepare an array for our stop words
		// Get the correct stop words for our language
		$stopwords = seokey_get_stopwords( $language );
		// If we have stop words (so if we passed in any of the languages in the previous switch) start the cleaning
		if ( !empty( $stopwords ) ) {
			// Clean all the stop words from $content
			$content = preg_replace( '/\b(' . remove_accents( implode( '|', $stopwords ) ) . ')\b/', '', $content );
			// Clean multiple whitespaces that may be created
			$content = preg_replace( '!\s+!', ' ', $content );
		}
	}
	return $content;
}