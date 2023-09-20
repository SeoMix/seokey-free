<?php
/**
 * Third party: Polylang
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


/**
 * First create new cache with array
 *
 */
$polylang_options = get_option( 'polylang' );
// First create list of languages ( minimum current )
if( function_exists('pll_languages_list') ){
    $languages = pll_languages_list( array('fields' => 'locale') );
}else{
    $languages[0] = get_locale();
}
// Add plugin name
$cachePolylang = [
    "plugin" => seokey_helper_cache_data('languages')
];

if (function_exists('pll_current_language')) {
    // We assume it will be suffix if nothing is found
    $force_lang = isset( $polylang_options['force_lang']) ? $polylang_options['force_lang'] : 0;

    // Détectez le type de domaine que nous avons
    switch ($force_lang) {
        case 0:
        case 1:
            $cachePolylang['site']['domain_type'] = "suffix";
            break;
        case 2:
            $cachePolylang['site']['domain_type'] = "subdomain";
            break;
        case 3:
            $cachePolylang['site']['domain_type'] = "domain";
            break;
        default:
            $cachePolylang['site']['domain_type'] = "unknown";
            break;
    }
}
if ( $polylang_options ) {
    $tab_lang = [];
    $domains["lang"] = [];
    foreach($languages as $v){
        $split = explode( "_",$v );
        $tab_lang[$split[0]] = array(
            "code" => $split[1],
            "locale" =>$v
        );
        // Avoid error for domains not found
        $domains["lang"][$split[0]] = home_url();
    }
    $code_countries = array_flip(seokey_helpers_get_codes_countries());
    if ( function_exists( 'pll_default_language' ) ) {
        $default_locale = pll_default_language('locale');
        $split = explode("_", $default_locale);
        $cachePolylang['site']['default_lang'] = $code_countries[$split[1]];
    }

    if ( function_exists( 'pll_default_language' ) ) {
        $languages = pll_languages_list();
    }else{
        $languages = [];
    }
    foreach ($languages as $language_slug) {
        // Récupérez le lien de la page d'accueil dans la langue courante
        $home_url = pll_home_url($language_slug);
        $domains[pll__($language_slug)] = pll_home_url($language_slug);
    }
    foreach( $domains as $lang => $domainName ){
        if($domainName == site_url() ){
            $cachePolylang['site']['base_domain_lang'] = $code_countries[$tab_lang[$lang]["code"]];
        }
        if( isset( $tab_lang[$lang] ) ){
            if( in_array( $tab_lang[$lang]["code"], seokey_helpers_get_codes_countries() ) ){
                $cachePolylang['lang'][$code_countries[$tab_lang[$lang]["code"]]] = array(
                    "iso2"      => $lang,
                    "locale"    => $tab_lang[$lang]["locale"],
                    "domain"    => $domainName,
                    //"french"    => seokey_helpers_get_iso_countries()[$code_countries[$tab_lang[$lang]["code"]]]['fr'],
                    "name"   => seokey_helpers_get_iso_countries()[$code_countries[$tab_lang[$lang]["code"]]]['en'],
                );
            }
        }
    }
    if ( ! function_exists( 'pll_default_language' ) ) {
        $cachePolylang['site']['default_lang'] = array_key_first( $cachePolylang['lang'] );
    }
    if (!isset($cachePolylang['site']['base_domain_lang'])){
        $cachePolylang['site']['base_domain_lang'] = $cachePolylang['site']['default_lang'];
    }
    unset($code_countries);
    unset($tab_lang);
    unset($domains);
    seokey_helper_cache_data('languages',$cachePolylang);
}

 add_filter( "seokey_filter_sitemap_sender_excluded", 'seokey_thirdparty_polylang_sitemaps' );
// Exclude fake post types from sitemaps
function seokey_thirdparty_polylang_sitemaps($excluded) {
    $excluded['taxonomy'][] = 'language';
    $excluded['taxonomy'][] = 'term-translations';
    return $excluded;
}

add_filter( 'seokey_filter_settings_add_contents_post_types', 'seokey_thirdparty_polylang_settings', 500 );
// Exclude post types from settings
function seokey_thirdparty_polylang_settings($default){
    unset($default['language']);
    unset($default['term-translations']);
    return $default;
}

add_filter( 'seokey_settings_filter_taxonomy_choice', 'seokey_thirdparty_polylang_exclude_taxo', 500 );
// Remove from taxonomy choices for each post type
function seokey_thirdparty_polylang_exclude_taxo( $default ){
    $default[] = 'post_translations';
    $default[] = 'language';
    $default[] = 'term-translations';
    return $default;
}


add_filter('seokey_filter_head_canonical_url', 'seokey_thirdparty_polylang_canonical');
// Fix canonical for certain languages and Polylang configurations
function seokey_thirdparty_polylang_canonical( $current_url ){
	if (
		function_exists('pll_home_url') && function_exists('PLL')
		&& ( PLL()->links_model->options['rewrite'] === 1 ) // When option for permalinks "delete /language/ from permalinks" in on
		&& ( PLL()->links_model->options['redirect_lang'] === 1 ) // When option "just language code for front page instead of slug" is on
	) {
		$home_url = untrailingslashit( pll_home_url() ); // Get home URL with PLL
		// If the current URL does not start like the home URL
		if ( !str_starts_with( $current_url, $home_url ) ) {
			$bad_home_url   = untrailingslashit( home_url() );
			$count          = 1;
			// Replace the beginning of the current URL with the home URL generated by PLL
			$current_url    = str_replace( $bad_home_url, $home_url, untrailingslashit( $current_url ), $count );
		}
	}
	return $current_url;
}


add_filter('seokey_filter_home_url', 'seokey_thirdparty_polylang_home_url');
function seokey_thirdparty_polylang_home_url( $home_url ) {
	if ( function_exists( 'pll_home_url' ) ) {
		return pll_home_url();
	}
	return $home_url;
}

add_filter('seokey_filter_sitemap_native_redirect_lang', 'seokey_thirdparty_polylang_current_language');
function seokey_thirdparty_polylang_current_language( $lang ) {
	if ( function_exists( 'pll_current_language' ) ) {
		return pll_current_language( 'locale' );
	}
	return $lang;
}

