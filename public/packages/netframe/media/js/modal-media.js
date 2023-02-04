/**
 * Created by kevin on 23/02/2018.
 */

(function(){
    'use strict';

    var MediaTypes = {
        TYPE_IMAGE: 0,
        TYPE_VIDEO: 1,
        TYPE_AUDIO: 2,
        TYPE_DOCUMENT: 3,
        TYPE_ARCHIVE: 4,
        TYPE_APPLICATION: 5,
        TYPE_SCRIPT: 6,
        TYPE_OTHER: 7,
        TYPE_FONT: 8
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
        this.$arrayMedia = null;
        this.$mediaId = 0;
        this.$currentPos = 0;
        this.$directoryMode = options.$directoryMode || false;
    }

    PlayMediaModal.prototype.setupModal = function () {
        var that = this;
        this.$modal.on('hidden.bs.modal', function () {
            that.$modalContent.html('');
            that.$arrayMedia = null;
            that.$mediaId = 0;
        });

    };

    PlayMediaModal.prototype.slideModal = function (currentPos, len) {
        var divEmpty = '';

        if (currentPos === 0 && len > 1){
            var prevbutton = divEmpty;
            var nextbutton = '<a class="carousel-control-next" href="#modal-carousel" role="button" data-slide="next" id="nextBtnImg"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>';
        } else if ((len-1) === currentPos && len > 1) {
            var prevbutton = '<a class="carousel-control-prev" href="#modal-carousel" role="button" data-slide="prev" id="prevBtnImg"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>';
            var nextbutton = divEmpty;
        } else if (len === 1) {
            var prevbutton = divEmpty;
            var nextbutton = divEmpty;
        } else {
            var prevbutton = '<a class="carousel-control-prev" href="#modal-carousel" role="button" data-slide="prev" id="prevBtnImg"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>';
            var nextbutton = '<a class="carousel-control-next" href="#modal-carousel" role="button" data-slide="next" id="nextBtnImg"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>';
        }
        return [prevbutton, nextbutton];
    };

    /* COMMENTS MEDIA MODAL */
    PlayMediaModal.prototype.postSocialSharing = function (that, mediaId) {
        $.post(that.baseUrl + laroute.route('social.media', {mediaId: mediaId }))

            .success(function (dataJson) {
                that.$modal.find('.block-comment').length = 0;
                that.$modal.find('.modal-infos').html(dataJson.view);
                
                // get media action menu
                    
                var dataMedia = {
                    mediaId: mediaId
                }
                
                $.ajax({
                    type: "POST",
                    url: laroute.route('media.actions.menu'),
                    data: dataMedia,
                    success: function (dataMenu) {
                        // display action menu on image
                        that.$modal.find('.carousel-item').append(dataMenu.actionMenu);
                    }
                });

                //get more comments
                that.moreComments();

                //hide moreComments link
                $('#Media--'+that.$mediaId+' #link-more-comments-media-'+that.$mediaId).addClass(dataJson.removeMoreComments);
            });
        that.$modal.modal('show');
    };

    /* LINK MORE COMMENTS */
    PlayMediaModal.prototype.moreComments = function () {
        var that = this;

        that.$modal.on('click', '.fn-modal-more-comments-'+that.$mediaId, function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();

            var dateComment = $('#modal-comment-media-'+that.$mediaId+' ul li:first-child').data("date");

            $.ajax({
                type: "POST",
                url: laroute.route('more.comments'),
                data: {
                    dateComment: dateComment,
                    mediaId: that.$mediaId
                },

                success: function (dataJson) {
                    $('#modal-comment-media-'+that.$mediaId+' ul.comments-list').prepend(dataJson.view);
                    $('#Media--'+that.$mediaId+' #link-more-comments-media-'+that.$mediaId).addClass(dataJson.removeMoreComments);
                },

                error: function(){
                    alert("There was an error. Try again please !");
                }
            });
        });

    }
    /* END LINK MORE COMMENTS */

    PlayMediaModal.prototype.compileMediaView = function (media) {
        var that = this;

        var mediaName = media.name;
        var mediaId = media.id;
        var mediaType = media.type;
        var mediaPlatform = media.platform;
        var mediaMimeType = media.mime_type;
        var mediaFileName = media.file_name;

        var mediaView = '';
        var mediaTypeReturn = '';

        // check GDPR
        if(mediaPlatform !== 'local' && gdprConsent == 0) {
            mediaView = '<p style="color:#fff">'+gdprContentBlockedTxt+'</p>';
        }

        // Image
        else if (mediaType === MediaTypes.TYPE_IMAGE) {
            mediaView= '<img class="d-block" src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" />';
        }

        // Vieojs
        else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === 'local') {

            var videoSrc = that.baseUrl + laroute.route('media_download', {id : mediaId});
            var feedImage = that.baseUrl + laroute.route('media_download', {id : mediaId}) + '?feed=1';
            mediaTypeReturn = 'localVideo';

            mediaView = '<video autoplay class="video-js vjs-default-skin" controls preload="auto" data-setup=\'{"poster": "'+feedImage+'"}\' width="100%" height="100%">' +
            '<source src="' + videoSrc + '" type="' + mediaMimeType + '" />' +
            '</video>';
        }

        // Audiojs
        else if (mediaType === MediaTypes.TYPE_AUDIO && mediaPlatform === 'local') {
            mediaView = '<audio src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" preload="auto"  controls autoplay ></audio>';
            //audiojs.createAll();
        }

        // Youtube
        else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.YOUTUBE) {
            mediaView = '<iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/' + mediaFileName + '" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
        }

        // Dailymotion
        else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.DAILYMOTION) {
            mediaView = '<iframe src="http://www.dailymotion.com/embed/video/' + mediaFileName + '?api=true" width="100%" height="100%" frameborder="0"></iframe>';
        }

        // Vimeo
        else if (mediaType === MediaTypes.TYPE_VIDEO && mediaPlatform === MediaPlatforms.VIMEO) {
            mediaView = '<iframe src="//player.vimeo.com/video/' + mediaFileName + '" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }

        // Soundcloud
        else if (mediaType === MediaTypes.TYPE_AUDIO && mediaPlatform === MediaPlatforms.SOUNDCLOUD) {
            mediaView = '<iframe width="100%" height="100%" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' + mediaFileName + '&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>';
        }

        // documents to download
        else{
            mediaView = '<div class="document"><a href="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '"><img src="' + that.baseUrl +'/assets/img/icons/file.png"><br><br>'+mediaName+'</a></div>'
        }

        return [mediaView, mediaTypeReturn];
    };

    /* CLICK ON IMG TO OPEN MODAL MEDIA */
    PlayMediaModal.prototype.setupMediaClick = function () {
        var that = this;

        //this.$media.on('click', function (e) {
        $(document).on('click', '.viewMedia', function (e) {
            // add test for dragging medias in Xplorer
            var testDragLi = $(this).closest('li.draggable');
            if(testDragLi.length && testDragLi.hasClass('ui-draggable-dragging')){
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();

            var nbCommentsDisplay = 0;
            var el = $(this);
            var newsFeedId = el.closest('article').data('newsfeed-id');
            var mediaId = el.data('media-id');


            //var currentImg = '<img id="img'+mediaId+'" class="img-modal img-responsive" width="100%" src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" />';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if(that.$directoryMode){
                var container = $(this).closest('.xplorer-main-view');

                var data = {
                    mediaId: mediaId,
                    newsFeedId: newsFeedId,
                    nbCommentsDisplay: nbCommentsDisplay,
                    directoryMode: that.$directoryMode,
                    profileType: container.data('profile-type'),
                    profileId: container.data('profile-id'),
                    folderId: container.data('folder-id'),
                };
            }
            else{
                var data = {
                    mediaId: mediaId,
                    newsFeedId: newsFeedId,
                    nbCommentsDisplay: nbCommentsDisplay,
                    directoryMode: that.$directoryMode
                };
            }

            $.ajax({
                type: "POST",
                url: laroute.route('modal.media.player'),
                data: data,

                success: function (data) {
                    that.$arrayMedia = data.allMedias;
                    that.$mediaId = data.mediaId;
                    that.$currentPos = data.currentPos;

                    var len = that.$arrayMedia.length;
                    var btnArr = that.slideModal(that.$currentPos, len);
                    var prevbutton = btnArr[0];
                    var nextbutton = btnArr[1];

                    var currentImg = that.compileMediaView(that.$arrayMedia[that.$currentPos]);

                    that.$modalContent.html(currentImg[0]+prevbutton+nextbutton);

                    // CLICK BTN PREV AND NEXT GALLERY
                    $(document).on('click', '#prevBtnImg, #nextBtnImg', function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        e.stopPropagation();

                        that.$modalContent.html('');

                        if ($(this).attr('id') === 'prevBtnImg'){
                            that.$currentPos--;
                            that.$mediaId--;

                            var len = that.$arrayMedia.length;
                            var btnArr = that.slideModal(that.$currentPos, len);
                            var prevbutton = btnArr[0];
                            var nextbutton = btnArr[1];
                            var mediaId = that.$arrayMedia[that.$currentPos].id;

                            var currentImg = that.compileMediaView(that.$arrayMedia[that.$currentPos]);

                            //var currentImg = '<img id="img'+mediaId+'" class="img-modal img-responsive" width="100%" src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" />';

                            that.$modalContent.html(currentImg[0]+prevbutton+nextbutton+'');

                            //get social sharing links for this media
                            that.postSocialSharing(that, mediaId);
                        }

                        if ($(this).attr('id') === 'nextBtnImg'){
                            that.$currentPos++;
                            that.$mediaId++;

                            var len = that.$arrayMedia.length;
                            var btnArr = that.slideModal(that.$currentPos, len);
                            var prevbutton = btnArr[0];
                            var nextbutton = btnArr[1];
                            var mediaId = that.$arrayMedia[that.$currentPos].id;

                            var currentImg = that.compileMediaView(that.$arrayMedia[that.$currentPos]);

                            //var currentImg = '<img id="img'+mediaId+'" class="img-modal img-responsive" width="100%" src="' + that.baseUrl + laroute.route('media_download', {id: mediaId}) + '" />';

                            that.$modalContent.html(currentImg[0]+prevbutton+nextbutton);

                            //get social sharing links for this media
                            that.postSocialSharing(that, mediaId);
                        }

                        if(currentImg[1] == 'localVideo'){
                            videojs(document.getElementsByClassName("video-js")[0], {}, function(){});
                        }
                    });
                    /* END CLICK ON BTN PREV/NEXT */

                    //get social sharing links for this media
                    that.postSocialSharing(that, mediaId);
                },

                error: function(){
                    alert("There was an error. Try again please !");
                }

            });

        });

    };

    window.PlayMediaModal = function (options) {
        var playMediaModal = new PlayMediaModal(options);
        $(document).ready(function () {
            playMediaModal.setupModal();
            playMediaModal.setupMediaClick();
        });

        return playMediaModal;
    }

})();
