String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

function loadTimelinePosts() {
    $(document).find('div.post-loader[data-news-loaded="0"]').each(function(){
        let container = $(this);
        let newsFeedUrl = $(this).data('news-feed-url');
        $.ajax({
            url: newsFeedUrl + '?fromAjax=1',
            type: "GET",
            success: function( data ) {
                container.replaceWith(data.view);
                loadMapEvents(data.containerId);
            },
            error: function(textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });
}

function multiDimensionArray2JSON(thearray) {
    var output = '';
    var opentag = '';
    var closetag ='';

    /* Only process if user input is an array */
    if(thearray instanceof Array) {
        var arraykey = [];
        var keyneeded = false;

        /*
         * Get the key, if the key is non numeric then will output the key
         */
        for (var key in thearray) {
            if (thearray[key] != undefined)
            {
                arraykey.push(key);
                if (!/^[0-9]*$/.test(key))
                {
                    keyneeded = true;
                }
            }
        }

        /* use difference tag for associate array */
        if (keyneeded) {
            opentag = '{';
            closetag ='}';
        } else {
            opentag = '[';
            closetag =']';
        }

        output += opentag;

        for(var i = 0; i < arraykey.length; i++) {
            /* insert key */
            if (keyneeded) {
                output += '"'+arraykey[i]+'":';
            }

            /* if sub node still a array then process it */
            if(thearray[arraykey[i]] instanceof Array) {
                output += multiDimensionArray2JSON(thearray[arraykey[i]]);
            } else {
                /* if string, quoate it */
                if (typeof thearray[arraykey[i]] == 'string') {
                    output += '"'+thearray[arraykey[i]].replace(/\n/, '\\n').replace(/\r/, '\\r')+'"';
                } else {
                    output += thearray[arraykey[i]];
                }
            }


            /* end of element, terminate it */
            if((i+1) != arraykey.length) {
                output += ',';
            }
        }
        output += closetag;
    } else {
        output += '"'+thearray+'"';
    }

    return output;
}

/** Converts numeric degrees to radians */
if (typeof(Number.prototype.toRad) === "undefined") {
  Number.prototype.toRad = function() {
    return this * Math.PI / 180;
  }
}

function mainDropdownHeight(){
    if($('#netframe-main-dropdown').height() > ($(window).height() - 85)){
        maxHeight = $(window).height() - 85;
        $('#netframe-main-dropdown').css('max-height', maxHeight);
        $('#netframe-main-dropdown').css('overflow-y', 'auto');
    }
    else{
        $('#netframe-main-dropdown').css('max-height', 'none');
        $('#netframe-main-dropdown').css('overflow-y', 'inherit');
    }
}

// AJAX messages history
var historyTab = '';
function tlHistory(){
    if(historyTab == ''){
        history.go(-1)
    }
    else{
        top.location = "";
        historyTab = "";
        for(var i = 0; i < historyTab.length; i++) {
            eval('$(\''+historyTab[i][0]+'\').'+historyTab[i][1]+'(\''+historyTab[i][2]+'\');');
        }
    }
}

function submitModal(event, _form){
    event.preventDefault();

    //var modalId = '#modal-ajax';
    var modalId = "#"+_form.closest('.modal').attr('id');
    var modalContent = $('#modal-ajax .modal-content');
    var actionUrl = _form.attr('action');
    var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

    // add data to object array serialized json
    formData.push({
        name: "httpReferer",
        value: requestUrl
    });

    //var el = $(this);
    var targetJoinWay = $('.join-way').closest('a');

    $.ajax({
        url: actionUrl,
        data: formData,
        type: "POST",
        success: function( data ) {
            $(modalId).find('.modal-content').html(data.view);
            if(typeof data.redirect != 'undefined') {
                window.open(data.redirect, typeof data.target!='undefined' ? data.target : null);
            }
            else if(typeof data.reloadPage != 'undefined') {
                location.reload();
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

            // reload new url in modal
            if(typeof data.modalReplaceUrl != 'undefined') {
                $(modalId).find('.modal-content').load(data.modalReplaceUrl);
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
                // replace button with json view
                targetJoinWay = $("a[data-profile='"+data.joinProfile+"']");
                targetJoinWay.fadeOut('slow', function() {
                    targetJoinWay.replaceWith(data.viewContent);
                    targetJoinWay.fadeIn('slow');
                });
            }
        },
        error: function(textStatus, errorThrown) {
            //console.log(textStatus);
        }
    });
}

function loadMapEvents(mainContainer)
{
    $(mainContainer + ' .panel-event-map').each(function(e){
        var currentEvent = $(this);
        var currentMapLatitude = parseFloat(currentEvent.data('latitude'));
        var currentMapLongitude = parseFloat(currentEvent.data('longitude'));

        var zoom = 13;
        var center = {lat: currentMapLatitude, lng: currentMapLongitude};

        var map = new google.maps.Map(currentEvent.get(0), {
            center: center,
            //  styles: mapsTheme,
            zoom: zoom,
            disableDefaultUI: true,
            gestureHandling: 'cooperative',
            scrollwheel: false
        });

        var icon = {
                url: '/assets/img/pin@2x.png',
                size: new google.maps.Size(26, 31),
                scaledSize: new google.maps.Size(26, 31)
        };

        var marker = new google.maps.Marker({
            map: map,
            position: center,
            icon: icon
        });
    });
}



function confirmWindow(message)
{
    // display confirm modal with custom message
    $('#modal-confirm .modal-body').html('test');
    // get and return response
    $('#modal-confirm').modal('show');
    $('#modal-confirm').on('click', '.fn-confirm', function(e){
        alert('confirm');
        return true;
    });


}

// js tanslator functions

function trans(key, replace = {})
{
    let translation = key.split('.').reduce((t, i) => t[i] || null, window.translations);

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation;
}

function trans_choice(key, count = 1, replace = {})
{
    let translation = key.split('.').reduce((t, i) => t[i] || null, window.translations).split('|');

    translation = count > 1 ? translation[1] : translation[0];

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation;
}
