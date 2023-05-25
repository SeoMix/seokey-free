<?php
/**
 * Load SEOKEY modules
 *
 * @Loaded on plugins_loaded
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

// Define module location for our seokey_helper_require_file functions below
$modules    = SEOKEY_PATH_PUBLIC . 'modules/';
$condition  = ['seokey_option_first_wizard_seokey_notice_wizard' => 'goodtogo'];

// Load header actions
seokey_helper_require_file( 'header',                   $modules, 'everyone', $condition );
// Load footer actions
seokey_helper_require_file( 'footer',                   $modules, 'everyone', $condition );
// Load meta title sitename improvements
seokey_helper_require_file( 'meta-title',               $modules, 'everyone', $condition );
// Load meta description sitename improvements
seokey_helper_require_file( 'meta-desc',                $modules, 'everyone', $condition );
// Load /category/ functions (needs to be loaded on admin and front side)
seokey_helper_require_file( 'url-category-base',        $modules, 'everyone', $condition );
// Load comments module
seokey_helper_require_file( 'comments',                 $modules, 'everyone', $condition  );
// Load content visibility functions
seokey_helper_require_file( 'meta-robots',              $modules, 'everyone', $condition );
// Load robots module
seokey_helper_require_file( 'alt-images',               $modules, 'everyone', $condition );

// All modules below should not be loaded on admin pages
if ( is_admin() ) {
	return;
}

// Load robots module
seokey_helper_require_file( 'robots-txt',               $modules, 'everyone', $condition );
// Load canonical module
seokey_helper_require_file( 'meta-canonical',           $modules, 'everyone', $condition );
// Load RSS improvements
seokey_helper_require_file( 'rss',                      $modules, 'everyone', $condition );
// Load login page functions
seokey_helper_require_file( 'login-page',               $modules, 'everyone', $condition );
// Load date archives module
seokey_helper_require_file( 'archives-date',            $modules, 'everyone', $condition );
// Load author archives module
seokey_helper_require_file( 'archives-author',          $modules, 'everyone', $condition );
// Load Automatic redirection module
seokey_helper_require_file( 'redirections-automatic',       $modules. 'redirections/', 'everyone', $condition );
// Load Manual redirection module
seokey_helper_require_file( 'redirections-manual',      $modules. 'redirections/', 'everyone', $condition );
// Load schema.org json-ld markup
seokey_helper_require_file( 'schema-org',               $modules, 'everyone', $condition );
// Load sitemap module
seokey_helper_require_file( 'sitemaps',                 $modules, 'everyone', $condition );
// Load breadcrumbs module
seokey_helper_require_file( 'breadcrumbs',              $modules, 'everyone' );