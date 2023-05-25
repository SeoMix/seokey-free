<?php
/**
 * Load SEOKEY Dashboard page functions
 *
 * @Loaded on 'init' & is_admin()
 *
 * @see seokey_settings_api_get_config_sections()
 * @see seokey_settings_api_get_config_fields()
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

add_action( 'seokey_action_admin_pages_wrapper', 'seokey_admin_page_dashboard', 50 );
/**
 * Generate admin dashboard content
 *
*
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see seokey_admin_page_content_*()
 *
 * @hook seokey_action_admin_pages_wrapper, 50
 * @return void (string) $render Main menu content
 */
function seokey_admin_page_dashboard() {
    $screen       = seokey_helper_get_current_screen();
	$current_page = $screen->base;
	// Are we in the dashboard page ?
	if ( $current_page === 'toplevel_page_seo-key' ) {
        // Display content when wizard has been finished
        $current_wizard = get_option('seokey_option_first_wizard_seokey_notice_wizard');
        if ( 'goodtogo' === $current_wizard ) { ?>
            <div class="seokey-wrapper-limit">
                <h2><?php esc_html_e( 'The most efficient SEO plugin', 'seo-key' );?></h2>
                <p><?php esc_html_e( 'Here is a quick access to all you need to soar on Google!', 'seo-key' );?></p>
                <div class="seokey-subwrapper">
                    <div class="seokey-item">
                        <h2><i class="dashicons dashicons-analytics"></i> <?php esc_html_e('Your SEO Score', 'seo-key' );?></h2>

                        <section class='seokey-dashboard-section'>
                            <?php
                            $score                              = get_option('seokey_audit_global_data_score_now');
                            $score_display                      = ( false === $score  ) ? '-' : (int) $score;
                            $score_class = $score_class_move    = '';
                            $score_variation                    = seokey_audit_global_data_score_diff();
                            if ( ! empty ( $score_variation ) ) {
	                            if ( $score_variation > 0 ) {
		                            $score_class     = $score_class_move = 'audit-up';
		                            $score_variation = sprintf( _n( '+ %s point', '+ %s points', $score_variation, 'seo-key' ), number_format_i18n( $score_variation ) );
	                            } elseif ( $score_variation < 0 ) {
		                            $score_class     = $score_class_move = 'audit-down';
		                            $score_variation = sprintf( _n( '%s point', '%s points', $score_variation, 'seo-key' ), number_format_i18n( $score_variation ) );
	                            }
                            }
                            ?>
                            <div id="seokey-audit-score" data-score="<?php echo esc_attr($score_display);?>" class="flexboxcolumn">
                                <div id="seokey-audit-score-outter-circle" class="good">
                                    <div id="seokey-audit-score-inner-circle">
                                        <div id="seokey-audit-score-int" class="<?php echo esc_attr( $score_class_move );?>"><?php echo $score_display;?></div>
                                        <div id="seokey-audit-score-scale">/ 50</div>
                                        <div id="seokey-audit-score-variation" class="<?php echo esc_attr( $score_class );?>"><?php echo $score_variation;?></div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="seokey-item">
                        <h2><i class="dashicons dashicons-admin-tools"></i> <?php esc_html_e('Tools', 'seo-key' );?></h2>
                        <section class='seokey-dashboard-section'>
                            <a class="button button-primary button-hero" href="<?php echo seokey_helper_admin_get_link( 'audit' ); ?>">
                                <?php esc_html_e('SEO Audit', 'seo-key' );?>
                            </a>
                            <a class="button button-primary button-hero" href="<?php echo seokey_helper_admin_get_link( 'redirections' ); ?>">
                                <?php esc_html_e('Redirections', 'seo-key' );?>
                            </a>
                            <a class="button button-primary button-hero" href="<?php echo admin_url( 'upload.php?mode=list&seokeyalteditor=yes' ) ?>">
                                <?php esc_html_e('Image ALT editor', 'seo-key' );?>
                            </a>
                        </section>
                    </div>
                    <div class="seokey-item">
                        <h2><i class="dashicons dashicons-admin-generic"></i> <?php esc_html_e('Configuration and help', 'seo-key' );?></h2>
                        <section class='seokey-dashboard-section'>
                            <a class="button button-primary button-hero" href="<?php echo seokey_helper_admin_get_link( 'settings' ); ?>">
                                <?php esc_html_e('Settings', 'seo-key' );?>
                            </a>
                            <a class="button button-primary button-hero" href="<?php echo seokey_helper_admin_get_link( 'support' ); ?>">
                                <?php esc_html_e('Support', 'seo-key' );?>
                            </a>
                        </section>
                    </div>
                </div>
            </div>
            <?php }
        echo '</div>';
	}
}