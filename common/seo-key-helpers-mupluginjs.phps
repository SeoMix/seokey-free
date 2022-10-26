// Main links to check
var element = '#delete-seokey, #delete-seo-key';
// have we already open the pointer
var done = 0;
// our uninstall link has changed ?
var changed = 0;
jQuery(element).click(function( e ) {
	// First iteration 0, or user has chosen one of the option 2 (delete all or just delete files)
	if ( done === 0 || done === 2 ) {
		// Prevent default behaviour of the link
		e.preventDefault();
		// Get data before creating pointer
		var url_delete      = jQuery(this).attr('href');
		var url_delete_data = url_delete+'&delete_data=yes';
		// Translation functions
    	const { __, _x, _n, _nx } = wp.i18n;
    	// Define translated text
    	var title 			= __( 'Warning! What do you want to do?', 'seo-key' );
    	var delete_files 	= __( 'Just delete files', 'seo-key' );
    	var delete_all 		= __( 'Delete files and ALL data!', 'seo-key' );
		var text = '<h3>'+title+'</h3><p><a id="delete-seo-key-pro-files" class="primary button" href="'+url_delete+'">'+delete_files+'</a> <a id="delete-seo-key-pro-all" class="button" href="'+url_delete_data+'">'+delete_all+'</a></p>';
		// Create pointer
		jQuery('#' + id).pointer({
			content: text,
			pointerClass: "seokey-wp-pointer",
			  position:{
				edge: 'top',
				align: 'left',
			},
			close: function() {
			}
		}).pointer('open');

		// Allow a new popup to appear if user close the WordPress native JS alert
		if ( done === 2 ) {
			done = 0;
		}

		// User just want to delete files, keep normal behaviour for the link
		jQuery('#delete-seo-key-pro-files').click(function( e ){
			// Prevent default behaviour for this secondary link
			e.preventDefault();
			done = 1;
			// close pointer
			jQuery('#' + id).pointer('close');
			// Remove class to change WordPress popup content: now WordPress won't talk about data
			jQuery('#' + id).closest('tr').removeClass('is-uninstallable');
			jQuery('#' + id).closest('tr').addClass('was-uninstallable');
			// Do we need to change the URL because user messed up with the popup before ?
			if ( changed === 1 ) {
				seokey_delete_option_free( 'files' );
				changed = 0;
			}
			// click on '#delete-seo-key-pro' => delete plugin but KEEP it's data
			jQuery('#' + id).trigger('click');
			// Allow a new popup to appear
			done = 2;
		});

		// User wants to delete ALL (files + data)
		jQuery('#delete-seo-key-pro-all').click(function( e ){
			// Prevent default behaviour for this secondary link
			e.preventDefault();
			done = 1;
			// It may be the second time we have this popup, and we may have already cleaned too much HTML for the native WordPress popup
			if (jQuery('#' + id).closest('tr').hasClass("was-uninstallable")) {
				jQuery('#' + id).closest('tr').addClass('is-uninstallable');
				jQuery('#' + id).closest('tr').removeClass('was-uninstallable');
			}
			// close pointer
			jQuery('#' + id).pointer('close');
			// Do we need to change the URL in order to tell our own function de delete data ?
			if ( changed === 0 ) {
				// var changeurl = jQuery(element).attr('href');
				// changeurl = changeurl+"&delete_data=yes";
				// jQuery(element).attr( 'href', changeurl );
				seokey_delete_option( 'all' );
				changed = 1;
			}
			// click on '#delete-seo-key-pro' => delete plugin and it's data
			jQuery('#' + id).trigger('click');
			// Allow a new popup to appear
			done = 2;
		});

		// The end (keep it to prevent errors)
		return false;
	}
});

// TODO Comment
function seokey_delete_option_free( $type ){
	jQuery.ajax({
        url: seokey_delete_option_free_script.ajaxurl,
        dataType: 'json',
        data: {
            'security': seokey_delete_option_free_script.security,
            'action': 'seokey_delete_option_free',
            'type': $type
        },
        success: function (response) {
            console.log('succes');
            console.log($type);
        },
        error: function (response) {
            console.log('failure');
            console.log($type);
        }
    });
}