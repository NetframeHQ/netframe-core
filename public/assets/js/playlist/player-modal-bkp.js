(function () {

    var MediaTypes = {
        IMAGE: 0,
        VIDEO: 1,
        AUDIO: 2,
        DOCUMENT: 3,
        ARCHIVE: 4
    };

    var MediaPlatforms = {
        LOCAL: 'local',
        YOUTUBE: 'youtube',
        DAILYMOTION: 'dailymotion',
        VIMEO: 'vimeo'
    };

    function PlayerModal(options) {
        var that = this;
        this.$modal = options.$modal;
        this.$backwardButton = options.$backwardButton;
        this.$forwardButton = options.$forwardButton;
        this.$mediaTitle = options.$mediaTitle;
        this.$playerWrapper = options.$playerWrapper;
        this.baseUrl = options.baseUrl;

        this.allMedias = [];
        this.currentMedia = {};
        this.mediaPos = 0;

        this.initializeMedias();
        this.initializeButtons();

        this.$modal.on('shown.bs.modal', function () {
            that.computeButtons();
            that.playCurrentMedia();
        });
		
		this.$modal.on('hidden.bs.modal', function () {
            that.$playerWrapper.html('');
        });
    }

    PlayerModal.prototype.initializeMedias = function () {
        var that = this;

        $('.playlistItem[data-media-id]').each(function () {
            var selector = $(this);

            that.allMedias.push({
                id: selector.data('media-id'),
                title: selector.data('media-title'),
                type: selector.data('media-type'),
                platform: selector.data('media-platform'),
                file_name: selector.data('media-file-name'),
                mime_type: selector.data('media-mime-type')
            });
        });

        if (that.allMedias.length > 0) {
            that.currentMedia = that.allMedias[0];
        }
    };

    PlayerModal.prototype.initializeButtons = function () {
        var that = this;

        this.$backwardButton.click(function () {
            this.previous();
        }.bind(this));

        this.$forwardButton.click(function () {
            this.next();
        }.bind(this));
    };

    PlayerModal.prototype.previous = function () {
        if (this.allMedias[this.mediaPos - 1]) {
            this.mediaPos -= 1;
        } else {
            this.mediaPos = this.allMedias.length - 1;
        }

        this.currentMedia = this.allMedias[this.mediaPos];
        this.playCurrentMedia();
    };

    PlayerModal.prototype.next = function () {
        if (this.allMedias[this.mediaPos + 1]) {
            this.mediaPos += 1;
        } else {
            this.mediaPos = 0;
        }

        this.currentMedia = this.allMedias[this.mediaPos];
        this.playCurrentMedia();
    };

    PlayerModal.prototype.computeButtons = function () {
        if (this.allMedias.length === 0) {
            this.$backwardButton.hide();
            this.$forwardButton.hide();
        }
    };

    PlayerModal.prototype.playCurrentMedia = function () {
        var that = this;
        var src = this.baseUrl + laroute.route('media_download', {id: this.currentMedia.id});

        this.$mediaTitle.text(this.currentMedia.title);

        // Audio
        if (this.currentMedia.type === MediaTypes.AUDIO) {
            this.$playerWrapper.html('<audio src="' + src + '" />');
            var element = document.getElementsByTagName('audio')[0];
            audiojs.create(element);
        }

        // Image
        else if (this.currentMedia.type === MediaTypes.IMAGE) {
            that.$playerWrapper.html('<img class="img-responsive" width="100%" src="' + src + '" >');
        }

        // Document & Archive
        else if (this.currentMedia.type === MediaTypes.DOCUMENT || this.currentMedia.type === MediaTypes.ARCHIVE) {
            that.$playerWrapper.html(
                '<div class="text-center">' +
                    '<a class="btn btn-lg btn-default" href="' + src + '"><span class="glyphicon glyphicon-download"></span> Download</a>' +
                '</div>'
            );
        }

        // Vieojs
        else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.LOCAL) {
            that.$playerWrapper.html(
                '<video id="playlistVideoId" class="video-js vjs-default-skin" controls preload="auto" width="100%">' +
                    '<source src="' + src + '" type="' + this.currentMedia.mime_type + '" />' +
                '</video>'
            );
        }

        // Youtube
        else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.YOUTUBE) {
            that.$playerWrapper.html('<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/' + this.currentMedia.file_name +
            '" width="100%" height="280px" frameborder="0" allowfullscreen></iframe>');
        }

        // Dailymotion
        else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.DAILYMOTION) {
            that.$playerWrapper.html('<iframe src="http://www.dailymotion.com/embed/video/' + this.currentMedia.file_name +
            '?api=true" width="100%" height="280px" frameborder="0"></iframe>');
        }

        // Vimeo
        else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.VIMEO) {
            that.$playerWrapper.html('<iframe src="//player.vimeo.com/video/' + this.currentMedia.file_name
            + '" width="100%" height="280px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
        }
        
        // Soundcloud
        else if (mediaType === MediaTypes.TYPE_AUDIO && mediaPlatform === MediaPlatforms.SOUNDCLOUD) {
            that.$playerWrapper.html('<iframe width="100%" height="350px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' + this.currentMedia.file_name + '&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>');
        }
    };

    // Publish the object
    window.PlayerModal = function (options) {
        new PlayerModal(options);
    };

})();
