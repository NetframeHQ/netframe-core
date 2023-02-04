(function () {
    'use strict';
    
    var mediaType = '';
    var mediaId = '';

    function FileXplorer(options) {
        this.$wrapper = options.$wrapper;
        this.$profileId = options.$profileId || '0';
        this.$profileType = options.$profileType || '';
        this.$idFolder = options.$idFolder || '0';

        this.selectedProfile = {
            id: this.$profileId,
            type: this.$profileType
        };
    }
    
    $('#modal-confirm-delete').on('click', '.fn-confirm-modal-delete', function(e){
        e.preventDefault();
        
        console.log(mediaType + ':' + mediaId);
        
        var data = {
            mediaType: mediaType,
            mediaId: mediaId
        };

        $.ajax({
            url: laroute.route('xplorer_delete_element'),
            data: data,
            type: "POST",
            success: function(data) {
                if(typeof data.delete != 'undefined' && data.delete){
                    $(data.targetId).fadeOut('slow', function() {
                        $(this).remove();
                        $('#modal-confirm-delete').modal('hide');
                    });
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
                //console.log('error');
                $('#modal-confirm-delete .modal-body').html(textStatus);
            }
        });
    });

    FileXplorer.prototype.listenEvent = function () {
        var that = this;

        /*
        * change display mode
        */
        $(document).on('click', '.fn-switch-display', function(e){
            e.preventDefault();
            let viewMode = $(this).data('view-mode');
            let viewSlug = $(this).data('view-slug');
            
            $(document).find('.fn-switch-display').removeClass('nf-customactive btn-nohov');
            $(this).addClass('nf-customactive btn-nohov');
            
            if (viewMode == 'netframe-list-wrapper') {
                that.$wrapper.find('.xplorer-main-view').removeClass('netframe-grid-wrapper');
                that.$wrapper.find('.xplorer-main-view').addClass('netframe-list-wrapper');
            } else if (viewMode == 'netframe-grid-wrapper') {
                that.$wrapper.find('.xplorer-main-view').removeClass('netframe-list-wrapper');
                that.$wrapper.find('.xplorer-main-view').addClass('netframe-grid-wrapper');
            }
            
            // send slug to back end to store preference
            var data = {
                viewSlug: viewSlug
            };
            $.ajax({
                url: laroute.route('medias_explorer.switch_view'),
                data: data,
                type: "POST",
                success: function(data) {
                    
                },
                error: function(textStatus, errorThrown) {
                    //console.log(textStatus);
                    //console.log('error');
                }
            });
        });

        that.$wrapper.on('click', '.fn-delete-xplorer', function(e){
            e.preventDefault();
            var mediaItem = $(this).closest('.container');
            mediaType = mediaItem.data('type');
            mediaId = mediaItem.data('id');
            
            console.log(mediaType + ':' + mediaId);
            
            // add connet
            $('#modal-confirm-delete .modal-body').html('<strong>' + mediaItem.data('confirm-message') + '</strong>');
            if (parseInt(mediaItem.data('workflows')) > 0) {
                $('#modal-confirm-delete .modal-body').append('<div class="text-danger">' + mediaItem.data('confirm-workflow') + '</div>');
            }
            
            $('#modal-confirm-delete').modal('show');
        });

        that.$wrapper.on('click', '.fn-star-media', function(e){
            e.preventDefault();
            that.starMedia($(this));
        });
        
        that.$wrapper.on('click', '.fn-modify-lock', function(e){
            e.preventDefault();
            that.modifyLock($(this));
        });

        $(document).on('submit', 'form.fn-add-folder', function(e){
            e.preventDefault();
            that.addFolder($(this));
        });

        $(document).on('submit', 'form.fn-add-file', function(e){
            e.preventDefault();
            that.addFile($(this));
        });

        $(document).on('submit', 'form.fn-move-file', function(e){
            e.preventDefault();
            that.moveFile($(this));
        });

        $(document).on('click', 'div[data-target-form=".fn-move-file"] .f-publish-as', function(e){
            e.preventDefault();
            that.loadFolders($(this));
        });

        /*
         * Close modal-files hook
         */
        $(document).on('hidden.bs.modal', '#modal-files', function (e) {
            //get all uploaded files and implement in file explorer
            var filesForm = $(this).find('form.fn-add-file');

            // check if form update
            var checkUpdate = filesForm.find("input[name='idFile']");

            if(filesForm.length != 0 && checkUpdate.length == 0){
                filesForm.submit();
            }

            //$(this).data('bs.modal', null);
            //$(this).find(".modal-body").empty()
        });

        that.orderElements();

    };
    
    FileXplorer.prototype.modifyLock = function(clickElement){
        var mediaId = clickElement.data('media-id');
        var newState = clickElement.data('new-state');
        
        var data = {
            mediaId: mediaId,
            newState: newState
        };
        
        $.ajax({
            url: laroute.route('xplorer_modify_lock'),
            data: data,
            type: "POST",
            success: function(data) {
                // reload document sub view in xplorer
                if(typeof data.replaceContent != 'undefined'){
                    var elTarget = $(data.targetId);
                    elTarget.fadeOut('slow', function() {
                             elTarget.replaceWith(data.viewContent);
                             elTarget.fadeIn('slow');
                         });
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    FileXplorer.prototype.starMedia = function(clickElement){
        var container = clickElement.closest('li.media-file');
        var mediaId = container.data('id');

        var data = {
            mediaId: mediaId
        };

        $.ajax({
            url: laroute.route('xplorer_star_media'),
            data: data,
            type: "POST",
            success: function(data) {
                if(typeof data.success != 'undefined'){
                    if(data.state == 'on'){
                        clickElement.addClass('active')
                    }
                    else if(data.state == 'off'){
                        clickElement.removeClass('active')
                    }
                    // clickElement.html(clickElement.data('linked-'+data.state));
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    FileXplorer.prototype.addDroppable = function(element){
        element.droppable({
            // Lorsque un element arrive sur un autre
            over: function(event, ui){
                var draggued = ui.draggable;
                draggued.addClass('move');
                $(this).addClass('receiver');

            },
            // Lorsque un element en quite un autre
            out: function(event, ui){
                var draggued = ui.draggable;
                draggued.removeClass('move');
                $(this).removeClass('receiver');
            },
            // Lorsque l'on relache un élément sur un autre
            drop: function(event, ui){
                var draggued = ui.draggable;
                var movedElementId = draggued.data('id');
                var movedElementType = draggued.data('type');

                var targetId = $(this).data('id');

                var data = {
                    movedElementType: movedElementType,
                    movedElementId: movedElementId,
                    targetId: targetId
                };

                $.ajax({
                    url: laroute.route('xplorer_drag_element'),
                    data: data,
                    type: "POST",
                    success: function(data) {
                        if(typeof data.success != 'undefined' && data.success){
                            draggued.remove();
                        }
                    },
                    error: function(textStatus, errorThrown) {
                        //console.log(textStatus);
                    }
                });

                $(this).removeClass('receiver');
            }
        })
    };

    FileXplorer.prototype.orderElements = function(){
        var that = this;

        $('.draggable').draggable({ 
            revert: true,
            start: function(event, ui){
                var draggingElement = ui.helper;
                draggingElement.css('z-index', '1000');
            },
            stop: function(event, ui){
                var draggingElement = ui.helper;
                draggingElement.css('z-index', '10');
            },
        });

        that.$wrapper.find('.droppable').each(function(e){
            that.addDroppable($(this));
        });
    };

    FileXplorer.prototype.addFolder = function (form) {
        var that = this;

        var modalId = '#modal-ajax';
        var modalContent = $('#modal-ajax .modal-content');
        var actionUrl = form.attr('action');
        var formData = form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: requestUrl,
        });
        formData.push({
            name: "idFolder",
            value: that.$idFolder,
        });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                //$(modalId).find('.modal-content').html(data.view);
                if(typeof data.replaceContent != 'undefined'){
                    var elTarget = $(data.targetId);
                    elTarget.fadeOut('slow', function() {
                            elTarget.replaceWith(data.viewContent);
                            elTarget.fadeIn('slow');
                        });
                }
                // wait for variable closeModal to TRUE from php script
                if(typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
                }

                // wait for variable waitCloseModal to TRUE from php script to close modal after delay
                if(typeof data.waitCloseModal != 'undefined') {
                    setTimeout( function() {
                        $(modalId).modal('hide');
                    }, data.waitCloseModal);
                }

                // display displayView from ajax return append data.target
                if(typeof data.newContent != 'undefined') {
                    if(typeof data.folderId != 'undefined'){
                        that.$wrapper.find('#folder-'+data.folderId).remove();
                    }
                    that.insertElement('folder', data.folderName, data.viewContent);
                }

            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    FileXplorer.prototype.addFile = function (form) {
        var that = this;

        var modalId = '#modal-files';
        var modalContent = $(modalId+' .modal-content');
        var actionUrl = form.attr('action');
        var formData = form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: requestUrl,
        });
        formData.push({
            name: "idFolder",
            value: that.$idFolder,
        });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                //$(modalId).find('.modal-content').html(data.view);

                if(typeof data.replaceContent != 'undefined'){
                    var elTarget = $(data.targetId);
                    elTarget.fadeOut('slow', function() {
                            elTarget.replaceWith(data.viewContent);
                            elTarget.fadeIn('slow');
                        });
                }

                // if error
                if(typeof data.error != 'undefined'){
                    // reload tags
                }

                // wait for variable closeModal to TRUE from php script
                if(typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
                }

                // wait for variable waitCloseModal to TRUE from php script to close modal after delay
                if(typeof data.waitCloseModal != 'undefined') {
                    setTimeout( function() {
                        $(modalId).modal('hide');
                    }, data.waitCloseModal);
                }

                // display displayView from ajax return append data.target
                if(typeof data.newContent != 'undefined') {
                    $.each(data.viewContent, function(index, element){
                        that.insertElement('file', index, element);
                    });
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    FileXplorer.prototype.insertElement = function(elementType, index, element){
        var that = this;
        var loop = 0;
        var prevName = '';

        if(element.type == 'insert'){
            if(elementType == 'file'){
                var elementDom = "li[data-type='media']";
            }
            else if(elementType == 'folder'){
                var elementDom = "li[data-type='folder']";
            }

            var totalItems = that.$wrapper.find(elementDom).length;
            if(totalItems > 0){
                that.$wrapper.find(elementDom).each(function(e){
                    if(index.toUpperCase() < $(this).data('name').toUpperCase() && loop == 0){
                        $(element.view).insertBefore($(this));
                        return false;
                    }
                    else if(loop > 0 && index.toUpperCase() >= prevName.toUpperCase() && index.toUpperCase() <= $(this).data('name').toUpperCase()){
                        $(element.view).insertBefore($(this));
                        return false;
                    }
                    loop = loop + 1;
                    prevName = $(this).data('name');
                    if(loop == totalItems){
                        $(element.view).insertAfter($(this));
                    }
                });
            }
            else if(elementType == 'file'){
                that.$wrapper.find('.file-display').append(element.view);
            }
            else if(elementType == 'folder'){
                that.$wrapper.find('.file-display').prepend(element.view);
            }

            // add droppable and draggable capacities
            var newElementId = $(element.view).attr('id');
            if(elementType == 'folder'){
                that.addDroppable($('#'+newElementId));
            }

            $('#'+newElementId).draggable({ revert: true });
        }
        else if(element.type == 'replace'){
            // get element id for replacement
            var newElementId = $(element.view).attr('id');
            var elTarget = $('#'+newElementId);
            elTarget.fadeOut('slow', function() {
                elTarget.replaceWith(element.view);
                elTarget.fadeIn('slow');
            });
        }
    };

    FileXplorer.prototype.loadFolders = function (profileLink) {
        var profileId = profileLink.data('profile-id');
        var profileType = profileLink.data('profile');
        var formElement = profileLink.closest('div.tl-publish-as').data('target-form');
        var movedElementType = $(formElement).find("input[name='movedElementType']").val();
        var movedElementId = $(formElement).find("input[name='movedElementId']").val();
        var selectField = $(formElement).find("select[name='target']");

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

    FileXplorer.prototype.moveFile = function (form) {
        var that = this;

        var modalId = '#modal-ajax';
        var modalContent = $('#modal-ajax .modal-content');
        var actionUrl = form.attr('action');
        var formData = form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: requestUrl,
        });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                // wait for variable closeModal to TRUE from php script
                if(typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
                }

                // confirm copy modal
                if(typeof data.returnMessage != 'undefined' && typeof data.autoFireModal != 'undefined'){
                    // call success modal
                    $('#modal-ajax').find('.modal-content').html(data.autoFireModal);
                    $("#modal-ajax").modal('show');
                    setTimeout(function(){
                        $("#modal-ajax").modal("hide");
                        }, 2000);
                }

                // if copy in same folder insert element
                if(typeof data.viewContent != 'undefined'){
                    that.insertElement(data.viewContent.elementType, data.viewContent.name, data.viewContent.view);
                }

                // wait for variable waitCloseModal to TRUE from php script to close modal after delay
                if(typeof data.waitCloseModal != 'undefined') {
                    setTimeout( function() {
                        $(modalId).modal('hide');
                    }, data.waitCloseModal);
                }
                if(typeof data.success != 'undefined'){
                    that.$wrapper.find(data.movedElement).fadeOut(function() {
                        $(this).remove();
                    });
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    window.FileXplorer = function (options) {
        var fileXplorer = new FileXplorer(options);
        fileXplorer.listenEvent();

        return fileXplorer;
    };

})();
