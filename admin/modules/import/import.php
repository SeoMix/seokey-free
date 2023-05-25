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
function seokey_admin_import_display( $defaulttext = false ) {
    $plugin_list = seokey_admin_import_list();
    if ( $plugin_list ) {
        echo '<h2 id="import-other-seo-plugin">' . esc_html__('Import from other SEO plugins', 'seo-key' ) . ' </h2>';
        if ( count( $plugin_list ) === 1 ) {
            echo '<p id="import-other-seo-plugin-explanation">';
            printf(
                esc_html__( 'Another SEO plugin has been detected. Would you like to import data from %1$s?', 'seo-key' ),
                '<strong>' . reset( $plugin_list ) . '</strong>'
            );
            echo '</p>';
            echo '<span id="seokey_import_value" data-value="' . esc_attr( array_key_first( $plugin_list ) ). '"></span>';
        } else {
            echo '<p>';
            esc_html_e( 'You have several active SEO plugins. Tell us from which plugin you want to import data.', 'seo-key' );
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
        echo '<button class="button button-secondary button-hero" id="seokey-launch-import">'. esc_html__('Import data', 'seo-key' ) .'</button>';
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
 * @author Daniel Roch, Gauvain Van Ghele
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
        wp_raise_memory_limit();
        // TODO later move each import into their own functions and file
        switch ( $_GET['plugin'] ) {
            case "wordpress-seo/wp-seo.php":
                seokey_helper_cache_data('sx_plugin_name','yoast');
                // Load Yoast Import functions
                seokey_helper_require_file( 'yoast', SEOKEY_PATH_ADMIN . 'modules/import/', 'admin' );
                // The end
                seokey_helper_import_goodotgo();
                break;
            case "seo-by-rank-math/rank-math.php":
                seokey_helper_cache_data( 'sx_plugin_name','rankmath' );
                // Load Yoast Import functions
                seokey_helper_require_file( 'rankmath', SEOKEY_PATH_ADMIN . 'modules/import/', 'admin' );
                // The end
                seokey_helper_import_goodotgo();
                break;
            case "wp-seopress/seopress.php":
                seokey_helper_cache_data('sx_plugin_name','seopress');
                // Load SeoPress Import functions
                seokey_helper_require_file( 'seopress', SEOKEY_PATH_ADMIN . 'modules/import/', 'admin' );
                // The end
                seokey_helper_import_goodotgo();
                break;
            default:
                wp_send_json_error( esc_html__( 'Incorrect plugin name.', 'seo-key' ) );
                break;
        }
    }
}

/**
 * Handle import end
 *
 * @since  1.2.0
 * @author Daniel Roch
 */
function seokey_helper_import_goodotgo() {
    // Renew sitemaps if wizard has ended
    if ( 'goodtogo' === get_option( 'seokey_option_first_wizard_seokey_notice_wizard' ) ) {
        // Flush rewrites rules
        flush_rewrite_rules();
        // Allow sitemap creation
        update_option( 'seokey_sitemap_creation', 'running', true );
    }
    // Do we need another notice after import ?
    $plugin = seokey_helper_cache_data('sx_plugin_name' );
    update_option('seokey_import_from', $plugin, true );
    // Delete this meta to prevent user importing data within the wizard to skip first wizard step
    delete_user_meta( get_current_user_id(), 'seokey_cache_wizard_pre_update_option' );
    // Send ajax call success message
    wp_send_json_success( esc_html__( 'Import completed. ', 'seo-key' ) );
}


/**
 * Get public Post Types
 *
 * @author Daniel Roch
 * @since  1.2.0
 */
function seokey_helper_import_get_cpts_public(){
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
    // Return data
    $all_cpts = array_keys( $all_cpts );
    return $all_cpts;
}

/**
 * Get public Taxonomies
 *
 * @author Daniel Roch
 * @since  1.2.0
 */
function seokey_helper_import_get_taxonomies(){
    $_builtin = get_taxonomies( ['_builtin' => true, 'public' => true ], 'objects' );
    $_custom = get_taxonomies( ['_builtin' => false, 'public' => true ], 'objects' );
    $all_taxos = array_merge( $_builtin, $_custom );
    unset( $_builtin );
    unset( $_custom );
    // Remove post format taxonomy
    if ( isset ( $all_taxos['post_format'] ) ) {
        unset( $all_taxos['post_format'] );
    }
    return $all_taxos;
}

/**
 * Handle Placeholders
 *
 * @see https://yoast.com/help/list-available-snippet-variables-yoast-seo/
 * @see https://www.seopress.org/support/guides/manage-titles-meta-descriptions/
 *
 * @since  1.3.1
 * @author Daniel Roch, Gauvain Van Ghele
 */
function seokey_helper_import_seo_fields(){
    $all_plugins = [
        // Global
        'sitename' => [
            'yoast'     => '%%sitename%%',
            'seopress'  => '%%sitetitle%%',
            'rankmath'  => '%sitename%',
        ],
        'sitedesc' => [
            'yoast'     => '%%sitedesc%%',
            'seopress'  => '%%tagline%%',
            'rankmath'  => '%sitedesc%',
        ],
        'sep' => [
            'yoast'     => '%%sep%%',
            'rankmath'  => '%sep%',
            'seopress'  => '%%sep%%',
        ],
        'currentdate' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%currentdate%%',
            'rankmath'  => '%currentdate%',
        ],
        'currentday' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%currentday%%',
            'rankmath'  => '%currentday%',
        ],
        'currenttime' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%currenttime%%',
            'rankmath'  => '%currenttime%',
        ],
        // Always ignore (won't be imported but they need to be removed from imported data)
        'searchphrase' => [
            'yoast'     => '%%searchphrase%%',
            'seopress'  => '%%search_keywords%%',
            'rankmath'  => '%search_query%',
        ],
        // Current pagination
        'page' => [
            'yoast'     => '%%page%%',
            'seopress'  => '%%page%%',
            'rankmath'  => '%page%',
        ],
        'pagetotal' => [
            'yoast'     => '%%pagetotal%%',
            'rankmath'  => '%pagetotal%',
            'seopress'  => '',
        ],
        'pagenumber' => [
            'yoast'     => '%%pagenumber%%',
            'seopress'  => '%%current_pagination%%',
            'rankmath'  => '%pagenumber%',
        ],
        'term404' => [
            'yoast'     => '%%term404%%',
            'seopress'  => '',  // does not exists
            'rankmath'  => '', // does not exists
        ],
        'post_thumbnail' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%post_thumbnail_url%%',
            'rankmath'  => '%post_thumbnail%',
        ],
        'filename' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '',  // does not exists
            'rankmath'  => '%filename%',
        ],
        'countvarname' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%count(X)%',
        ],
        'categorieslimits' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%categories(X)%',
        ],
        'tagslimits' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '',  // does not exists
            'rankmath'  => '%tags(X)%',
        ],
        'customterm' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '',  // does not exists
            'rankmath'  => '%customterm(X)%',
        ],
        'customterm_desc' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '',  // does not exists
            'rankmath'  => '%customterm_desc(X)%',
        ],
        'customfield' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%customfield(X)%',
        ],
        // For posts
        'id' => [
            'yoast'     => '%%id%%',
            'seopress'  => '', // does not exists
            'rankmath'  => '%id%',
        ],
        'date' => [
            'yoast'     => '%%date%%',
            'seopress'  => '%%post_date%%',
            'rankmath'  => '%date%',
        ],
        'modified' => [
            'yoast'     => '%%modified%%',
            'seopress'  => '%%post_modified_date%%',
            'rankmath'  => '%modified%',
        ],
        'currenttimeX' => [
            'yoast'     => '', // does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%currenttime(X)%',
        ],
        'dateX' => [
            'yoast'     => '', // does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%date(X)%',
        ],
        'modifiedX' => [
            'yoast'     => '', // does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%modified(X)%',
        ],
        'title' => [
            'yoast'     => '%%title%%',
            'seopress'  => '%%post_title%%',
            'rankmath'  => '%title%',
        ],
        'parent_title' => [
            'yoast'     => '%%parent_title%%',
            'seopress'  => '',
            'rankmath'  => '%parent_title%',
        ],
        'excerpt' => [
            'yoast'     => '%%excerpt%%',
            'seopress'  => '%%post_excerpt%%',
            'rankmath'  => '%excerpt%',
        ],
        'post_content' => [
            'yoast'     => '',  // Does not exists
            'seopress'  => '%%post_content%%',
            'rankmath'  => '',  // Does not exists
        ],
        'excerpt_only' => [
            'yoast'     => '%%excerpt_only%%',
            'seopress'  => '',
            'rankmath'  => '%excerpt_only%',
        ],
        'tag' => [
            'yoast'     => '%%tag%%',
            'seopress'  => '%%post_tag%%',
            'rankmath'  => '%tags%',
        ],
        'category' => [
            'yoast'     => '%%category%%',
            'seopress'  => '%%post_category%%',
            'rankmath'  => '%categories%',
        ],
        'primary_category' => [
            'yoast'     => '%%primary_category%%',
            'seopress'  => '',
            'rankmath'  => '%category%',
        ],
        'focuskw' => [
            'yoast'     => '%%focuskw%%',
            'seopress'  => '',
            'rankmath'  => '%focuskw%',
        ],
        'keywords' => [
            'yoast'     => '', // does not exists
            'seopress'  => '', // does not exists
            'rankmath'  => '%keywords%', // all main keywords
        ],
        'currentyear' => [
            'yoast'     => '%%currentyear%%',
            'seopress'  => '%%currentyear%%',
            'rankmath'  => '%currentyear%',
        ],
        'currentmonth' => [
            'yoast'     => '%%currentmonth%%',
            'seopress'  => '%%currentmonth_short%%',
            'rankmath'  => '%currentmonth%',
        ],
        'currentmonth_num' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%currentmonth_num%%',
            'rankmath'  => '', // does not exists
        ],
        'currentmonth_long' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%currentmonth%%',
            'rankmath'  => '', // does not exists
        ],
        'url' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%post_url%%',
            'rankmath'  => '%url%',
        ],
        'primary_taxonomy_terms' => [
            'yoast'     => '', // does not exists
            'seopress'  => '',
            'rankmath'  => '%primary_taxonomy_terms%',
        ],
        // For posts & post type archive
        'archive_title' => [
            'yoast'     => '%%archive_title%%',
            'seopress'  => '%%archive_title%%',
            'rankmath'  => '', // does not exists
        ],
        'archive_date' => [
            'yoast'     => '', // does not exists
            'seopress'  => '%%archive_date%%',
            'rankmath'  => '', // does not exists
        ],
        'archive_date_day' => [
            'yoast'     => '', // does not exists
            'seopress'  => '%%archive_date_day%%',
            'rankmath'  => '', // does not exists
        ],
        'archive_date_month' => [
            'yoast'     => '', // does not exists
            'seopress'  => '%%archive_date_month%%',
            'rankmath'  => '', // does not exists
        ],
        'archive_date_year' => [
            'yoast'     => '', // does not exists
            'seopress'  => '%%archive_date_year%%',
            'rankmath'  => '', // does not exists
        ],
        'archive_date_month_name' => [
            'yoast'     => '', // does not exists
            'seopress'  => '%%archive_date_month_name%%',
            'rankmath'  => '', // does not exists
        ],
        'pt_single' => [
            'yoast'     => '%%pt_single%%',
            'seopress'  => '', // does not exists
            'rankmath'  => '%pt_single%',
        ],
        'pt_plural' => [
            'yoast'     => '%%pt_plural%%',
            'seopress'  => '%%cpt_plural%%',
            'rankmath'  => '%pt_plural%',
        ],
        // Schema
        'org_name' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%org_name%',
        ],
        'org_logo' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%org_logo%',
        ],
        'org_url' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%org_url%',
        ],
        // For posts and authors
        'name' => [
            'yoast'     => '%%name%%',
            'seopress'  => '%%post_author%%',
            'rankmath'  => '%name%',
        ],
        'namebis' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%post_author%',
        ],
        'userid' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%userid%',
        ],
        'user_description' => [
            'yoast'     => '%%user_description%%',
            'seopress'  => '%%author_bio%%',
            'rankmath'  => '%user_description%',
        ],
        'user_first_name' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%author_first_name%%',
            'rankmath'  => '', // Does not exists
        ],
        'user_last_name' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%author_last_name%%',
            'rankmath'  => '', // Does not exists
        ],
        'user_website' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%author_website%%',
            'rankmath'  => '', // Does not exists
        ],
        'user_nickname' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%author_nickname%%',
            'rankmath'  => '', // Does not exists
        ],
        // For terms
        'category_title' => [
            'yoast'     => '',  // Does not exists
            'seopress'  => '%%_category_title%%',
            'rankmath'  => '', // Does not exists
        ],
        'category_description' => [
            'yoast'     => '%%category_description%%',
            'seopress'  => '%%_category_description%%',
            'rankmath'  => '', // Does not exists
        ],
        'tag_title' => [
            'yoast'     => '', // Does not exists
            'rankmath'  => '', // Does not exists
            'seopress'  => '%%tag_title%%',
        ],
        'tag_description' => [
            'yoast'     => '%%tag_description%%',
            'seopress'  => '%%tag_description%%',
            'rankmath'  => '', // Does not exists
        ],
        'term_description' => [
            'yoast'     => '%%term_description%%',
            'seopress'  => '%%term_description%%',
            'rankmath'  => '%term_description%',
        ],
        'term_title' => [
            'yoast'     => '%%term_title%%',
            'seopress'  => '%%term_title%%',
            'rankmath'  => '%term%',
        ],
        'term_hierarchy' => [
            'yoast'     => '%%term_hierarchy%%',
            'seopress'  => '', // Does not exists
            'rankmath'  => '', // Does not exists
        ],
        // For posts and terms
        'tags' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '', // Does not exists
            'rankmath'  => '%tag%', // nom de tag dans la page de tag ou 1er tag
        ],
        // Woocommerce
        'wc_single_cat' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_single_cat%%',
            'rankmath'  => '', // Does not exists
        ],
        'wc_single_tag' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_single_tag%%',
            'rankmath'  => '', // Does not exists
        ],
        'wc_single_short_desc' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_single_short_desc%%',
            'rankmath'  => '', // Does not exists
        ],
        'wc_single_price' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_single_price%%',
            'rankmath'  => '', // Does not exists
        ],
        'wc_single_price_exc_tax' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_single_price_exc_tax%%',
            'rankmath'  => '', // Does not exists
        ],
        'wc_sku' => [
            'yoast'     => '', // Does not exists
            'seopress'  => '%%wc_sku%%',
            'rankmath'  => '', // Does not exists
        ],
    ];
    return $all_plugins;
}

/**
 * Fallback function for plugins Import %%
 *
 * @return array|string|string[]
 * @author Daniel Roch
 * @since  1.2.0
 *
 * @param string $data data to parse (searching for %% items)
 * @param string $type content type (term, post, user...)
 * @param integer $ID ID for this content
 * @param string $cpt Post type (page, post...)
 * @return string Cleaned data
 */
function seokey_helper_import_parsing( $data, $type = '', $ID = 0, $cpt = ''  ) {
    // TODO TRANSIENT ?
    // Get current plugin being imported
    $plugin         = seokey_helper_cache_data('sx_plugin_name');
    $separator      = seokey_helper_import_separator( $plugin );
    $all_plugins    = seokey_helper_import_seo_fields();
    $replacements = [
        // Global
        $all_plugins['sitename'][$plugin]               => wp_strip_all_tags( get_bloginfo( 'name' ), true ),
        $all_plugins['sitedesc'][$plugin]               => wp_strip_all_tags( get_bloginfo( 'description' ) ),
        $all_plugins['sep'][$plugin]                    => $separator,
        // Always ignore
        $all_plugins['searchphrase'][$plugin]           => '',
        $all_plugins['page'][$plugin]                   => '',
        $all_plugins['pagetotal'][$plugin]              => '',
        $all_plugins['pagenumber'][$plugin]             => '',
        $all_plugins['term404'][$plugin]                => '',
        $all_plugins['post_thumbnail'][$plugin]         => '',
        $all_plugins['filename'][$plugin]               => '',
        // For posts
        $all_plugins['id'][$plugin]                     => '',
        $all_plugins['date'][$plugin]                   => '',
        $all_plugins['modified'][$plugin]               => '',
        $all_plugins['title'][$plugin]                  => '',
        $all_plugins['parent_title'][$plugin]           => '',
        $all_plugins['post_content'][$plugin]           => '',
        $all_plugins['excerpt'][$plugin]                => '',
        $all_plugins['excerpt_only'][$plugin]           => '',
        $all_plugins['tag'][$plugin]                    => '',
        $all_plugins['category'][$plugin]               => '',
        $all_plugins['primary_category'][$plugin]       => '',
        $all_plugins['focuskw'][$plugin]                => '',
        $all_plugins['keywords'][$plugin]               => '',
        $all_plugins['url'][$plugin]                    => '',
        $all_plugins['currentdate'][$plugin]            => date_i18n(get_option('date_format')),
        $all_plugins['currentday'][$plugin]             => date_i18n('j'),
        $all_plugins['currenttime'][$plugin]            => current_time(get_option('time_format')),
        $all_plugins['currentyear'][$plugin]            => '',
        $all_plugins['currentmonth'][$plugin]           => '',
        $all_plugins['currentmonth_long'][$plugin]      => '',
        $all_plugins['currentmonth_num'][$plugin]       => '',
        // For posts & post type archive
        $all_plugins['archive_title'][$plugin]          => '',
        $all_plugins['archive_date'][$plugin]           => '',
        $all_plugins['archive_date_day'][$plugin]       => '',
        $all_plugins['archive_date_month'][$plugin]     => '',
        $all_plugins['archive_date_year'][$plugin]      => '',
        $all_plugins['archive_date_month_name'][$plugin] => '',
        $all_plugins['pt_single'][$plugin]              => '',
        $all_plugins['pt_plural'][$plugin]              => '',
        // For posts and authors
        $all_plugins['name'][$plugin]                   => '',
        $all_plugins['namebis'][$plugin]                => '',
        $all_plugins['user_description'][$plugin]       => '',
        $all_plugins['userid'][$plugin]                 => '',
        // For terms
        $all_plugins['category_description'][$plugin]   => '',
        $all_plugins['tag_description'][$plugin]        => '',
        $all_plugins['term_description'][$plugin]       => '',
        $all_plugins['term_title'][$plugin]             => '',
        $all_plugins['term_hierarchy'][$plugin]         => '',
        // For posts and terms
        $all_plugins['tags'][$plugin]                   => '',
        $all_plugins['primary_taxonomy_terms'][$plugin] => '',
        $all_plugins['user_description'][$plugin]       => '',
        $all_plugins['user_first_name'][$plugin]        => '',
        $all_plugins['user_last_name'][$plugin]         => '',
        $all_plugins['user_website'][$plugin]           => '',
        $all_plugins['user_nickname'][$plugin]          => '',
        // For terms
        $all_plugins['category_title'][$plugin]         => '',
        $all_plugins['category_description'][$plugin]   => '',
        $all_plugins['tag_title'][$plugin]              => '',
        $all_plugins['tag_description'][$plugin]        => '',
        $all_plugins['term_title'][$plugin]             => '',
        $all_plugins['term_description'][$plugin]       => '',
        // For Woocommerce
        $all_plugins['wc_single_cat'][$plugin]          => '',
        $all_plugins['wc_single_tag'][$plugin]          => '',
        $all_plugins['wc_single_short_desc'][$plugin]   => '',
        $all_plugins['wc_single_price'][$plugin]        => '',
        $all_plugins['wc_single_price_exc_tax'][$plugin] => '',
        $all_plugins['wc_sku'][$plugin]                 => '',
    ];
    if ( 'rankmath' === $plugin ) {
        // RANKMATH SPECIFIC
        // TODO create function
        $rankmath_wpseo_titles = get_option( 'rank-math-options-titles' );
        $replacements[] = [
            $all_plugins['org_name'][ $plugin ]           => ( false !== $rankmath_wpseo_titles['knowledgegraph_name'] ) ? sanitize_text_field( $rankmath_wpseo_titles['knowledgegraph_name'] ) : '',
            $all_plugins['org_logo'][ $plugin ]           => ( false !== $rankmath_wpseo_titles['knowledgegraph_logo'] ) ? esc_url( $rankmath_wpseo_titles['knowledgegraph_logo'] ) : '',
            $all_plugins['org_url'][ $plugin ]            => ( false !== $rankmath_wpseo_titles['url'] ) ? esc_url( $rankmath_wpseo_titles['url'] ) : '',
        ];
    }
    // now define correct value according to each data
    switch ( $type ) {
        case 'post':
            $replacements[$all_plugins['id'][$plugin]]                  = (int) $ID;
            $replacements[$all_plugins['date'][$plugin]]                = get_the_date( '', $ID );
            $replacements[$all_plugins['modified'][$plugin]]            = get_the_modified_date( '', $ID );
            $replacements[$all_plugins['title'][$plugin]]               = get_the_title( $ID );
            $replacements[$all_plugins['parent_title'][$plugin]]        = ( false !== ( $parent_id = wp_get_post_parent_id( $ID ) ) ) ? get_the_title( $parent_id ) : '';
            $excerpt    = get_the_excerpt( $ID );
            if ( empty( $excerpt ) ) {
                $post    = esc_html( strip_tags( do_shortcode( get_post( $ID )->post_content ) ) );
                $excerpt = substr( $post, 0, strrpos( substr( $post, 0, METADESC_COUNTER_MAX ), ' ' ) );
            }
            $replacements[$all_plugins['post_content'][$plugin]]        =  $excerpt;
            $replacements[$all_plugins['post_thumbnail'][$plugin]]      = ( false !== get_the_post_thumbnail_url( $ID,'full') ) ? get_the_post_thumbnail_url( $ID,'full' ) : '';
            $replacements[$all_plugins['excerpt'][$plugin]] = $excerpt;
            $replacements[$all_plugins['excerpt_only'][$plugin]] = $excerpt;
            $terms_post_tag =  get_the_terms( $ID, 'post_tag' );
            if ( !empty( $terms_post_tag ) ) {
                $replacements[$all_plugins['tag'][$plugin]]             = join(', ', wp_list_pluck( get_the_terms( $ID, 'post_tag' ), 'name') );
            }
            $terms_category =  get_the_terms( $ID, 'category' );
            if ( !empty( $terms_category ) ) {
                $replacements[$all_plugins['category'][$plugin]]        = join(', ', wp_list_pluck( get_the_terms( $ID, 'category' ), 'name') );
                $replacements[$all_plugins['category_title'][$plugin]]  = $replacements[$all_plugins['category'][$plugin]];
            }
            $replacements[$all_plugins['currentyear'][$plugin]]         = date_i18n( 'Y' );
            $replacements[$all_plugins['currentmonth'][$plugin]]        = date_i18n( 'M' );
            $replacements[$all_plugins['currentmonth_long'][$plugin]]   = date_i18n( 'F' );
            $replacements[$all_plugins['currentmonth_num'][$plugin]]    = date_i18n( 'm' );
            // TODO later factoriser
            $user_data  = get_userdata( get_post_field( 'post_author', $ID ) );
            $replacements[$all_plugins['name'][$plugin]]                    = $user_data->display_name;
            $replacements[$all_plugins['namebis'][$plugin]]                 = $user_data->display_name;
            $replacements[$all_plugins['user_description'][$plugin]]        = get_user_meta( $ID, 'description', true );
            $replacements[$all_plugins['user_first_name'][$plugin]]         = get_user_meta( $ID, 'first_name', true );
            $replacements[$all_plugins['user_last_name'][$plugin]]          = get_user_meta( $ID, 'last_name', true );
            $replacements[$all_plugins['user_website'][$plugin]]            = $user_data->user_url;
            $replacements[$all_plugins['user_nickname'][$plugin]]           = get_user_meta( $ID, 'nickname', true );
            $replacements[$all_plugins['userid'][$plugin]]                  = $ID;
            unset($user_data);
            $post_type = get_object_vars( get_post_type_object( $cpt ) );
            $replacements[$all_plugins['archive_title'][$plugin]]       = $post_type['name'];
            $replacements[$all_plugins['pt_single'][$plugin]]           = $post_type['labels']->singular_name;
            $replacements[$all_plugins['pt_plural'][$plugin]]           = $post_type['label'];
            $replacements[$all_plugins['archive_date'][$plugin]]            = get_post_time( 'm, Y', false, $post_type, true );
            $replacements[$all_plugins['archive_date_day'][$plugin]]        = get_post_time( 'd', false, $post_type, true );
            $replacements[$all_plugins['archive_date_month'][$plugin]]      = get_post_time( 'm', false, $post_type, true );
            $replacements[$all_plugins['archive_date_year'][$plugin]]       = get_post_time( 'Y', false, $post_type, true );
            $replacements[$all_plugins['archive_date_month_name'][$plugin]] = get_post_time( 'M', false, $post_type, true );
            // These are specific for plugin
            $replacements[$all_plugins['primary_category'][$plugin]]    = seokey_helper_import_primary_category($plugin, $ID);
            $replacements[$all_plugins['focuskw'][$plugin]]             = seokey_helper_import_focuskw( $plugin, $ID );
            $replacements[$all_plugins['url'][$plugin]]                 = get_permalink( $ID );
            if ( !empty( $terms_post_tag ) ) {
                if ( is_array( $terms_post_tag )) {
                    $replacements[$all_plugins['tags'][$plugin]] = $terms_post_tag[0]->name;
                }
            }
            // RANKMATH SPECIFIC
            // TODO create function
            $replacements[$all_plugins['keywords'][$plugin]]            = implode( ', ', get_post_meta( $ID, 'rank_math_focus_keyword' ) );
            $rankmath_wpseo_titles        = get_option( 'rank-math-options-titles' );
            $name = 'pt_' . $cpt . '_primary_taxonomy';
            if ( !empty( $rankmath_wpseo_titles[$name] ) ) {
                $terms =  get_the_terms( $ID, $rankmath_wpseo_titles[$name] );
                if ( !empty( $terms ) ) {
                    $replacements[ $all_plugins['primary_taxonomy_terms'][ $plugin ] ] = join(', ', wp_list_pluck( get_the_terms( $ID, $rankmath_wpseo_titles[$name] ), 'name') );
                }
            }
            unset($rankmath_wpseo_titles);
            unset($name);
            // Woocommerce
            if ( function_exists( 'wc_get_product' ) ) {
                $product = wc_get_product( $ID );
                // do not do anything if it's not a product
                if( $product ) {
                    $catTerms         = get_the_terms( $ID, 'product_cat' );
                    $product_cat_name = '';
                    if( !empty( $catTerms ) ) {
                        foreach ($catTerms as $catTerm) {
                            $product_cat_name = $product_cat_name . $catTerm->name . ', ';
                        }
                    }
                    $tagTerms         = get_the_terms( $ID, 'product_tag' );
                    $product_tag_name = '';
                    if( !empty( $tagTerms ) ){
                        foreach ( $tagTerms as $tagTerm ) {
                            $product_tag_name = $product_tag_name . $tagTerm->name . ', ';
                        }
                    }

                    $replacements[$all_plugins['post_content'][$plugin]]                = $product->get_description();
                    $replacements[ $all_plugins['wc_single_cat'][ $plugin ] ]           = substr( $product_cat_name, 0, - 2 );
                    $replacements[ $all_plugins['wc_single_tag'][ $plugin ] ]           = substr( $product_tag_name, 0, - 2 );
                    $replacements[ $all_plugins['wc_single_short_desc'][ $plugin ] ]    = $product->get_short_description();
                    $replacements[ $all_plugins['wc_single_price'][ $plugin ] ]         = $product->get_price();
                    if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                        $replacements[ $all_plugins['wc_single_price_exc_tax'][ $plugin ] ] = wc_get_price_excluding_tax( $product );
                    }
                    $replacements[ $all_plugins['wc_sku'][ $plugin ] ]                  = $product->get_sku();
                }
                unset( $product );
                unset( $tagTerms );
                unset( $catTerms );
            }
            break;
        case 'term':
            $term = get_term( $ID );
            $termdescription = wp_html_excerpt( htmlspecialchars_decode( wp_strip_all_tags( $term->description ), ENT_QUOTES ), METADESC_COUNTER_MAX );
            $replacements[$all_plugins['term_title'][$plugin]]              = $term->name;
            $replacements[$all_plugins['term_description'][$plugin]]        = $termdescription;
            $replacements[$all_plugins['tag_title'][$plugin]]               = $term->name;
            $replacements[$all_plugins['tag_description'][$plugin]]         = $termdescription;
            $replacements[$all_plugins['category_description'][$plugin]]    = $termdescription;
            $replacements[$all_plugins['term_hierarchy'][$plugin]]          = '';
            if ( $term->parent !== 0 ) {
                $args = [
                    'format'    => 'name',
                    'separator' => ' ',
                    'link'      => false,
                    'inclusive' => true,
                ];
                $replacements[$all_plugins['term_hierarchy'][$plugin]]      = ' ' . $separator . ' ' . get_term_parents_list( $ID, $term->taxonomy, $args );
            }
            $replacements[$all_plugins['tags'][$plugin]]                    = $term->name;
            break;
        case 'users':
            // Get user data by user id
            // TODO later factoriser
            $user_data  = get_userdata( $ID );
            $replacements[$all_plugins['name'][$plugin]]                    = $user_data->display_name;
            $replacements[$all_plugins['namebis'][$plugin]]                 = $user_data->display_name;
            $replacements[$all_plugins['user_description'][$plugin]]        = get_user_meta( $ID, 'description', true );
            $replacements[$all_plugins['user_first_name'][$plugin]]         = get_user_meta( $ID, 'first_name', true );
            $replacements[$all_plugins['user_last_name'][$plugin]]          = get_user_meta( $ID, 'last_name', true );
            $replacements[$all_plugins['user_website'][$plugin]]            = $user_data->user_url;
            $replacements[$all_plugins['user_nickname'][$plugin]]           = get_user_meta( $ID, 'nickname', true );
            $replacements[$all_plugins['userid'][$plugin]]                  = $ID;
            break;
        case 'post_type_archive':
            $post_type                                                      = get_object_vars( get_post_type_object( $cpt ) );
            $replacements[$all_plugins['archive_title'][$plugin]]           = $post_type['name'];
            $replacements[$all_plugins['pt_single'][$plugin]]               = $post_type['labels']->singular_name;
            $replacements[$all_plugins['pt_plural'][$plugin]]               = $post_type['label'];
            $replacements[$all_plugins['archive_date'][$plugin]]            = get_post_time( 'm, Y', false, $post_type, true );
            $replacements[$all_plugins['archive_date_day'][$plugin]]        = get_post_time( 'd', false, $post_type, true );
            $replacements[$all_plugins['archive_date_month'][$plugin]]      = get_post_time( 'm', false, $post_type, true );
            $replacements[$all_plugins['archive_date_year'][$plugin]]       = get_post_time( 'Y', false, $post_type, true );
            $replacements[$all_plugins['archive_date_month_name'][$plugin]] = get_post_time( 'M', false, $post_type, true );
            break;
    }
    unset($post_type);
    unset($term);
    unset($terms);
    unset($excerpt);
    unset($terms_post_tag);
    // Replace with correct value
    $data = str_replace( array_keys( $replacements ), array_values( $replacements ), $data );
    // TODO move code below in function
    // Rankmath cleaning => return empty value to complex and useless variables
    if ( 'rankmath' === seokey_helper_cache_data( 'sx_plugin_name' ) ) {
        $data = preg_replace('/%count((.*?))%/','', $data );
        $data = preg_replace('/%currenttime((.*?))%/','', $data );
        $data = preg_replace('/%date((.*?))%/','', $data );
        $data = preg_replace('/%modified((.*?))%/','', $data );
        $data = preg_replace('/%categories((.*?))%/','', $data );
        $data = preg_replace('/%tags((.*?))%/','', $data );
        $data = preg_replace('/%customterm((.*?))%/','', $data );
        $data = preg_replace('/%customterm_desc((.*?))%/','', $data );
        $data = preg_replace('/%customfield((.*?))%/','', $data );
    }
    if ( 'seopress' === $plugin ) {
        $data = preg_replace('/%%_cf_((.*?))%%/', '', $data);
        $data = preg_replace('/%%_ct_((.*?))%%/', '', $data);
        $data = preg_replace('/%%_ucf_((.*?))%%/', '', $data);
    }
    // Remove double spaces
    $data = str_replace( '  ', ' ', $data );
    return $data;
}

/**
 * Get separator
 *
 * @author Daniel Roch
 * @since  1.2.0
 */
function seokey_helper_import_separator( $plugin ){
    $separator = "-";
    switch ( $plugin ){
        case 'yoast':
            $yoast_wpseo = get_option( 'wpseo_titles' );
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
            break;
        case 'rankmath':
            $rankmath_wpseo_titles  = get_option( 'rank-math-options-titles' );
            $separator              = ( !empty ( $rankmath_wpseo_titles['title_separator'] ) ) ? $rankmath_wpseo_titles['title_separator'] : '-';
            unset( $rankmath_wpseo_titles );
            break;
        default:
            break;
    }
    return $separator;
}

/**
 * Get primary category
 *
 * @author Daniel Roch
 * @since  1.2.0
 */
function seokey_helper_import_primary_category( $plugin, $ID ){
    switch ( $plugin ){
        case 'yoast':
            $primary_category = get_cat_name( get_post_meta( $ID, '_yoast_wpseo_primary_category', true ) );
            break;
        case 'seopress':
            $primary_category = get_cat_name( get_post_meta( $ID, '_seopress_robots_primary_cat', true ) );
            break;
        case 'rankmath':
            $primary_category = get_cat_name( get_post_meta( $ID, 'rank_math_primary_category', true ) );
            break;
        default:
            $primary_category = "";
            break;
    }
    return $primary_category;
}

/**
 * Get targeted keyword
 *
 * @author Daniel Roch
 * @since  1.2.0
 */
function seokey_helper_import_focuskw( $plugin, $ID ){
    switch ( $plugin ) {
        case 'yoast':
            $focuskw = get_post_meta( $ID, '_yoast_wpseo_focuskw', true );
            break;
        case 'seopress':
            $focuskw = get_post_meta( $ID, '_seopress_analysis_target_kw', true );
            break;
        case 'rankmath':
            $focuskw = get_post_meta( $ID, 'rank_math_focus_keyword', true );
            if ( str_contains( $focuskw, ',') ) {
                $focuskw = explode( ",", $focuskw );
                $focuskw = $focuskw[0];
            }
            break;
        default:
            $focuskw = "";
            break;
    }
    return $focuskw;
}