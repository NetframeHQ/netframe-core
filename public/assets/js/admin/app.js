(function($) {
   
    // TOOLTIP
    $('[data-toggle=tooltip]').tooltip();
    
    // POPOVER
    $('[data-toggle=popover]').popover();
    
    // Initialise ajax header for token csrf
    $.ajaxSetup({
        headers : {
            'X-CSRF-Token' : $('meta[name="_token"]').attr('content')
        }
    });
	
	// --------------------- CONFIRM ALERT TO DELETE ITEM IN AJAX
    /*
     * ex: <a href="#url" class="confirm-delete" data-txtconfirm="my texte
     * displaying"></a>
     */
    $(document).on('click', '.fn-confirm-delete', function(e) {
        var _confirm = confirm($(this).data('txtconfirm'));

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else{
            e.preventDefault();
            e.stopPropagation();
            var linkHref = $(this).attr('href');

            var el = $(this);

            $.get(linkHref, function( data ) {
                if(data.delete === true) {
                    el.parents(data.targetId).fadeOut('slow', function() {
                        $(this).remove();
                    });
                }
            });
        }

    });
    
    //========= Post Ajax Form for Modal publish and return response
    $('#modal-ajax').on('click', 'button[type="submit"]', function(event) {
        var _form = $(this).parents('.modal-content').find('form');
        if(!_form.hasClass('no-auto-submit')){
            submitModal(event, _form);
        }
    });
    
    $('#modal-ajax').on('submit', 'form', function(event) {
        if(!$(this).hasClass('no-auto-submit')){
            submitModal(event,$(this));
        }
    });
       
    function submitModal(event, _form){
        event.preventDefault();

        var modalId = '#modal-ajax';
        var modalContent = $('#modal-ajax .modal-content');
        var actionUrl = _form.attr('action');
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer"
            });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                $(modalId).find('.modal-content').html(data.view);
                if(typeof data.redirect != 'undefined') {
                    window.open(data.redirect, typeof data.target!='undefined' ? data.target : null);
               }
               else if(typeof data.replaceContent != 'undefined'){
                   var elTarget = $(data.targetId);
                   elTarget.fadeOut('slow', function() {
                            elTarget.replaceWith(data.viewContent);
                            elTarget.fadeIn('slow');
                        });
               }

                if(typeof data.reload != 'undefined' && data.reload===true) {
                    document.location.reload();
                }

                // wait for variable closeModal to TRUE from php script
                if(typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
               }
               
               //delete content from page
               if(typeof data.deleteContent != 'undefined') {
                    var elTarget = $(data.targetId);
                    $(elTarget).fadeOut('slow');
               }
               
               //display displayView from ajax return append data.target
               if(typeof data.newContent != 'undefined') {
                    $(data.viewContent).appendTo($(data.targetId)).show().slideDown('normal');
               }
               
               // variable return for Alert
                if(typeof data.joinNotify != 'undefined') {
                   targetJoinWay.text(data.displayText);
                   targetJoinWay.parents("a").addClass(data.fnJoin).removeClass('btn-default')
                   .addClass('btn-primary').removeAttr("data-target").removeAttr("data-toggle")
                   .removeAttr("href").attr("data-tl-join", data.dataJsonEncoded);
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    }
    
    //========= Fixed & Hack Modal Bootstrap, Reload Content & Remove Content of modal when click open
    $(document).on('hidden.bs.modal', '#modal-ajax', function (e) {
        $(e.target).removeData('bs.modal');
    });
    
    $(document).on("click", '[data-toggle=modalajax]', function(e) {
        e.preventDefault();
        
        var _targetClicked = $(this);
        var _route = $(this).attr('href');
        var $modal = $("#modal-ajax");
        
        var xhrGet = $.get(_route);
        netframe.spinner.displayBlock($modal.find(".modal-content"));
        
        $modal.modal("show");
        
        
        xhrGet.success(function(data) {
        
            $modal.find(".modal-content").html(data);
            
        }).error(function(xhr) {
            $modal.find(".modal-content").empty()
                  .html('<div class="alert alert-danger">'+ xhr.responseText +'</div>');
        });
        
    });
    
    $('#modal-ajax').on("hidden.bs.modal", function(i) {
        $(this).find(".modal-content").empty();
    });
    

})(jQuery, netframe);