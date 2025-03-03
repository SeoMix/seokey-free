<?php
/**
 * Load every SEOKEY admin module
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

/* Define useful loading var */
$modules     = SEOKEY_PATH_ADMIN . 'modules/';
$condition  = ['seokey_option_first_wizard_seokey_notice_wizard' => 'goodtogo'];

// Load admin assets (CSS, fonts, icons and JS)
seokey_helper_require_file( 'admin-assets',                 SEOKEY_PATH_ADMIN, 'contributor' );
// Load notification functions
seokey_helper_require_file( 'notifications',                SEOKEY_PATH_ADMIN . 'api/', 'contributor' );

// Load admin only functions
if ( is_admin() ) {
    // Always loaded
	    // Load metabox functions helpers
	    seokey_helper_require_file( 'admin-helpers-metabox',    SEOKEY_PATH_ADMIN. 'helpers/', 'contributor' );
	    // Load admin content for each SEOKEY admin page
	    seokey_helper_require_file( 'class-settings-api',       SEOKEY_PATH_ADMIN . 'api/', 'admin' );
	    // Load Interface (menus and plugin links)
	    seokey_helper_require_file( 'admin-links-menus',        SEOKEY_PATH_ADMIN . 'admin-menus/', 'author' );
	    // Load SEOKEY admin Class Settings API
	    seokey_helper_require_file( 'admin-pages',              SEOKEY_PATH_ADMIN . 'admin-pages/', 'author' );

		
    // Loaded after wizard
        // Load Seokey Check Other Extension class
	    seokey_helper_require_file( 'plugin-check-plugins',     SEOKEY_PATH_ADMIN, 'admin' );
	    // Load admin tinymce functions
	    seokey_helper_require_file( 'editor-tinymce',           $modules, 'contributor', $condition );
	    // Load metaboxes for both title and desc
	    seokey_helper_require_file( 'metabox',                  $modules, 'contributor', $condition  );
	    // Load Robots.txt admin functions
	    seokey_helper_require_file( 'robots-txt',               $modules, 'admin', $condition );
	    // Load Ping functions
	    seokey_helper_require_file( 'pings',                    $modules, 'contributor', $condition );
	    //Load admin attachement functions
	    seokey_helper_require_file( 'medias-attachments',       $modules, 'contributor', $condition );
	    // Load Media library functions
	    seokey_helper_require_file( 'medias-library',           $modules, 'editor', $condition );
	    // Load Media functions
	    seokey_helper_require_file( 'medias-upload',            $modules, 'contributor', $condition );
	    // Load Default Settings pages functions
	    seokey_helper_require_file( 'settings',                 $modules, 'admin', $condition );
	    // Load User profiles functions
	    seokey_helper_require_file( 'user-profile',             $modules, 'author', $condition );
}

// Always loaded
	// Custom extended WP List table class for next modules
	seokey_helper_require_file( 'class-wp_list_table',          SEOKEY_PATH_COMMON, 'contributor' );
	// Load Installation and update Wizard
	seokey_helper_require_file( 'automatic_optimizations',      $modules, 'editor' );
	// Load hoempage meta title and meta desc sync
	seokey_helper_require_file( 'homepage',                     $modules, 'admin' );
	// Load Installation and update Wizard
	seokey_helper_require_file( 'wizard',                       SEOKEY_PATH_ADMIN, 'admin' );
	// Load admin bar
	seokey_helper_require_file( 'admin-links-admin-bar',        SEOKEY_PATH_ADMIN . 'admin-menus/', 'author' );
	// Load Search Console
	seokey_helper_require_file( 'search-console',               $modules . 'search-console/', 'everyone' );
	// Load watcher functions
	seokey_helper_require_file( 'watcher-new-content',          $modules, 'everyone' );
	seokey_helper_require_file( 'watcher-401',                  $modules, 'editor' );
	// Load Import functions
	seokey_helper_require_file( 'import',                       $modules. 'import/', 'admin' );
	// Load blocks
	seokey_helper_require_file( 'blocks',                       $modules. 'blocks/', 'everyone' );

// Loaded after wizard
	// Load redirection module
	seokey_helper_require_file( 'redirections',                 $modules . 'redirections/', 'editor', $condition );
	// Load sitemaps
	seokey_helper_require_file( 'sitemaps',                     $modules. 'sitemap/', 'author', $condition );
	// Load TinyMCE while editing a term
	seokey_helper_require_file( 'term-tinymce',                 $modules, 'contributor',$condition );
	// Load Post Type archive menu
	seokey_helper_require_file( 'admin-links-pt-archive-menu',  SEOKEY_PATH_ADMIN . 'admin-menus/', 'editor', $condition );
	// Load audit module
	seokey_helper_require_file( 'audit',                        $modules . 'audit/', 'contributor', $condition );
	// Load ajax functions
	seokey_helper_require_file( 'admin-ajax',                   SEOKEY_PATH_ADMIN . 'helpers/', 'contributor', $condition );
	// Load Keyword module
	seokey_helper_require_file( 'keywords',                     $modules. 'keywords/', 'contributor', $condition );