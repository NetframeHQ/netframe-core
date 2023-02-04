(function () {
    'use strict';

    function AttachmentModal(options) {
        this.$wrapper = options.$wrapper;
        this.$fileUpload = options.$fileUpload || $('#fileupload');
        this.$postAs = options.$postAs || 'publish-as-hidden-md';
        this.$importForm = options.$importForm || $('#import');
        this.$importHelp = options.$importHelp || $('#importUrl .help-block');
        this.$importUrlGroup = options.$importUrlGroup || $('#importUrl');
        this.$importUrl = options.$importUrl || $('input[name=url]');
        this.$previewDomClass = options.$previewDomClass || '';
        this.$formHiddenDomClass = options.$formHiddenDomClass || '';
        this.$profileMedia = options.$profileMedia || '0';
        this.$favoriteMedia = options.$favoriteMedia || '0';
        this.$fromFormTalent = options.$fromFormTalent || '0';
        this.$postMediaModal = options.$postMediaModal || '0';
        this.$mediaTemplate = options.$mediaTemplate || '';
        this.$autoUpload = options.$autoUpload || '0';
        this.$confidentiality = options.$confidentiality || $('input:radio[name=confidentiality]');
        this.$profileIdInput = this.$wrapper.find('#'+this.$postAs).find('input[name="id_foreign"]');
        this.$profileTypeInput = this.$wrapper.find('#'+this.$postAs).find('input[name="type_foreign"]');

        // JSON object in the form
        // {
        //   "type": "talent",
        //   "id": 3
        // }
        
        this.selectedProfile = {
            id: this.$profileIdInput.val(),
            type: this.$profileTypeInput.val()
        };
    }

    AttachmentModal.prototype.setupUploadForm = function () {
		var that = this;
        var autoUploadParam = true;
        this.$fileUpload.fileupload({
            add: function(e, data) {
                var totalSize = 0;
                for(var file in data.originalFiles){
                    totalSize = totalSize + parseInt(data.originalFiles[file]['size']);
                }
                if(totalSize > 524288000) {
                    alert('Max : 500 Mo');
                } else {
                    data.submitted = new Date().getTime();
					that.$wrapper.find('#progress-files').removeClass('d-none');
					data.submit();
                }
            },
            autoUpload: autoUploadParam,
            singleFileUploads:false,
            progress: function(e,data){
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$('#progress-files .progress-bar').css( 'width', progress + '%');
				
				/**
				* compute remaining upload time 
				**/
				var progressTime = data.loaded / data.total;
				var timeSpent = new Date().getTime() - data.submitted;
				var secondsRemaining = Math.round(((timeSpent / progressTime) - timeSpent) / 1000);
				var hours = Math.round(parseInt( secondsRemaining / 3600 ) % 24);
				var minutes = Math.round(parseInt( secondsRemaining / 60 ) % 60);
				var seconds = Math.round(secondsRemaining % 60);

				if(hours > 0 && minutes >= 0){
					var remaining = (hours < 10 ? "0" + hours : hours) +"h"+(minutes < 10 ? "0" + minutes : minutes);
				}
				else if(hours == 0 && minutes > 0){
					var remaining = (minutes < 10 ? "0" + minutes : minutes);
				}
				else{
					var remaining = "< 1 min.";
				}
				that.$wrapper.find('#progress-files .progress-time .remaining-time').html(remaining);
            }
        });
            
        this.$fileUpload.bind('fileuploadsubmit', function (e, data) {
            var confidentiality = that.$confidentiality.filter(':checked').val();
            data.formData = {
                confidentiality: confidentiality,
                profile: JSON.stringify(that.selectedProfile),
                profileMedia: that.$profileMedia,
                favoriteMedia: that.$favoriteMedia,
                postMediaModal: that.$postMediaModal
            };
        });
        
        if(that.$profileMedia == 1 || that.$favoriteMedia == 1){
            this.$fileUpload.bind('fileuploaddone', function (e, data) {
                var mediaUrl = data._response.result.files[0].mediaUrl;
                $("."+that.$previewDomClass).attr('src',mediaUrl);
                $('.choosePictureModal').modal('hide');
                that.$wrapper.find('#progress-files .progress-bar').css( 'width', '0%');

                if(that.$fromFormTalent == 1){
                    var mediaId = data._response.result.files[0].mediaId;
                    $("#"+that.$formHiddenDomClass+"-id").val(mediaId);
                }
            });
        }
        else if(that.$postMediaModal == 1){
            this.$fileUpload.bind('fileuploaddone', function (e, data) {
                //make html response to display in post modal medias
                var html = '';
                var source = that.$mediaTemplate.html();
                var inputVal = $("#postSelectedMediasId").val();
                this.template = Handlebars.compile(source);

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
                    //media.name = truncateString(media.name);

                    // For the local platform we build the url to download the media
                    // For other platforms like youtube, the url is the file path
                    if (media.platform === 'local') {
                        media.link = this.baseUrl + laroute.route('media_download', {id: media.id});
                    } else {
                        media.link = media.file_path;
                    }
                    
                    this.selectedMediaIds.push(media.id);

                    html += this.template({media: media});
                }
                //put html in post modal
                var previewElement = $("#modal-ajax").find("#selectedMediasPreview");
				previewElement.append(html);
				
                
                //add ids in hidden form field
                $("#modal-ajax").find("#postSelectedMediasId").val(this.selectedMediaIds.join(','));
                $('#postAttachMediaModal').modal('hide');
                $('#progress-files .progress-bar').css( 'width', '0%');
                
                
            });
        }
        else {
            this.$fileUpload.bind('fileuploaddone', function (e, data) {
                $('#navigationAttachMediaModal').modal('hide');
                $('#progress-files .progress-bar').css( 'width', '0%');
                location.reload();
            });
        }
    };

    AttachmentModal.prototype.setupImportForm = function () {
        var that = this;

        this.$importForm.on('submit', function (e) {
            e.preventDefault();
            that.clearImportUrlErrors();
            var modalImport = $(this);
            var confidentiality = that.$confidentiality.filter(':checked').val();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: {
                    url: that.$importUrl.val(),
                    confidentiality: confidentiality,
                    profile: JSON.stringify(that.selectedProfile),
                    postMediaModal: that.$postMediaModal
                } 
            }).done(function(data){
                if(that.$profileMedia == 1 || that.$favoriteMedia == 1){
                    var mediaUrl = data.import.thumb_path;
                    $("."+that.$previewDomClass).attr('src',mediaUrl);
                    if(that.$fromFormTalent == 1){
                        var mediaId = data.import.id;
                        $("#"+that.$formHiddenDomClass+"-id").val(mediaId);
                    }
                    modalImport.closest('.modal').modal('hide');
                }
                else if(that.$postMediaModal == 1){
                    //make html response to display in post modal medias
                    var html = '';
                    var source = that.$mediaTemplate.html();
                    var inputVal = $("#postSelectedMediasId").val();
                    this.template = Handlebars.compile(source);

                    // Initialize the selected medias
                    if ($.isNumeric(inputVal)) {
                        this.selectedMediaIds = [inputVal];
                    } else if (inputVal && inputVal.indexOf(',') > -1) {
                        this.selectedMediaIds = inputVal.split(',');
                    } else {
                        this.selectedMediaIds = [];
                    }

                    var media = data.import;
                    media.name = media.file_name;
                    this.selectedMediaIds.push(media.id);

                    html += this.template({media: media});
                    
                    //put html in post modal
                    $("#selectedMediasPreview").append(html);
                    //add ids in hidden form field
                    $("#postSelectedMediasId").val(this.selectedMediaIds.join(','));
                    modalImport.closest('.modal').modal('hide');
                }
                else{
                    modalImport.closest('.modal').modal('hide');
                    location.reload();
                }
            }).fail(function (response) {
                that.addImportUrlErrors(JSON.parse(response.responseText).errors);
            });
        });
    };

    AttachmentModal.prototype.clearImportUrlErrors = function () {
        this.$importUrlGroup.removeClass('has-error');
        this.$importHelp.empty();
    };

    AttachmentModal.prototype.addImportUrlErrors = function (errors) {
        this.$importUrlGroup.addClass('has-error');

        for (var i = 0; i < errors.length; i++) {
            this.$importHelp.append('<p>' + errors[i] + '</p>');
        }
    };

    AttachmentModal.prototype.listenClickEvent = function () {
        var that = this;

        //$('.f-publish-as').on('click', function(e) {
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
    };

    window.AttachmentModal = function (options) {
        var modal = new AttachmentModal(options);
        modal.setupUploadForm();
        modal.setupImportForm();
        modal.listenClickEvent();

        return modal;
    };

})();
