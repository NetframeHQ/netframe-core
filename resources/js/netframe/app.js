//========= Setup the Attachment Modal.
/*
(function () {
    var attachmentModal = $('#navigationAttachMediaModal');
    new AttachmentModal({
        $wrapper: attachmentModal,
        $fileUpload: attachmentModal.find('#fileupload'),
        $postAs: 'publish-as-hidden-md',
        $importForm: attachmentModal.find('#import'),
        $importHelp: attachmentModal.find('#importUrl .help-block'),
        $importUrlGroup: attachmentModal.find('#importUrl'),
        $importUrl: attachmentModal.find('input[name=url]'),
        $profileMedia: 0,
        $autoUpload: 1,
        $confidentiality: attachmentModal.find('input:radio[name=navigationAttachMediaModalConfidentiality]')
    });
})();

(function() {
    var attachmentModal = $('#postAttachMediaModal');
    new AttachmentModal({
        $wrapper: attachmentModal,
        $fileUpload: attachmentModal.find('#fileupload'),
        $postAs: 'publish-as-hidden-md',
        $importForm: attachmentModal.find('#import'),
        $importHelp: attachmentModal.find('#importUrl .help-block'),
        $importUrlGroup: attachmentModal.find('#importUrl'),
        $importUrl: attachmentModal.find('input[name=url]'),
        $profileMedia: 0,
        $postMediaModal: 1,
        $mediaTemplate: $('#mediaNewsTemplate'),
        $autoUpload: 1,
        $confidentiality: attachmentModal.find('input:radio[name=navigationAttachMediaModalConfidentiality]')
    });
})();
*/

(function ($) {

    /* geolocate */
    /* has been moved in page needed map
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(myPosition, errorPosition, {maximumAge:600000, enableHighAccuracy:true});
    }
    */

    $("img.lazy").lazyload();

    if ($('#sidebar').is(':visible')) {
        $('#sidebar').scrollToFixed({
            marginTop: function () {
                var marginTop = $(window).height() - $('#sidebar').outerHeight(true) - 40;
                if (marginTop >= 0) return 20;
                return marginTop;
            }
        });

        $('#messages-list').scrollToFixed({
            marginTop: function () {
                var marginTop = $(window).height() - $('#messages-list').outerHeight(true) - 40;
                if (marginTop >= 0) return 20;
                return marginTop;
            }
        });
    }


    // Initialise ajax header for token csrf
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

    // "ajax history"
    $(window).bind('hashchange', function () {
        if (historyTab != '') {
            tlHistory();
        }
    });

    //auto fire modal if id exists
    if ($("#autoFireModal").length) {
        $("#autoFireModal").modal('show');
    }

    //auto fire modal if id exists
    if ($("#autoFireMediaModal").length) {
        $("#autoFireMediaModal").modal('show');
    }

    // Fix input element click problem
    $('.dropdown-menu .menu-profile-list').click(function (e) {
        e.stopPropagation();
    });

    /*
    mainDropdownHeight();
    $( window ).resize(function(){
            mainDropdownHeight();
    });
    */

    // Add down arrow only to menu items with submenus
    $(".dropdown > li:has('ul')").each(function () {
        $(this).find("a:first").append("<img src='images/down-arrow.png' />");
    });

    //toggle visibility
    $(document).on('click', "[data-toggle='tl-toggle']", function (e) {
        e.preventDefault();
        that = $(this);
        var target = that.data('target');
        var state = that.data('state');
        if (state == "closed") {
            $(target).each(function () {
                if (!$(this).hasClass('active')) {
                    $(this).toggle();
                }
            });
            that.find('span').removeClass('glyphicon-plus');
            that.find('span').addClass('glyphicon-minus');
            that.data('state', 'opened');
        }
        if (state == "opened") {
            $(target).each(function () {
                if (!$(this).hasClass('active')) {
                    $(this).toggle();
                }
            });
            $that.find('span').removeClass('glyphicon-minus');
            $that.find('span').addClass('glyphicon-plus');
            $that.data('state', 'closed');
        }
    });

    //block page scroll when mouseWheel on tchat, menus
    $(document).on('DOMMouseScroll mousewheel', '.chatUserList, #netframe-main-dropdown', function (ev) {
        var $this = $(this),
            scrollTop = this.scrollTop,
            scrollHeight = this.scrollHeight,
            height = $this.height(),
            delta = (ev.type == 'DOMMouseScroll' ?
                ev.originalEvent.detail * -40 :
                ev.originalEvent.wheelDelta),
            up = delta > 0;

        var prevent = function () {
            ev.stopPropagation();
            ev.preventDefault();
            ev.returnValue = false;
            return false;
        }

        if (!up && -delta > scrollHeight - height - scrollTop) {
            // Scrolling down, but this will take us past the bottom.
            $this.scrollTop(scrollHeight);
            return prevent();
        } else if (up && delta > scrollTop) {
            // Scrolling up, but this will take us past the top.
            $this.scrollTop(0);
            return prevent();
        }
    });

    // form html for browser don't support fields date, time...
    webshims.setOptions('waitReady', false);
    webshims.setOptions('forms-ext', { types: 'date' });
    webshims.polyfill('forms forms-ext');

    // ========= Fixed & Hack Modal Bootstrap, Reload Content & Remove Content
    // of modal when click open
    $(document).on('hidden.bs.modal', '.modal-emptyable', function (e) {
        $(this).data('bs.modal', null);
        $(this).find(".modal-content").empty()
    });

    // TOOLTIP
    $('[data-toggle=tooltip]').tooltip();

    // POPOVER
    $('.fn-tl-popover').popover({
        trigger: 'click',
        animate: true,
        html: true,
        content: function () {
            var div_id = "tmp-id-" + $.now();
            return popoverContent($(this), div_id);
        }
    });

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    function popoverContent(popoverLink, div_id) {
        $.get(popoverLink.data('url'), function (data) {
            var height1 = $('#' + div_id).height();
            $('#' + div_id).html(data.view);
            var height2 = $('#' + div_id).height();
            if ($('.navbar-fixed-bottom').is(':visible') && height1 != null && height2 != null) {
                var popoverTop = $('#' + div_id).closest('.popover').offset().top;
                var newPopoverTop = popoverTop - (height2 - height1 - 30);
                $('#' + div_id).closest('.popover').css('top', newPopoverTop);
            }
        });
        return '<div id="' + div_id + '"><div class="loader-notification media"><div class="media-body"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></div></div></div>';
    }

    // --------------------- CONFIRM ALERT TO DELETE ITEM IN AJAX
    /*
     * ex: <a href="#url" class="confirm-delete" data-txtconfirm="my texte
     * displaying"></a>
     */
    $(document).on('click', '.fn-confirm-delete', function (e) {
        var _confirm = confirm($(this).data('txtconfirm'));

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else {
            e.preventDefault();
            e.stopPropagation();
            var linkHref = $(this).attr('href');
            var el = $(this);

            $.get(linkHref, function (data) {
                if (data.delete === true) {
                    el.parents(data.targetId).fadeOut('slow', function () {
                        $(this).remove();
                    });
                }
            });
        }

    });

    // --------------------- CHOOSE PROFILE NAVIGATE
    $('.f-navigate-profile').bind('click', function (e) {
        e.preventDefault();
        var el = $(this);
        var targetEl = $('#profile-navbar');
        var targetDropdown = el.parents('.dropdown').find('li.row-fluid');

        targetDropdown.on('hide.bs.dropdown', function () {
            return false;
        });

        $.post("/netframe/navigate-profile", {
            profile: el.data('profile'),
            id: el.data('id'),
            name: el.data('name')
        }, function (data) {
            var profileTag = targetEl.find('.profile-name');
            profileTag.slideUp('normal', function () {
                var renderHtml = null;

                renderHtml = data.profileName;

                $(this).text(renderHtml).slideDown('normal');
            });

            targetEl.attr('href', data.url);
            $('.navbar-fixed-bottom').find('#profile-navbar').attr('href', data.url);

            var profileTagImg = targetEl.find('.profile-image');
            var profileTagImgXs = $('.navbar-fixed-bottom').find('#profile-navbar').find('.profile-image');
            if (data.profileImage != 'null') {
                srcImage = '/media/download/' + data.profileImage + '?thumb=1';
            }
            else {
                srcImage = '/assets/img/avatar/' + data.profileType + '.jpg';
            }
            profileTagImg.attr('src', srcImage);
            profileTagImgXs.attr('src', srcImage);
        });

    });

    $(document).on('click', ".f-publish-as", function (e) {
        e.preventDefault();
        var el = $(this);
        var dataProfileType = el.data('profile');
        var dataProfileId = el.data('profile-id');
        var target = el.closest('.tl-publish-as').find('.tl-display-as');
        var mainForm = el.closest('.tl-publish-as').data('target-form');
        var secondaryChoice = el.closest('.tl-publish-as').data('secondary');
        var postfixTarget = el.closest('.tl-publish-as').data('postfix');
        var targetInputId = el.closest('.tl-publish-as').data('target-input-id');
        var targetInputType = el.closest('.tl-publish-as').data('target-input-type');

        // target main post form
        var targetForm = $(mainForm).find("#publish-as-hidden-" + postfixTarget);
        console.log(mainForm);
        console.log(targetForm);
        var inputHiddenIdForeign = targetForm.find('input[name="' + targetInputId + '"]');
        var inputHiddenTypeForeign = targetForm.find('input[name="' + targetInputType + '"]');

        if (dataProfileType != 'user' && secondaryChoice == 0) {
            $('.tl-publish-as-choice').removeClass('hidden');
        }
        else if (secondaryChoice == 0) {
            $('.tl-publish-as-choice').addClass('hidden');
        }

        // modify selector choice
        target.html(el.html());

        // update hidden author fields
        inputHiddenIdForeign.val(dataProfileId);
        inputHiddenTypeForeign.val(dataProfileType);
    });

    // --------------------- LIKE BUTTON FUNCTION
    $(document).on('click', '.fn-netframe-like', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var el = $(this);
        var refreshIcon = '.glyphicon-refresh-animate';

        // manage loading icon
        el.find('.label-info').stop().slideUp('fast', function () {
            el.find(refreshIcon).removeClass('hide');
        });

        var dataForeign = el.data('tl-like');

        var jqXhr = $.post("/netframe/like", {
            postData: dataForeign
        });

        jqXhr.success(function (data) {
            var targetLineNumber = el.find('.like-number');
            targetLineNumber.text(parseInt(targetLineNumber
                .text())
                + parseInt(data.increment));

            if (data.hasOwnProperty('likeThis')) {
                el.addClass('active');
            } else {
                el.removeClass('active');
            }

        }).error(function (xhr) {
            console.log(xhr.responseText);
        }).complete(function (data) {
            el.find(refreshIcon).addClass('hide');
            el.find('.label-info').stop().slideDown('normal',
                function () {
                    $(this).removeAttr('style');
                });
        });

    });


    // --------------------- ADD FRIEND PROFILE USER FUNCTION
    $(document).on('click', '.fn-add-friend', function (e) {
        e.preventDefault();
        var el = $(this);
        var targetAddWay = el.find('.add-way');
        var refreshIcon = '.glyphicon-refresh-animate';
        var actionTypeButton = null;

        el.find(targetAddWay).stop().slideUp('fast', function () {
            el.find(refreshIcon).removeClass('hide');
        });

        if (el.data('tl-add')) {
            var dataForeign = el.data('tl-add');
            actionTypeButton = "add";

        } else if (el.data('tl-unlocked')) {
            var dataForeign = el.data('tl-unlocked');
            actionTypeButton = 'unlocked';
        }

        var jqXhr = $.post(laroute.route('friend.ask'), {
            postData: dataForeign,
            type: actionTypeButton
        });

        jqXhr.success(function (data) {
            if (data.hasOwnProperty('addThis') || data.hasOwnProperty('ReAddThis')) {
            }
            else if (data.hasOwnProperty('unlockedThis')) {
                //el.removeClass('btn-danger').addClass('btn-default');
            }
            else if (data.hasOwnProperty('suppThis')) {
                el.removeClass('friends-ok');
            }

            el.find(targetAddWay).text(data.displayText);
            el.find(refreshIcon).addClass('hide');
            el.find(targetAddWay).stop().slideDown('normal');
        });

    });

    //--------------------- CHANGE MEMBERS STATUS
    $(document).on('click', '.fn-change-member', function (e) {
        e.preventDefault();
        var el = $(this);
        if (el.data('confirm')) {
            var loadAction = confirm(el.data('confirm'));
        }
        else {
            var loadAction = true;
        }
        if (loadAction) {
            var action = el.data('tl-action');
            var user = el.data('tl-user');
            var param = {
                action: action
            };
            var jqXhr = $.post(laroute.route('join.answer', param), {
                postData: user
            });

            jqXhr.success(function (data) {
                var elTarget = el.closest('li.member-card');
                elTarget.fadeOut('slow', function () {
                    elTarget.replaceWith(data.viewContent);
                    elTarget.fadeIn('slow');
                });
            });
        }
    });

    //--------------------- CHANGE INSTANCE USER ROLE
    $(document).on('click', '.fn-change-instance-role', function (e) {
        e.preventDefault();
        var el = $(this);
        if (el.data('confirm')) {
            var loadAction = confirm(el.data('confirm'));
        }
        else {
            var loadAction = true;
        }
        if (loadAction) {
            var action = el.data('tl-action');
            var user = el.data('tl-user');
            var param = {
                action: action
            };
            var jqXhr = $.post(laroute.route('instance.role', param), {
                postData: user
            });

            jqXhr.success(function (data) {
                var elTarget = el.closest('li.member-card');
                elTarget.fadeOut('slow', function () {
                    elTarget.replaceWith(data.viewContent);
                    elTarget.fadeIn('slow');
                });
            });
        }
    });

    //--------------------- FUNCTION ANSWER BUTTON FUNCTION
    function answerNotif(selector, route) {
        //selector.bind("click", function(e) {
        $(document).on('click', selector, function (e) {

            e.preventDefault();
            var el = $(this);
            var user = el.data('tl-user');
            var action = el.data('tl-action');
            var containerElement = el.closest(".container-element");
            var param = {
                action: action
            };
            var jqXhr = $.post(laroute.route(route, param), {
                postData: user
            });

            jqXhr.success(function (data) {
                if (user) {
                    containerElement.fadeOut();
                }
            });
        });
    };

    //--------------------- ANSWER JOIN BUTTON
    var route = 'join.answer';
    var selector = '.fn-join-answer';
    answerNotif(selector, route);

    //--------------------- ANSWER ASKED FRIEND BUTTON
    var route = "friend.answer";
    var selector = '.fn-ask-friend';
    answerNotif(selector, route);

    //------------------INVITE ANSWER
    var route = "join.invite.answer";
    var selector = '.fn-join-invite';
    answerNotif(selector, route);

    //------------------ANSWER FOLLOW PROJECT
    var btnFollow = $('.fn-follow-project');

    btnFollow.bind('click', function (e) {
        e.preventDefault();
        var _href = $(this).attr('href');

        var el = $(this);
        var panel = el.closest(".panel");

        var jqXhr = $.get(_href, function (data) {
        }).success(function (data) {
            if (data) {
                panel.fadeOut();
            }
        });
    });

    // --------------------- REMOVE JOIN PROFILE FUNCTION
    $(document).on('click', '.fn-remove-join', function (e) {
        e.preventDefault();
        var el = $(this);

        if (confirm(el.data('confirm'))) {
            var targetJoinWay = el.find('.join-way');
            var refreshIcon = '.glyphicon-refresh-animate';
            var join = el.find('.glyphicon');

            var dataForeign = el.data('tl-join');
            var jqXhr = $.post(laroute.route("join.remove"), dataForeign);
            jqXhr.success(function (data) {
                // replace button with json view
                el.fadeOut('slow', function () {
                    el.replaceWith(data.viewContent);
                    el.fadeIn('slow');
                });
            });
        }
    });

    // --------------------- LIKE BUTTON PROFILE FUNCTION
    $(document).on("click", '#like-profile .fn-like-profile', function (e) {
        e.preventDefault();
        var el = $(this);
        var route = null;
        var actionTypeProfile = null;

        if (el.data('tl-like')) {
            var dataForeign = el.data('tl-like');
            route = "/netframe/like-profile";
            actionTypeProfile = "like";

        } else if (el.data('tl-subscrib')) {
            var dataForeign = el.data('tl-subscrib');
            route = "/netframe/subscrib-profile";
            actionTypeProfile = "subscrib";

        } else {
            return false;
        }

        var jqXhr = $.post(route, {
            postData: dataForeign
        });

        jqXhr.done(function (data) {

            if (actionTypeProfile === 'like') {
                if (data.hasOwnProperty('liked')
                    && data.hasOwnProperty('subscrib')) {
                    // Change status button in On like & subscribe
                    var targetLineNumber = el.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                    if (data.subscrib) {
                        var targetLineNumber = el.closest('#like-profile').find('.follower-number');
                        targetLineNumber.text(parseInt(targetLineNumber
                            .text())
                            + 1);
                        el.siblings().addClass('active');
                    }
                    el.addClass('active');

                } else if (data.hasOwnProperty('liked')) {
                    var targetLineNumber = el.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                    // Change status button in On just like
                    el.addClass('active');

                } else if (data.hasOwnProperty('unlike')) {
                    // Change status button like in Off
                    el.removeClass('active');
                    var targetLineNumber = el.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                }
            } else if (actionTypeProfile === 'subscrib') {
                if (data.hasOwnProperty('subscrib') && data.subscrib) {
                    // Change status button subscrib in On
                    el.addClass('active');
                    var targetLineNumber = el.find('.follower-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + 1);
                } else if (data.hasOwnProperty('unsubscrib')) {
                    // Change status button subscrib in Off
                    el.removeClass('active');
                    var targetLineNumber = el.find('.follower-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        - 1);
                }
            }
        });
    });

    // --------------------- NO LONGER FOLLOW BUTTON
    $(document).on("click", '.fn-stop-follow', function (e) {
        e.preventDefault();
        var el = $(this);

        var profileType = $(this).data('profile-type');
        var profileId = $(this).data('profile-id');

        var param = {
            profile_type: profileType,
            profile_id: profileId,
            noFollow: 1
        };

        $.post("/netframe/subscrib-profile", { postData: param })
            .success(function (data) {
                if (data.hasOwnProperty('unsubscrib')) {
                    $('article.post-' + profileType + '-' + profileId).fadeOut('slow', function () {
                        $(this).remove();
                    });
                }
            });
    });

    // --------------------- BOOKMARK (CLIP)
    $(document).on('click', '.fn-tl-clip', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var el = $(this);
        var profileType = $(this).data('profile-type');
        var profileId = $(this).data('profile-id');

        if ($(this).attr('data-media-id')) {
            //clip media
            var mediaId = $(this).data('media-id');

            var param = {
                profileType: profileType,
                profileId: profileId,
                mediaId: mediaId
            };
            var routeClip = 'playlist_user_profile_media_bookmark';
        }
        else {
            //clip profile
            var param = {
                profileType: profileType,
                profileId: profileId
            };
            var routeClip = 'playlist_user_profile_bookmark';
        }


        $.post(baseUrl + laroute.route(routeClip, param))
            .success(function (data) {
                if (data.result == 'add') {
                    el.addClass('active');
                    el.find('span').addClass('text-secondary');
                }
                else if (data.result == 'remove') {
                    el.removeClass('active');
                    el.find('span').removeClass('text-secondary');
                }
            });
    });

    // --------------------- SHARE BUTTON FUNCTION
    $(document).on('click', '.fn-netframe-share', function (e) {
        e.preventDefault();
    });

    // --------------------- PINTOP BUTTON FUNCTION
    $(document).on('click', '.fn-netframe-pintop', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var el = $(this);

        var dataForeign = el.data('tl-pintop');

        var jqXhr = $.post("/netframe/pintop", {
            postData: dataForeign
        });

        jqXhr.success(function (data) {
            window.location.reload();
        }).error(function (xhr) {
            console.log(xhr.responseText);
        }).complete(function (data) {
            el.find(refreshIcon).addClass('hide');
            el.find('.label-info').stop().slideDown('normal',
                function () {
                    $(this).removeAttr('style');
                });
        });

    });

    //========= Post Ajax Form for Modal publish and return response
    $('#modal-ajax').on('click', 'button[type="submit"]', function (event) {
        _form = $(this).parents('.modal-content').find('form');
        if (!_form.hasClass('no-auto-submit')) {
            submitModal(event, _form);
        }
    });

    $('#modal-ajax').on('submit', 'form', function (event) {
        if (!$(this).hasClass('no-auto-submit')) {
            submitModal(event, $(this));
        }
    });

    $('.fn-tl-post').on('submit', 'form', function (event) {
        if (!$(this).hasClass('no-auto-submit')) {
            submitModal(event, $(this));
        }
    });

    // -------------------------- Modal ajax comments
    $('.modal-ajax-comments').on('click', 'button[type="submit"]', function (event) {
        event.preventDefault();

        var modalId = '#' + $(this).parents('.modal.modal-ajax-comments').attr('id');
        var modalContent = $(modalId + ' .modal-content');
        var _form = $(this).parents('.modal-content').find('form');
        var actionUrl = _form.attr('action');
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: requestUrl
        });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function (data) {
                $(modalId).find('.modal-content').html(data.view);

                if (typeof data.redirect != 'undefined') {
                    window.open(data.redirect, typeof data.target!='undefined' ? data.target : null);
                }

                if(typeof data.reload != 'undefined' && data.reload===true) {
                    document.location.reload();
                }

                if (typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
                }

                // If publish model is A COMMENTARY, getback response
                if (typeof data.viewComment != 'undefined') {
                    $(modalId).modal('hide');

                    var elTarget = $(data.targetId);

                    // stop duplicate add when is in Ajax
                    event.stopPropagation();
                    // If is COMMENT in Insert Mode
                    //if(typeof data.edit != "undefined") {
                    if (data.edit == 'false') {
                        $(data.viewComment).appendTo($('.block-comment', elTarget)).hide().slideDown('normal');
                    }
                    else {
                        // If is COMMENT in Edit Mode
                        elTarget.fadeOut('slow', function () {
                            elTarget.replaceWith(data.viewComment);
                            elTarget.fadeIn('slow');
                        });
                    }
                }
            },
            error: function (textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });

    });

    // --------------------------  hide panel with slide up
    $(document).on('click', '.fn-close-panel', function (e) {
        e.preventDefault();
        $(this).closest('.panel')['slideUp']();
    });

    // display or hide importers
    $(document).on('click', '#displayImport', function (e) {
        var el = $(this);
        modalParent = el.closest('.modal');
        if (modalParent.find('.importMediaModal').hasClass('hidden')) {
            modalParent.find('.importMediaModal').removeClass('hidden');
        } else {
            modalParent.find('.importMediaModal').addClass('hidden');
        }
    });

    //LIMIT textarea input max chars
    $('.fn-textarea-limit').each(function () {
        maxChars = $(this).data('textarea-limit');
        leftChars = maxChars - $(this).val().length;

        textDisplayed = $(this).data('limit-text');
        var countDown = $('<span class="texearea-counter pull-right"><span class="counter">' + leftChars + '</span> ' + textDisplayed + '</span>').insertAfter($(this));

        $(this).keyup(function () {
            leftChars = maxChars - $(this).val().length;
            if (leftChars <= 0) {
                countDown.addClass("text-danger");
                leftChars = 0
                inputText = $(this).val();
                cutInputText = inputText.substring(0, maxChars);
                $(this).val(cutInputText);
            }
            if (leftChars > 0) {
                countDown.removeClass("text-danger");
            }
            countDown.find('.counter').text(leftChars);

        });

    });

    // ========= participate to event
    $(document).on('click', '.fn-event-participate', function (e) {
        e.preventDefault();
        var el = $(this);
        var params = {
            eventId: el.data('event')
        };

        $.post(baseUrl + laroute.route('event.participate', params))
            .success(function (data) {
                //modify button
                var targetLineNumber = el.find('.participant-number');
                if (data.participate) {
                    targetLineNumber.text(parseInt(targetLineNumber.text()) + parseInt(data.increment));
                    el.addClass('active');
                }
                else {
                    targetLineNumber.text(parseInt(targetLineNumber.text()) + parseInt(data.increment));
                    el.removeClass('active');
                }
            });
    });

    //=============== switch post content view from digest to complete view
    $(document).on('click', '.fn-switch-post-content', function (e) {
        e.preventDefault();
        var el = $(this);
        targetDisplay = el.data('target');
        el.closest('p').hide();
        $(targetDisplay).show();

    });

    // ========= 
    $(document).on('click', '.fn-tl-hide', function (e) {
        elementToHide = $(this).data('container');
        elementToShow = $(this).data('target');
        $(elementToHide).hide();
        $(elementToShow).show();
        if ($(elementToShow).hasClass('hidden')) {
            $(elementToShow).removeClass('hidden');
        }
    });


    // RE-GELOCALIZE WITH BROWSER COORDINATES
    function myPosition(position) {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;

        // send geoloc via ajax
        $.ajax({
            url: "/netframe/set-geolocation",
            data: { latitude: latitude, longitude: longitude },
            type: "POST",
            success: function (data) { }
        });
    }

    function errorPosition(error) {
        // stop geolocation and switch to geoloc by ip
        $.ajax({
            url: "/netframe/set-geolocation",
            data: { stop: '1' },
            type: "POST",
            success: function (data) {
            }
        });
    }

    //send gmt to server
    gmtOffset = new Date().getTimezoneOffset();
    $.ajax({
        url: "/netframe/set-gmt",
        data: { gmt: gmtOffset },
        type: "POST",
        success: function (data) {
        }
    });
})(jQuery);
//# sourceMappingURL=app.js.map
