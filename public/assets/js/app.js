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

//var mobile = (/iphone|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));
var mobile = '';
var terminalType = '';

var playMediaModal = '';

(function ($) {

    mobile = (device.mobile() || device.tablet());
    
    const appHeight = () => {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    window.addEventListener('resize', appHeight)
    appHeight()
    
    // ctrl+k interception
    var isCtrlHold = false;

    $(document).keyup(function (e) {
        if (e.which == 17 || e.which == 91) //17 is the code of Ctrl button
            isCtrlHold = false;
    });
    $(document).keydown(function (e) {
        if (e.which == 17 || e.which == 91)
            isCtrlHold = true;
        ShortcutManager(e);
    });

    function ShortcutManager(e){
        //Ctrl+K:
        if (isCtrlHold && e.which == 75) { //75 is the code of K button
            e.preventDefault(); //prevent browser from the default behavior
            $('#search-input').focus();
        }
    }

    // mobile detection
    if (device.mobile()) {
        terminalType = 'Smartphone';
    }

    /* geolocate */
    // has been moved in page needed map

    if (mobile && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(myPosition, errorPosition, { maximumAge: 600000, enableHighAccuracy: true });
    }

    // manage update post
    /*
    $(document).on('click', '.nf-fn-update-post', function(e) {
        e.preventDefault();
        
        var article = $(this).closest('article.topic');
        
        $.ajax({
            url: $(this).attr('href'),
            type: "GET",
            success: function (data) {
                article.replaceWith(data.view);
            }
        });
    });
    */

    // manage sidebar preference
    $(document).on('click', '.content-sidebar-toggle', function (e) {
        e.preventDefault();
        $('body').toggleClass('show-content-sidebar');
        if ($('body').hasClass('show-content-sidebar')) {
            var currentstate = 'open';
        }
        else {
            var currentstate = 'close';
        }
        $.ajax({
            url: laroute.route('sidebar.toggle'),
            data: { sidebarstate: currentstate },
            type: "POST",
            success: function (data) {
                //console.log('posted');
            }
        });
    });

    $(document).on('click', "[data-toggle='modal']", function (e) {
        e.preventDefault();
        var targetedModal = $(this).data('target');
        $(targetedModal + ' .modal-body').load($(this).attr('href'));
    });

    $('.modal').on('hidden.bs.modal', function (e) {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });

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

    var currentScrollTop = 0;
    $(document).on('click', '.fn-sidebar-toggle', function (e) {
        e.preventDefault();
        var sidebar = $("aside#sidebar");
        if (sidebar.is(':visible')) {
            sidebar.removeClass('mobile-display');
            $('#content .main-page-column').show();
            $("html, body").animate({ scrollTop: currentScrollTop }, "fast");
            $(this).find('span').addClass('glyphicon-plus');
            $(this).find('span').removeClass('glyphicon-minus');
        }
        else {
            sidebar.addClass('mobile-display');
            $("html, body").animate({ scrollTop: 0 }, "fast");
            currentScrollTop = $(document).scrollTop();
            $('#content .main-page-column').hide();

            $(this).find('span').removeClass('glyphicon-plus');
            $(this).find('span').addClass('glyphicon-minus');
        }
    });


    // Initialise ajax header for token csrf
    if (typeof io != 'undefined') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'X-Socket-ID': Echo.socketId()
            }
        });
    }
    else {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            }
        });
    }

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

    /* fix button group click on input */
    $(document).off('click.bs.button.data-api')
    $(document).on('click.bs.button.data-api', '[data-toggle^=button]', function (e) {
           var $btn = $(e.target)
           if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
           $btn.button('toggle')
           if (!$(e.target).is('input:radio')) {
               e.preventDefault()
           }
    });

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
        $(this).find(".modal-body").empty()
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

            //remove badge
            $(this).find('span.badge-notif').remove();
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

    // --------------------- CONFIRM ALERT TO DELETE ITEM IN HTTP GET
    $(document).on('click', '.fn-confirm-delete-get', function (e) {
        var _confirm = confirm($(this).data('txtconfirm'));

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    });


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


    //--------------------- LEFT SIDEBAR VIEW MORE
    $(document).on('click', '.fn-more-sidebar-link', function (e) {
        e.preventDefault();
        var targetList = $(this).data('target');
        $('#sidebar-wrapper ul.sidebar-links#' + targetList + ' li').removeClass('d-none');
        $('#sidebar-wrapper ul.sidebar-users#' + targetList + ' li').removeClass('d-none');
        $(this).remove();
    });

    //--------------------- PUBLISH SELECTOR

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
        var inputHiddenIdForeign = targetForm.find('input[name="' + targetInputId + '"]');
        var inputHiddenTypeForeign = targetForm.find('input[name="' + targetInputType + '"]');

        if (dataProfileType != 'user' && dataProfileType != 'channel' && secondaryChoice == 0) {
            $('.tl-publish-as-choice').removeClass('d-none');
        }
        else if (secondaryChoice == 0) {
            $('.tl-publish-as-choice').addClass('d-none');
        }

        // modify selector choice
        target.html(el.html());

        // update hidden author fields
        inputHiddenIdForeign.val(dataProfileId);
        inputHiddenTypeForeign.val(dataProfileType);

        // close menu
        $(this).closest('.menu-wrapper').removeClass('active');
    });

    $(document).on('click', '.fn-netframe-like', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var el = $(this);
        var dataForeign = el.data('tl-like');
        var jqXhr = $.post("/netframe/like", {
            postData: dataForeign
        });

        jqXhr.done(function (data) {
            if (el.closest('.foot-left').length) {
                var likeContainer = el.closest('.foot-left');
            }
            else if (el.closest('.comments-like').length) {
                var likeContainer = el.closest('.comments-like');
            }
            
            var targetLineNumber = likeContainer.find('.btn-digit');
            // console.log(targetLineNumber.find('a').length);
            if (targetLineNumber.find('a').length) {
                targetLineNumber = targetLineNumber.find('a');
            }
            var likesCount = parseInt(targetLineNumber.text()) + parseInt(data.increment)
            if (likesCount < 1) {
                el.closest('.foot-left').find('.nf-post-reacts').hide()
            }
            else {
                targetLineNumber.text(likesCount);
                el.closest('.foot-left').find('.nf-post-reacts').html(data.reacts).show()
            }

            // if(data.hasOwnProperty('emoji')){

            likeContainer.find('.fn-reaction').html(data.view);
            // }
            if (data.hasOwnProperty('likeThis')) {
                if (el.closest('.comments-like').length)
                    el.addClass('active');
                else {
                    likeContainer.find('.fn-netframe-like.nf-btn').addClass('active');
                }
            } else {
                if (el.closest('.comments-like').length)
                    el.removeClass('active');
                else
                    likeContainer.find('.fn-netframe-like.nf-btn').removeClass('active');
            }

        }).fail(function (xhr) {
            //console.log(xhr.responseText);
        });

    });

    //For comment
    $(document).on('focus', 'textarea.comment-content', function(){
        var el = $(this);
        el.closest('.mycomment').addClass('active');
        if(!$(this).hasClass('in-mention')){
            $(this).addClass('in-mention');
            $(this).mentionsInput({ source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels' });
            $(this).focus();
        }
    })
    var commentIsPosting = false;
    //$(document).on('click', '.fn-netframe-comment', function (e) {
    $(document).on('submit', 'form.fn-comment-form', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if(!commentIsPosting){
            commentIsPosting = true;
            var el = $(this);
            var container = $(this);
            //var container = el.closest('.fn-comment-form');

            var data = container.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            // add data to object array serialized json
            // formData.push({
            //     name: "httpReferer",
            //     value: requestUrl
            // });
            // data = Object.assign({}, data);
            // var dataForeign = el.data('content');
            var jqXhr = $.post("/netframe/comment-publish", data);

            jqXhr.done(function (data) {
                el.closest('.panel-comments-wrapper').find('.comments-list').append(data.viewComment)
                container.find('textarea').val('')
                container.closest('.mycomment').removeClass('active')
                commentIsPosting = false;
            });
        }
    });

    $(document).on('click', '.fn-revive-validation', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var el = $(this);
        var data = el.data();
        var jqXhr = $.post("/tasks/revive", data);

        jqXhr.done(function (data) {
            alert('Relance envoyée!')
        });
    });


    var delayLike = 500, setTimeoutConst;

    $(document).on('mouseenter', '.panel-like', function () {
        var $this = $(this)
        setTimeoutConst = setTimeout(function () {
            $this.find('.fn-like-reactions').addClass("active");
        }, delayLike);
    }).on('mouseleave', '.panel-like', function () {
        $(this).find('.fn-like-reactions').removeClass("active");
        clearTimeout(setTimeoutConst);
    });

    // --------------------- REPLY COMMENT
    var requestSended = false;

    $(document).on('click', '.fn-all-replies', function (e) {
        e.preventDefault();
        var newsFeedId = $(this).data('post-id');
        var commentId = $(this).data('comment-id');

        var formData = {
            id: newsFeedId,
            comment: commentId
        };

        var article = $(this).closest('article');
        var moreLink = $(this);

        requestSended = true;
        if (requestSended) {
            $.ajax({
                url: laroute.route('post.all.comments'),
                data: formData,
                type: "POST",
                success: function (data) {
                    if (typeof data.view != 'undefined') {
                        article.find('.panel-comments-wrapper .comments-list .comment-' + commentId).after(data.view);
                    }
                    moreLink.hide();
                    requestSended = false;
                },
                error: function (textStatus, errorThrown) {
                    requestSended = false;
                }
            });
        }
    });


    // --------------------- ADD FRIEND PROFILE USER FUNCTION
    $(document).on('click', '.fn-add-friend', function (e) {
        e.preventDefault();
        var el = $(this);
        var targetAddWay = el.find('.btn-txt');
        var actionTypeButton = null;

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

        jqXhr.done(function (data) {
            if (data.hasOwnProperty('addThis') || data.hasOwnProperty('ReAddThis')) {
            }
            else if (data.hasOwnProperty('unlockedThis')) {
                //el.removeClass('btn-danger').addClass('btn-default');
            }
            else if (data.hasOwnProperty('suppThis')) {
                el.removeClass('friends-ok');
            }

            el.find(targetAddWay).text(data.displayText);

            //manage icon
            if(typeof data.suppThis != 'undefined' && data.suppThis){
                el.find('.svgicon.icon-plus').removeClass('d-none');
                el.find('.svgicon.icon-check').addClass('d-none');
            }
            else if(typeof data.addThis != 'undefined'){
                el.find('.svgicon.icon-check').removeClass('d-none');
                el.find('.svgicon.icon-plus').addClass('d-none');
            }
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

            jqXhr.done(function (data) {
                var elTarget = el.closest('li.nf-list-setting');
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
            var containerElement = el.closest(".nf-list-setting");
            var param = {
                action: action
            };
            var jqXhr = $.post(laroute.route(route, param), {
                postData: user
            });

            jqXhr.done(function (data) {
                if (user) {
                    containerElement.fadeOut('slow', function () {
                        $(this).remove();
                    });
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
        }).done(function (data) {
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
            jqXhr.done(function (data) {
                // replace button with json view
                if (typeof data.removeElement != 'undefined') {
                    $(data.removeElement).fadeOut('slow', function () {
                        $(this).remove();
                    });
                }
                el.fadeOut('slow', function () {
                    el.replaceWith(data.viewContent);
                    el.fadeIn('slow');
                });
            });
        }
    });

    // --------------------- LIKE BUTTON PROFILE FUNCTION
    $(document).on("click", '.fn-like-profile', function (e) {
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
                    var elContainer = el.closest('div.profile-like');
                    var targetLineNumber = elContainer.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                    if (data.subscrib) {
                        el.closest('aside').find('.button-subscribe').addClass('status-subscribed show-leave counter');
                        var targetLineNumber = el.closest('aside').find('.button-subscribe .num');
                        targetLineNumber.text(parseInt(targetLineNumber
                            .text())
                            + 1);

                        if (el.closest('aside').find('.button-subscribe .num').val() == "0") {
                            el.closest('aside').find('.button-subscribe .num').addClass('d-none');
                        }
                        else {
                            el.closest('aside').find('.button-subscribe .num').removeClass('d-none');
                        }
                    }
                    elContainer.addClass('active');

                } else if (data.hasOwnProperty('liked')) {
                    var elContainer = el.closest('div.profile-like');
                    var targetLineNumber = elContainer.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                    // Change status button in On just like
                    elContainer.addClass('active');

                } else if (data.hasOwnProperty('unlike')) {
                    // Change status button like in Off
                    var elContainer = el.closest('div.profile-like');
                    elContainer.removeClass('active');
                    var targetLineNumber = elContainer.find('.like-number');
                    targetLineNumber.text(parseInt(targetLineNumber
                        .text())
                        + parseInt(data.increment));
                }
            } else if (actionTypeProfile === 'subscrib') {
                if (data.hasOwnProperty('subscrib') && data.subscrib) {
                    // Change status button subscrib in On
                    var targetLineNumber = el.find('.num');
                    var newFollowers = parseInt(targetLineNumber.text()) + 1;
                    targetLineNumber.text(newFollowers);
                    if (newFollowers > 0) {
                        el.addClass('status-subscribed show-leave counter');
                        el.find('span.num').removeClass('d-none');
                    }
                    else {
                        el.addClass('status-subscribed show-leave');
                    }
                }
                else if (data.hasOwnProperty('unsubscrib')) {
                    // Change status button subscrib in Off
                    var targetLineNumber = el.find('.num');
                    var newFollowers = parseInt(targetLineNumber.text()) - 1;
                    targetLineNumber.text(newFollowers);

                    if (newFollowers == 0) {
                        el.removeClass('counter');
                        el.find('span.num').addClass('d-none');
                    }
                    el.removeClass('status-subscribed show-leave');
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
            .done(function (data) {
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
            .done(function (data) {
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

        jqXhr.done(function (data) {
            window.location.reload();
        }).error(function (xhr) {
            //console.log(xhr.responseText);
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
                    if (typeof data.target != 'undefined') {
                        window.open(data.redirect, targetOpen);
                    }
                    else{
                        window.location = data.redirect;
                    }

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
                    $('.panel-comments-wrapper', elTarget).removeClass('d-none');
                    if (data.edit == 'false') {
                        $(data.viewComment).appendTo($('.comments-list', elTarget)).hide().slideDown('normal');
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

    //----------------------------- SEE ALL COMMENS BUTTON
    $(document).on('click', '.fn-all-comments', function (e) {
        e.preventDefault();
        var newsFeedId = $(this).data('post-id');

        var formData = {
            id: newsFeedId
        };

        var article = $(this).closest('article');
        var moreLink = $(this).closest('div');

        $.ajax({
            url: laroute.route('post.all.comments'),
            data: formData,
            type: "POST",
            success: function (data) {
                if (typeof data.view != 'undefined') {
                    article.find('.panel-comments-wrapper .comments-list').html(data.view);
                    // moreLink.hide();
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
            .done(function (data) {
                //modify button
                var targetParticipants = el.closest('.panel-event-head').find('.nf-post-actions');
                var targetLineCounter = targetParticipants.find('.btn-txt');
                if (data.participate) {
                    targetLineCounter.text(parseInt(targetLineCounter.text()) + parseInt(data.increment));
                    targetParticipants.removeClass('d-none');
                    // switch buton txt
                    el.find('.btn-txt .fn-enter-participation').addClass('d-none');
                    el.find('.btn-txt .fn-leave-participation').removeClass('d-none');
                }
                else {
                    targetLineCounter.text(parseInt(targetLineCounter.text()) + parseInt(data.increment));
                    el.find('.btn-txt .fn-enter-participation').removeClass('d-none');
                    el.find('.btn-txt .fn-leave-participation').addClass('d-none');
                    if(parseInt(targetLineCounter.text()) == 0){
                        targetParticipants.addClass('d-none');
                    }
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

    // ================ EMOJIS
    if (mobile) {
        var style = $('<style>.emoji-keyboard { display: none !important; }</style>');
        $('html > head').append(style);
    }

    $(document).on('click', '.fn-display-emojis-panel', function (e) {
        e.preventDefault();
        var currentEmoji = $(this).closest('.emoji-keyboard');
        var emojiPanel = currentEmoji.find('.emojis-panel');
        emojiPanel.toggle();
    });

    $(document).on('click', 'body', function (e) {
        if ($('.emojis-panel').is(':visible')) {
            $('.emojis-panel').toggle();
        }
    });

    $(document).on('click', '.fn-add-unicode', function (e) {
        e.preventDefault();
        var target = $(this).closest('ul').data('target');

        // detect cursor position and spli text before and after
        var cursorPos = $(target).prop('selectionStart');
        var v = $(target).val();
        var textBefore = v.substring(0, cursorPos);
        var textAfter = v.substring(cursorPos, v.length);

        // insert emoji
        $(target).val(textBefore + $(this).data('unicode') + textAfter);
        var newPosition = textBefore.length + $(this).data('unicode').length;

        // update cursor position
        $(target).caretTo(newPosition);
        $(target).trigger("change");

        // close emoji keyboard
        var currentEmoji = $(this).closest('.emoji-keyboard');
        var emojiPanel = currentEmoji.find('.emojis-panel');
        emojiPanel.toggle();
    });

    $(document).on('click', '.emoji-keyboard a', function (e) {
        e.stopPropagation();
    });

    // ================ XPLORER
    $(document).on('click', '.fn-copy-media-link', function (e) {
        e.preventDefault();

        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).data('clipboard-content')).select();
        document.execCommand("copy");
        $temp.remove();
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

    // copy link function
    // need into link hidden input with class "link-href"
    $(document).on('click', '.fn-copy-media-link', function(e){
        e.preventDefault();
        const el = document.createElement('textarea');
        el.value = $(this).data('href');
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    });
    
    // rights management
    $(document).on('click', '.fn-right-management a:not(".fn-change-member,.fn-resend-invitation")', function(e){
        var el = $(this)
        var params = el.data();
        var frominvite = el.closest('.fn-right-management').data('invite');
        params.fromInvite = frominvite;

        var parent = el.parent().parent().parent()
        var route = laroute.route('join.change.rights');
        var jqXhr = $.post(route, params);
        jqXhr.success(function(data) {
            var targetReplace = $(el.closest('.fn-right-management').data('target-return'));
            targetReplace.fadeOut('slow', function () {
                targetReplace.replaceWith(data.view);
                targetReplace.fadeIn('slow');
            });
        });
    });

    //send gmt to server
    /*
    gmtOffset = new Date().getTimezoneOffset();
    $.ajax({
            url: "/netframe/set-gmt",
            data: {
                gmt: gmtOffset,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            },
            type: "POST",
            success: function( data ){
            }
        });
    */
})(jQuery);
//# sourceMappingURL=app.js.map

//# sourceMappingURL=app.js.map
