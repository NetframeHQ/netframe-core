/**
 *  jQuery Object in namespace netframe
 *  Call netframe object javascript for get reflection objects api
 *  
 */
(function($) {
    
    netframe = {
        
        // Manage Spinner svg loader
        spinner: {
            el: $('.spinner-svg:first'),
            // Check if spinner loader is hide or show
            checkState: function() {
                if(this.el.hasClass('hide')) {
                    // it is hide
                    return false;
                } else {
                    // it is display
                    return true;
                }
            },
            
            // Display or Hide Spinner loader
            toggle: function() {
                if(this.checkState() === true) {
                    return this.hide();
                } else {
                    return this.show();
                }
            },
            // Display spinner loader
            show: function() {
                return this.el.hide().removeClass('hide').show('fast');
            },
            // Hide spinner loader
            hide: function() {
                return this.el.hide().addClass('hide');
            },
            
            // Display div Block type for spinner loader
            displayBlock: function(_selector) {
                var clonning = this.el.clone().removeClass('hide').show();
                clonning.prependTo(_selector);
            }
        }
        
    };
    
})(jQuery);


