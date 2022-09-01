<?php
/**
 * Audit score and count functions
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
 * Get tasks score
 * "Global" is used to sort out the tasks between them.
 * Other values arte used for scoring functions
 */
function seokey_audit_get_task_score() {
	$score = array(
	    // Content tasks
		'title_length'             => array(
			'global'        => 6,
			'critical'      => 6, // h1 in content
			'error'         => 6, // h1 in content
            'type'          => 'critical',
		),
        'meta_desc_length'      => array(
            'global'        => 5,
            'warning'       => 5, // Main keyword not on Google first page
            'type'          => 'warning',
        ),
		'image_alt_missing'         => array(
            'global'        => 3,
            'warning'       => 3, // images without ALT in content
            'type'          => 'warning',
        ),
		'no_image'         => array(
			'global'        => 1,
			'warning'       => 1, // images without ALT in content
			'type'          => 'warning',
		),
		'no_links'         => array(
			'global'        => 6,
			'error'         => 1, // contents without internal links
			'type'          => 'warning',
		),
        'main_keyword_selection'    => array(
            'global'        => 0,
            'information'   => 0, // No keyword has been selected for this post
            'type'          => 'information',
        ),
        'words_count'               => array(
            'global'        => 100,
            'critical'      => 500, // no content
            'error'         => 100, // far from limit
            'type'          => 'critical',
        ),
    );
	return apply_filters( 'seokey_filter_audit_get_task_score', $score );
}

/**
 * Get URL quartiles scoring
 * source : https://gist.github.com/piercemcgeough/d9e1e1da2a0b403854b5
 */
function seokey_audit_get_tasks_quartiles( $array, $quartile ) {
    // quartile position is number in array + 1 multiplied by the quartile i.e. 0.25, 0.5, 0.75
    $pos = ( count( $array ) + 1 ) * $quartile;
    // if the position is a whole number
    // return that number as the quartile placing
    if ( fmod( $pos, 1 ) == 0 ) {
		while ( empty( $array[ $pos ] ) ) {
			$pos = $pos - 1;
		}
        return $array[$pos];
    } else {
        // get the decimal i.e. 5.25 = .25
        $fraction = $pos - floor( $pos );
        // get the values in the array before and after position
        $lower = floor( $pos ) - 1;
        $upper = ceil( $pos ) - 1;
		$pos = ( isset( $array[$pos] ) ) ? $array[$pos] : 0;
        $lower_num = ( isset( $array[$lower] ) ) ?
	        $array[$lower] :
	        $pos;
        $upper_num = ( isset( $array[$upper] ) ) ?
	        $array[$upper] :
	        $pos;
        // get the difference between the two
        $difference = $upper_num - $lower_num;
        // the quartile value is then the difference multiplied by the decimal
        // add to the lower number
        return $lower_num + ($difference * $fraction);
    }
}

add_filter( 'seokey_filter_audit_get_task_score', 'seokey_audit_get_task_score_sub_priority');
// TODO Comments
function seokey_audit_get_task_score_sub_priority( $array ) {
    $no_score = array(
        'global'        => 0,
        'information'   => 0, // Main keyword not on Google first page
        'type'          => 'information',
    );
    $new_data['traffic_main_keyword1'] = $no_score;
    $new_data['traffic_main_keyword2'] = $no_score;
    $new_data['traffic_main_keyword3'] = $no_score;
    $new_data['traffic_main_keyword4'] = $no_score;

    $score = array(
        'global'        => 5,
        'warning'       => 5, // no meta desc
        'type'          => 'warning',
    );
    $new_data['meta_desc_length1'] = $score;
    $score['warning'] = 2; // meta desc too long or too short
    $new_data['meta_desc_length2'] = $score;
    $new_data['meta_desc_length3'] = $score;

    $array = array_merge( $array, $new_data);
    return $array;
}

/**
 * Get audit issues count
 */
function seokey_audit_global_data_issues_count(){
    // TODO Transients ?
    // Connect to Database
    global $wpdb;
    // Get audit data
    $table   = $wpdb->base_prefix . 'seokey_audit';
    $results = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE priority NOT IN ( 4 )" );
    // No data, do nothing
    if ( empty( $results ) ) {
        return (int) 0;
    }
    $old_count = get_option('seokey_audit_global_issues_count_now');
    // TODO faire uniquement lors d'un changement d'audit
    update_option( 'seokey_audit_global_issues_count_now', (int) $results, true );
    update_option( 'seokey_audit_global_issues_count_old', $old_count, false );
    // return $count
    return (int) $results;
}

/**
 * Global score difference
 */
function seokey_audit_global_data_score_diff() {
    // TODO Transients
    $old_score = (int) get_option( 'seokey_audit_global_data_score_old' );
    $new_score = (int) get_option( 'seokey_audit_global_data_score_now' );
    if ( is_numeric( $old_score ) && is_numeric( $new_score ) ) {
	    return ( $new_score - $old_score ) ?: '';
    }
    return '';
}

/**
 * Global score
 */
function seokey_audit_global_data_score() {
	// Get ignored contents or tasks*
	$ignored = seokey_audit_global_data_ignored_tasks();
	// Get our score
	$final_score = seokey_audit_global_data_score_contents( $ignored ) - seokey_audit_global_data_score_global();
	// No negative or strange score
	$final_score = round( $final_score, 0);
	if ( (int) $final_score < 0 ) {
		$final_score = 0;
	}
    // Add traffic score
    $final_score = $final_score + round( seokey_audit_global_data_score_keywords(), 0);
	// Update old score
	update_option( 'seokey_audit_global_data_score_old', (int) get_option('seokey_audit_global_data_score_now'), false );
	// Update new score
	update_option( 'seokey_audit_global_data_score_now', (int) $final_score, false );
}

// TODO Comments
function seokey_audit_global_data_ignored_tasks(){
	$values = [];
	// Get all public post types
	$post_types = seokey_helper_get_option( 'cct-cpt', get_post_types( ['public' => true ] ) );
	// Get all posts indexed
	$query = new WP_Query( array(
		'posts_per_page'    => -1,
		'post_type'         => $post_types,
		'ignore_sticky_posts' => 1,
	) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			// First, we need to know which contents are noindexed
			$noindex = get_post_meta( $query->post->ID, 'seokey-content_visibility', true );
			if ( !empty( $noindex) ) {
				$values['noindex'][] = $query->post->ID;
			}
			// Content is index, let's check the discarded options
			else {
				$meta = get_post_meta( $query->post->ID, 'seokey_issue_discard', TRUE );
				if ( ! empty( $meta ) ) {
					$values['metas'][ $query->post->ID ] = $meta;
				}
			}
		}
	}
	return $values;
}

// TODO Comments
function seokey_audit_global_data_score_keywords() {
    // Half score for traffic
	if ( false === get_option( 'seokey-gsc-site')  ) {
		return 0;
	}
    $score_base = 50;
    // Get URL count
    // TODO later : improve count to exclude recent contents (30 days)
    $count_url = (int) get_option('seokey_audit_content_count');
	$count_url = max( $count_url, 1 );
    // How much is an URL weight ?
    $score_url = $score_base / $count_url;
    // Get URL traffic count
    $count = get_option( 'seokey_audit_score_count_url_with_traffic');
    // Let's do some math
    $count = ( empty ( $count ) ) ? 0 : $count;
    $score = $count * $score_url;
    // Return trafic score
	return $score;
}

// TODO Comments
function seokey_audit_global_data_score_global() {
	global $wpdb;
	$results = $wpdb->get_results(
		'SELECT *
		FROM ' . $wpdb->base_prefix . 'seokey_audit
		WHERE item_id IN ( 0 );'
	);
	$score = 0;
	foreach ( $results as $result ) {
		$priority = $result->priority;
		switch ( $priority ) {
			case 4:
				$score = $score + 0;
				break;
			case 3:
				$score = $score + 3;
				break;
			case 2:
				$score = $score + 6;
				break;
			case 1:
				$score = $score + 12;
				break;
		}
	}
	return( $score );
}

// TODO Comments
function seokey_audit_global_data_score_contents( $ignored = [] ) {
	// Count URL
	$count_url = (int) get_option('seokey_audit_content_count');
	// Exclusion list: global tasks + noindexed contents
	$exclude[] = 0;
	if ( !empty( $ignored ['noindex'] ) ) {
		$exclude    = array_merge($ignored ['noindex'], $exclude);
		$count_url  = $count_url - count($exclude);
	}
	$exclude        = implode(',', $exclude);
	// Data
	$score_base     = 50;
	$count_url      = max( $count_url, 1 );
	// Default URL value
	$url_scoring    = $score_base / $count_url;
	// Get URL data and exclude global tasks + noindexed contents
	global $wpdb;
	$results = $wpdb->get_results(
		'SELECT * FROM ' . $wpdb->base_prefix . 'seokey_audit WHERE item_id NOT IN ( ' . $exclude . ' );'
	);
	// Let's define score for each URL according to each audit issue
	if ( null !== $results ) {
		foreach ( $results as $item ) {
			$change     = 0;
			$item       = get_object_vars( $item );
			$priority   = $item["priority"];
			// Change score for each task and URL
			switch ( $priority ) {
				case 3:
					$change = $url_scoring * 5 / 100;
					break;
				case 2:
					$change = $url_scoring * 25 / 100;
					break;
				case 1:
					$change = $url_scoring * 80 / 100;
					break;
			}
			// Exclude noindex contents
			if ( isset( $ignored['metas'] ) ) {
				if ( in_array( $item["item_id"], array_keys( $ignored['metas'] ) ) ) {
					if ( in_array( $item["task"], $ignored['metas'][$item["item_id"]] ) ) {
						$change = 0;
					}
				}
			}
			$score_base = $score_base - $change;
		}
	}
	// perf
	unset($results);
	unset($ignored);
	// Return score
	return $score_base;
}

/**
 * Count issue for each issue global type
 *
 * @return void
 */
function seokey_audit_global_data_issues_type_count() {
    // No text if no audit yet
    $last_date              = get_option( 'seokey_audit_global_last_update' );
    if ( $last_date === false ) {
        return;
    }
    if ( false === ( $html = get_transient('seokey_transient_audit_issues_type_count') ) ) {
	    // Load tasks to do
	    $tasks = seokey_audit_task_list_global_types();
	    // Connect to Database
	    global $wpdb;
	    // Get audit data
	    $table   = $wpdb->base_prefix . 'seokey_audit';
	    $results = $wpdb->get_results( "SELECT audit_type, COUNT(*) as count FROM $table WHERE priority NOT IN ( 4 ) GROUP BY audit_type" );
	    $items   = [];
	    if ( NULL !== $results ) {
		    foreach ( $results as $type ) {
			    $type        = get_object_vars( $type );
			    $issue_type  = $type['audit_type'];
			    $issue_count = $type['count'];
			    $items[]     = sprintf( '<span class="seokey-audit-show-numbers">%s</span> ' . $tasks[ $issue_type ], number_format_i18n( $issue_count, 0 ) );
		    }
	    }
        $html = '<ul>';
        foreach ( $items as $item ) {
            $html .= '<li>' . $item . '</li>';
        }
        $html .= '</ul>';
        set_transient('seokey_transient_audit_issues_type_count', $html );
    }
    echo $html;
}