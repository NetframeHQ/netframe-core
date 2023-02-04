 (function () {
    'use strict';
    
    function WorkflowSystem(options) {
        this.$wrapper = options.$wrapper;
        this.$type = options.$type;
        this.$objectType = options.$objectType;
        this.$profileType = options.$profileType;
        this.$profileId = options.$profileId;
    }
    
    WorkflowSystem.prototype.addStep = function () {
        var wfObject = this;
        
        $.ajax({
            url: laroute.route('wf.list.actions', { objectType : wfObject.$objectType }),
            type: "GET",
            success: function(data) {
                if(typeof data.view != 'undefined'){
                    wfObject.$wrapper.find('.make-container').append(data.view);
                }
            }
        });
    }
    
    WorkflowSystem.prototype.listenEvents = function () {
        var wfObject = this;
        wfObject.$wrapper.on('change', '.select-action', function(e){
            var selectedAction = $(this).val();
            var wfLine = $(this).closest('.wf-line');
            var actionSlug = wfLine.find('.action-slug').val();
            var postDatas = { 
                actionType: selectedAction, 
                actionSlug: actionSlug,
                profileId: wfObject.$profileId,
                profileType: wfObject.$profileType,
            };
            if(selectedAction != 0){
                $.ajax({
                    url: laroute.route('wf.choose.actions'),
                    type: "POST",
                    data: postDatas,
                    success: function(data) {
                        if(typeof data.view != 'undefined'){
                            wfLine.find('.action-details').html(data.view);
                            wfLine.find('.action-details').removeClass('d-none');
                        }
                        
                        if(typeof data.action_type != 'undefined'){
                            // depending action type, launch js script
                            if(data.action_type == 'user_valid'){
                                wfObject.selectUsers(data.target_element, 1);
                            }
                            else if(data.action_type == 'group_valid'){
                                wfObject.selectUsers(data.target_element, 100);
                            }
                        }
                        
                        if(data.is_final == 1){
                            wfObject.$wrapper.find('.add-more').addClass('d-none');
                        }
                        else{
                            wfObject.$wrapper.find('.add-more').removeClass('d-none');
                        }
                    }
                });
            }
        });
        
        wfObject.$wrapper.on('click', '.wf-add-field', function(e){
            e.preventDefault();
            wfObject.addStep();
        });
        
        wfObject.$wrapper.on('click', 'div[data-target-form=".fn-create-wf-move-file"] .f-publish-as', function(e){
            e.preventDefault();
            wfObject.loadFolders($(this));
        });
    }
    
    // manage select2 user(s) fields
    WorkflowSystem.prototype.selectUsers = function(targetElement, maxItems) {
        $(targetElement).select2({
            placeholder: "Tapez ici",
            language: {
                inputTooShort: function () {
                    return "Tapez 2 caractères minimum...";
                }
            },
            allowClear: true,
            minimumInputLength: 2,
            multiple: true,
            maximumSelectionLength: maxItems,
            ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                url: laroute.route('wf.search.users'),
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
    }
    
    // manage move file to folder action
    WorkflowSystem.prototype.loadFolders = function (profileLink) {
        var profileId = profileLink.data('profile-id');
        var profileType = profileLink.data('profile');
        var formElement = profileLink.closest('div.wf_action_container');
        var movedElementType = $(formElement).find("input[name='movedElementType']").val();
        var movedElementId = $(formElement).find("input[name='movedElementId']").val();
        var selectField = formElement.find("select[name='"+formElement.data('field')+"']");

        var data = {
            profileType: profileType,
            profileId: profileId,
            movedElementType: movedElementType,
            movedElementId: movedElementId
        };

        $.ajax({
            url: laroute.route('xplorer.load.profile.folders'),
            data: data,
            type: "POST",
            success: function( data ) {
                var $option;
                selectField.find('option').remove();
                if(typeof data.options != 'undefined'){
                    $.each(data.options, function(index, option) {
                        $option = $("<option></option>")
                          .attr("value", option.value)
                          .html(option.text);
                        selectField.append($option);
                      });
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };
        
    window.WorkflowSystem = function (options) {
        var wfs = new WorkflowSystem(options);
        wfs.listenEvents();
        
        return wfs;
    };
    
})();

(function($){
     var wf = null;
     
     // listen event on files workflow
     $(document).on('change', '#modal-files .fn-active-workflow', function(e){
         var wfContainer = $(this).closest('.workflow-container');
         if($(this).is(':checked')){
             wfContainer.find('.make-container').removeClass('d-none');
             wfContainer.find('.add-more').removeClass('d-none');
             
             if(wf == null || wfContainer.find('.make-container').html() == ''){
                 wf = new WorkflowSystem({
                     $wrapper: wfContainer,
                     $type: 'files',
                     $objectType: 'Media',
                     $profileType: wfContainer.data('profile-type'),
                     $profileId: wfContainer.data('profile-id'),
                 });
                 wf.addStep();
             }
             
         }
         else{
             wfContainer.find('.make-container').addClass('d-none');
             wfContainer.find('.add-more').addClass('d-none');
         }
     });
     
     $(document).on('click', '.wf-validate-file', function(e){
         e.preventDefault();
         var actionId = $(this).data('wf-action-id');
         var validateStatus = $(this).data('validate-status');
         var notifId = $(this).data('notif-id');
         var fileId = $(this).data('file-id');
         
         if(validateStatus == 'decline'){
             $('.notif-decline-form-'+notifId).removeClass('d-none');
         }
         else{
             var postDatas = {
                 actionId: actionId,
                 validateStatus: validateStatus,
                 notifId: notifId,
                 fileId: fileId
             };
             if(validateStatus == 'decline-send'){
                 var declineReason = $('.notif-decline-form-'+notifId).find('textarea').val();
                 postDatas.reason = declineReason;
             }
             
             $.ajax({
                 url: laroute.route('wf.answer.action'),
                 type: "POST",
                 data: postDatas,
                 success: function(data) {
                     $('#notif-'+data.notifId).fadeOut('slow', function(){
                         $(this).remove();
                     });
                 }
             });
         }
     });
     
     $(document).on('click', '.wf-remove-line', function(e){
         e.preventDefault();
         $(this).closest('div.wf-line').remove();
     });
     
 })(jQuery);

