(function ($) {
    // Audit page is ready
    $(window).on('load', function () {
        // define data
        var seokeyAuditView = {
            init: function () {
                // define data
                this.$obj = $('#seokey-audit');
                this.$tabs = $('.nav-tab', this.$obj);
                this.$button = $('#seokey-audit-button', this.$obj);
                this.$contents = $('.tabs-content', this.$obj);
                this.$statusheader = $('.task header', this.$obj);
                this.initActions();
            },
            // Init functions
            initActions: function () {
                var self = this;
                // Select new tab
                this.$tabs.on('click', function () {
                    self.setTabs($(this));
                    history.pushState(null, null, $(this).attr("href"));
                    return false;
                });
            },
            // Switch tabs function
            setTabs: function ($tab) {
                this.$contents.removeClass('is-opened');
                var tabId = $tab.attr('href');
                $($tab.attr('href')).addClass('is-opened');
                this.$tabs.removeClass('nav-tab-active');
                $tab.addClass('nav-tab-active');
                var url = new URL(window.location.href);
                var page = url.searchParams.get("page");
                var currenttab = $tab.attr("id");
                $.post(ajaxurl, {
                    'action': 'seokey_audit_tab',
                    '_ajax_nonce': $("#tabs").data("ajax-nonce"),
                    'tab': currenttab,
                    'page': page
                });
            },
        };
        // Audit view functions
        seokeyAuditView.init();
    });
})(jQuery);












jQuery(document).ready(function ($) {
    const { __, _x, _n, _nx } = wp.i18n;

    // Prevent buttons to do anything
    $(".seokey-audit-button-ajax").on("click", function (e) {
        e.preventDefault();
    });

    // On audit button clic, let's launch a new audit
    function prepare_audit_launch() {
        $(".seokey-audit-button-ajax").one("click", function (e) {
            // Prevent default link behaviour
            e.preventDefault();
            seokey_empty_data();
            var url = ($(this).attr('href'));
            var action = getURLParameter(url, 'type');
            $("#seokey-audit-button-ajax-run").attr("disabled", true);
            $("#audit-loader-main-text-count").text(__( "Please wait", "seo-key" ));
            launch_audit(action);
        });
        // Audit running ?
        if ( "running" === $("#seokey-audit").attr( 'data-state') ) {
            $("#seokey-audit-button-ajax-run").click();
        }
    }

    // Display loader if audit is running while loading the page
    var audit_running = $("#audit-loader-main").data("audit-running");
    if ( audit_running === 1 ) {
        $('#audit-loader-main').show();
        $('#audit-loader-main').css('display', 'inline-block');
        $( "#seokey-audit-button-ajax-run" ).attr( "disabled", true );
        $( "#audit-loader-main-text-count" ).text( "Please wait will we are gathering current audit data" );
        check_current_audit();
    } else {
        // Allow new audits
        prepare_audit_launch();


    }

    // Function used to launch an audit
    function launch_audit(action, url) {
        $.ajax( {
            url: ajaxurl,
            dataType: 'json',
            data: {
                'security': adminAjaxaudit.security,
                'action': adminAjaxaudit.launch_action, // _seokey_audit_ajax_launch => new SeoKey_Audit_Launch();
                'type': action,
            },
            success: function (response) {
                console.log('success ajax audit launch');
                console.log(response);
                check_current_audit();
            },
            error: function (data) {
                console.log('error ajax audit launch');
                console.log(data);
            }
        } );
    }

    // Checking current audit status
    function check_current_audit (){
        var timeout = 3000;
        var retry = 0;
        var lastcount = 0;
        var lasttask = '';
        var i = setInterval(function(){
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    'security': adminAjaxaudit.security,
                    'action': '_seokey_audit_get_status',
                    'type': 'animate',
                },
                success: function (response) {
                    console.log('success check_current_audit');
                    console.log(response);
                    if ( response.success === true ) {
                        // Translation functions
                        const { __, _x, _n, _nx } = wp.i18n;
                        // get data
                        var tasks = response.data;
                        // Password protected website
                        if ( 401 === tasks.status ) {
                            // End script
                            clearInterval(i);
                            setTimeout(() => {
                                // Add text for user
                                var url = tasks.setting_url;
                                var text = __('Error, audit is not working !', 'seo-key');
                                text = text + '<br>' + __('You have a htpasswd protection. Please enter your login and password on the Tools Tabs here:', 'seo-key');
                                text = text + ' <a href="' + url + '">' + __('Settings Page', 'seo-key') + '</a>';
                                $("#audit-loader-main-text-count").html(text);
                                $("#main-audit-loader .seokey-spinner").addClass('stop-animation');
                                $('#audit-last-time').text(' ');
                            }, 1000);

                        } else if ( tasks.tasks_remaining_count > 0 ) {
                            // remaining tasks
                            lasttask = tasks.tasks_done_since_lasttime;
                            if ( typeof lasttask === 'object' ) {
                                lasttask = lasttask[Object.keys(lasttask)[0]];
                            }
                            if ( lasttask === undefined ) {
                                // get value
                                $text_alternate = $("#audit-loader-main-text-details").text();
                            } else {
                                $text_alternate = __( 'Last task done:', 'seo-key' ) + ' ' + lasttask;
                            }
                            $text = tasks.tasks_remaining_count + __(" tasks left<br>", "seo-key" );
                            $("#audit-loader-main-text-details").html( ' ' );
                            if ( false === tasks.tasks_done || jQuery.isEmptyObject(tasks.tasks_done) ) {
                                retry = retry + 1;
                                if ( retry > 6 ) {
                                    // End script
                                    clearInterval(i);
                                    // Reset audit
                                    seokey_kill_audit();
                                    // Add text for user
                                    var url = tasks.setting_url;
                                    var text = __( 'Error, audit is not working !', 'seo-key' );
                                    text = text + ' ' + __( 'Reload this page or wait a few moments to try again.', 'seo-key' );
                                    $("#audit-loader-main-text-count").html( text );
                                    $("#main-audit-loader .seokey-spinner").addClass( 'stop-animation' );
                                    var timer = 10;
                                    var y = setInterval(function() {
                                        // No negative text
                                        if ( timer <= 0 ) {
                                            // Reload
                                            document.location.reload(true);
                                            // Clear data while reload has not finished yet
                                            clearInterval(y);
                                        }
                                        timer--;
                                    }, 1000);
                                }
                            } else {
                                $("#audit-loader-main-text-count").html($text);
                                $("#audit-loader-main-text-details").html($text_alternate);
                                // if remaining count = celui d'avant
                                if ( lastcount === tasks.tasks_remaining_count ) {
                                    retry = retry + 1;
                                    if ( retry > 13 ) {
                                        // End script
                                        clearInterval(i);
                                        var text = __( 'Error, audit seems stuck.', 'seo-key' );
                                        text = text + '<br>' + __( 'Please reload this page or wait a few moments, we will reload it for you.', 'seo-key' );
                                        $("#audit-loader-main-text-count").html( text );
                                        $("#main-audit-loader .seokey-spinner").addClass( 'stop-animation' );
                                        $("#main-audit-loader .seokey-spinner").addClass( 'stop-animation' );
                                        var timer = 10;
                                        var y = setInterval(function() {
                                            // No negative text
                                            if ( timer <= 0 ) {
                                                // Reload
                                                document.location.reload(true);
                                                // Clear data while reload has not finished yet
                                                clearInterval(y);
                                            }
                                            timer--;
                                        }, 1000);
                                    }
                                } else {
                                    retry = 0
                                }
                                lastcount = tasks.tasks_remaining_count;
                            }
                        }
                    } else {
                        // not running or not running anymore
                        clearInterval(i);
                        // // Allow new audits
                        // prepare_audit_launch();
                        $( "#audit-loader-main-text-count" ).text( __( "Audit is finished, we will reload your data in: 4 seconds", "seo-key") );
                        var timer = 3;
                        var y = setInterval(function() {
                            // No negative text
                            if ( timer > 0 ) {
                                // _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n( $people ) );
                                $("#audit-loader-main-text-count").text( __( "Audit is finished, we will reload your data in: ", "seo-key") + timer + __( " seconds", "seo-key") );
                            } else {
                                $("#audit-loader-main-text-count").text( __( "Audit is finished, we will reload now", "seo-key") );
                                // Reload
                                document.location.reload(true);
                                // Clear data while reload has not finished yet
                                clearInterval(y);
                            }
                            timer--;
                        }, 1000);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }, timeout);
    }

    // Clean data when launching an audit
    function seokey_empty_data(){
        const { __, _x, _n, _nx } = wp.i18n;
        $( '#main-audit-loader' ).show();
        $( '#seokey-audit-issues-h2' ).hide();
        $( '#seokey-audit-score-outter-circle' ).removeAttr( 'style' );
        $( '#seokey-audit-score-outter-circle' ).css('background', '#595959');
        $( '#seokey-audit-score-variation' ).removeAttr( "class" );
        $( '#seokey-audit-score-int' ).text('');
        $( '#seokey-audit-score-scale' ).text('');
        $( '#seokey-audit-score-variation' ).text('');
        $( '#seokey-audit-issues' ).text('');
        var name = $( '#audit-welcome-name' ).text();
        $( '#audit-welcome' ).text( __('Please wait ', 'seo-key' ) + name);
        $( '#audit-message' ).text(' ');
        $( '#audit-last-time' ).text(' ');
        $( '#audit-details' ).text('');
        $( '#audit-main' ).html('');
    }

    function seokey_kill_audit(){
        // Clean data
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            data: {
                'security': adminAjaxaudit.security,
                'action': '_seokey_audit_kill_process',
            },
            success: function (response) {
                console.log(response);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

});

// Function to get URL parameter
function getURLParameter(url, name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}