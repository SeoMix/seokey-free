// cd seo-key puis npm run build
(function ($) {

    var seokeyAuditContent = {
        /**
         * Constants
         */
        // Type of editor //
        editorType: '',
        canRefresh: false,
        Refreshing: false,
        Throttling: false,
        PrivateContent: false,
        // Time before audit launch
        refreshDelay: 500,
        refreshDelayTrottle: 2400,

        /**
         * Initialisation
         */
        init: function () {
            // Init elements
            this.$metabox       = $('#seokey-metabox .inside #tab-seokey-audit');
            if( this.$metabox.length < 1 ) {
                return;
            }
            this.$optimisations = $('#seokey-audit-content-optimisations');
            // Detect editor type
            this.setEditorType();
            // Initialisation of user actions //
            this.initActions();
            // Launch first audit if noindex is not checked
            if ( $('input[name="content_visibility"]').prop( "checked" ) !== true ) {
                this.refresh();
            } else {
                const { __ } = wp.i18n;
                this.$optimisations.html( __( 'This is a private content: we do not audit them.', 'seo-key') );
            }
            // this.registerPlugin();
        },

        /**
         * Detect type of content editor : tinyMCE or Gutenberg
         */
        setEditorType: function () {
            // Gutenberg editor ?
            var gut = document.body.classList.contains('block-editor-page');
            if ( gut === true ) {
                this.editorType = 'gutenberg';
                this.editor = wp.data.select("core/editor");
            } else {
                // TinyMCE editor
                this.editorType = 'mce';
                this.editor = tinyMCE.activeEditor;
            }
        },

        /**
         * Get content form current editor
         */
        getContent: function () {
            this.content = {};
            // main keyword
            var main_keyword = $('input[name="seokey_audit_content_main_keyword"]').val();
            console.log(this.editorType);
            switch (this.editorType) {
                case 'gutenberg':
                    // Title
                    var title = $('input[name="metatitle"]').val();
                    if ( !title) {
                        title = this.editor.getEditedPostAttribute('title');
                    }
                    // Excerpt
                    var excerpt = $('textarea[name="metadesc"]').val();
                    if ( !excerpt) {
                        excerpt = this.editor.getEditedPostAttribute('excerpt');
                    }
                    // data
                    // var date = new Date();
                    // date.setSeconds(0,0);
                    // date = date.toISOString();
                    // date = date.replace(/.000Z/, "");
                    // Clean content
                    var content = this.editor.getEditedPostContent();
                    // create a new div container
                    var div = document.createElement('div');
                    // assing your HTML to div's innerHTML
                    div.innerHTML = content;
                    // get all <a> elements from div
                    var elements = div.getElementsByTagName('iframe');
                    // remove all <a> elements
                    while (elements[0])
                        elements[0].parentNode.removeChild(elements[0])
                    // get div's innerHTML into a new variable
                    content = div.innerHTML;
                    // Metadesc
                    var metadesc = $('#seokey-googlepreview-desc').text()
                    if( metadesc.length < 1 ) {
                        metadesc = '';
                    }
                    // Define data
                    this.content = {
                        'title': title,
                        'excerpt': excerpt,
                        'metadesc': metadesc,
                        'id': wp.data.select("core/editor").getCurrentPostId(),
                        'date': wp.data.select('core/editor').getEditedPostAttribute('date'),
                        'author': wp.data.select('core/editor').getEditedPostAttribute('author'),
                        'content': content,
                        'permalink': this.editor.getPermalink(),
                        'keyword': main_keyword,
                        'editortype': 'gutenberg',
                    };
                    break;
                case 'mce':
                default:
                    // Excerpt
                    var excerpttinymce = $('#excerpt').text()
                    if( excerpttinymce.length < 1 ) {
                        excerpttinymce = '';
                    }
                    // Metadesc
                    var metadesc = $('#seokey-googlepreview-desc').text()
                    if( metadesc.length < 1 ) {
                        metadesc = '';
                    }
                    this.content = {
                        'title': $('#seokey-googlepreview-title').text(),
                        'excerpt': excerpttinymce,
                        'metadesc': metadesc,
                        'id': $('#post_ID').val(),
                        // 'date': mydate,
                        // 'last_date': date,
                        'author': $('#post_author_override').find(":selected").val(),
                        // TODO Later Hook
                        'content': tinymce.get("content").getContent(),
                        // 'content': this.editor.getContent(), // use JSON to avoid bad url forming
                        'permalink': $('#sample-permalink a').text(),
                        'keyword': main_keyword,
                        'editortype': 'classic',
                    };
                    break;
            }
        },




        /**
         * Init user action : trigger audits only when necessary
         */
        initActions: function () {
            var self = this;
            // Define if content is not indexable on page loading
            if ( $('input[name="content_visibility"]').checked === true ) {
                self.PrivateContent = true;
            } else {
                self.PrivateContent = false;
            }

            // Throttle function: Input as function which needs to be throttled and delay is the time interval in milliseconds
            var throttleFunction = function (func, delay) {
                // If setTimeout is already scheduled, no need to do anything
                if (timerId) { return }
                // Already running, do nothin
                if ( self.Throttling === true ) { return }
                // Schedule a setTimeout after delay seconds
                var timerId = setTimeout(function () { func()
                    // Once setTimeout function execution is finished, timerId = undefined so that in <br>
                    // the next scroll event function execution can be scheduled by the setTimeout
                    timerId = undefined; }, delay)
            }
            // After delay, launch audit checks
            function audit_after_delai() {
                // Lets check one last time if user is still typing
                switch (self.editorType) {
                    case 'gutenberg':
                        if ( wp.data.select('core/block-editor').isTyping() !== true ) {
                            self.refresh();
                        }
                        break;
                    case 'mce':
                        self.refresh();
                        break;
                }
                self.Throttling = false;
            }
            // After specific action, check if we need to trigger an audit throttleFunction
            function audit_throttle_launch() {
                // Private content, do not trottle an audit
                if ( $('input[name="content_visibility"]').prop( "checked" ) === true ) {
                    self.PrivateContent = true;
                    self.$optimisations.empty();
                    const { __ } = wp.i18n;
                    self.$optimisations.html( __( 'This is a private content: we do not audit them.', 'seo-key') );
                }
                // Public content, trottle audit if necessary
                else {
                    // Audit already running ?
                    if ( self.Throttling === false && self.Refreshing === false ) {
                        throttleFunction( audit_after_delai, self.refreshDelayTrottle );
                        self.Throttling = true;
                    }
                    self.PrivateContent = false;
                }
            }


            // Check any change on main keyword
            $('#content_main_keyword_submit').on('click', function () {
                audit_throttle_launch();
            });
            // Check any change on the noindex checkbox
            $('input[name="content_visibility"]').on("click", function () {
                audit_throttle_launch();
            });
            
            // Actions within editors (user is saving post, is writing content, etc.)
            switch (this.editorType) {
                // Gutenberg editor
                case 'gutenberg':
                    // Thanks Nicolas Juen (BeAPI) for his help ;)
                    // No state defined at first init
                    // var state_typing = false;

                    // Check what user is doing with gutenberg
                    wp.data.subscribe( function () {
                        // Saving post
                        var isSavingPost = wp.data.select('core/editor').isSavingPost();
                        var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
                        if (isSavingPost && !isAutosavingPost) {
                            self.canRefresh = true;
                            self.refresh();
                        }
                        if ( !isAutosavingPost ) {
                            // Attribute change
                            const currentPost = wp.data.select('core/editor').getCurrentPost();
                            const allPostAttributeKeys = Object.keys(currentPost);
                            if(allPostAttributeKeys){
                                allPostAttributeKeys.forEach( attributeKey => {
                                    const attributeValue = wp.data.select('core/editor').getEditedPostAttribute(attributeKey);
                                    if( attributeValue !== currentPost[attributeKey] && ! self.Throttling ){
                                        if ( attributeKey !== 'guid' ) {
                                            throttleFunction(audit_after_delai, self.refreshDelayTrottle);
                                            self.Throttling = true;
                                        }
                                    }
                                });
                            }
                        }
                    });
                    break;
                // TODO Classic editor
                case 'mce':
                    // Edit post //
                    this.editor.on('keyup', function () {
                        self.refresh();
                    });
                    this.editor.on('Change', function () {
                        self.refresh();
                    });
                    break;
            }

        },


        /**
         * Audit launch checks
         * - Launch timer
         * - Launch request only when time ends
         */
        refresh: function () {
            var self = this;
            if ( self.PrivateContent === false ) {
                self.canRefresh = false;
                // Clear previous timer if it exists
                clearInterval(this.timer);
                // Launch with timer
                this.timer = setTimeout(function () {
                    self.canRefresh = true;
                    self.request();
                }, this.refreshDelay);
            } else {
                self.$optimisations.empty();
                const { __ } = wp.i18n;
                self.$optimisations.html( __( 'This is a private content: we do not audit them.', 'seo-key') );
            }
        },

        /**
         * Ajax request to do all tasks
         */
        request: function () {
            if ( this.canRefresh === true && this.Refreshing === false ) {
                var self = this;
                // stop refresh
                this.canRefresh = false;
                this.Refreshing = true;
                // get content
                this.getContent();
                // launch an audit
                self.$optimisations.empty();
                $('#audit-content-loader').show();
                $.ajax({
                    url: seokey_audit_content.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'seokey_audit_content_check',
                        security: seokey_audit_content.security,
                        datas: self.content,
                    },
                    success: function (response) {
                        // response = JSON.parse(response);
                        if ( response.success === true ) {
                            setTimeout(function () {
                                self.$optimisations.empty();
                                self.$optimisations.html('<ul>' + response.data + '</ul>');
                                self.Refreshing = false;
                                $('#audit-content-loader').hide();
                                // enable issues actions
                                if( typeof seokey_issues_handler === "function") {
                                    seokey_issues_handler();
                                }
                            }, 2000);
                        } else {
                            self.Refreshing = false;
                            $('#audit-content-loader').hide();
                        }
                    },
                    error: function (response) {
                        self.Refreshing = false;
                        $('#audit-content-loader').hide();
                    }
                });
            }
        }
    };


    /**
     * Wait page loaded status
     */
    $(window).on('load', function () {
        setTimeout(function(){
            seokeyAuditContent.init();
        }, 3000);
    });

})(jQuery);