(function () {

    var MediaTypes = {
        TYPE_IMAGE: 0,
        TYPE_VIDEO: 1,
        TYPE_AUDIO: 2,
        TYPE_DOCUMENT: 3,
        TYPE_ARCHIVE: 4
    };

    var MediaPlatforms = {
        YOUTUBE: 'youtube',
        DAILYMOTION: 'dailymotion',
        VIMEO: 'vimeo',
        SOUNDCLOUD: 'soundcloud',
    };

    function PlayMediaModal(options) {
        this.$modal = options.$modal;
        this.$modalTitle = options.$modalTitle;
        this.$modalContent = options.$modalContent;
        this.$media = options.$media;
        this.baseUrl = options.baseUrl;
    }

    PlayMediaModal.prototype.setupModal = function () {
        var that = this;
        this.$modal.on('hidden.bs.modal', function () {
            that.$modalContent.html('');
        });
    };

    PlayMediaModal.prototype.setupMediaClick = function () {
        var that = this;

        this.$media.on('click', function (e) {
            e.preventDefault();

            var el = $(this);
            var mediaName = el.data('media-name');
            var mediaId = el.data('media-id');
            var mediaType = el.data('media-type');
            var mediaPlatform = el.data('media-platform');
            var mediaMimeType = el.data('media-mime-type');
            var mediaFileName = el.data('media-file-name');
            
            // Image
            if (mediaType === MediaTypes.TYPE_IMAGE) {
                mediaName = '&nbsp;';
                that.$modalContent.html('<img class="img-responsive" width="100%" src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" />');
            }

            // Vieojs
            else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === 'local') {
                mediaName = '&nbsp;';
                
                var videoSrc = that.baseUrl + laroute.route('media_download', {id : mediaId});
                var feedImage = that.baseUrl + laroute.route('media_download', {id : mediaId}) + '?feed=1';
                
                that.$modalContent.html('<video class="video-js vjs-default-skin" controls preload="auto" data-setup=\'{"poster": "'+feedImage+'"}\' width="100%" height="300px">' +
                '<source src="' + videoSrc + '" type="' + mediaMimeType + '" />' +
                '</video>');
                
                videojs(document.getElementsByClassName("video-js")[0], {}, function(){
                  // Player (this) is initialized and ready.
                  (this).watermark({
                        file: '/assets/img/watermarks.png',
                        xpos: 0,
                        ypos: 0,
                        xrepeat: 0,
                        opacity: 0.9,
                    });
            });
            }

            // Audiojs
            else if (mediaType === MediaTypes.TYPE_AUDIO && mediaPlatform === 'local') {
                mediaName = '&nbsp;';
                that.$modalContent.html('<audio src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" preload="auto"  controls autoplay ></audio>');
                //audiojs.createAll();
            }

            // Youtube
            else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.YOUTUBE) {
                that.$modalContent.html(mediaFileName+'<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/' + mediaFileName + '" width="100%" height="400px" frameborder="0" allowfullscreen></iframe>');
            }

            // Dailymotion
            else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.DAILYMOTION) {
                that.$modalContent.html('<iframe src="http://www.dailymotion.com/embed/video/' + mediaFileName + '?api=true" width="100%" height="400px" frameborder="0"></iframe>');
            }

            // Vimeo
            else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.VIMEO) {
                that.$modalContent.html('<iframe src="//player.vimeo.com/video/' + mediaFileName + '" width="100%" height="400px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
            }

            // Soundcloud
            else if (mediaType === MediaTypes.TYPE_AUDIO && mediaPlatform === MediaPlatforms.SOUNDCLOUD) {
                that.$modalContent.html('<iframe width="100%" height="350px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' + mediaFileName + '&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>');
            }
            
            //get social sharing links for this media
			$.post(that.baseUrl + laroute.route('social.media', {mediaId: mediaId }))
            .success(function (data) {
                that.$modal.find('.fn-social-media').html(data);
            });
			
            //that.$modalTitle.html(mediaName);
            that.$modal.modal('show');
        });
    };

    window.PlayMediaModal = function (options) {
        var playMediaModal = new PlayMediaModal(options);

        $(document).ready(function () {
            playMediaModal.setupModal();
            playMediaModal.setupMediaClick();
        });
    }

})();
