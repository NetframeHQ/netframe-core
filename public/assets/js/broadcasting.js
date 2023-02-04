//subscribe global channel for post delete
Echo.private('Instance-'+instanceId)
    .listen('DeletePost', function(e) {
        if(typeof e.elementId != 'undefined'){
            var element = $('#'+e.elementId);
            $('#'+e.elementId).fadeOut('slow', function() {
                $(this).remove();
            });
        }
    })
    .listen('DeleteComment', function(e) {
        if(typeof e.elementId != 'undefined'){
            $('[id^="'+e.elementId+'-"]').fadeOut('slow', function() {
                $(this).remove();
            });
        }
    });

