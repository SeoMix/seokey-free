<?php
/**
 * Audit admin page rendering
 *
 * @Loaded  on 'init' & is_admin() && role editor
 *
 * @see     admin-module.php
 * @see     Seokey_Audit->seokey_audit_render();
 * @package SEOKEY
 */

/**
 * Security
 *
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// User Data
global $current_user;
$first_name             = get_user_meta( $current_user->ID, 'first_name', true );
$name                   = ( ! empty( $first_name ) ) ? $first_name : $current_user->display_name;
// Audit score + dates
$score                              = get_option('seokey_audit_global_data_score_now');
$score_display                      = ( false === $score  ) ? '-' : (int) $score;
$last_date                          = get_option( 'seokey_audit_global_last_update' );
$last_date_timestamp                = ( $last_date !== false ) ? $last_date : '';
$friendly_date                      = ( $last_date !== false ) ? date_i18n( _x( 'Y-m-d H:i', 'timezone date format' ), $last_date_timestamp ) : '';
$score_variation                    = seokey_audit_global_data_score_diff();
$score_class = $score_class_move    = '';

if ( (int) $score_variation > 0 ) {
	$score_class = $score_class_move    = 'audit-up';
	$score_variation                    = sprintf( _n( '+ %s point', '+ %s points', $score_variation, 'seo-key' ), number_format_i18n( $score_variation ) );
} elseif ( (int) $score_variation < 0 ) {
	$score_class = $score_class_move    = 'audit-down';
	$score_variation                    = sprintf( _n( '%s point', '%s points', $score_variation, 'seo-key' ), number_format_i18n( $score_variation ) );
}

$audit_status = ( false === get_option( 'seokey_audit_running' ) ) ? "none" : "running";
// Per_page value for ajax calls (beacause current screen can not be correctly defined
echo '<input id="global_per_page" type="hidden" value="' . (int) seokey_helper_get_screen_option('per_page', 'seokey_audit_per_page', 20) . '">';
?>
<h1 class="screen-reader-text"><?php
    esc_html_e('SEO Audit of: ', 'seo-key');
    echo get_bloginfo('name'); ?>
</h1>
<div id="seokey-audit" class="wrap" data-state="<?php echo esc_attr( $audit_status );?>">
    <section id="audit-header">
        <div class="flexbox" id="seokey-audit-global">
            <div class="flexboxcolumn">
                <h2 class="has-explanation">
                    <?php
                        esc_html_e( 'SEO Score', 'seo-key');
                        echo seokey_helper_help_messages('seo-score-explanation');
                    ?>
                </h2>
                <div id="seokey-audit-score" data-score="<?php echo esc_attr( $score_display );?>" class="flexboxcolumn">
                    <div id="seokey-audit-score-outter-circle" class="good">
                        <div id="seokey-audit-score-inner-circle">
                            <div id="seokey-audit-score-int" class="<?php echo esc_attr( $score_class_move );?>"><?php echo $score_display;?></div>
                            <div id="seokey-audit-score-scale">/ 100</div>
                            <div id="seokey-audit-score-variation" class="<?php echo esc_attr( $score_class );?>"><?php echo $score_variation;?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="seokey-audit-actions" class="flexboxcolumn">
                <p id="audit-welcome"><?php
                    esc_html_e( seokey_audit_message( $score) );
                    echo '<span id="audit-welcome-name">';
                        esc_html_e( ucfirst( $name ) );
                    echo '</span>';?>
                </p>
                <p id="audit-message"><?php esc_html_e( seokey_audit_message_detail( $score ) ); ?></p>
                <?php seokey_audit_message_detail_issues(); ?>

                <section id="audit-launch" class="loader-dark">
                    <span>
                        <?php
                            // Audit button
                            seokey_audit_global_launch_button_ajax();
                        ?>
                        <p id="audit-last-time">
                            <?php
                            // Last audit date
                            if ( $last_date !== false ) {
                                // Check real current date
	                            $immutable_date = date_create_immutable_from_format( 'Y-m-d H:i:s', current_time('Y-m-d H:i:s'), new DateTimeZone( 'UTC' ) );
	                            $format = DATE_W3C;
	                            $strtotime = strtotime ( $immutable_date->format( $format ) ) - DAY_IN_SECONDS;
	
	                            // Define text
	                            $human_time = human_time_diff( $last_date_timestamp, current_time( 'timestamp' ) );
	                            //Switch then display last audit date
                                if ( $last_date_timestamp >= $strtotime ) {
	                                printf( _x( 'Last audit %1$s ago', '%1$s is a human-readable time difference', 'seo-key' ), $human_time );
                                } else {
	                                printf( _x( 'Last audit on %1$s', '%1$s is the last audit date', 'seo-key' ), $friendly_date );
                                }

                            } else {
                                echo '&nbsp';
                            } ?>
                        </p>
                    </span>
                    <?php
                        // Audit spinner
                        seokey_helper_loader( 'main-audit' );
                    ?>
                    <div id="audit-loader-main-text">
                        <span id="audit-loader-main-text-count"></span>
                        <span id="audit-loader-main-text-details"></span>
                    </div>
                    </section>
            </div>
            <?php seokey_audit_message_issues_details();?>
        </div>

    </section>

    <section id="audit-main">

        <?php
        $continue = true;
        if (  "running" === $audit_status || true === get_option( 'seokey_audit_running' ) ) {
            $continue = false;
            ?>
            <p><?php printf( esc_html__('%s, your audit is still running right now.', 'seo-key'), esc_html ( ucfirst( $name ) ) ); ?></p>
            <?php
        }
        if ( false === $score ) {
            $continue = false;
        }
        if ( true === $continue ) {?>
            <p><?php printf( esc_html__('%s, you will find below the results of your SEO audit:', 'seo-key'), esc_html ( ucfirst( $name ) ) ); ?></p>
            <?php
            // Get audit tabs and user data
            $audits     = seokey_audit_global_types();
            // Find user tab
            $user_tabs  = get_user_meta( wp_get_current_user()->ID, 'seokey-audit-tab', true );
            // Define tabs
            foreach ( $audits as $key => $type ) {
                $tab        = sanitize_title( $key );
                $filepath   = SEOKEY_PATH_ADMIN . 'modules/audit/parts/view-' . $tab . '.php';
                // Find the active tab
                if ( !empty( $user_tabs['seo-key-audit'] ) ) {
                    $tab_active = ( $tab === $user_tabs['seo-key-audit'] ) ? true : false;
                } else {
                    $tab_active = false;
                    if ($key === array_key_first( $audits ) ) {
                        $tab_active = true;
                    }
                }
                if ( file_exists( $filepath ) ) {
                    $tabs[] = array(
                        'name'      => esc_html( $type['name'] ),
                        'tab'       => $tab,
                        'filepath'  => $filepath,
                        'active'    => $tab_active,
                        'count'     => (int) $type['count'],
                    );
                }
            }
            if ( !empty ( $tabs ) ) {
                // Nonce for tab ajax call (update user meta to remind wich tab was on)
                $nonce = wp_create_nonce( 'seokey-audit-tabs-nonce' );
                // Tab loading
                echo '<div id="tabs" data-ajax-nonce="' . esc_attr( $nonce ) . '">';
                    echo '<nav class="nav-tab-wrapper">';
                        $i = 0;
                        foreach ( $tabs as $tab ) {
                            $class = ( true === $tab['active'] ) ? ' nav-tab-active' : '';
                            echo '<a id="' . $tab['tab'] . '" href="#audit-' . $tab['tab'] .'" class="nav-tab' . $class . '">'. $tab['name'] . '</a>';
                            $active = '';
                            $i++;
                        }
                    echo '</nav>';
                // Content loading for each tab
                foreach ( $tabs as $tab ) {
                    $active = ( true === $tab['active'] ) ? ' is-opened' : '';
                    echo '<div id="audit-' . $tab['tab'] .'" class="tabs-content' . $active . '">';
                        // TODO FILTRES PAR PRIORITE/EFFORT
                        seokey_helper_cache_data('seokey-audit-tab-id', $tab['tab'] );
                        include $tab['filepath'];
                    echo '</div>';
                    // Only for the first one
                    $active = '';
                }
                echo '</div>';
            }
        }
        ?>
    </section>
</div>
