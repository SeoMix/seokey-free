/* Settings forms */
jQuery(document).ready(function ($) {

    // Translation functions
    const { __ } = wp.i18n;

    $('#refreshme').on('click', function (e) {
        e.preventDefault();
        location.reload();
    });


    /* Toggle display of htpass credentials*/
    $('#showhtpass').on('click', function () {
        $( "#seokey-field-tools-htpasslogin-tr .tdspan input" ).toggle();
        $( "#seokey-field-tools-htpasspass-tr" ).toggle();
    });

    /* 401 protected website > Show credentials */
    if ( $('#seokey_notice_401').length > 0 ) {
        $('#showhtpass').click();
    }

    /* Show loader while change site */
    $('#gsc-change_site').on('click', function () {
        $('#seokey-loader-gscchangesite-loader').show();
    });

    /* copy and past code for breacrumbs*/
    $('#copy-breadcrumb-message').hide();
    $('#copy-breadcrumb-button').on('click', function (e) {
        e.preventDefault();
        var str = document.getElementById('copy-breadcrumb');
        window.getSelection().selectAllChildren(str);
        document.execCommand("Copy");
        window.getSelection().empty();
        $('#copy-breadcrumb-message').show();
        setTimeout(function () {
            $('#copy-breadcrumb-message').hide();
        }, 8000);
    });

    /* Min and max range prices */
    $('#min-amount-seokey').on('keyup mouseup', function () {
        var element = document.getElementById('max-amount-seokey');
        $(element).attr( 'min', $(this).val() );
    });

    /* Add class to target fields that user has entered but may be invalid */
    $('#seokey-form-settings input:not([type=\'submit\'])').on('blur', function () {
        $(this).addClass('seokey-input-wasfocused');
    });

    /* Add class to all invalid fields when user is submitting form */
    $('#seokey-form-settings #submit').on('click', function () {
        $('#seokey-form-settings input:not([type=\'submit\'])').each(function() {
            $(this).addClass('seokey-input-wasfocused');
        });
    });

    /* search console */
    if ( $('#seokey-gsc-select-site').length > 0 ) {
        if ( $('#seokey-gsc-select-site').closest('.seokey-fieldset').css('display') === 'none' ){
            var url      = window.location.href;
            var hash = url.substring(url.indexOf("#")+1);
            if ( hash === 'search-console' ) {
                $('.seokey-form-save-section .submit').hide();
            }
        }
    }

    if ( $('#seokey-wizard-top').length > 0 ) {
        $('#js-seokey-gsc-meta-html').on('click', function (e) {
            e.preventDefault();
            // Show loader
            var meta = $('#seokey-field-search-console-searchconsole-google-verification-code').val();
            console.log(meta);
            $('#js-seokey-gsc-meta-html-loader').show();
            // Ajax
            console.log(seokey_gsc.ajaxurl);
            $.ajax({
                url: seokey_gsc.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'seokey_gsc_meta',
                    security: seokey_gsc.security,
                    meta: meta,
                },
                success: function (response) {
                    console.log(response);
                    // TODO Message avec timer
                    $('#js-seokey-gsc-meta-html-loader').hide();
                },
                error: function (response) {
                    console.log("Error");
                    console.log(response);
                    $('#js-seokey-gsc-meta-html-loader').hide();
                }
            });
        });

    }


    // i18n
    // Set the label on checkbox-slide
    if (typeof (seokey_tabs) !== 'undefined') {
        $(".seokey-wrapper .onoffswitch-inner").attr('data-label-on', seokey_tabs.label_on);
        $(".seokey-wrapper .onoffswitch-inner").attr('data-label-off', seokey_tabs.label_off);
    }

    /**
     * Show / Hide items on checkbox value
     */
    $('input:checkbox.seokey-toggle-display-button').on('change', function () {
        seokey_toggle_display_checkbox($(this));
    });
    $('input:checkbox.seokey-toggle-display-button').each(function(){
        seokey_toggle_display_checkbox($(this));
    });
    function seokey_toggle_display_checkbox($checkbox) {
        var target = $checkbox.data('seokey-toggle-display');
        if (target !== undefined) {
            if ($checkbox.is(':checked')) {
                $('[data-field-id="' + target + '"]').show();
            } else {
                $('[data-field-id="' + target + '"]').hide();
            }
        }
    }

    // ************************************************** Dependencies
    // Manage depends on checkboxes and radios
    $('input[type=checkbox],input[type=radio]').on('click', function () {
        seokeyManageDepends(this, false);
    });

    // Double click to prevent checkboxes to be unchecked
    $('input[type=checkbox]:checked,input[type=radio]:checked').click().click();
    // Do the same on select with change
    $('select.depends').on('change', function () {
        var parenttr = $(this).closest('tr');
        seokeyManageDepends($(this).find(':selected'), parenttr);
    });

    // And select correct items on windows load
    $('select.depends').change();

    // jQuery function to hide/show subvalues (options with dependencies)
    function seokeyManageDepends(_this, parenttr) {
        // If there is a dependance
        if ($(_this).data('depends-off')) {
            var dependson = $(_this).data('depends-on').replace('seokey-field-', '');
            // Check all <tr>
            $( "tr" ).each(function() {
                // Hide all <tr> with dependencies
                var attr = $( this ).attr('data-depends-on');
                if ( undefined !== attr ) {
                    $(this).hide().addClass('is-hidden').removeClass('is-visible');
                }
                // reset var
                attr = undefined;
            });
            // Then show all with correct depends ON value
            $("tr[data-depends-on*='" + dependson + "'").show().removeClass('is-hidden').addClass('is-visible');
            // IN some case, we need to show specific subvalues
            if ( false !== parenttr ) {
                $(parenttr).show().removeClass('is-hidden').addClass('is-visible');
                var item = $("tr[data-depends-on*='" + dependson + "'").find('input[type=checkbox]');
                seokey_toggle_display_checkbox(item);
            }
            // Change required attributes
            $("tr.is-hidden").find(':required').removeAttr('required').attr('has-required','true');
            $("tr.is-visible").find('input[has-required="true"]').removeAttr('has-required').attr('required',true);
        }
    }

    // ************************************************** Repeater
    // Add + button on page load
    $('.seokey-repeater-container').each(function () {
        // Add the + button at the end of the main container
        $(this).parent().append('<span class="seokey-repeater-button-add dashicons dashicons-plus-alt"></span> <p>'+__( 'Add More', 'seo-key' )+'</p>');
        // Add [] at the end of the main container
        $(this).find('*[name]').each(function () {
            // Get this element name
            var current_name = $(this).attr('name');
            // Get current key
            current_name = current_name.substring(current_name.indexOf("[") );
            var key = current_name. slice(1, 2);
            if( isNaN( key ) ){
                $(this).attr('name', $(this).attr('name') + '[0]');
            } else {
            }
        });
    });

    // Handle click on + button
    $('.seokey-repeater-button-add').on('click', function () {
        var next_id = $('div[data-uniqid="' + $(this).prev().data('uniqid') + '"]').length;
        // get previous element
        var prev = $(this).prev('.seokey-repeater-container');
        if ( $(prev).length < 1 ) {
            prev = $(this).prev();
        }
        // Get previous element (container), clone it with correct class, then add the clone to its parent
        var clone = $(prev)
            .clone()
            .addClass('seokey-repeater-container-cloned')
            // .attr('name', $(this).attr('name').replace('[0]', '[' + next_id + ']'))
            .insertBefore($(this).before());
        // Now, let's correctly define all input names
        clone.find('*[name]').each(function () {
            // Get this element name
            var current_name = $(this).attr('name');
            // Get current key
            current_name = current_name.substring(current_name.indexOf("[") );
            var key = current_name. slice(1, 2);
            // Change name key
            $(this).attr('name', $(this).attr('name').replace('[' + key + ']', '[' + next_id + ']'));
        });
        // Now, within this clone, find any element with a NAME (input, select, textarea etc), uncheck all and empty the value if not a radio or checkbox
        clone.find('*[name]')
            .prop('checked', false)
            .filter(':not([type=checkbox],[type=radio])')
            .val('')
            .removeClass('seokey-input-wasfocused')
            .on('blur', function () {
                $(this).addClass('seokey-input-wasfocused');
            });
        $(this).prev().find('input[type=text],input[type=url]').last().after('<span class="seokey-repeater-button-del dashicons dashicons-dismiss"></span>');
        seokey_setting_load_delete_actions();
    });

    // reattach reloaddelete function to newly created delete buttons
    function seokey_setting_load_delete_actions() {
        $(document).one("click", '.seokey-repeater-button-del', function () {
            $(this).parent().remove();
            seokey_setting_load_delete_actions();
        });
    }
    // Do it also on page load
    seokey_setting_load_delete_actions();
});