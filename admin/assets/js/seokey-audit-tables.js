/**
 * Handle Redirection AJAX calls for Issues or URL tables
 *
 */
jQuery(document).ready(function($) {
    $('.task header').on('click', function (e) {
        e.preventDefault();
        const { __, _x, _n, _nx } = wp.i18n;
        var seeall = __('See all', 'seo-key');
        var seeless = __('See less', 'seo-key');
        $(this).parent().toggleClass('is-closed');
        if ( $(this).find('.audit-show-table').text() === seeall ) {
            $(this).find('.audit-show-table').text( seeless );
        } else {
            $(this).find('.audit-show-table').text( seeall );
        }
    });


    $('.task header').one('click', function (e) {
        var loader = '.task-' + $(this).find('.audit-show-table').attr('data') + ' .seokey-loader';
        console.log(loader);
        console.log( 'loader '  + loader );
        $(loader).show();
        var task = $(this).find('.audit-show-table').attr('data');
        var tab = $(this).find('.audit-show-table').attr('tab');
        var item_type_global = $(this).attr('data-table');
        var myaction = adminAjax.display_action;
        var tabtask = '#' + task + '-' + tab;
        var per_page = $('#global_per_page' ).val() || '20';
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            data: {
                'security': adminAjax.security, // We can access it this way
                'action': myaction,
                'per_page': per_page,
                'task': task,
                'tab': tab,
                'item_type_global': item_type_global,
            },
            success: function (response) {
                console.log('succes');
                $(tabtask).html(response.display);
                // Lets get ajax calls available
                list.init(tabtask, tab, task, myaction);
                $(loader).hide();
            },
            error: function (data) {
                console.log('error display audit list');
            }
        });

    });

    list = {
        /**********************************************************************
         * Init  WP_LIST_TABLE actions
         *********************************************************************/
        init: function ( tabtask, tab, task, myaction ) {
            var item_type_global = $(this).attr('data-table');
            /***********************************
             * Default WP LIST TABLE actions
             **********************************/
            // Search Form
            $( tabtask + ' #search-submit').on('click', function (e) {
                e.preventDefault();
                var data = {
                    per_page: $('#global_per_page' ).val() || '20',
                    s: $( tabtask + ' #search_id-search-input' ).val(),
                    task: task,
                    tab: tab,
                    tabtask:tabtask,
                    item_type_global: item_type_global,
                };
                list.update(data, myaction);
            });
            // Sortable columns
            $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                e.preventDefault();
                var query = this.search.substring(1);
                var data = {
                    paged: list.__query(query, 'paged') || '1',
                    order: list.__query(query, 'order') || 'ASC',
                    orderby: list.__query(query, 'orderby') || 'priority',
                    per_page: $('#global_per_page' ).val() || '20',
                    s: $( tabtask + ' #search_id-search-input' ).val(),
                    task: task,
                    tab: tab,
                    tabtask:tabtask,
                    item_type_global: item_type_global,
                };
                list.update(data, myaction);
            });
            // Pagination input (WordPress will handle it)
            $('.current-page').on('keyup', function (e) {
                e.preventDefault();
            });
        },

        /**********************************************************************
         * Update WP_LIST_TABLE
         *********************************************************************/
        update: function (data, myaction) {
            console.log("update");
            console.log(data);
            var pagination = $('input[name=paged]').first().val() || '1';
            // Check if user has sent a numeric value
            if ( $.isNumeric( pagination ) ) {} else {
                pagination = '1';
            }
            console.log(pagination);
            console.log(data);
            console.log(myaction);
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: $.extend({
                        'security': adminAjax.security, // nonce
                        'action': myaction,
                        paged: pagination,
                    },
                    data
                ),
                success: function (response) {
                    console.log('success update list');
                    if ( response.success === false) {
                        alert (response.data);
                    } else {
                        $( data.tabtask).empty();
                        $( data.tabtask).html(response.display);
                        // Lets get ajax calls available
                        list.init( data.tabtask, data.tab, data.task, myaction );
                    }
                },
                error: function (data) {
                    console.log('error update list');
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
});