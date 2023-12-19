(function ($) {
    const { __ } = wp.i18n;
    
    var seokeyFaqImport = {
        /**
         * Initialisation
         */
        init: function () {
            // Detect editor type
            var gut = document.body.classList.contains('block-editor-page');
            // If we are one Gutemberg, we are good to go
            if ( gut === true ) {
                this.getContent()
            } 
        },
        /**
         * Get content form current editor
         */
        getContent: function () {
            // Create a button for all Yoast FAQ blocks that have the problem when Yoast is deactivated
            $('.wp-block-yoast-faq-block').each(function () {
                const button = '<span class="block-editor-warning__action"><button type="button" class="components-button is-primary seokey-button-block-replacement">' +  __( 'Replace this block with SEOKEY block', 'seo-key' ) + '</button></span>'
                $(this).parent().siblings('div').find('.block-editor-warning__actions').prepend(button);
            })
            // On the click of one of the buttons created above
            $('button.seokey-button-block-replacement').click(function() {
                // Get the block client id thanks of the id of the element
                var blockClientId = $(this).parents('div[data-type="core/missing"]')[0].id;
                // Get rid of the "block-"
                blockClientId = blockClientId.replace("block-", "");
                // Get the attributes of this block
                var blockAttributes = wp.data.select('core/block-editor').getBlockAttributes(blockClientId);
                // Check for which extension block we are dealing with 
                switch ( blockAttributes.originalName ) {
                    case "yoast/faq-block":
                        // Parse the HTML of this block, easier this way
                        var jqueryObject = $( $.parseHTML( blockAttributes.originalUndelimitedContent ) );
                        var questions = []; // Prepare our array
                        // Select all the FAQ sections (question AND response)
                        var faqSections = jqueryObject.find( ".schema-faq-section" );
                        // Go through all the sections
                        faqSections.each(function() {
                            // Push the data in our array
                            questions.push( { question : $( this ).find( ".schema-faq-question" ).text(), response : $( this ).find( ".schema-faq-answer" ).html() } )
                        });
                        // Replace the block with the SeoKey block and the datas !
                        wp.data.dispatch( 'core/block-editor' ).replaceBlock( blockClientId, wp.blocks.createBlock( 'seokey/faq-block', { faq: questions } ) )
                        break;
                    default:
                        alert( __( 'This block is not regonised!', 'seo-key' ) )
                        break;
                }
            })
        },
    }
    /**
     * Wait page loaded status
     */
    $(window).on('load', function () {
        setTimeout(function(){
            seokeyFaqImport.init();
        }, 3000);
    });
})(jQuery);