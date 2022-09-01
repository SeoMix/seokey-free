/**
 * Handle Redirection AJAX calls for main Table
 */
jQuery(document).ready(function($) {
    list = {
        /**********************************************************************
         * Display data for the first time
         *********************************************************************/
        display: function () {
            var per_page = $('#perpage').val();
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'security': adminAjax.security, // We can access it this way
                    'action': adminAjax.display_action,
                    'per_page': per_page
                },
                success: function (response) {
                    // Show our list
                    $(adminAjax.table_name_div).html(response.display);
                    // We have JS people : remove static list ;)
                    $(adminAjax.table_name_div_nojs).remove();
                    // loader
                    $('.seokey-loader').hide();
                    // Prevent default actions
                    $("tbody").on("click", ".toggle-row", function (e) {
                        e.preventDefault();
                        $(this).closest("tr").toggleClass("is-expanded")
                    });
                    // Lets get ajax calls available
                    list.init();
                },
                error: function (data) {
                    console.log('error display redirection list');
                }
            });
        },

        /**********************************************************************
         * Init future WP_LIST_TABLE actions
         *********************************************************************/
        init: function () {
            var timer;
            var delay = 500;

            /***********************************
             * Default WP LIST TABLE actions
             **********************************/
            // Prevent form default usage
            $(adminAjax.table_name).on('submit', function (e) {
                e.preventDefault();
            });

            // Search Form
            $('#search-submit').on('click', function (e) {
                e.preventDefault();
                var search = $('#search_id-search-input').val();
                var data = {
                    per_page: $('input[name=per_page]').val() || '20',
                    s: $('#search_id-search-input').val(),
                };
                list.update(data);
            });

            // Sortable columns
            $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                e.preventDefault();
                var query = this.search.substring(1);
                var data = {
                    paged: list.__query(query, 'paged') || '1',
                    order: list.__query(query, 'order') || 'DESC',
                    orderby: list.__query(query, 'orderby') || 'hits_last_at',
                    per_page: $('input[name=per_page]').val() || '20',
                    s: $('#search_id-search-input').val(),
                };
                $('input[name=order]').val(data.order);
                $('input[name=orderby]').val(data.orderby);
                list.update(data);
            });

            // Pagination input (WordPress will handle it)
            $('#current-page-selector').on('keyup', function (e) {
                e.preventDefault();
            });

            /**********************************************************************
             * Specific redirections actions
             *********************************************************************/
            // Delete
            $('.delete-redirection').on('click', function (e) {
                e.preventDefault();
                // Get url and type parameter
                var href = $(this).attr('href');
                var type = getURLParameter(href, 'type');
                // We want to delete a redirection
                if (type === 'delete-redirection') {
                    var id = getURLParameter(href, 'id');
                    // We have an ID, proceed
                    if (id) {
                        $.ajax({
                            url: adminAjax.ajax_url,
                            method: 'POST',
                            data: {
                                'action': '_seokey_redirections_delete', // Action
                                'security': adminAjax.ajax_nonce, // nonce
                                'id': id,
                            },
                            success: function (data) {
                                if (data.success) {
                                    var rowCount = $('#the-list tr').length;
                                    if ( rowCount === 1 ) {
                                        var paged = parseInt($('input[name=paged]').val() -1 ) || '1';
                                    } else {
                                        var paged = parseInt($('input[name=paged]').val() ) || '1';
                                    }
                                    var mydata = {
                                        paged: paged,
                                        order: $('input[name=order]').val() || 'DESC',
                                        orderby: $('input[name=orderby]').val() || 'hits_last_at',
                                        per_page: $('input[name=per_page]').val() || '20',
                                        s: $('#search_id-search-input').val(),
                                    };
                                    // TODO bug delete one item if item is "alone" on its own page
                                    list.update(mydata);
                                } else {
                                    console.log(data.data);
                                    console.log(data);
                                    alert(data.data);
                                }
                            },
                            error: function (data) {
                                console.log('error');
                            }
                        });
                    }
                }
            });

            // Edit redirection
            $('.edit-redirection').on('click', function (e) {
                e.preventDefault();
                $(this).parent().addClass('edit-deployed');
                // Get url and type parameter
                var href = $(this).attr('href');
                var type = getURLParameter(href, 'type');
                // We want to delete a redirection
                if (type === 'edit-redirection') {
                    var where = $(this).parent();
                    var id = getURLParameter(href, 'id');
                    var source = $('#redirectionrow-' + id + ' .sourceurl').attr('href');
                    var target = $('#redirectionrow-' + id + ' .targeturl').attr('href');
                    // Go
                    if (id) {
                        $.ajax({
                            url: adminAjax.ajax_url,
                            method: 'POST',
                            data: {
                                'action': '_seokey_redirections_edit', // Action
                                'security': adminAjax.ajax_nonce, // nonce
                                'id': id,
                                'source': source,
                                'target': target,
                            },
                            success: function (data) {
                                if (data.success) {
                                    $(this).parent().removeClass('edit-deployed');
                                    removeElementsByClass('seokey-redirections-form');
                                    where.append(data.data);
                                    list.reload();
                                } else {
                                    console.log(data.data);
                                    console.log(data);
                                    alert(data.data);
                                }
                            },
                            error: function (data) {
                                console.log('error');
                            }
                        });
                    }
                }

            });





            $('#seokey-redirections-form').one('submit', function (e) {
                e.preventDefault();
                var source = $('#seokey-redirections-form #source').val();
                var target = $('#seokey-redirections-form #target').val();
                seokey_redirections_submit(e, source, target);
                $('#seokey-redirections-form #source').val('');
                $('#seokey-redirections-form #target').val('');
                $('#seokey-redirections-form #id').val('');
            });

        },

        /**********************************************************************
         * Update WP_LIST_TABLE
         *********************************************************************/
        update: function (data) {
            // loader
            $('.seokey-loader').show();
            var pagination = $('input[name=paged]').first().val() || '1';
            // Check if user has sent a numeric value
            if ( $.isNumeric( pagination ) ) {} else {
                pagination = '1';
            }
            $.ajax({
                url: ajaxurl,
                data: $.extend({
                        'security': adminAjax.ajax_nonce, // nonce
                        action: '_seokey_redirections_ajax_fetch_history',
                        paged: pagination,
                    },
                    data
                ),
                success: function (response) {
                    console.log(data);
                    console.log(response);
                    if ( response.success === false) {
                        console.log(response.data);
                        console.log(response);
                        alert (response.data);
                    } else {
                        var response = $.parseJSON(response);
                        if (response.rows.length)
                            $('#the-list').html(response.rows);
                        if (response.column_headers.length)
                            $('thead tr, tfoot tr').html(response.column_headers);
                        if (response.pagination.bottom.length)
                            $('.tablenav.top .tablenav-pages').html($(response.pagination.top).html());
                        if (response.pagination.top.length)
                            $('.tablenav.bottom .tablenav-pages').html($(response.pagination.bottom).html());
                        list.init();
                        // loader
                        $('.seokey-loader').hide();
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

        /**********************************************************************
         * Bind event to ajax loaded content
         *********************************************************************/
        reload: function () {
            $(document).one("click", '.seokey_redirection_edit_button', function (e) {
                e.preventDefault();
                var idsource = '#source-' + $(this).val();
                var idtarget = '#target-' + $(this).val();
                var idid = '#id-' + $(this).val();
                var source = $( idsource ).val();
                var target = $( idtarget ).val();
                var id = $(idid).val();
                seokey_redirections_submit(e, source, target, id);
            });
            $(document).one("click", '.edit-redirection-cancel', function (e) {
                e.preventDefault();
                $(this).closest('.edit-deployed').removeClass('edit-deployed');
                removeElementsByClass('seokey-redirections-form');
            });
        }
    }


    /**********************************************************************
     * Redirection form submit (add or edit)
     *********************************************************************/
    function seokey_redirections_submit(e, source, target, id) {
        e.preventDefault();
        $.ajax({
            url: adminAjax.ajax_url,
            method: 'POST',
            data: {
                'action': '_seokey_redirections_form_submit', // Action
                'security': adminAjax.ajax_nonce, // nonce
                'source': source, // data
                'target': target, // data
                'id': id, // data
            },
            success: function (data) {
                // Update WP_LIST_TABLE below
                if (data.success) {
                    var query = window.location.search.substring(1);
                    var datalist = {
                        paged: list.__query(query, 'paged') || '1',
                        order: 'DESC',
                        orderby: 'hits_last_at',
                        per_page: $('input[name=per_page]').val() || '20',
                        s: $('#search_id-search-input').val(),
                    };
                    list.update(datalist);
                } else {
                    console.log(data.data);
                    console.log(data);
                    alert(data.data);
                    list.reload();
                }
            },
            error: function (data) {
                console.log('error form redirection default');
            }
        });
    }

    /**********************************************************************
     * Launch all wings
     *********************************************************************/
    list.display();
});




/* Get specific URL data */
function getURLParameter(url, name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}

function removeElementsByClass(className){
    var elements = document.getElementsByClassName(className);
    while(elements.length > 0){
        elements[0].parentNode.removeChild(elements[0]);
    }
}