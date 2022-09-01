jQuery(document).ready(function ($) {
    // Translation functions
    const { __, sprintf } = wp.i18n;

    $('#seokey-import-loader').hide();

    $('#seokey-launch-import-abort').on('click', function (e) {
        $('#seokey-launch-import').hide();
        $('#import-other-seo-plugin-explanation').hide();
        $('#seokey-launch-import-abort').hide();
        $('#start').text( __( "Start Wizard", "seo-key" ) ).css({"display": "block"});
        $('#seokey-import-message').text( __( "Import ignored", "seo-key" ) ).show();
    });

    $('#seokey-launch-import').on('click', function (e) {
        e.preventDefault();

        if ( $('#start').length) {
            $('#seokey-launch-import-abort').hide();
        }
        // Show loader
        $('#seokey-import-loader').show();
        // Get plugin choice
        var plugin = '';
        if ( $('#seokey_import_value').length) {
            plugin = $('#seokey_import_value').attr("data-value");
        } else {
            plugin = $('#seokey_import_values').val();
        }
        // Ajax call
        $.ajax({
            url: seokey_data_import.ajaxurl,
            dataType: 'json',
            data: {
                action: 'seokey_import',
                security: seokey_data_import.security,
                plugin: plugin,
            },
            success: function (response) {
                console.log(response);
                var text = response.data;
                if ( response.success === true) {
                    // settings page: need a reload to renew all imported options
                    if ( $('.seokey_page_seo-key-settings').length) {
                        console.log('settings');
                        $text =
                        $('#seokey-import-message').html( text + '<strong>' + __( 'Please wait, we will reload this page in 8 seconds', 'seo-key' ) + '</strong>' ).show();
                        var timer = 7;
                        var y = setInterval(function() {
                            // No negative text
                            if ( timer <= 0 ) {
                                // Reload
                                document.location.reload(true);
                                // Clear data while reload has not finished yet
                                clearInterval(y);
                            }
                            console.log(timer);
                            $('#seokey-import-message').html( text + '<strong>' + sprintf( __( 'Please wait, we will reload this page in %s seconds', 'seo-key' ), timer ) );
                            timer--;
                        }, 1000);
                    }
                    // Wizard, only show value
                    else {
                        $('#seokey-import-message').text(text).show();
                        if ( $('#seokey-launch-import').length) {
                            $('#seokey-launch-import').hide();
                            $('#import-other-seo-plugin-explanation').hide();
                            $('#start').text(__( "Continue", "seo-key" ) ).css({"display": "block"});
                        }
                    }
                }
                // Import failed, show message
                else {
                    $('#seokey-import-message').text(text).show();
                }
                // Hide loader
                $('#seokey-import-loader').hide();
            },
            error: function (response) {
                console.log(response);
                $('#seokey-import-message').text('Error').show();
                $('#seokey-import-loader').hide();
            }
        });
    });
});