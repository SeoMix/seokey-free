<?php
/**
 * Load every SEOKEY bot functions
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 *
 * @package SEOKEY
 * @see https://www.in-cloaking-veritas.com/ InCloakingVeritas
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}

// Check if user is Googlebot
function seokey_helper_googlebot_test() {
    // TODO Later : detect Bing + Yandex + Baidu + Qwant + DuckDuckGO

    // First basic check : useragent
    if ( false === seokey_helper_googlebot_checkUserAgent() ) {
        return false;
    }
    // Reverse DNS from IP adress
    $ip = seokey_helper_googlebot_get_ip();
    if ( false === seokey_helper_googlebot_check_dns( $ip ) ) {
        return false;
    }
    // TODO Maybe : use the ip list https://developers.google.com/search/apis/ipranges/googlebot.json?hl=fr
    return true;
}

// Check if user agent contains Googlebot
function seokey_helper_googlebot_checkUserAgent() {
	if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		preg_match( "/Googlebot/", $_SERVER['HTTP_USER_AGENT'], $matchesUAgent, PREG_OFFSET_CAPTURE );
		if ( count( $matchesUAgent ) > 0 ) {
			return TRUE;
		}
	}
    return false;
}

// Get Current user IP (warning, can be spoofed)
function seokey_helper_googlebot_get_ip(){
    $ip = isset($_SERVER['HTTP_CLIENT_IP'])
        ? $_SERVER['HTTP_CLIENT_IP']
        : (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : $_SERVER['REMOTE_ADDR']);
    return $ip;
}

// Get host by IP
function seokey_helper_googlebot_get_host( $ip ){
    $host = gethostbyaddr( $ip );
    return $host;
}

// Check Googlebot DNS
function seokey_helper_googlebot_check_dns( $ip ){
    $dns = seokey_helper_googlebot_get_host( $ip );
    preg_match( "/googlebot/", $dns, $matchesDnsName, PREG_OFFSET_CAPTURE );
    if( count( $matchesDnsName) > 0 ) {
        return true;
    }
    return false;
}