jQuery(document).ready(function ($) {
    // Clic on a SEOKEY dismiss notification button
    $('.seokey-notice .notice-dismiss, .seokey-notice .seokey-notice-dismiss-in-content ').on('click', function (e) {
        // Prevent default clic behaviour
        e.preventDefault();
        // Get current notice ID
        var id = $(this).parent().attr("id");
        // ID undefined, we may be using a custom dismiss button within content
        if ( id === undefined ) {
            id = $(this).closest('.seokey-notice');
            var dismissbutton = $(id).children('button.notice-dismiss');
            dismissbutton.click();
        } else {
            // Dismiss notice
            $.ajax({
                url: seokey_notifications.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'seokey_dismiss_notice',
                    security: seokey_notifications.security,
                    id: id,
                },
                success: function (response) {
                    console.log(response);
                    $('#'+id).fadeOut();
                },
                error: function (response) {
                    console.log("Error Dismiss Notice");
                    console.log(response);
                }
            });
        }
    });
});