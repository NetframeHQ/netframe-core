(function () {
    'use strict';

    function InstancesMedias(options) {
        this.$wrapper = options.$wrapper;
        this.$fileUpload = options.$fileUpload || $('#fileupload');
        this.$previewDomClass = options.$previewDomClass || '';
        this.$formHiddenDomClass = options.$formHiddenDomClass || '';
        this.$favoriteMedia = options.$favoriteMedia || '0';
        this.$mediaTemplateRender = options.$mediaTemplateRender || '.tl-posted-medias';
        this.$mediaType = options.$mediaType || '';
        this.$route = options.$route || '';
        this.$finalField = options.$finalField || '';
        this.$inBackground = options.$inBackground || 0;

        if(this.$route == ''){
            this.$route = this.$fileUpload.attr('action');
        }
    }

    InstancesMedias.prototype.setupUploadForm = function () {
        var that = this;
        this.$fileUpload.fileupload({
            url: that.$route,
            add: function(e, data) {
                var totalSize = 0;
                for(var file in data.originalFiles){
                    totalSize = totalSize + parseInt(data.originalFiles[file]['size']);
                }
                if(totalSize > 524288000) {
                    alert('Max : 500 Mo');
                }
                else{
                    data.submitted = new Date().getTime();
                    that.$wrapper.find('#progress-files').removeClass('d-none');
                    data.submit();
                }
            },
            autoUpload: true,
            singleFileUploads:true,
            progress: function(e,data){
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress-files .progress-bar').css( 'width', progress + '%');
            }
        });

        this.$fileUpload.bind('fileuploadsubmit', function (e, data) {
            data.formData = {
                mediaType: that.$mediaType,
            };
        });

        this.$fileUpload.bind('fileuploaddone', function (e, data) {
            var result = data.result
            if(that.$inBackground == 1){
                that.$wrapper.css("background-image", "url(" + result.filename+'?'+Math.random() +")");
                that.$wrapper.find('#updel-cover').removeClass('d-none');
                that.$wrapper.find('.cover-placeholder').addClass('d-none');
            }
            else{
                that.$wrapper.find(result.filePreview).attr('src',result.filename+'?'+Math.random());
                that.$wrapper.find('.fn-remove-avatar').removeClass('d-none');
            }
            $("input:hidden[name='"+that.$finalField+"']").val(result.filename);
            that.$wrapper.find('#progress-files').addClass('d-none');
            that.$wrapper.find('#progress-files .progress-bar').css( 'width', '0%');
        });
    };

    window.InstancesMedias = function (options) {
        var modal = new InstancesMedias(options);
        modal.setupUploadForm();
        return modal;
    };

})();
