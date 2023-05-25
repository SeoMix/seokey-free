<?php
/**
 * Third party: 1 lang only
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

$cacheLanguage['site']['domain_type'] = "single";

// Add Default locale in cache
if( function_exists('get_locale' ) ){
    $code_countries = array_flip( seokey_helpers_get_codes_countries() );
    $locale = get_locale();
    $split = explode( "_", $locale );
    $single_lang = $split[0];

    // Add plugin name
    $cacheLanguage["lang"][$single_lang] = home_url();
    $cacheLanguage['site']['default_lang'] = $cacheLanguage['site']['base_domain_lang'] = $code_countries[$split[1]];
    $cacheLanguage["lang"] = array(
        $code_countries[$split[1]] => array(
            "iso2"      => $single_lang,
            "locale"    => $locale,
            "domain"    => home_url(),
            "name"      => seokey_helpers_get_iso_countries()[ $code_countries[ $split[1] ] ]['en'],

        )
    );
}
else{
    seokey_dev_write_log('Function get_locale() must exists in i18n/single.php line 22 !');
}
unset($code_countries);
seokey_helper_cache_data('languages', $cacheLanguage );
unset($cacheLanguage);