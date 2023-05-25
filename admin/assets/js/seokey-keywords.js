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
                console.log('search');
                var data = {
                    per_page: $('#global_per_page' ).val() || '20',
                    s: $( '#search_id-search-input' ).val(),
                };
                list.update(data);
            });
            // Sortable columns
            $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                e.preventDefault();
                console.log('sortable');
                var query = this.search.substring(1);
                var data = {
                    paged: list.__query(query, 'paged') || '1',
                    order: list.__query(query, 'order') || 'ASC',
                    orderby: list.__query(query, 'orderby') || 'priority',
                    s: $( '#search_id-search-input' ).val(),
                };
                console.log('before update');
                list.update(data);
            });
            // Pagination input (WordPress will handle it)
            $('.current-page').on('keyup', function (e) {
                e.preventDefault();
            });
            $('body').reloadTooltip();
        },

        /**********************************************************************
         * Update WP_LIST_TABLE
         *********************************************************************/
        update: function (data) {
            console.log("update");
            var pagination = $('input[name=paged]').first().val() || '1';
            // Check if user has sent a numeric value
            if ( $.isNumeric( pagination ) ) {} else {
                pagination = '1';
            }
            console.log(pagination);
            console.log(ajaxurl);
            console.log(adminAjax.security);
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
                },
                error: function (response) {
                    console.log('error update list');
                    console.log(response);
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
         * @param    string    query The URL query part containing the variables
         * @param    string    variable Name of the variable we want to get
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