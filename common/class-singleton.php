<?php
/**
 * Singleton CLASS
 *
 * @Loaded  during plugin load
 * @see     seokey_load()
 * @see     seo-key-helpers.php
 *
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );


/**
 * Class singleton
 *
 * @author Daniel Roch
 * @since  0.0.1
 */
class SeoKey_Class_Singleton {
    /**
     * @var $_instance : only one instance for our classes
     */
    protected static $_instance;

    // Init function : needs to be overrided
    protected function _init() {}

    // Only get one instance for our classes
    final public static function get_instance() {
        if ( ! isset( static::$_instance ) ) {
            static::$_instance = new static;
        }
        return static::$_instance;
    }

    /**
     * Private constructor
     */
    final private function __construct() {
        $this->_init();
    }

    /**
     * Private clone method
     *
     * @since 0.0.1
     */
    private function __clone() {}

    /**
     * Private unserialize method
     *
     * @since 0.0.1
     */
    public function __wakeup() {}
}
