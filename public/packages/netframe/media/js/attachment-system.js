(function () {
    'use strict';

    function AttachmentSystem(options) {
        this.$wrapper = options.$wrapper;
        this.$fileUpload = options.$fileUpload || $('#fileupload');
        this.$previewDomClass = options.$previewDomClass || '';
        this.$formHiddenDomClass = options.$formHiddenDomClass || '';
        this.$profileMedia = options.$profileMedia || '0';
        this.$profileCover = options.$profileCover || '0';
        this.$favoriteMedia = options.$favoriteMedia || '0';
        this.$fromFormTalent = options.$fromFormTalent || '0';
        this.$postMedia = options.$postMedia || '0';
        this.$fromXplorer = options.$fromXplorer || '0';
        this.$idFolder = options.$idFolder || '0';
        this.$mediaTemplateRender = options.$mediaTemplateRender || '.tl-posted-medias';
        this.$confidentiality = options.$confidentiality || $('input:radio[name=confidentiality]');
        this.$profileId = options.$profileId || '0';
        this.$profileType = options.$profileType || '';
        this.$route = options.$route || '';
        this.$specificField = options.$specificField || '';
        this.$mediaId = options.$mediaId || '';
        this.$checkExist = options.$checkExist || false;
        this.$displayElement = options.$displayElement || false;

        this.selectedProfile = {
            id: this.$profileId,
            type: this.$profileType
        };

        if(this.$route == ''){
            this.$route = this.$fileUpload.attr('action');
        }

    }

    AttachmentSystem.prototype.setupUploadForm = function () {
        var that = this;
        if(that.$profileMedia == 1 || that.$profileCover == 1 || that.$favoriteMedia == 1){
            this.$fileUpload.fileupload({
                url: that.$route,
                add: function(e, data) {
                    var totalSize = 0;
                    for(var file in data.originalFiles){
                        totalSize = totalSize + parseInt(data.originalFiles[file]['size']);
                    }
                    if(totalSize > 1048576000) {
                        alert('Max : 1 Go');
                    } else {
                        data.submitted = new Date().getTime();
                        that.$fileUpload.find(' #progress-files').removeClass('d-none');
                        data.submit();
                    }
                },
                autoUpload: true,
                singleFileUploads:true,
                progress: function(e,data){
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    that.$fileUpload.find('#progress-files .progress-bar').css( 'width', progress + '%');
                },
                done: function(){
                    that.$fileUpload.find(' #progress-files').addClass('d-none');
                }
            });
        }

        if(that.$postMedia == 1 || this.$fromXplorer == 1){
            this.$fileUpload.fileupload({
                filesContainer: that.$wrapper.find(that.$mediaTemplateRender),
                autoUpload: true
            });
        }

        this.$fileUpload.bind('fileuploadsubmit', function (e, data) {
            var replaceFile = false;
            var originalId = null;
            if(that.$fromXplorer == 1){
                // test if same file exists in folder
                $.each(data.files, function (index, file) {
                    var dataTest = {
                        filename: file.name,
                        idFolder: that.$idFolder,
                        profile: JSON.stringify(that.selectedProfile),
                    };

                    var fileExists = $.ajax({
                        type: "POST",
                        url: laroute.route('xplorer.file.in.folder'),
                        data: dataTest,
                        async: !1,
                        error: function() {
                            alert("Error occured")
                        }
                    });
                    var responseExists = fileExists.responseJSON;
                    if(responseExists.response == true){
                        var testDuplicate = confirm('Le fichier '+file.name+' existe déjà dans ce répertoire.\nVoulez vous le remplacer?');
                        if(testDuplicate){
                            replaceFile = true;
                            originalId = responseExists.originalId;
                        }
                        else{
                            originalId = responseExists.originalId;
                        }
                    }
                });
            }
            var confidentiality = that.$confidentiality.filter(':checked').val();
            data.formData = {
                specificField: that.$specificField,
                confidentiality: confidentiality,
                profile: JSON.stringify(that.selectedProfile),
                profileMedia: that.$profileMedia,
                profileCover: that.$profileCover,
                favoriteMedia: that.$favoriteMedia,
                postMedia: that.$postMedia,
                fromXplorer: that.$fromXplorer,
                idFolder: that.$idFolder,
                mediaId: that.$mediaId,
                replace: replaceFile,
                originalId: originalId
            };
        });

        if(that.$profileMedia == 1 || that.$profileCover == 1 || that.$favoriteMedia == 1){
            this.$fileUpload.bind('fileuploaddone', function (e, data) {

                if(data._response.result.files[0].type == 0){ //image
                    var mediaUrl = data._response.result.files[0].mediaUrl;
                    var mediaFullUrl = data._response.result.files[0].mediaFullUrl;
                }
                else if(data._response.result.files[0].type == 1){ //video
                    var mediaUrl = baseUrl+"/assets/img/icons/video.png";
                }
                else if(data._response.result.files[0].type == 2){ //audio
                    var mediaUrl = baseUrl+"/assets/img/icons/audio.png";
                }
                else if(data._response.result.files[0].type == 3){ //document
                    var mediaUrl = baseUrl+"/assets/img/icons/file.png";
                }
                else if(data._response.result.files[0].type == 4){ //archive
                    var mediaUrl = baseUrl+"/assets/img/icons/file.png";
                }

                // test if image or svg is displayed
                if(that.$profileMedia == 1){
                    if(that.$wrapper.find(that.$previewDomClass+' div.nf-thumbnail').length > 0){
                        that.$wrapper.find(that.$previewDomClass).find('div.nf-thumbnail').css('background-image','url('+mediaUrl+')');
                    }
                    else{
                        that.$wrapper.find(that.$previewDomClass+' span.svgicon').addClass('d-none');
                        var newProfileImage = '<span class="avatar profile-image"><div class="nf-thumbnail" style="background-image:url('+mediaUrl+')"></span></div>';
                        that.$wrapper.find('input[name="profileMediaId"]').val(data._response.result.files[0].mediaId);
                        that.$wrapper.find(that.$previewDomClass).prepend(newProfileImage);
                    }
                }

                if(that.$profileCover == 1){
                    // add id to hidden field, change css of cover, hide and show elements
                    that.$wrapper.find(that.$previewDomClass+' .cover-placeholder').addClass('d-none');
                    that.$wrapper.find(that.$previewDomClass+' #updel-cover').removeClass('d-none');
                    that.$wrapper.find(that.$previewDomClass).css('background-image','url('+mediaFullUrl+')');
                    that.$wrapper.find('input[name="coverMediaId"]').val(data._response.result.files[0].mediaId);
                }

                // manage auto show elements
                if(that.$displayElement != false){
                    $.each(that.$displayElement, function( index, value ) {
                        that.$wrapper.find(value).removeClass('d-none');
                    });
                }

                $('.choosePictureModal').modal('hide');

                that.$wrapper.find('#progress-files').addClass('d-none');
                that.$wrapper.find('#progress-files .progress-bar').css( 'width', '0%');
            });
        }
        else if(that.$postMedia == 1){
            this.$fileUpload.bind('fileuploaddone', function (e, data) {
                //make html response to display in post modal medias

                var inputVal = that.$wrapper.find("#postSelectedMediasId").val();

                // Initialize the selected medias
                if ($.isNumeric(inputVal)) {
                    this.selectedMediaIds = [inputVal];
                } else if (inputVal && inputVal.indexOf(',') > -1) {
                    this.selectedMediaIds = inputVal.split(',');
                } else {
                    this.selectedMediaIds = [];
                }

                for (var i = 0; i < data._response.result.files.length; i++) {
                    var media = data._response.result.files[i];
                    this.selectedMediaIds.push(media.id);
                }
                //add ids in hidden form field
                that.$wrapper.find("#postSelectedMediasId").val(this.selectedMediaIds.join(','));

                //fire event when attachment finished
                $(document).trigger('attachmentSystem:finish');
            });
        }
        else if(this.$fromXplorer == 1){
            this.$fileUpload.bind('fileuploadcompleted', function (e, data) {
                // implement tags select
                var tagsS = that.$wrapper.find('.fn-select2-tag');
                that.$wrapper.find('.fn-select2-tag').select2({
                    language: "fr",
                    minimumInputLength: 2,
                    multiple: true,
                    ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                        url: laroute.route('tags.autocomplete'),
                        dataType: 'json',
                        contentType: "application/json",
                        type: "POST",
                        data: function (params) {
                            return  JSON.stringify({
                                q: params.term
                            });
                        },
                        processResults: function (data, page) {
                            return data;
                        },
                    },
                    escapeMarkup: function (markup) { return markup; },
                });
            });
        }
    };

    AttachmentSystem.prototype.listenClickEvent = function () {
        var that = this;

        this.$wrapper.on('click', '.f-publish-as', function(e) {
            e.preventDefault();

            var el = $(this);
            var dataProfileType = el.data('profile');
            var dataProfileId = el.data('profile-id');
            that.selectedProfile = {
                id: dataProfileId,
                type: dataProfileType
            };
        });

        this.$wrapper.on('dragenter', 'input[type="file"]', function(e){
            that.$wrapper.find('.fileinput-button').addClass('dragover');
        });

        this.$wrapper.on('dragleave', 'input[type="file"]', function(e){
            that.$wrapper.find('.fileinput-button').removeClass('dragover');
        });


        this.$wrapper.on('click', '.fn-remove-media', function(e){
            e.preventDefault();
            var delMedia = $(this).closest('li');
            var mediaId = delMedia.data('file-id');

            var inputVal = that.$wrapper.find("#postSelectedMediasId").val();
            // Initialize the selected medias
            if ($.isNumeric(inputVal)) {
                this.selectedMediaIds = [inputVal];
            } else if (inputVal && inputVal.indexOf(',') > -1) {
                this.selectedMediaIds = inputVal.split(',');
            } else {
                this.selectedMediaIds = [];
            }

            var index = this.selectedMediaIds.indexOf(mediaId.toString());
            if(index != -1){
                this.selectedMediaIds.splice(index, 1);

                that.$wrapper.find("#postSelectedMediasId").val(this.selectedMediaIds.join(','));

                var mediasContainer = $(this).closest('ul');
                mediasContainer.find('li.file-'+mediaId).remove();
            }
        });
    };

    window.AttachmentSystem = function (options) {
        var modal = new AttachmentSystem(options);

        modal.setupUploadForm();
        modal.listenClickEvent();

        return modal;
    };

})();
