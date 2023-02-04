var mediaTypes = 'all';

(function() {

    var MediaTypes = {
        IMAGE : 0,
        VIDEO : 1,
        AUDIO : 2,
        DOCUMENT : 3,
        ARCHIVE : 4
    };

    var MediaPlatforms = {
        LOCAL : 'local',
        YOUTUBE : 'youtube',
        DAILYMOTION : 'dailymotion',
        VIMEO : 'vimeo',
        SOUNDCLOUD : 'soundcloud'
    };
    
    var autoLoadImage = '';

    function PlayerModal(options) {
        var that = this;
        this.$modal = options.$modal;
        this.$backwardButton = options.$backwardButton;
        this.$forwardButton = options.$forwardButton;
        this.$mediaTitle = options.$mediaTitle;
        this.$playerWrapper = options.$playerWrapper;
        this.$mediaTypes = options.$mediaTypes;
        this.baseUrl = options.baseUrl;
		this.$media = options.$media;
		this.$inModal = options.$inModal || false;
        this.allMedias = [];
        this.currentMedia = {};
        this.mediaPos = 0;
        
        this.initializeMedias();
        this.initializeButtons();

        // this.$modal.on('shown.bs.modal', function () {
        that.computeButtons();
        if(this.$inModal){
        	that.setupMediaClick();
        	/*
        	that.$modal.on('shown.bs.modal', function () {
        		that.playCurrentMedia();
        	});
        	*/
        	this.$modal.on('hidden.bs.modal', function () {
          	that.$playerWrapper.html('');
          	clearTimeout(autoLoadImage);
        	});
        }
        else{
        	that.playCurrentMedia();
        }
        
        $(document).keydown(function(e) {
	        if(($('#sliderPlayer').is(':visible') || $('#playerList').is(':visible')) && !$('#modal-ajax-comment').is(':visible')){
                if (e.keyCode == '37') {
                   that.previous();
                   e.preventDefault();
                }
                else if (e.keyCode == '39') {
                   that.next();
                   e.preventDefault();
                }
            }
	    });
        
        // });
        return this;
    }
    
    PlayerModal.prototype.setMediaType = function(mediaType) {
        alert(mediaType);
    };
    
    PlayerModal.prototype.initializeMedias = function() {
        var that = this;

        $('.playlistItem[data-media-id]')
                .each(
                        function() {
                            var selector = $(this);

                            if (that.$mediaTypes == 'all' || selector.data('media-type') == that.$mediaTypes) {
                                that.allMedias.push({
                                    id : selector.data('media-id'),
                                    title : selector.data('media-title'),
                                    type : selector.data('media-type'),
                                    platform : selector.data('media-platform'),
                                    file_name : selector.data('media-file-name'),
                                    mime_type : selector.data('media-mime-type'),
                                    position : selector.data('media-position')
                                });
                            }
                        });

        if (that.allMedias.length > 0) {
            that.currentMedia = that.allMedias[0];
        }
    };

    PlayerModal.prototype.initializeButtons = function() {
        var that = this;

        this.$backwardButton.click(function() {
            this.previous();
        }.bind(this));

        this.$forwardButton.click(function() {
            this.next();
        }.bind(this));
    };

    PlayerModal.prototype.previous = function() {
        clearTimeout(autoLoadImage);
        if (this.allMedias[this.mediaPos - 1]) {
            this.mediaPos -= 1;
        } else {
            this.mediaPos = this.allMedias.length - 1;
        }

        this.currentMedia = this.allMedias[this.mediaPos];
        this.playCurrentMedia();
    };

    PlayerModal.prototype.next = function() {
        clearTimeout(autoLoadImage);
        if (this.allMedias[this.mediaPos + 1]) {
            this.mediaPos += 1;
        } else {
            this.mediaPos = 0;
        }
				this.currentMedia = this.allMedias[this.mediaPos];
        this.playCurrentMedia();
    };

    PlayerModal.prototype.computeButtons = function() {
        if (this.allMedias.length === 0) {
            this.$backwardButton.hide();
            this.$forwardButton.hide();
        }
    };
    
    PlayerModal.prototype.setupMediaClick = function () {
        var that = this;
        this.$media.on('click', function (e) {
            e.preventDefault();
            that.mediaPos = $(this).data('media-position');
    				that.currentMedia = that.allMedias[that.mediaPos];
    				that.$modal.modal('show');
    				that.playCurrentMedia();
    		});
    }

    PlayerModal.prototype.playCurrentMedia = function() {
        var that = this;
        if(mediaTypes == 'all' || this.currentMedia.type == mediaTypes){
            var src = this.baseUrl + laroute.route('media_download', {id : this.currentMedia.id});
            var feedImage = this.baseUrl + laroute.route('media_download', {id : this.currentMedia.id}) + '?feed=1';
            
            //get social sharing links for this media
            $.post(that.baseUrl + laroute.route('social.media', {mediaId: this.currentMedia.id }))
            .success(function (data) {
                that.$modal.find('.fn-social-media').html(data);
            });
            
            //this.$mediaTitle.text(this.currentMedia.title);

            // Audio
            if (this.currentMedia.type === MediaTypes.AUDIO
                    && this.currentMedia.platform === MediaPlatforms.LOCAL) {
                this.autoplay = true;
                this.$playerWrapper.html('<p class="text-center"><audio id="audioPlayer" src="'+ src + '" controls autoplay /></p>');
                $("#audioPlayer").play();
                $("#audioPlayer").bind('ended', function() {
                    this.next();
                }.bind(this));
            }

            // Image
            else if (this.currentMedia.type === MediaTypes.IMAGE && this.currentMedia.platform === MediaPlatforms.LOCAL) {
                this.autoplay = true;
                that.$playerWrapper.html('<img class="img-responsive" src="' + src
                        + '" >');
                autoLoadImage = setTimeout(function() {
                    this.next();
                }.bind(this), 5000);
            }

            // Document & Archive
            else if (this.currentMedia.type === MediaTypes.DOCUMENT
                    || this.currentMedia.type === MediaTypes.ARCHIVE
                    && this.currentMedia.platform === MediaPlatforms.LOCAL) {
                this.autoplay = true;
                that.$playerWrapper
                        .html('<div class="text-center">'
                                + '<a class="btn btn-lg btn-default" href="'
                                + src
                                + '"><span class="glyphicon glyphicon-download"></span> Download</a>'
                                + '</div>');
            }

            // Videojs
            else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.LOCAL) {
                this.autoplay = true;
                that.$playerWrapper.html('<video id="playlistVideoId" class="video-js vjs-default-skin" controls preload="auto" width="100%" height="300px" data-setup=\'{ "poster": "'+feedImage+'" }\'>'+ '<source src="' + src + '" type="' + this.currentMedia.mime_type + '" />' + '</video>');
                videojs(document.getElementsByClassName("video-js")[0], {},
                        function() {
                            // Player (this) is initialized and ready.
                            (this).watermark({
                                file : '/assets/img/watermarks.png',
                                xpos : 0,
                                ypos : 0,
                                xrepeat : 0,
                                opacity : 0.9,
                            });
                            
                            (this).play();

                            (this).on('ended', function() {
                                that.next();
                            }.bind(that));
                        });
            }

            // Youtube
            else if (this.currentMedia.type === MediaTypes.VIDEO && this.currentMedia.platform === MediaPlatforms.YOUTUBE) {
                this.autoplay = true;
                that.$playerWrapper
                        .html('<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/'
                                + this.currentMedia.file_name
                                + '?enablejsapi=1&showinfo=0&rel=0" width="100%" height="280px" frameborder="0" allowfullscreen></iframe>');

                var playerYT = new YT.Player('ytplayer', {
                    events : {
                        'onStateChange' : function(event) {
                            if (event.data == YT.PlayerState.ENDED) {
                                that.next();
                            }
                        }.bind(that),
                        'onReady' : onPlayerReady,
                    }
                });

                function onPlayerReady(event) {
                    playerYT.playVideo();
                }
            }
            
            // Dailymotion
            else if (this.currentMedia.type === MediaTypes.VIDEO
                    && this.currentMedia.platform === MediaPlatforms.DAILYMOTION) {
                this.autoplay = true;
                // that.$playerWrapper.html('<iframe id="dailymotionPlayer"
                // src="http://www.dailymotion.com/embed/video/' +
                // this.currentMedia.file_name +
                // '?api=true&autoplay=1&id=dailymotionPlayer" width="100%"
                // height="280px" frameborder="0"></iframe>');
                that.$playerWrapper.html('<div id="dailymotionPlayer"></div>');
                DM.init({
                    apiKey : '476bf463a1b54e129e0a',
                    status : true, // check login status
                    cookie : true
                // enable cookies to allow the server to access the session
                });

                var videoId = that.currentMedia.file_name;
                var playerDm = DM.player('dailymotionPlayer', {
                    video : videoId,
                    width : '100%',
                    height : '400px',
                    params : {
                        html : 0,
                        wmode : 'opaque',
                        autoplay : true
                    }
                });

                playerDm.addEventListener("ended", function(e) {
                    that.next();
                }.bind(that));
            }

            // Vimeo
            else if (this.currentMedia.type === MediaTypes.VIDEO
                    && this.currentMedia.platform === MediaPlatforms.VIMEO) {
                this.autoplay = true;
                that.$playerWrapper
                        .html('<iframe id="vimeoplayer" src="//player.vimeo.com/video/'
                                + this.currentMedia.file_name
                                + '?api=1&autoplay=1&player_id=vimeoplayer" width="100%" height="280px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                var iframeVimeo = $('#vimeoplayer')[0];
                var playerVimeo = $(iframeVimeo);

                playerVimeo.addEvent('ready', function() {
                    playerVimeo.addEvent('finish', function(event) {
                        that.next();
                    }.bind(that));
                });
            }

            // Soundcloud
            else if (this.currentMedia.type === MediaTypes.AUDIO
                    && this.currentMedia.platform === MediaPlatforms.SOUNDCLOUD) {
                this.autoplay = true;
                that.$playerWrapper
                        .html('<iframe id="soundCloudPlayer" width="100%" height="350px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'
                                + this.currentMedia.file_name
                                + '&amp;auto_play=true&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>');
                var widgetIframe = document.getElementById('soundCloudPlayer'), widget = SC
                        .Widget(widgetIframe);
                widget.bind(SC.Widget.Events.READY, function() {
                    widget.bind(SC.Widget.Events.FINISH, function() {
                        // get information about currently playing sound
                        widget.getCurrentSound(function(currentSound) {
                            that.next();
                        }.bind(that));
                    });
                });

            }
        }
        else{
            that.next();
        }
    };

    // Publish the object
    window.PlayerModal = function(options) {
        new PlayerModal(options);
    };

})();
