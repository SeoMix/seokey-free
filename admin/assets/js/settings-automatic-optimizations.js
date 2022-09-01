jQuery(document).ready(function ($) {

    $('#automatic_optimizations_list_button_choices').on('click', function (e) {
        // No real clic
        e.preventDefault();
        $('.seokey-fieldset .form-table .seo-key').toggleClass( 'showoptimisations' );
        $('#automatic_optimizations_list_button').toggle();
        $('#automatic_optimizations_list_button2').toggle();
        $('#automatic_optimizations_list_button_choices').toggleClass('remove');
        $('#automatic_optimizations_list_text').toggle();
        $('#automatic_optimizations_list_items').toggleClass( 'hidemanual' );
        $('#automatic_optimizations_list_items').toggleClass( 'nohidemanual' );
    });

    $(['#automatic_optimizations_list_button','#automatic_optimizations_list_button2'].join(',')).one('click', function (e) {
        // No real clic
        e.preventDefault();
        // Show loader and disable buttons
        $('#automatic-optimizations-loader').show();
        // $('#automatic_optimizations_list_button').removeClass('button-primary').attr("disabled", true);
        $('#automatic_optimizations_list_button').hide();
        $('#automatic_optimizations_list_button2').hide();
        $('#tab-seo-optimizations #automatic_optimizations_list_text').hide();
        $('#tab-seo-optimizations .form-table .seo-key').removeClass( 'showoptimisations' );
        $('#automatic_optimizations_list_button_choices').hide();
        // select first item
        var element = $('#automatic_optimizations_list .automatic_optimizations_item').first();
        // Let' start
        setTimeout(function(){
            seokey_auto_item_nextone(element);
        }, 700);
    });

    function seokey_auto_item_nextone( element ){
        setTimeout(function(){
            if ( element.length > 0 ) {
                $(element).addClass('automatic_optimizations_done');
                $(element)[ 0 ].scrollIntoView({ block: 'center',  behavior: 'smooth' });
                seokey_auto_item_nextone_checked(element);
                element = $(element).next('.automatic_optimizations_item');
                seokey_auto_item_nextone(element);
            } else {
                $('.seokey_page_seo-key-wizard #submit').show();
                $('.seokey_page_seo-key-wizard .submit').show();
                $('#automatic-optimizations-loader').hide();
                var seokeyversion = $('#automatic_optimizations_list').attr( "data-version" )
                $('#seokey-field-seooptimizations-wizardstatus').val( seokeyversion );
                $('.seokey_page_seo-key-wizard #submit')[0].scrollIntoView({ block: 'start',  behavior: 'smooth' });
            }
        }, 1500);
    }

    function seokey_auto_item_nextone_checked( element ){
        setTimeout(function(){
            if ( element.length > 0 ) {
                let input = $(element).find('.automatic_optimization_input');
                $(input).prop("checked", true)
                $(element).find('.automatic_optimization_span').addClass('automatic_optimizations_display_span');;
            }
        }, 800);
    }
});