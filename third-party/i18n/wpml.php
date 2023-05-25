<?php
/**
 * Third party: WPML
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

// Add plugin name
$cacheWPML = [
    "plugin" => seokey_helper_cache_data('languages')
];

if (function_exists('wpml_get_setting_filter')) {
    $language_negotiation_type = wpml_get_setting_filter(false, 'language_negotiation_type');

    // Detect what sub-domain type we have
    switch ( $language_negotiation_type ){
        case 1:
            $cacheWPML['site']['domain_type'] = "suffix";
            break;
        case 2:
            $cacheWPML['site']['domain_type'] = "subdomain";
            break;
        case 3:
            $cacheWPML['site']['domain_type'] = "domain";
            break;
        default:
            $cacheWPML['site']['domain_type'] = "unknown";
            break;
    }
}

global $sitepress;

// First get All domains
if ( empty( $sitepress ) || ! method_exists( $sitepress, 'language_url' ) ) {
    $domains = [];
}else{
    $languages  = apply_filters( 'wpml_active_languages', [] );
    foreach ( $languages as $language ) {
        $domain = $sitepress->language_url( $language['code'] );
        $domain = rtrim($domain, "/");
        $domains[ $language['code'] ] = [
            'domain' => $domain
        ];
    }
}
// Second get domain type
if ( empty( $sitepress ) || ! method_exists( $sitepress, 'get_setting' ) ) {
    $cacheWPML['site']['domain_type'] = 'unknown';
}else{
    $language_negotiation_type = $sitepress->get_setting( 'language_negotiation_type' );
    // Detect what sub-domain type we have
    switch ($language_negotiation_type){
        case 1:
            $cacheWPML['site']['domain_type'] = "suffix";
            break;
        case 2:
            $cacheWPML['site']['domain_type'] = "subdomain";
            break;
        case 3:
            $cacheWPML['site']['domain_type'] = "get";
            break;
    }
}

$languages  = apply_filters( 'wpml_active_languages', [] );
$tab_lang = [];
$cacheWPML["lang"] = [];
$code_countries = array_flip( seokey_helpers_get_codes_countries() );
//TODO GOV NOW : base_domain_lang
foreach( $languages as  $language ){
    $split = explode( "_", $language['default_locale'] );
    $domainLang = isset( $domains[ $language['code'] ]['domain'] ) ? $domains[ $language['code'] ]['domain'] : site_url();
    if($domainLang == site_url() ){
        $cacheWPML['site']['base_domain_lang'] = $code_countries[ $split[1] ];
    }
    $cacheWPML['lang'][ $code_countries[ $split[1] ] ] = array(
        "iso2"      => $language['code'],
        "locale"    => $language['default_locale'],
        "domain"    => $domainLang,
        //"french"    => seokey_helpers_get_iso_countries()[$code_countries[ $split[1] ]]['fr'],
        "name"   => seokey_helpers_get_iso_countries()[$code_countries[ $split[1] ]]['en'],
    );
}
// Default Language
if( function_exists('wpml_get_default_language') && function_exists('icl_get_languages')) {
    $default_lang = wpml_get_default_language();
    $default_locale = icl_get_languages()[$default_lang]['default_locale'];
    $split = explode( "_", $default_locale );
    $cacheWPML['site']['default_lang'] = $code_countries[$split[1]];
}else{
    // Take random 1st if not exists
    $cacheWPML['site']['default_lang'] = array_key_first( $cacheWPML['lang'] );
}
if (!isset($cacheWPML['site']['base_domain_lang'])){
    $cacheWPML['site']['base_domain_lang'] = $cacheWPML['site']['default_lang'];
}
unset($code_countries);
unset($domains);
seokey_helper_cache_data('languages',$cacheWPML);

add_filter('seokey_filter_home_url', 'seokey_thirdparty_wpml_home_url');
// TODO Comment
function seokey_thirdparty_wpml_home_url( $home_url ) {
	return apply_filters( 'wpml_home_url', home_url() );
}

add_filter('seokey_filter_sitemap_native_redirect_lang', 'seokey_thirdparty_wpml_current_language');
// TODO Comment
function seokey_thirdparty_wpml_current_language( $lang ) {
	$lang = apply_filters( 'wpml_current_language', $lang );
	return $lang;
}