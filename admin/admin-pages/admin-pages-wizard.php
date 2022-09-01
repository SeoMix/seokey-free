<?php
/**
 * Load SEOKEY Admin Wizard content and functions
 *
 * @Loaded  on 'plugin_loaded'
 * @Loaded  with plugin configuration file + admin-menus-and-links.php
 *
 * @see     seokey_settings_api_get_config_sections()
 * @see     seokey_settings_api_get_config_fields()
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

add_action ('current_screen', 'seokey_admin_wizard_section_custom_settings', 100 );
/**
 * Customize Settings API
 *
 * @author  Daniel Roch
 * @since   0.0.1
 *
 * @see seokey_admin_page_content_*()
 *
 * @hook seokey_action_admin_pages_wrapper, 50
 * @return void (string) $render Main menu content
 */
function seokey_admin_wizard_section_custom_settings(){
    $screen       = seokey_helper_get_current_screen();
    $current_page = $screen->base;
    // Are we in the dashboard page ?
    if ( $current_page === 'seokey_page_seo-key-wizard' ) {
        add_action( 'seokey_action_admin_pages_wrapper',                'seokey_admin_page_wizard', 10 );
        add_filter( 'seokey_filter_settings_form_with_submit_button',   'seokey_wizard_section_button_save', 100 );
        add_action( 'seokey_action_setting_form_button_after',          'seokey_wizard_section_button_ignore' );
        add_action( 'seokey_action_setting_form_button_before',         'seokey_wizard_section_button_back' );
    }
}

/**
 * Generate admin Wizard top content
 *
 * @author  Daniel Roch
 * @since   0.0.1
 **
 * @hook seokey_action_admin_pages_wrapper, 10
 * @return void
 */
function seokey_admin_page_wizard() {
    echo '<section id="seokey-wizard-top">';
        echo '<h1 class="screen-reader-text">' . esc_html__('SEOKEY Installation and upgrade Wizard', 'seo-key' ) .'</h1>';
        echo '<img class="inlineb" src="' . esc_url( SEOKEY_URL_ASSETS . 'img/logo-seo-key-blanc.webp' ) . '" alt="SEOKEY">';
        echo '<p>'. esc_html__( 'Unlock WordPress SEO','seo-key' ) .'</p>';
    echo '</section>';
    seokey_admin_page_wizard_steps_breacdrumb();
    do_action ( 'seokey_admin_page_wizard_content' );
}





/**
 * Change "Save all" button to "save" button
 *
 * @author  Daniel Roch
 * @since   0.0.1
 **
 * @hook seokey_filter_settings_form_with_submit_button
 * @return string Button text value
 */
function seokey_wizard_section_button_save( $text ){
    return esc_html_x ("Save and continue", "Next button in the wizard page", "seo-key" );
}

/**
 * Add "Ignore" button
 *
 * @author  Daniel Roch
 * @since   0.0.1
 **
 * @hook seokey_wizard_section_button_ignore
 */
function seokey_wizard_section_button_ignore() {
    // Data
    $current_url = seokey_helper_admin_get_link('wizard');
    $current = seokey_admin_wizard_get_status();
    $steps = seokey_admin_wizard_steps();
    // Next link data
    $current_next = seokey_wizard_get_next_step( $current );
    $keys = array_key_first( $current_next );
    $anchor_next = '#'. sanitize_title( $steps[$keys][0] );
    // Construct next step URL
    $url = add_query_arg( 'wizard-status', array_key_first( $current_next ), $current_url );
    echo '<a id="skip" href="' . esc_url( $url ) . $anchor_next . '">' . esc_html__( 'Skip this step', 'seo-key' ) . '</a>';
}

/**
 * Add "Back" button
 *
 * @author  Daniel Roch
 * @since   0.0.1
 **
 * @hook seokey_wizard_section_button_ignore
 */
function seokey_wizard_section_button_back() {
    // Data
    $current_url = seokey_helper_admin_get_link('wizard');
    $current = seokey_admin_wizard_get_status();
    $steps = seokey_admin_wizard_steps();
    // Previous link data
    $current_previous = seokey_wizard_get_previous_step($current);
    $keys = array_key_first($current_previous);
    $anchor_previous = '#'. sanitize_title( $steps[$keys][0] );
    // Construct previous step URL
    $url = add_query_arg( 'wizard-status', array_key_first( $current_previous ), $current_url );
	$startanchor = '#' . sanitize_title( _x( 'Start', 'Wizard Step', 'seo-key' ) );
    if ( $startanchor === $anchor_previous ) {
        $anchor_previous = '';
        $url = remove_query_arg( 'wizard-status', $url );
    }
    echo '<a id="previous" class="seokey-button seokey-secondary" href="' . esc_url( $url ) . $anchor_previous . '">' . esc_html__( 'Back', 'seo-key' ) . '</a>';
}

add_action('seokey_admin_page_wizard_content', 'seokey_admin_page_wizard_steps_content');
/**
 * First wizard page content (new installation or update)
 *
 * @author  Daniel Roch
 * @since   0.0.1
 **
 * @hook seokey_wizard_section_button_ignore
 */
function seokey_admin_page_wizard_steps_content() {
    $step = seokey_admin_wizard_get_status();
    echo '<section id="seokey-wizard-form" data-step="'. esc_attr( $step ) . '">';
        $status = seokey_admin_wizard_get_status();
        echo '<span id="' . sanitize_html_class( $status ) . '">';
            // Default starting content
            switch ( $status ) {
                case 'none':
                    echo '<section id="wizard-start">
						<h2 class="main-title">'.esc_html__( 'Welcome to SEOKEY !', 'seo-key' ) .'</h2>';
                        echo '<section id="wizard-start-bloc"><span>';
                        echo '<p>' . esc_html__( 'It will only takes a few minutes to get up and running.', 'seo-key' ) .'</p>';
                        echo '<p><strong>' . esc_html__( 'Why is SEOKEY the best SEO plugin?', 'seo-key' ) .'</strong></p>';
                        echo '<ul class="seokey-ul">
                            <li>' . esc_html__( 'Automatic optimization of your website', 'seo-key' ) .'</li>
                            <li>' . esc_html__( 'All you need to skyrocket on Google', 'seo-key' ) .'</li>
                            <li>' . esc_html__( 'Powerful SEO audit', 'seo-key' ) .'</li>
                        </ul>';
						//  Continue / Start button data
                        $current_url = seokey_helper_admin_get_link('wizard');
                        $steps = seokey_admin_wizard_steps();
                        $keys = array_keys( $steps );
                        $next = $keys[ array_search( "start",$keys ) + 1 ];
                        $anchor = sanitize_title( $steps[$next][0] );
	                    // Import data
		                $plugin_list = seokey_admin_import_list();
	                    $class = '';
						// Show content
						if ( $plugin_list ) {
			                seokey_admin_import_display( 'wizard' );
							$class = " hidestart";
		                }
                        echo '</span><span id="wizard-start-bloc-span-last">';
	                    echo '<a id="start" class="button button-primary button-hero ' . $class . '" href="' . esc_url( add_query_arg( 'wizard-status', $next, $current_url ) . '#' . $anchor ) . '">' . esc_html__( 'Let\'s start!', 'seo-key' ) . '</a>';
					echo '</span></section></section>';
                    break;
                case '1_0_5':
	                echo '<section id="wizard-end">';
					echo '<h2 class="main-title">'.esc_html__( 'SEOKEY is ready !', 'seo-key' ) .'</h2>';
                	// end of Wizard
                    seokey_wizard_end();
                    // Show text and add a button to access audit page
                    echo '<p>'.esc_html__( 'You can now start to audit your website!', 'seo-key' ) .'</p>';
	                echo '<p><strong>'.esc_html__( '(only PRO version provides the full SEO audit)', 'seo-key' ) .'</strong></p>';
                    $audit_url = seokey_helper_admin_get_link('audit');
                    echo '<a id="end" class="button-primary button-hero seokey-button seokey-primary" href="'. esc_url ( $audit_url ).'">'.esc_html__( 'Go to audit', 'seo-key' ) .'</a>';
                    echo '</section>';
					break;
                default:
                    break;
            }
        echo '</span>';
    echo '</section>';
}

/**
 * Display Wizard steps
 *
 * @author  Daniel Roch
 * @since   0.0.1
 */
function seokey_admin_page_wizard_steps_breacdrumb() {
	//Get current data
    $status = ( !empty( $_GET['wizard-status'] ) ) ? sanitize_title( $_GET['wizard-status'] ) : '0';
    $current_status = (int) str_replace( '_', '', $status );
	$status = seokey_admin_wizard_steps();
	// Rendering
    $render = '<ol id="seokey-wizard-steps">';
    $url_base = seokey_helper_admin_get_link( 'wizard' );
    // First values for links
    $from = 'start';
	$search = 0;
	$next = false;
    foreach ( $status as $id => $name ) {
        if ( $id === 'end' ) {
            $id = 10000;
        }
        $id = (int) str_replace( '_', '', $id );
        $class = "step step-" . sanitize_title($name[0]);
        if ( $current_status == $id ) {
            $class  .= " step-current";
        } elseif ( $current_status > $id ) {
            $class .= " step-done";
        } else {
        	$next = true;
	    }
        // Button name
        $name = '<span class="wizard-step-name">' . esc_html( $name[0] ) . '</span>';
        // Find next step
        $wizardstatus = array_key_first( seokey_wizard_get_next_step( $from, $search ) );
        // For next iterations, we will correct values for next step
	    $from = $wizardstatus;
	    $search = 1;
	    // Create a link
	    if ( 'start' === $wizardstatus ) {
		    $url = $url_base;
	    } else {
		    $url = $url_base . '&wizard-status=' . $wizardstatus . '#' .sanitize_title( $name );
	    }
	    $render .= '<li class="' . $class . '">';
		    if ( false === $next ) {
			    $render .= '<button onclick="window.location=\'' . $url . '\';">' . $name .'</button>';
		    } else {
			    $render .= '<span>' . $name . '</span>';
		    }
	    $render .= '</li>';
    }
    $render .= '</ol>';
    echo $render;
}
