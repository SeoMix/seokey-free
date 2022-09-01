<?php
/**
 * Add more data to user profils
 *
 * @Loaded on plugins_loaded + is_admin() + capability author
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
 * User profils new fields configuration
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_users_profile_form_fields(){
	$fields = [
		'birthdate' => [
			'label'         => __( 'Birthdate', 'seo-key'),
			'description'   => __( 'When were you born?', 'seo-key'),
			'field_type'    => 'input',
			'type'          => 'date',
		],
	    'company' => [
            'label'         => __( 'Company', 'seo-key'),
            'description'   => __( 'Who do you work for?', 'seo-key'),
            'field_type'    => 'input',
            'type'          => 'test',
        ],
	    'jobTitle' => [
		    'label'         => __( 'Job', 'seo-key'),
		    'description'   => __( 'What do you do for a living?', 'seo-key'),
		    'field_type'    => 'input',
		    'type'          => 'test',
	    ],
    ];
    return apply_filters( 'seokey_filter_users_profile_form_fields', $fields );
}

/**
 * Show custom user profile fields
 *
 * @see seokey_meta_boxes_profile_callback()
 * @param  object $user A WP_User object
 * @return void
 */
function seokey_users_profile_form( $user ) {
    // Values
    $metas      = get_the_author_meta( 'seokey_usermetas', $user );
    $fields     = seokey_users_profile_form_fields();
	?>
    <hr>
    <h2 class="has-explanation">
        <?php echo esc_html_x( 'Give Google more information about you ', 'H2 for additional field on user edit profil', 'seo-key'); ?>
        <?php echo seokey_helper_help_messages( 'seokey_users_profile_form' ); ?>
    </h2>
    <table class="form-table seokey_user_form">
        <?php
        foreach ( $fields as $key => $value ) {
            // Get correct ID for this meta
            $key        = sanitize_html_class( $key );
            $uservalue  = ( !empty( $metas[$key] ) ) ? $metas[$key] : '';
            $metaid     = 'seokey_user_' . $key;
            // Rendering
            echo '<tr><th>';
                echo '<label for="' . $metaid . '">' . esc_html( $value['label'] ) . '</label>';
            echo '</th><td>';
            switch ( $value['field_type'] ) {
	            case 'input':
		            echo '<input type="' . $value['type'] .'" name="' . $metaid . '" id="' . $metaid . '"
                       value="' . esc_attr( $uservalue ) . '" class="regular-text" />';
		            echo '<br><span class="description">' .esc_html( $value['description'] ) . '</span>';
		            break;
	            }
	            // TODO Later add new fields types
            echo '</td></tr>';
        }
        ?>
    </table>
	<?php
}

add_action( 'personal_options_update',  'seokey_users_profile_form_save' );
add_action( 'edit_user_profile_update', 'seokey_users_profile_form_save' );
/**
 * Save custom user profile fields
 *
 * @param  int $user_id User ID
 * @return void
 */
function seokey_users_profile_form_save( $user_id ) {
    // Security
	if ( empty( $_POST['_wpnonce'] )
         || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id )  ) {
		return;
	}
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	// Prepare data
	$user_metas = [];
	$fields     = seokey_users_profile_form_fields();
	foreach ( $fields as $key => $value ) {
		// Get correct ID for this meta
		$key        = sanitize_html_class( $key );
		$metaid     = 'seokey_user_' . sanitize_html_class( $key );
		if ( !empty( $_POST[$metaid] ) ){
			$user_metas[$key] = esc_html ( $_POST[$metaid] );
		}
	}
	if ( !empty ( $user_metas ) ) {
	    update_user_meta( $user_id, 'seokey_usermetas', $user_metas );
	}
}


add_action( 'current_screen', 'seokey_admin_users_profile_add_metabox', SEOKEY_PHP_INT_MAX );
/**
 * Tell people we will need a metabox
 *
 * @since   0.0.1
 * @author  Daniel Roch
 */
function seokey_admin_users_profile_add_metabox() {
    // When viewing term list
    global $current_screen;
    if ( $current_screen->base === 'user-edit' || $current_screen->base === 'profile' ) {
	    seokey_helper_cache_data( 'SEOKEY_METABOX', true );
    }
}
