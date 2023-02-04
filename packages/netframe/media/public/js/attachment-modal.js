(function () {
    'use strict';

    function AttachmentModal(options) {
        this.$wrapper = options.$wrapper;
        this.$fileUpload = options.$fileUpload || $('#fileupload');
        this.$postAs = options.$postAs || $('#post-has');
        this.$importForm = options.$importForm || $('#import');
        this.$importHelp = options.$importHelp || $('#importUrl .help-block');
        this.$importUrlGroup = options.$importUrlGroup || $('#importUrl');
        this.$importUrl = options.$importUrl || $('input[name=url]');
        this.$confidentiality = options.$confidentiality || $('input:radio[name=confidentiality]');
        this.$profileIdInput = this.$wrapper.find('input[name="id_foreign"]');
        this.$profileTypeInput = this.$wrapper.find('input[name="type_foreign"]');

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

        this.$fileUpload.fileupload({});

        this.$fileUpload.bind('fileuploadsubmit', function (e, data) {
            var confidentiality = that.$confidentiality.filter(':checked').val();

            data.formData = {
                confidentiality: confidentiality,
                profile: JSON.stringify(that.selectedProfile)
            };
        });
    };

    AttachmentModal.prototype.setupImportForm = function () {
        var that = this;

        this.$importForm.on('submit', function (e) {
            e.preventDefault();

            that.clearImportUrlErrors();

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: {
                    url: that.$importUrl.val(),
                    confidentiality: that.$confidentiality.val(),
                    profile: JSON.stringify(that.selectedProfile)
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

        this.$wrapper.on('click', '.f-select-profile', function(e) {
            e.preventDefault();

            var el = $(this);
            var dataProfileType = el.data('profile');
            var dataProfileId = el.data('profile-id');

            // Update selected profile
            that.$profileIdInput.val(dataProfileId);
            that.$profileTypeInput.val(dataProfileType);
            that.selectedProfile = {
                id: dataProfileId,
                type: dataProfileType
            };

            // Display selected profile
            that.$postAs.text(dataProfileType + ' : ' + el.text());
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
