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

// TODO : Faire fonctionner la clé API en DEV
// Add Default locale in cache
if( function_exists('get_locale' ) ){
    $locale = get_locale();
    $split = explode( "_", $locale );
    $single_lang = $split[0];
} else {
    $single_lang = 'all';
}
// Add plugin name
$cacheLanguage["lang"][$single_lang] = home_url();
// Add plugin name
$cacheWeglot = [
    "plugin" => seokey_helper_cache_data('languages')
];

// Check if Weglot is active
if ( function_exists( 'weglot_get_languages' ) ) {
    $languages = weglot_get_languages();
    $domains = array();
    // Loop through the languages
    foreach ( $languages as $language ) {
        $domains[] = $language['url'];
    }
    // Output the domains
    echo '<pre>';
    print_r( $domains );
    echo '</pre>';
} else {
    echo 'Site is not using Weglot';
}


$code_countries = array_flip( seokey_helpers_get_codes_countries() );

$cacheLanguage["lang"] = array(
    $code_countries[$split[1]] => array(
        "iso2"      => $single_lang,
        "locale"    => $locale,
        "domain"    => home_url(),
        //"name"      => seokey_helpers_get_iso_countries()[$code_countries[$split[1]]['en']],

    )
);
seokey_helper_cache_data('languages', $cacheLanguage );
