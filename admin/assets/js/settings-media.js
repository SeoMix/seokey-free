/* Settings MEDIAS */
jQuery(document).ready(function ($) {

    const { __, _x, _n, _nx } = wp.i18n;

    // Handle upload button
    $('.seokey-settings-upload-button').click(function (e) {
        // Define uploader var
        var custom_uploader;
        // Get object ID
        var id = '#' + $(this).data('optionname');
        console.log(id);
        // prevent page reload
        e.preventDefault();
        // Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            multiple: false
        });
        // When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            // Get Image data
            attachment = custom_uploader.state().get('selection').first().toJSON();
            // Show Image Preview and change input value
            seokeyRunImage( attachment.url, id + '-preview' );
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

    // Handle remove button
    $('.seokey-settings-upload-remove-button').on('click', function () {
        const { __, _x, _n, _nx } = wp.i18n;
        $(this).parent().find('.seokey-settings-upload-data').val('');
        $(this).parent().find('.seokey-settings-upload-data').attr( 'value', '' ); // .val('') is not enough
        $(this).parent().find('.seokey-settings-upload-button').val( __('Upload', 'seo--key' ) );
        $(this).parent().find('.seokey-settings-upload-data').attr( 'value', __('Upload', 'seo--key') ); // .val('') is not enough
        $(this).next().find('img').attr('src', '').parent().hide();
        $(this).hide();
    });

    // Show image preview on init (if an image is already saved)
    $('.seokey-img-preview').each(function () {
        if ($(this).attr('src') !== '') {
            $(this).parent().show();
        }
    });

    // Check image validity and return data
    function seokeyTestImage(url) {
        return new Promise(function (resolve, reject) {
            var timeout = 2000;
            var timer, img = new Image();
            img.onerror = img.onabort = function () {
                clearTimeout(timer);
                reject("error");
            };
            img.onload = function () {
                clearTimeout(timer);
                resolve("success");
            };
            timer = setTimeout(function () {
                // reset .src to invalid URL so it stops previous
                // loading, but doesn't trigger new load
                img.src = "//!!!!/dummy.jpg";
                reject("timeout");
            }, timeout);
            img.src = url;
        });
    }

    // Show image function (or hide it on failure)
    function seokeyRecord(url, id, result) {
        // Find our image preview ID and show image
        if ('success' === result) {
            $(id).show().attr("src", url).parent().show();
        } else {
            $(id).hide();
        }
    }

    // Check if image is valid, then launch update input and preview
    function seokeyRunImage(url, id) {
        // alert (id);
        seokeyTestImage(url).then(seokeyRecord.bind(null, url, id), seokeyRecord.bind(null, url, id));
        seokey_image_update(id);
        var inputid = id.replace( '-preview','' );
        $(id).parent().parent().find(inputid).attr( 'value', url );
    }

    // If image is correct, display it and change input value
    function seokey_image_update(id) {
        if ( id.endsWith("-preview") ) {
            id = id.replace( '-preview','' );
        }
        $(id).parent().find('.seokey-settings-upload-button').val(__('Change Image','seo-key'));
        $(id).parent().find('.seokey-settings-upload-remove-button').show();
    }
});