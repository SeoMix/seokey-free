/**
 * Handle Redirection AJAX calls for Keywords table
 *
 */
jQuery(document).ready(function($) {
    list = {
        /**********************************************************************
         * Init  WP_LIST_TABLE actions
         *********************************************************************/
        init: function () {
            /***********************************
             * Default WP LIST TABLE actions
             **********************************/
            // Search Form
            $( '#search-submit').on('click', function (e) {
                e.preventDefault();
                $('.seokey-wrapper-loading').css('display', 'flex');
                var data = {
                    per_page: $('#global_per_page' ).val() || '20',
                    filter2: $('#filter2').prop('checked') ? '1' : '0',
                    filter3: $('#filter3').prop('checked') ? '1' : '0',
                    s: $( '#search_id-search-input' ).val(),
                };
                list.update(data);
            });

            $('.tablenav .seokey-filter').on('click', function (e) {
                $('.seokey-wrapper-loading').css('display', 'flex');
                var filter2 = $('#filter2').prop('checked') ? '1' : '0';
                var filter3 = $('#filter3').prop('checked') ? '1' : '0';
                // Correct filters
                if (this.id === 'filter2') {
                    filter3 = 0;
                }
                else if (this.id === 'filter3') {
                    filter2 = 0;
                }
                var data = {
                    per_page: $('#per_page' ).val() || '20',
                    order: $( '#order' ).val() || 'ASC',
                    orderby: $( '#orderby' ).val() || 'content',
                    filter2: filter2,
                    filter3: filter3,
                    s: $( '#search_id-search-input' ).val() || '',
                };
                console.log(data);
                list.update(data);
            });

            // Sortable columns
            $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                e.preventDefault();
                $('.seokey-wrapper-loading').css('display', 'flex');
                var query = this.search.substring(1);
                var data = {
                    paged: list.__query(query, 'paged') || '1',
                    per_page: $('#per_page' ).val() || '20',
                    order: list.__query(query, 'order') || 'ASC',
                    orderby: list.__query(query, 'orderby') || 'content',
                    filter2: $('#filter2').prop('checked') ? '1' : '0',
                    filter3: $('#filter3').prop('checked') ? '1' : '0',
                    s: $( '#search_id-search-input' ).val(),
                };
                list.update(data);
            });
            $('body').reloadTooltip();
        },

        /**********************************************************************
         * Update WP_LIST_TABLE
         *********************************************************************/
        update: function (data) {
            // pagination
            var pagination = $('input[name=paged]').first().val() || '1';
            // Check if user has sent a numeric value
            if ( $.isNumeric( pagination ) ) {} else {
                pagination = '1';
            }
            // Ajax launch
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: $.extend({
                        'security': adminAjax.security, // nonce
                        'action': adminAjax.display_action_url,
                        paged: pagination,
                    },
                    data
                ),
                success: function (response) {
                    console.log('success update list');
                    if ( response.success === false) {
                        alert (response.data);
                    } else {
                        console.log('success');
                        $( '#seokey-keywords-content' ).empty().html(response.display);
                        // Lets get ajax calls available
                        list.init();
                    }
                    $('.seokey-wrapper-loading').hide();
                },
                error: function (response) {
                    console.log('error update list');
                    console.log(response);
                    $('.seokey-wrapper-loading').hide();
                }
            });
        },

        /**********************************************************************
         * Various Functions
         *********************************************************************/
        /**
         * Filter the URL Query to extract variables
         *
         * @see http://css-tricks.com/snippets/javascript/get-url-variables/
         *
         * @param query    string    query The URL query part containing the variables
         * @param variable   string    variable Name of the variable we want to get
         *
         * @return   string|boolean The variable value if available, false else.
         */
        __query: function (query, variable) {
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable)
                    return pair[1];
            }
            return false;
        },
    }
    list.init();
});