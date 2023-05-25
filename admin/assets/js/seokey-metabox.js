/**
 * Gestion des meta title et description
 * Cette fonction nécessite un wp_cache_get('SEOKEY_METABOX' )
 *  - Met à jour en temps reel le preview du résultat de recherche
 *  - Alerte sur le nombre de caratère à mettre dans les metas
 */
jQuery(document).ready(function ($) {

    // Select correct tab and hide others
    $("#seokey-metabox .seokey-metabox-tab").hide();
    $("#seokey-metabox .seokey-metabox-tab-first").show();
    $("#seokey-metabox .nav-tab").on('click', function (e) {
        e.preventDefault();
        $("#seokey-metabox .nav-tab").removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $("#seokey-metabox .seokey-metabox-tab").hide();
        $("#tab-" + $(this).attr("id")).show();
    });

    // No metabox ? Abort.
    var metabox         = document.getElementById('meta-tags-inputs');
    var metaboxsettings = document.getElementById('seokey-field-title-metadesc_desc');
    var $counterColor = "rgb(60, 67, 74)";
    // TODO PERFORMANCES voir si on peut faire plus performant pour le test
    if ( metabox !== null || metaboxsettings !== null ) {
        /**
         * Google Preview Title
         */
        var $googlePreviewTitle = $('#seokey-googlepreview-title');
        if ($('input[name=seokey-field-title-metatitle]').length) {
            var $metatitleField = $('input[name=seokey-field-title-metatitle]');
        } else {
            var $metatitleField = $('input[name=metatitle]');
        }
        $metatitleField.keyup(function () {
            if ($(this).val() !== '') {
                const div = document.createElement('div');
                div.innerHTML = $(this).val();
                div.querySelectorAll("script, style").forEach(el => el.remove());
                $newtext = div.textContent.replace(/(<([^>]+)>)/ig, "");
                // TODO hardcoded data here !!!
                $newtext = $newtext.replace(/^(.{65}[^\s]*).*/, "$1" + " ...");
                $googlePreviewTitle.text($newtext);
            } else {
                $googlePreviewTitle.text($googlePreviewTitle.data('original'));
                $("#seokey-metatitle-counter").text("").css("color",$counterColor);
            }
        });

        /**
         * Google Preview Meta Desc
         */
        var $googlePreviewDesc = $('#seokey-googlepreview-desc');
        if ($('textarea[name=seokey-field-title-metadesc]').length) {
            var $metaDescField = $('textarea[name=seokey-field-title-metadesc]');
        } else {
            var $metaDescField = $('textarea[name=metadesc]');
        }
        $metaDescField.keyup(function () {
            if ($(this).val() !== '') {
                const div = document.createElement('div');
                div.innerHTML = $(this).val();
                div.querySelectorAll("script, style").forEach(el => el.remove());
                var $newtext = div.textContent.replace(/(<([^>]+)>)/ig, "");
                // TODO hardcoded data here !!!
                $newtext = $newtext.replace(/^(.{156}[^\s]*).*/, "$1" + " ...");
                $googlePreviewDesc.text($newtext);
            } else {
                $googlePreviewDesc.text($googlePreviewDesc.data('original'));
                $("#seokey-metadesc-counter").text("").css("color",$counterColor);
            }
        });

        /**
         * Meta Information alerts
         */
        $metaDescField.keyup(function () {
            var valLength = $(this).val().length;
            var $counter = $('#seokey-metadesc-counter');
            var count = 0;
            if ( valLength < seokey_metas.metadesc_counter_min && valLength > 0 ) {
                count = seokey_metas.metadesc_counter_min - valLength;
                if ( count === 1 ) {
                    $counter.css('color', 'red').text(seokey_metas.meta_counter_min_text_single.replace('%s', count));
                } else {
                    $counter.css('color', 'red').text(seokey_metas.meta_counter_min_text.replace('%s', count));
                }
            } else if (valLength >= seokey_metas.metadesc_counter_min && valLength <= seokey_metas.metadesc_counter_max) {
                $counter.text('');
            } else {
                if ( valLength > 0 ) {
                    count = valLength - seokey_metas.metadesc_counter_max;
                    if ( count === 1 ) {
                        $counter.css('color', 'red').text(seokey_metas.meta_counter_max_text_single.replace('%s', count));
                    } else {
                        $counter.css('color', 'red').text(seokey_metas.meta_counter_max_text.replace('%s', count));
                    }
                }
            }
        }).trigger('keyup');

        $metatitleField.keyup(function () {
            var valLength = $(this).val().length;
            var $counter = $('#seokey-metatitle-counter');
            var count = 0;
            if (valLength < seokey_metas.metatitle_counter_min && valLength > 0) {
                count = seokey_metas.metatitle_counter_min - valLength;
                if ( count === 1 ) {
                    $counter.css('color', 'red').text(seokey_metas.meta_counter_min_text_single.replace('%s', count));
                } else {
                    $counter.css('color', 'red').text(seokey_metas.meta_counter_min_text.replace('%s', count));
                }
            } else if (valLength >= seokey_metas.metatitle_counter_min && valLength <= seokey_metas.metatitle_counter_max) {
                $counter.css('color', 'green').text('');
            } else {
                if (valLength > 0) {
                    count = valLength - seokey_metas.metatitle_counter_max;
                    if ( count === 1 ) {
                        $counter.css('color', 'red').text(seokey_metas.meta_counter_max_text_single.replace('%s', count));
                    } else {
                        $counter.css('color', 'red').text(seokey_metas.meta_counter_max_text.replace('%s', count));
                    }
                }
            }
        }).trigger('keyup');

        // Toogle preview noindex rendering
        $('input[name=content_visibility]').on('change', function (e) {
            $('#seokey-google-preview').toggleClass('seokey-googlepreview-private');
        });
    }
});

jQuery(document).ready(function ($) {
    var oldkeyword = $('input[name=seokey_audit_content_main_keyword]').val();
    $('#content_main_keyword_submit').on('click', function (e) {
        e.preventDefault();
        var button = $('#content_main_keyword_submit');
        var id = '';
        if ( wp !== undefined ) {
            if ( wp.data !== undefined && wp.data.select("core/editor") !== undefined && typeof tinyMCE !== 'object' ) {
                id = wp.data.select("core/editor").getCurrentPostId();
            } else {
                id = $('#post_ID').val();
            }
        } else {
            id = $('#post_ID').val();
        }
        const { __ } = wp.i18n;
        // Translate text
        var good            = __( 'Keyword saved', 'seo-key' );
        var error           = __( 'Error', 'seo-key' );
        var addkeyword      = __( 'Target this keyword or phrase', 'seo-key' );
        var removekeyword   = __( 'Update keyword or phrase', 'seo-key' );
        var keyword         = $('input[name=seokey_audit_content_main_keyword]').val();
        $('#metabox-main-keyword-loader').show();
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            data: {
                'security': seokey_metas.security,
                'action': '_seokey_audit_save_keyword',
                'id': id,
                'seokey_audit_content_main_keyword' : keyword,
            },
            success: function (response) {
                console.log(response);
                if ( response.success == false ) {
                    $('#seokey_audit_content_main_keyword').val(null);
                    button.next().text(error).show();
                    button.text(addkeyword);
                    button.next().addClass('error');
                    setTimeout(function () {
                        button.next().hide();
                        button.next().removeClass('error');
                    }, 5000);
                }
                if ( response.success == true ) {
                    button.next().text(good).show();
                    oldkeyword = keyword;
                    if( !keyword ) {
                        button.text(addkeyword);
                    } else {
                        button.text(removekeyword);
                    }
                    setTimeout(function () {
                        button.next().hide();
                    }, 5000);
                }
                console.log(response.data);
                $('.seokey-whattodo-text').replaceWith(response.data);
                $('#metabox-main-keyword-loader').hide();
                $('body').reloadTooltip();
            },
            error: function (response) {
                console.log('error saving keyword');
            }
        });
    });
});