// TODO Comments
// See "Visual Term Description Editor" plugin : https://fr.wordpress.org/plugins/visual-term-description-editor/
jQuery(document).ready(
    function($) {
        // Are we editing a term ?
        if( $(".sk-term-description-tinymce").length ) {
            if( $("#edittag").length === 1 ) {
                $('textarea#description').closest('.form-field').remove();
            }
            // Are we adding a term ?
            if( $("#addtag").length === 1 ) {
                $('textarea#tag-description').closest('.form-field').remove();
                $(function () {
                    $('#addtag').on('mousedown', '#submit', function () {
                        tinyMCE.triggerSave();
                        $(document).bind('ajaxSuccess.seokey_add_term', function () {
                            if (tinyMCE.activeEditor) {
                                tinyMCE.activeEditor.setContent('');
                            }
                            $(document).unbind('ajaxSuccess.seokey_add_term', false);
                        });
                    });
                });
            }
        } else {
            $('textarea#description').closest('.form-field').show();
        }
    }
);