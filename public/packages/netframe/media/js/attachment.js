$(function () {
    'use strict';

    $(document).ready(function () {
        var modalSelector = $('#attachmentModal');
        var fileUploadSelector = $('#fileupload');
        var profileTypeSelector = $('#profileType');
        var profileTextSelector = $('#profileText');
        var publishAsPanelSelector = $('#publishAs');
        var publishAsHamburgerSelector = $('#publishAsButton');
        var publishAsCloseSelector = $('#publishAsClose');
        var importFormSelector = $('#import');
        var importHelpSelector = $('#importUrl .help-block');
        var importUrlGroupSelector = $('#importUrl');
        var importUrlSelector = $('input[name=url]');
        var confidentialitySelector = $('input:radio[name=confidentiality]:checked');

        var talentText = 'Talent';
        var selectedProfileSelector;

        // JSON object in the form
        // {
        //   "type": "talent",
        //   "id": 3
        // }
        var selectedProfile = {};

        // Upload form
        fileUploadSelector.fileupload({});

        fileUploadSelector.bind('fileuploadsubmit', function (e, data) {
            data.formData = {
                confidentiality: confidentialitySelector.val(),
                profile: JSON.stringify(selectedProfile)
            };
        });

        // Import form
        importFormSelector.on('submit', function (e) {
            e.preventDefault();

            clearImportUrlErrors();

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: {
                    url: importUrlSelector.val(),
                    confidentiality: confidentialitySelector.val(),
                    profile: JSON.stringify(selectedProfile)
                }
            }).fail(function (response) {
                addImportUrlErrors(JSON.parse(response.responseText).errors);
            });
        });

        function clearImportUrlErrors() {
            importUrlGroupSelector.removeClass('has-error');
            importHelpSelector.empty();
        }

        function addImportUrlErrors(errors) {
            importUrlGroupSelector.addClass('has-error');

            for (var i = 0; i < errors.length; i++) {
                importHelpSelector.append('<p>' + errors[i] + '</p>');
            }
        }

        // Publish as panel
        /*
        modalSelector.on('hidden.bs.modal', function (e) {
            publishAsPanelSelector.hide();
        });

        publishAsHamburgerSelector.on('click', function () {
            publishAsPanelSelector.fadeToggle('fast');
        });

        publishAsCloseSelector.on('click', function () {
            publishAsPanelSelector.fadeToggle('fast');
        });

        $('#talents').selectable({
            selected: function (event, ui) {
                if (selectedProfileSelector) {
                    selectedProfileSelector.removeClass('ui-selected');
                }

                // Store selected profile
                selectedProfileSelector = $(ui.selected);
                selectedProfile = {
                    type: 'talent',
                    id: selectedProfileSelector.attr('data-id')
                };

                // Modify DOM
                profileTextSelector.text(ui.selected.innerText);
                profileTypeSelector.text(talentText);
            }
        });
        */
    });
});
