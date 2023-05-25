<?php
/**
 * Suppression des sitesmap à la désactivation du plugin
 *
 * @since   0.0.1
 * @author  Léo Fontin
 *
 * @see     seokey_activate_deactivate_sitemap_delete_files()
 * @see     plugin-activate-deactivate-uninstall.php
 * @package SEOKEY
 *
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You lost the key...' );
}


class Seokey_Sitemap_Delete {

    /**
     * Liste des sitemaps générés par SEOKEY
     * @var array
     */
    protected $list = [];


    /**
     * Intialisation de la suppression des sitemap
     *
     * @since   0.0.1
     * @author  Leo Fontin
     */
    public function seokey_sitemap_delete_init() {
        $this->seokey_sitemap_get_list();
        $this->seokey_sitemap_delete_files();
        $this->seokey_sitemap_delete_bdd();

    }

    /**
     * Récupération de la liste des sitemap (en option)
     *
     * @since   0.0.1
     * @author  Leo Fontin
     */
    public function seokey_sitemap_get_list() {
        $this->list = get_option( 'seokey_option_sitemap_list');
    }

    /**
     * Suppression des fichiers sitemap
     *
     * @since   0.0.1
     * @author  Leo Fontin
     */
    public function seokey_sitemap_delete_files() {
        // Delete folder + all files inside
	    seokey_helper_delete_folder(SEOKEY_SITEMAPS_PATH );
        delete_option( 'seokey_option_sitemap_list' );
    }



    /**
     * Suppression des sitemap en bdd
     *
     * @since   0.0.1
     * @author  Leo Fontin
     */
    public function seokey_sitemap_delete_bdd() {

        if ( ! empty( $this->list ) ) {
            foreach ( $this->list as $item ) {
                delete_option( 'seokey-' . $item );
            }
        }
    }

}