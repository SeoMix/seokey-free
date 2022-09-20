<?php
/**
 * Load SEOKEY Sitemap background classes
 *
 * @Loaded  on 'plugins_loaded'
 *
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
 * Sitemap Background processing class
 */
class SeoKey_Class_Background_Sitemap_Trigger extends SeoKey_Class_Singleton {

    // Define instance for this class
    protected static $_instance;

    // Define our background process
    protected $background_process;

    // Always fire a background process
    protected function _init() {
        $this->background_process = new SeoKey_Class_Background_Sitemap_Process;
    }

    // Public function for running a sitemap creation
    public function run_sitemap_creation_all() {
        $task_list = [
            'seokey_sitemap_init_post',
            'seokey_sitemap_init_taxonomies',
            'seokey_sitemap_init_authors',
            'seokey_sitemap_init_end',
        ];
        // Prepare Queue for content issues
        if ( !empty ( $task_list ) ) {
            foreach ($task_list as $task) {
                $this->background_process->push_to_queue($task);
            }
        }
        $this->background_process->save()->dispatch();
        // The end
        return true;
    }
}

// TODO COMMENT
class SeoKey_Class_Background_Sitemap_Process extends SeoKey_WP_Background_Process {
    // Lets give a name to our stuff
    protected $prefix = 'seokey';
    protected $action = 'background-seokey';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        if ( ! $item || ! is_string( $item ) ) {
            seokey_dev_write_log('incorrect task');
            return false;
        }
        switch ( $item ) {
            // Sitemaps tasks
            case 'seokey_sitemap_set_term_lastmod':
                require_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-lastmod.php' );
                $create = new Seokey_Sitemap_Lastmod();
                $create->seokey_sitemap_set_term_lastmod();
                break;
            case 'seokey_sitemap_set_author_lastmod':
                require_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-lastmod.php' );
                $create = new Seokey_Sitemap_Lastmod();
                $create->seokey_sitemap_set_author_lastmod();
                break;
            case 'seokey_sitemap_init_post':
                require_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-render.php' );
                $create = new Seokey_Sitemap_Render();
                $create->seokey_sitemap_init( 'post' );
                break;
            case 'seokey_sitemap_init_taxonomies':
                require_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-render.php' );
                $create = new Seokey_Sitemap_Render();
                $create->seokey_sitemap_init( 'term' );
                break;
            case 'seokey_sitemap_init_authors':
                require_once( SEOKEY_PATH_ADMIN. 'modules/sitemap/sitemaps-render.php' );
                $create = new Seokey_Sitemap_Render();
                $create->seokey_sitemap_init( 'author' );
                break;
	        case 'seokey_sitemap_init_end':
		        // Change sitemap status
		        update_option( 'seokey_sitemap_creation', 'done', true );
		        // Force Robots.txt changes
                seokey_helper_files( 'delete', 'robots' );
                seokey_helper_files( 'create', 'robots' );
                // It was the last sitemap : sitemap creation has ended
	            // Submit to search console updated sitemap
	            if ( ! seokey_helpers_is_free() ) {
		            $sitemaps = new Seokey_SearchConsole_Sitemaps();
		            $sitemaps->seokey_gsc_sitemaps_push();
	            }
		        // Flush rewrite rules
		        flush_rewrite_rules();
		        break;
        }
        // Sitemap wait function
        sleep(2);
        // task completed, remove it from queue with false return value
        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();
    }
}