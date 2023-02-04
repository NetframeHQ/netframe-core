(function () {
    'use strict';

    function SelectModal(options) {
        this.baseUrl = options.baseUrl;
        this.$modal = options.$modal || $('#selectMediaModal');
        this.$pager = options.$pager || $('#pager-wrapper');
        this.$pageContent = options.$pageContent || $('#content-wrapper');
        this.$mediaTemplate = options.$mediaTemplate || $('#mediaTemplate');
        this.$input = options.$input;
        this.$thumbPreview = options.$thumbPreview;
        this.template = null;

        var that = this;
        var inputVal = this.$input.val();

        // Initialize the selected medias
        if ($.isNumeric(inputVal)) {
            this.selectedMediaIds = [inputVal];
        } else if (inputVal && inputVal.indexOf(',') > -1) {
            this.selectedMediaIds = inputVal.split(',');
        } else {
            this.selectedMediaIds = [];
        }

        // When the modal is shown we fetch and display the medias
        this.$modal.on('shown.bs.modal', function () {

            that.fetchMedias().success(function (response) {
                that.$pager.bootpag({
                    total: response.last_page
                })

                // When the page is shown we fetch and display the medias
                .on('page', function(event, pageNumber) {
                    that.fetchMedias(pageNumber).success(function (response) {
                        that.fillPageContent(response.data);
                        that.updateSelectedMediaClasses();
                    })
                });

                that.fillPageContent(response.data);
                that.updateSelectedMediaClasses();
            })
        });

        this.$pageContent.on('click', '.selectThumb', function() {
            that.mediaClicked($(this));
        });
    }

    /**
     * Update the classes of the selected medias.
     */
    SelectModal.prototype.updateSelectedMediaClasses = function () {
        for (var i = 0; i < this.selectedMediaIds.length; i++) {
            var mediaId = this.selectedMediaIds[i];
            this.$pageContent.find('[data-id="' + mediaId + '"]').addClass('selected');
        }
    };

    /**
     * Called when a media thumbnail is clicked.
     */
    SelectModal.prototype.mediaClicked = function ($mediaThumbnail) {
        var mediaId = $mediaThumbnail.attr('data-id');
        var mediaIndex = this.selectedMediaIds.indexOf(mediaId);
        var mediahtml = $mediaThumbnail.html();

        // Perform the selection
        if (mediaIndex > -1) {
            this.selectedMediaIds.splice(mediaIndex, 1);
            $mediaThumbnail.removeClass('selected');
            //remove thumb from parent
            $('#selectedMediasPreview').children("#"+mediaId).remove();
        } else {
            this.selectedMediaIds.push(mediaId);
            $mediaThumbnail.addClass('selected');
            //add thumb on parent
            $('#selectedMediasPreview').append('<div class="col-sm-3" id="'+mediaId+'">'+mediahtml+'</div>');
        }

        // Update the associated input
        this.$input.val(this.selectedMediaIds.join(','));
    };

    /**
     * Fills the content of the current page.
     */
    SelectModal.prototype.fillPageContent = function (medias) {
        var html = '';

        if (!this.template) {
            var source = this.$mediaTemplate.html();
            this.template = Handlebars.compile(source);
        }

        for (var i = 0; i < medias.length; i++) {
            var media = medias[i];

            media.name = truncateString(media.name);

            // For the local platform we build the url to download the media
            // For other platforms like youtube, the url is the file path
            if (media.platform === 'local') {
                media.link = this.baseUrl + laroute.route('media_download', {id: media.id});
            } else {
                media.link = media.file_path;
            }

            html += this.template({media: media});
        }

        this.$pageContent.html(html);

        audiojs.events.ready(function() {
            audiojs.createAll();
        });
    };

    /**
     * Fetches the media list for the given page and returns a promise.
     */
    SelectModal.prototype.fetchMedias = function (pageNumber) {
        var params = {};

        if (pageNumber) {
            params.page = pageNumber;
        }

        // We order by the selected medias first
        if (this.selectedMediaIds.length > 0) {
            params.ids = this.selectedMediaIds;
        }


        return $.get(this.baseUrl + laroute.route('media_json_list', params));
    };

    // Function used to truncate the media name
    function truncateString(string) {
        var length = 16;

        if (string.length <= length) {
            return string;
        }

        return string.substring(0, length - 3) + '...';
    }

    // Publish the object
    window.SelectModal = function (options) {
        new SelectModal(options);
    };

    // Handlebars helpers used by the modal
    Handlebars.registerHelper('if_eq', function(a, b, opts) {
        if (a == b) {
            return opts.fn(this);
        }

        return opts.inverse(this);
    });

    Handlebars.registerHelper('safe_html', function(value) {
        return new Handlebars.SafeString(value);
    });

})();
