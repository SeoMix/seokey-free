jQuery(document).ready(function ($) {

    function seokey_js_admin_media_library_update_alt( attachment ){
        var alt_id = $('#seokey_alt_id_' + attachment);
        var alt_text = alt_id.val();
        alt_id.next().next().css("display","inline-block");
        $.ajax({
            url: adminAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'seokey_medias_library_alt_form_update',
                security: adminAjax.ajax_nonce,
                'post_id': attachment,
                'alt_text': alt_text
            },
            success: function (data) {
                if (data.success) {
                    alt_id.next().next().show();
                    setTimeout(function () {
                        alt_id.next().next().hide();
                        alt_id.next().next().next().show();
                        setTimeout(function () {
                            alt_id.next().next().next().hide();
                        }, 5000);
                    }, 2500);
                } else {
                    alt_id.next().next().show();
                    setTimeout(function () {
                        alt_id.next().next().hide();
                        alt_id.next().next().next().show();
                        setTimeout(function () {
                            alt_id.next().next().next().hide();
                        }, 5000);
                    }, 2500);
                    console.log(data.data);
                }
            },
            error: function (data) {
                alt_id.next().val('');
                alt_id.next().next().show();
                setTimeout(function () {
                    alt_id.next().next().hide();
                    alt_id.next().next().next().show();
                    setTimeout(function () {
                        alt_id.next().next().next().hide();
                    }, 5000);
                }, 2500);
                console.log('Error');
            }
        });
    }
    $(this).on('keydown', 'input.seokey_alt_input', function(event){
        if(event.keyCode === 13) { // touche entrée
            $(this).blur();
            return false;
        }
    }).on('blur', 'input.seokey_alt_input', function () {
        var attachmentvalue = $(this).attr("id").replace('seokey_alt_id_', '');
        var alt_id = $('#seokey_alt_id_' + attachmentvalue);
        var old = $(this).attr("data-oldvalue");
        var alt_text = alt_id.val();
        console.log(old);
        // New ALT is not empty => update
        if ( alt_id.val().length !== 0 ) {
            // Only update if value change
            if ( alt_text !== old ) {
                seokey_js_admin_media_library_update_alt(attachmentvalue);
                // Update old value
                $(this).attr("data-oldvalue", alt_text );
            }
        } else {
            // New ALT is empty, check if it was filled before
            if ( old.length !== 0 ) {
                console.log('yes');
                seokey_js_admin_media_library_update_alt(attachmentvalue);
                // Update old value
                $(this).attr("data-oldvalue", alt_text );
            }
        }
        return false;
    });

    $('.seokey-alt-editor-submit').on('click', function (e) {
        e.preventDefault();
        seokey_js_admin_media_library_update_alt( $(this).attr("id").replace('seokey_alt_id_submit_', '') );
    });

});
