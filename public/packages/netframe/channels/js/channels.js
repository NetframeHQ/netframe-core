/*
 * in feed message management
 */

(function () {
    'use strict';

    function ChannelSystem(options) {
        this.$wrapper = options.$wrapper || $('channels');
        this.$channelId = options.$channelId || 0;
    }

    // modify current channel id
    ChannelSystem.prototype.changeId = function (channelId) {
        console.log('change ID');
        this.$channelId = channelId;
    };

    // load feeds list for left menu
    ChannelSystem.prototype.loadChannels = function () {
        var that = this;
        var totalUnread = 0;

        $.ajax({
            url: laroute.route('channels.feeds'),
            type: "GET",
            success: function (data) {
                if (typeof data.returnCode != 'undefined') {
                    if (data.returnCode == 'success') {
                        //subscribe to channels
                        for (var i = 0; i < data.channels.length; i++) {
                            var obj = data.channels[i];

                            // implement unread counter
                            if (typeof obj.newsfeed[0] != 'undefined') {
                                var channelUnreaded = obj.newsfeed.length;
                                $('channels .fn-menu feeds li#channel-' + obj.id + ' .badge-notif').html(channelUnreaded);
                                $('channels .fn-menu feeds li#channel-' + obj.id + ' a').addClass('notif-active');
                                $('channels .fn-menu feeds li#channel-' + obj.id + ' .notif-ctn').removeClass('d-none');
                                $('channels .fn-menu feeds li#channel-' + obj.id).closest('ul').prepend($('channels .fn-menu feeds li#channel-' + obj.id));
                                $('channels .fn-menu feeds li#channel-' + obj.id).removeClass('d-none');

                                totalUnread = totalUnread + channelUnreaded;
                            }

                            // subscribe channel notifications
                            that.subscribeUnit(obj.id);

                        }
                        that.badgeTotalUnread(totalUnread, 'all');
                    }
                    else if (data.returnCode == 'error') {
                        // replace form with error containing form

                    }
                }
            }
        });
    };

    ChannelSystem.prototype.subscribeUnit = function (channelId) {
        var that = this;

        Echo.private('Channel-' + channelId)
            .listen('PostChannel', function (e) {
                if (typeof e.channelId != 'undefined' && e.channelId != that.$channelId) {
                    that.getUnread(e.channelId);
                }
            })
            .listen('DeleteChannelPost', function (e) {
                if (typeof e.channelId != 'undefined' && e.channelId == that.$channelId) {
                    //delete post
                    var el = $('#' + e.deletedTarget);
                    el.fadeOut('slow', function () {
                        $(this).remove();
                    });
                }
                else {
                    //get unread message on channel
                    that.getUnread(e.channelId);
                }
            })
            .listen('LivechatChannel', function (e) {
                console.log(e);
            });;


    };

    /*
     * update total unread badge
     * action : all : total counter, add : increment
     */
    ChannelSystem.prototype.badgeTotalUnread = function (nbUnread, action) {
        if (action == 'all') {
            $('#navigation .channels-notifs').attr('data-nb', nbUnread);
            $('#navigation .channels-notifs').html(nbUnread);
        }
        else if (action == 'add') {
            var totalUnread = 0;
            $('channels feeds li .badge-notif').each(function (e) {
                var currentUnread = $(this).html();
                if (currentUnread != '') {
                    totalUnread = totalUnread + parseInt(currentUnread);
                }
            });
            $('#navigation .channels-notifs').attr('data-nb', totalUnread);
            $('#navigation .channels-notifs').html(totalUnread);
        }
    };

    //get unread Counter
    ChannelSystem.prototype.getUnread = function (channelId) {
        var that = this;

        var url = laroute.route('channels.unread', { id: channelId });
        $.ajax({
            url: url,
            type: "POST",
            success: function (data) {
                if (typeof data.returnCode != 'undefined') {
                    if (data.returnCode == 'success') {
                        $('channels .fn-menu feeds li#channel-' + data.channelId + ' .badge-notif').html(data.unread);
                        if(data.unread != 0){
                            $('channels .fn-menu feeds li#channel-' + data.channelId + ' a').addClass('notif-active');
                            $('channels .fn-menu feeds li#channel-' + data.channelId + ' .notif-ctn').removeClass('d-none');
                        }
                        else{
                            $('channels .fn-menu feeds li#channel-' + data.channelId + ' a').removeClass('notif-active');
                            $('channels .fn-menu feeds li#channel-' + data.channelId + ' .notif-ctn').addClass('d-none');
                        }
                        that.badgeTotalUnread(data.unread, 'add');
                        $('channels .fn-menu feeds li#channel-' + data.channelId).closest('ul').prepend($('channels .fn-menu feeds li#channel-' + data.channelId));
                        $('channels .fn-menu feeds li#channel-' + data.channelId).removeClass('d-none');
                     }
                    else if (data.returnCode == 'error') {
                        // replace form with error containing form

                    }
                }
            }
        });
    };

    window.ChannelSystem = function (options) {
        var channel = new ChannelSystem(options);
        channel.loadChannels();

        if (typeof channel.$psContainer != 'undefined') {
            channel.listenEvent();
            channel.liveUrl('#form-post-content');
        }

        return channel;
    };
})();

(function ($) {
    //search contacts
    $('channels').on('keyup', '#fn-search-contact', function (e) {
        var input = $(this).val();
        if (input.length > 2) {
            // start contacts and users search

            $.ajax({
                url: laroute.route('channels.contacts.search'),
                type: "POST",
                data: {
                    query: input
                },
                success: function (data) {
                    // display div under search with user result list
                    var source = $('#template-channels-search-users').html();
                    var template = Handlebars.compile(source);
                    var html = template({ users: data.users });
                    $("channels #display-users-results").html(html);
                }
            });
        }
    });
})(jQuery);
