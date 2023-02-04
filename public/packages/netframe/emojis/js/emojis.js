 (function () {
    'use strict';

    function Emojis(options) {
        this.$wrapper = options.$wrapper  || $('#posting');

    }

    Emojis.prototype.loadEmojis = function () {
        $.ajax({
            url: laroute.route('emojis.list'),
            type: "POST",
            success: function(data) {
                if(typeof data.emojis != 'undefined'){
                   // compile with handlebars
                }
            }
        });
    }

    window.Emojis = function (options) {
        var emojis = new Emojis(options);

        return emojis;
    };
});