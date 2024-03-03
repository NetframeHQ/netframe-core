(function () {
    'use strict';

    function PostingSystem(options) {
        this.$wrapper = options.$wrapper || $('#posting');
        this.$defaultRoute = options.$defaultRoute;
        this.$defaultTemplate = options.$defaultTemplate;
        this.$modal = options.$modal || false;
        this.$initFirstLoad = options.$initFirstLoad;
        this.$firstLoad = true;
        this.$importedUrls = [];
        this.$displayMap = false;
        this.$originalTarget = '';
        this.$profileId = options.$profileId;
        this.$profileType = options.$profileType;
        this.$linkImported = false;
        this.$listenedText = [];
        this.$wrapperTop = '';
        this.$wrapperBottom = '';
    }

    PostingSystem.prototype.reloadTags = function () {
        $('.fn-select2-tag').select2({
            language: localeLang,
            placeHolder: 'tapez ici',
            minimumInputLength: 2,
            multiple: true,
            ajax: { // instead of writing the function to execute the request we
                // use Select2's convenient helper
                url: laroute.route('tags.autocomplete'),
                dataType: 'json',
                contentType: "application/json",
                type: "POST",
                data: function (params) {
                    return JSON.stringify({
                        q: params.term
                    });
                },
                processResults: function (data, page) {
                    return data;
                },
            },
            escapeMarkup: function (markup) { return markup; },
        });
    };

    PostingSystem.prototype.listenClickEvent = function () {
        var that = this;

        that.$wrapper.on('click', '[data-action="tl-post"]', function (e) {
            e.preventDefault();
            var el = $(this);
            var route = el.attr('href');
            var type_post = el.data('type-post');
            var context_post = el.data('context');
            var data = {
                post_type: type_post,
                default_author_id: current_profile_id,
                default_author_type: current_profile_type
            };
            if (context_post == 'modal') {
                data['modal'] = true;
            }

            that.postData(route, data, that);
        });

        that.$wrapper.on('click', '.fn-remove-link', function (e) {
            e.preventDefault();
            var delLink = $(this).closest('div.link-preview');
            var linkId = delLink.data('id');

            var inputVal = that.$wrapper.find("#postImportedLinksId").val();
            // Initialize the selected medias
            if ($.isNumeric(inputVal)) {
                this.importedLinksId = [inputVal];
            } else if (inputVal && inputVal.indexOf(',') > -1) {
                this.importedLinksId = inputVal.split(',');
            } else {
                this.importedLinksId = [];
            }

            var index = this.importedLinksId.indexOf(linkId.toString());
            if (index != -1) {
                this.importedLinksId.splice(index, 1);

                that.$wrapper.find("#postImportedLinksId").val(this.importedLinksId.join(','));

                delLink.remove();
            }
        });
    };

    PostingSystem.prototype.listenEvent = function () {
        var postObject = this;

        postObject.$wrapper.on('submit', '.form-posting-main', function (e) {
            e.preventDefault();

            var _form = $(this);

            var submitButton = _form.find("button[type='submit']");
            submitButton.attr("disabled", "disabled");
            submitButton.find('.fn-submit').addClass('d-none');
            submitButton.find('.fn-spinner').removeClass('d-none');

            var actionUrl = _form.attr('action');
            var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

            postObject.postData(actionUrl, formData, postObject);
        });

        if (!postObject.$modal) {
            postObject.$wrapper.on('focusin', '.panel-body', function (e) {
                postObject.openOverlay();
            });

            postObject.$wrapper.on('click', '.panel-default.placeholder', function (e) {
                postObject.openOverlay();
                postObject.$wrapper.find('textarea').focus();
            });

            postObject.$wrapper.on('click', '.fileinput-button', function (e) {
                postObject.openOverlay();
            });

            postObject.$wrapper.on('click', '.tl-close-posting', function (e) {
                postObject.closeOverlay();
            });
        }
    }

    PostingSystem.prototype.openOverlay = function () {
        var postObject = this;

        $('.tl-hidden-post-form').each(function () {
            $(this).removeClass('d-none');

        });

        if (postObject.$wrapper.find('.autogrow-textarea-mirror').length == 0) {
            $(".autogrow").autoGrow({
                extraLine: true,
            });
        }

        postObject.$wrapper.find('article.panel').addClass('panel-focus');
        //$('body').addClass('post-focus');

        if ($('body').hasClass('show-content-sidebar')) {
            $('body').removeClass('show-content-sidebar').addClass('restore-content-sidebar');
        }

        postObject.$wrapper.css('z-index', 1020);
        postObject.$wrapper.css('position', 'relative');

        if (mobile && terminalType == 'Smartphone') {

            postObject.$wrapperTop = $('#navigation').outerHeight(true) + 'px';
            postObject.$wrapperBottom = $('#navigation .navigation-menu').outerHeight(true) + 'px';

            $('#navigation').addClass('d-none');
            $('#wrapper').css({ top: '0' });
            $('#wrapper').css({ bottom: '0' });

        }

        if (!$("#posting-overlay").length) {
            var overlay = jQuery('<div id="posting-overlay"> </div>');
            overlay.prependTo('.main-container .feed');
            var overlay2 = jQuery('<div id="posting-overlay2" class="nav"> </div>');
            overlay2.prependTo('#navigation');
            $(document).on('click', '#posting-overlay, #posting-overlay2', function (e) {
                $('#posting-overlay').remove();
                $('#posting-overlay2').remove();
                postObject.closeOverlay();
            });

            if ($('#posting-overlay').length) {
                var target = $("#wrapper")[0]; // <== Getting raw element
                $("#posting-overlay").scroll(function () {
                    target.scrollTop = this.scrollTop;
                });
            }
        }

    }

    PostingSystem.prototype.closeOverlay = function () {
        var postObject = this;

        postObject.$wrapper.find('article.panel').removeClass('panel-focus');
        //$('body').removeClass('post-focus');

        if ($('body').hasClass('restore-content-sidebar')) {
            $('body').addClass('show-content-sidebar').removeClass('restore-content-sidebar');
        }

        postObject.$wrapper.css('z-index', 10);

        $('.tl-hidden-post-form').each(function () {
            $(this).addClass('d-none');
        });

        if (mobile && terminalType == 'Smartphone') {
            $('#navigation').removeClass('d-none');
            $('#wrapper').css({ top: postObject.$wrapperTop });
            $('#wrapper').css({ bottom: postObject.$wrapperBottom });
        }

        $('#posting-overlay').remove();
        $('#posting-overlay2').remove();

        postObject.$firstLoad = true;
        postObject.setupForm();
    }

    PostingSystem.prototype.setupForm = function () {
        var postObject = this;
        var data = '';

        if (postObject.$initFirstLoad && postObject.$firstLoad) {
            postObject.$firstLoad = false;
            data = {
                hideControls: true,
                default_author_id: current_profile_id,
                default_author_type: current_profile_type
            };
        }
        this.postData(postObject.$defaultRoute, data, postObject);
    }


    window.PostingSystem = function (options) {
        var postBloc = new PostingSystem(options);

        if (postBloc.$initFirstLoad) {
            postBloc.setupForm();
        }
        postBloc.listenEvent();
        postBloc.listenClickEvent();
        postBloc.liveUrl('#form-post-content');

        return postBloc;
    };

    PostingSystem.prototype.postData = function (url, data, postObject) {
        var that = this;
        $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function (data) {
                let fromCalendar;
                // add listener on data.success to create autoLoad Modal with
                // success message
                if (typeof data.fromCalendar != 'undefined' && data.fromCalendar) {
                    fromCalendar = true;
                } else {
                    fromCalendar = false;
                }
                
                if (typeof data.returnCode != 'undefined') {
                    if (data.returnCode == 'success') {
                        // close modal if open
                        if (data.viewContent && data.modal && !fromCalendar) {
                            // update content in newsfeed
                            var elTarget = $(data.targetId);
                            if (that.$originalTarget == data.targetId) {
                                elTarget.replaceWith(data.viewContent);
                            } else {
                                $(that.$originalTarget).fadeOut('slow', function () {
                                    $(this).remove();
                                });;
                            }
                        } else if (data.viewContent && !data.modal) {
                            // insert content in newsfeed
                            if ($(data.targetId + " article.topic").length > 0) {
                                $(data.viewContent).insertBefore(data.targetId + " article.topic:first").hide().fadeIn('slow');
                            } else {
                                $(data.targetId).append(data.viewContent).hide().fadeIn('slow');
                            }
                        }

                        if (data.viewContent && !fromCalendar) {
                            // reload view media modal trigger
                            var $modal = $('#viewMediaModal');
                            new PlayMediaModal({
                                $modal: $modal,
                                $modalTitle: $modal.find('.modal-title'),
                                $modalContent: $modal.find('.modal-carousel .carousel-item'),
                                $media: $('.viewMedia'),
                                baseUrl: baseUrl
                            });
                            if (typeof data.targetMapId != 'undefined') {
                                loadMapEvents(data.targetMapId);
                            }
                        }

                        if (data.view) {
                            // replace form with return form error or empty
                            if (data.modal) {
                                postObject.$wrapper.find('.modal-body').html(data.view);
                            } else {
                                postObject.$wrapper.html(data.view);
                            }

                            postObject.$wrapper.find('.submenu-list').each(function (e) {
                                var psMenu = $(this)[0];
                                new PerfectScrollbar(psMenu);
                            });

                            postObject.$wrapper.find("textarea.mentions").mentionsInput({ source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels&types[5]=medias',wrapper: that.$wrapper });
                        }

                        if (typeof data.returnMessage != 'undefined' && typeof data.autoFireModal != 'undefined') {
                            if (!postObject.$modal) {
                                postObject.closeOverlay();
                            }

                            // call success modal
                            $('#modal-ajax').find('.modal-content').html(data.autoFireModal);
                            $("#modal-ajax").modal('show');
                            setTimeout(function () {
                                $("#modal-ajax").modal("hide");
                            }, 2000);
                        }
                        
                        if (fromCalendar && typeof data.event != undefined) {
                            $('#calendar').fullCalendar('renderEvent', data.event, true);
                        }
                        
                    } else if (data.returnCode == 'error') {
                        // replace form with error containing form
                        if (data.modal) {
                            postObject.$wrapper.find('.modal-content').html(data.view);
                        } else {
                            postObject.$wrapper.html(data.view);
                        }

                        postObject.$wrapper.find('.submenu-list').each(function (e) {
                            var psMenu = $(this)[0];
                            new PerfectScrollbar(psMenu);
                        });

                        postObject.$wrapper.find("textarea.mentions").mentionsInput({ source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels&types[5]=medias',wrapper: that.$wrapper });
                    }
                }

                postObject.$displayMap = (typeof data.displayMap != 'undefined') ? data.displayMap : false;

                if (data.typePost == 'event') {
                    postObject.setupEvent();
                }
                if (data.typePost == 'offer') {
                    postObject.setupOffer();
                }
                if (data.typePost == 'news') {
                    postObject.setupNews();
                }
                postObject.reloadTags();

                // new media post system on edit
                (function () {
                    var attachmentSystem = postObject.$wrapper;
                    new AttachmentSystem({
                        $wrapper: attachmentSystem,
                        $fileUpload: attachmentSystem.find('#fileupload'),
                        $profileMedia: 0,
                        $postMedia: 1,
                        $mediaTemplate: '.tl-posted-medias',
                        $confidentiality: attachmentSystem.find('input:radio[name=confidentiality]'),
                        $profileId: that.$profileId,
                        $profileType: that.$profileType,
                    });
                })();

                // $('.modal-body').updatePolyfill();
            }
        });
    }

    PostingSystem.prototype.setupEvent = function () {
        var postObject = this;
        postObject.$wrapper.updatePolyfill();

        postObject.$wrapper.on('change', 'input#panel-event-allday', function (e) {
            if ($(this).is(":checked")) {
                postObject.$wrapper.find('.time-selector').each(function (e) {
                    $(this).hide();
                });
            }
            else {
                postObject.$wrapper.find('.time-selector').each(function (e) {
                    $(this).show();
                });
            }
        });
        
        // check if time is input to disable all day selector
        postObject.$wrapper.on('change', '.panel-post-time-input', function (e) {
            let timeEmpty = true;
            postObject.$wrapper.find('.panel-post-time-input').each(function (e) {
                if ($(this).val() != '') {
                    timeEmpty = false;
                }
            });
            postObject.$wrapper.find('input#panel-event-allday').prop("checked", timeEmpty);
            
        });

        /*
        postObject.$wrapper.on('change', '.fn-change-timezone', function(e){
            var timeZoneDD = $(this);
            var userTimeZone = timeZoneDD.data('user-timezone');
            var userOffset = timeZoneDD.data('user-time-offset');
            postObject.$wrapper.find(".time-selector input[type='time']").each(function(e){
                var optionSelected = timeZoneDD.find(":selected");
                if(optionSelected.data("offset") != userOffset){
                    // update usertimezone for reference
                }
            });
        });
        */

        (function () {
            new MiniMapForm({
                $wrapper: postObject.$wrapper,
                $displayMap: postObject.$displayMap,
                $placeName: '',
                $elementType: '{{ get_class($post) }}'
            });
        })();

        postObject.$linkImported = false;
        postObject.$importedUrls = [];
        postObject.liveUrl('#form-event-content');
    }

    PostingSystem.prototype.setupNews = function () {
        var postObject = this;
        postObject.$linkImported = false;
        postObject.$importedUrls = [];
        postObject.liveUrl('#form-post-content');
    }

    PostingSystem.prototype.setupOffer = function () {
        var postObject = this;
        postObject.$wrapper.updatePolyfill();

        (function () {
            new MiniMapForm({
                $wrapper: postObject.$wrapper,
                $displayMap: postObject.$displayMap,
                $placeName: '',
                $elementType: '{{ get_class($post) }}'
            });
        })();

        postObject.$linkImported = false;
        postObject.$importedUrls = [];
        postObject.liveUrl('#form-offer-content');
    }

    PostingSystem.prototype.initializeMap = function () {
        var postObject = this;
    }

    PostingSystem.prototype.liveUrl = function (fieldSurvey) {
        var postObject = this;
        if (postObject.$listenedText.indexOf(fieldSurvey) == -1) {
            postObject.$listenedText.push(fieldSurvey);

            var url = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;

            postObject.$wrapper.on("keyup", fieldSurvey, function (e) {
                var urls, output = "";
                if (e.keyCode !== 8 && e.keyCode !== 9 && e.keyCode !== 13 && e.keyCode !== 32 && e.keyCode !== 46) {
                    // Return is backspace, tab, enter, space or delete was not
                    // pressed.
                    return;
                }

                while ((urls = url.exec(this.value)) !== null) {
                    if (postObject.$importedUrls.indexOf(urls[0]) == -1) {
                        output += urls[0] + ", ";

                        // post url to controller
                        if (postObject.importableUrls(urls[0])) {
                            postObject.importUrl(urls[0], fieldSurvey);
                        }
                        else {
                            postObject.linkPreview(urls[0], fieldSurvey);
                        }
                    }
                }
            });

            postObject.$wrapper.on("paste", fieldSurvey, function (e) {
                var urls, output = "";
                var contentInput = this;
                setTimeout(function () {

                    while ((urls = url.exec(contentInput.value)) !== null) {
                        if (postObject.$importedUrls.indexOf(urls[0]) == -1) {
                            output += urls[0] + ", ";
                            if (postObject.importableUrls(urls[0])) {
                                // post url to controller
                                postObject.importUrl(urls[0], fieldSurvey);
                            }
                            else {
                                postObject.linkPreview(urls[0], fieldSurvey);
                            }
                        }
                    }
                }, 100);
            });
        }
    }

    PostingSystem.prototype.importableUrls = function (url) {
        var regYt = /^(?:https?:\/\/)?(?:(?:www|m)\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        var regVim = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
        var regDm = /^(?:(?:http|https):\/\/)?(?:www.)?(dailymotion\.com|dai\.ly)\/((video\/([^_]+))|(hub\/([^_]+)|([^\/_]+)))$/;
        var regSc = /((https:\/\/)|(http:\/\/)|(www.)|(m\.)|(\s))+(soundcloud.com\/)+[a-zA-Z0-9\-\.]+(\/)+[a-zA-Z0-9\-\.]+/;

        if (url.match(regYt) != null || url.match(regVim) != null || url.match(regDm) != null || url.match(regSc) != null) {
            return true;
        }
        else {
            return false;
        }
    }

    PostingSystem.prototype.linkPreview = function (url, textField) {
        var that = this;

        if (!that.$linkImported) {
            that.$linkImported = true;
            $.ajax({
                url: laroute.route('link.metas'),
                type: "POST",
                data: {
                    url: url
                },
                success: function (data) {
                    if (data.result == 'success') {
                        that.$importedUrls.push(url);
    
                        // display in template
                        var source = $('#template-link-preview').html();
                        var templateLink = Handlebars.compile(source);
                        var htmlLink = templateLink(data);
    
                        that.$wrapper.find(".imported-link").append(htmlLink);
    
                        // update hidden field
                        var inputVal = that.$wrapper.find("#postImportedLinksId").val();
                        // Initialize the selected medias
                        if ($.isNumeric(inputVal)) {
                            that.importedLinksId = [inputVal];
                        } else if (inputVal && inputVal.indexOf(',') > -1) {
                            that.importedLinksId = inputVal.split(',');
                        } else {
                            that.importedLinksId = [];
                        }
                        that.importedLinksId.push(data.linkId);
                        that.$wrapper.find("#postImportedLinksId").val(that.importedLinksId.join(','));
    
                        // store imported url in tab
                        that.$importedUrls.push(url);
    
                        //remove url from content zone
                        var currentText = that.$wrapper.find(textField).val();
                        currentText = currentText.replace(url, '');
                        that.$wrapper.find(textField).val(currentText);
                    }
                },
                fail: function () {
                    that.$linkImported = false;
                }
            });
        }
    }

    PostingSystem.prototype.storeTarget = function () {
        var postObject = this;
        var postType = postObject.$wrapper.find("input[name='post_type']").val().capitalize();
        if (postType == 'Event') postType = 'TEvent';
        var postId = postObject.$wrapper.find("input[name='id']").val();
        var profileType = postObject.$wrapper.find("input[name='type_foreign']").val().capitalize();

        var orinigalTarget = '#' + postType + '-' + profileType + '-' + postId;
        postObject.$originalTarget = orinigalTarget;
    }

    PostingSystem.prototype.storeUrls = function () {
        var postObject = this;
        var url = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
        var urls, output = "";

        while ((urls = url.exec(postObject.$wrapper.find('textarea').val())) !== null) {
            if (postObject.$importedUrls.indexOf(urls[0]) == -1) {
                output += urls[0] + ", ";

                // post url to controller
                postObject.$importedUrls.push(urls[0]);
            }
        }

        // store imported url in tab
        postObject.$importedUrls.push(url);
    }

    PostingSystem.prototype.importUrl = function (url, textField) {
        var that = this;

        // display loader in media zone
        var loaderId = Math.random().toString(36).substring(7);
        var source = $('#template-media-import-loader').html();
        that.template = Handlebars.compile(source);
        var loaderHtml = that.template({ elementId: loaderId });
        that.$wrapper.find(".tl-posted-medias").append(loaderHtml);

        var confidentiality = 1;
        $.ajax({
            url: laroute.route('media_import'),
            type: "POST",
            data: {
                url: url,
                confidentiality: confidentiality,
                postMediaModal: 1,
            },
            success: function (data) {
                // make html response to display in post modal medias
                var html = '';
                var source = $('#template-media-import').html();
                var inputVal = that.$wrapper.find("#postSelectedMediasId").val();
                that.template = Handlebars.compile(source);

                // Initialize the selected medias
                if ($.isNumeric(inputVal)) {
                    that.selectedMediaIds = [inputVal];
                } else if (inputVal && inputVal.indexOf(',') > -1) {
                    that.selectedMediaIds = inputVal.split(',');
                } else {
                    that.selectedMediaIds = [];
                }

                var media = data.import;
                media.name = media.file_name;
                that.selectedMediaIds.push(media.id);

                html += that.template({ media: media });

                // put html in post modal
                //that.$wrapper.find(".tl-posted-medias").append(html);
                that.$wrapper.find('.tl-posted-medias #import-loader-' + loaderId).replaceWith(html);
                // add ids in hidden form field
                that.$wrapper.find("#postSelectedMediasId").val(that.selectedMediaIds.join(','));

                // store imported url in tab
                that.$importedUrls.push(url);

                //remove url from content zone
                var currentText = that.$wrapper.find(textField).val();
                currentText = currentText.replace(url, '');
                that.$wrapper.find(textField).val(currentText);
            },
            fail: function () {
                // remove loader
                var removeElement = that.$wrapper.find('.tl-posted-medias #import-loader-' + loaderId).remove();
            }
        });
    };

    PostingSystem.prototype.clearImportUrlErrors = function () {
        this.$importUrlGroup.removeClass('has-error');
        this.$importHelp.empty();
    };

    PostingSystem.prototype.addImportUrlErrors = function (errors) {
        this.$importUrlGroup.addClass('has-error');

        for (var i = 0; i < errors.length; i++) {
            this.$importHelp.append('<p>' + errors[i] + '</p>');
        }
    };

    // Handlebars helpers used by the modal
    Handlebars.registerHelper('if_eq', function (a, b, opts) {
        if (a == b) {
            return opts.fn(this);
        }

        return opts.inverse(this);
    });

    Handlebars.registerHelper('safe_html', function (value) {
        return new Handlebars.SafeString(value);
    });

})();