<?php
/**
 * Admin Redirection module : redirection form
 *
 * @Loaded  on 'init' + role editor
 *
 * @see     admin/modules/redirections/view.php
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
?>
<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"
      method="post" id="seokey-redirections-form">
	<?php
    // Check if we have values to use in this form
	$redirections = Seokey_Redirections_Form::get_instance();
	$redirections->init();
    $form_fields_values = $redirections->form_fields_values;
    if ( !empty ( $form_fields_values ) ) {
	    $form_values['source'] = ( ! empty( $form_fields_values->source ) ) ? $form_fields_values->source : '';
	    $form_values['target'] = ( ! empty( $form_fields_values->target ) ) ? $form_fields_values->target : '';
	    $form_values['id']     = ( ! empty( $form_fields_values->id ) ) ? $form_fields_values->id : '';
    }
    // Security nonce
	wp_nonce_field( 'seokey-redirections-form-nojs', 'seokey-redirections-form-nojs-name');
    ?>

    <input type="hidden" name="action" value="Seokey_Redirections_Form_Submit">
	<table class="form-table">
		<tbody>
		<tr>
			<td scope="row" class="tdaligncenter">
				<label class="seokey-arrowbelow" for="source"><?php _e( '<b>Redirect this URL</b> (source)', 'seo-key' ); ?></label>
			</td>
			<td class="seokey-source-row">
				<input autocomplete="off" placeholder="<?php esc_attr_e('Ex. /slug or https://mywebsite.com/slug', 'seo-key'); ?>" name="source" type="text" id="source" class="regular-text" required="required"  value="<?php echo ( ! empty( $form_values['source'] ) ) ? esc_url( $form_values['source'] ) : ''; ?>">
            </td>
		</tr>
		<tr>
			<td scope="row" class="tdaligncenter">
				<label for="target"><?php _e( '<b>To</b> (target)', 'seo-key' ); ?></label>
			</td>
			<td>
                <div class="seokey-submit-div">
                    <input placeholder="<?php esc_attr_e('Ex. https://mywebsite.com', 'seo-key'); ?>" name="target" type="text" id="target" class="regular-text" required="required"  value="<?php echo ( ! empty( $form_values['target'] ) ) ? esc_url( $form_values['target'] ) : ''; ?>">
                    <?php
                    // default label for submit button
                    $label = esc_html__( 'Add this redirection', 'seo-key' );
                    // change it if user is trying to edit a specific redirection
                    if ( ! empty( $form_fields_values->id ) && $form_fields_values->id !== null ) {
                        $label = esc_html__( "Update this redirection", "seo-key" );
                    }

                    if ( !empty( $form_values['id'] ) ) {
                        echo '<input name="id" type="hidden" id="id" value="'. (int) $form_values['id'] .'">';
                    }
                    ?>
                    <button type="submit" class="button button-primary seokey-submitredirect"><?php echo $label; ?></button>

                    <?php
                    // Cancel button if user wants to edit a redirection
                    if ( ! empty( $form_fields_values->id ) && $form_fields_values->id !== null ) {
                        $admin_url = esc_url( seokey_helper_admin_get_link( $redirections::ADMIN_MENU_SLUG ) );
                        $link = '<a href="' . $admin_url . '" class="button">' . esc_html__( 'Cancel edit', 'seo-key' ) . '</a>';
                        echo $link;
                    } ?>
                </div>
            </td>
		</tr>
		</tbody>
	</table>
</form>