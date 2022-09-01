/* Settings TABS */
jQuery(document).ready(function ($) {
    // Handle tab navigation
    $(".seokey-wrapper .nav-tab").on('click', function (e) {
        e.preventDefault();
        // Let's deploy select with dependencies
        var currenttab = '#tab-'+$(this).attr('id');
        $( currenttab+' select.depends').change();
        // Hide the fieldsets
        $(".seokey-form fieldset").hide();
        // Show the correct one
        $("fieldset#tab-" + $(this).attr("id")).show();
        const { __ } = wp.i18n;
        if ( $('#automatic_optimizations_list').closest('.seokey-fieldset').css('display') !== 'none' ) {
            $('#skip').hide();
        }
        if ( $('#seokey-gsc-connect-gsc').closest('.seokey-fieldset').css('display') !== 'none' ) {
            $('#skip').hide();
            // Hide submit
            let wizardparams = new URLSearchParams(window.location.search);
            let stepparam = wizardparams.get('wizard-status');
            if ( stepparam == '1_0_4' ) {
                setTimeout(function(){
                    $('.seokey-form-save-section .submit #submit').hide();
                }, 1);
            }
            if ( $('#seokey-wizard-form').length > 0 && $('#seokey-gsc-connected').length < 1 ) {
                var url      = window.location.href;
                var hash = url.substring(url.indexOf("#")+1);
                // We have not completed the Search Console form
                if ( $('#seokey-gsc-code-auth').length > 0 || hash === 'search-console' || $('#seokey-gsc-select-site').length > 0 ) {
                    // we still are viewing the Search Console form
                    if ( $('.step-search-console.step-current').length > 0 ) {
                        $('#submit').val(__('Ignore and continue', 'seo-key'));
                    }
                }
            }
        }
        $(".seokey-form-save-section").css({"display": "block"});
        $(".seokey-loader").hide();
        if ( $(this).attr("id") === 'automatic-optimizations' ) {
            var seokeyversion = $('#automatic_optimizations_list_items').attr( "data-settings" );
            if ( ' checked' != seokeyversion ) {
                $("#submit").hide();
            }
        } else {
            $("#submit").show();
            $(".submit").show();
        }
        // Remove other active tab class
        $(".nav-tab-wrapper .nav-tab-active").removeClass("nav-tab-active");
        // Add active tab class to current clicked tab
        $(this).addClass("nav-tab-active");
        var url = new URL(window.location.href);
        var page = url.searchParams.get("page");
        var tab = $(this).attr("id");
        // Change windows history in settings page
        if( $('#seokey-wizard-form').length < 1 ) {
            var target = $(this).attr("href");
            var end = url.href.split('?')[0];
            window.history.pushState("object or string", "SEOKEY", end + target);
        }
        // Save user last visited tab
        if( $('#seokey-wizard-top').length != 1 ) {
            $.post(ajaxurl, {
                'action': 'seokey_settings_tab',
                '_ajax_nonce': $("#seokey-form-settings").data("ajax-nonce"),
                'tab': tab,
                'page': page
            });
        }
        return true;
    });

    // Only one tab? Hide all
    if ( $(".seokey-wrapper .nav-tab").length === 1 ) {
        $(".seokey-wrapper .nav-tab").hide();
    }
    // Several tabs, select the correct one
    else {
        // Use hash to select correct tab
        if ( window.location.hash ) {
            // Wizard : use a timeout function
            if ( $("#seokey-wizard-form").length === 1 ) {
                setTimeout(function(){
                    $('.seokey-wrapper .nav-tab' + window.location.hash).click();
                }, 750);
            } else {
                $('.seokey-wrapper .nav-tab' + window.location.hash).click();
            }
        }
        // No hash ? Select first one
        else {
            $('.seokey-wrapper .nav-tab-active:first').click();
        }
    }
});