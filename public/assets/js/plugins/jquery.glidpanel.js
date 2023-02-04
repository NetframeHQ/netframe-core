/**
 * Plugin SlidePanel Illisite
 * 
 * @author hervé marion
 * @copyright 2014 Illisite
 * @package jQuery 1.11.x
 * 
 * use put data-glidpanel="toggle" on button or link and for display icon
 * hamburger insert class glid-hamburger on span element child on
 * data-glidpanel="toggle" ex <a data-glidpanel="toggle"><span
 * class="glid-hamburger"></span></a>
 * 
 * Tag must be used: class glid-wrap = class tag who wrap container for sliding
 * panel class glid-toggle = class tag for run action slide class glid-burger =
 * class tag for enabled icon hamburger touch responsive class glid-panel =
 * class tag for pick out contenaire nav element slide panel
 * 
 * schema: <div class='glid-wrap'> <div class="glid-panel">
 * <ul>
 * <li>...</li>
 * </ul>
 * </div> <button class="glid-toggle"><span class="glid-burger">...</span></button>
 * ... {my content} </div>
 * 
 */
(function($) {

    $.fn.glidPanel = function(options) {

        var el = this;
        var $el = $(this);

        var params = $.extend({
            tagToggle : '.glid-toggle',
            tagParent : '.glid-wrap',
            tagIcon : '.glid-burger',
            tagPanel : '.glidpanel',
            action : 'click',
            speed : 'fast',
            classOpen : 'glid-open'
        }, options);

        $(document).on(params.action, el.selector, function(e) {
            e.preventDefault();

            var _parent = $(this).parents(params.tagParent);
            var _panel = _parent.find(params.tagPanel);
            var _btnToggle = $(this);
            var _panelWidth = _panel.outerWidth();

            if (_parent.hasClass(params.classOpen)) {
                // Close panel
                _parent.removeClass(params.classOpen);

                _panel.animate({
                    right : '-230px'
                }, params.speed);

                _btnToggle.animate({
                    marginRight : 0
                }, params.speed);

            } else {
                // Open Panel
                _parent.addClass(params.classOpen);
                _btnToggle.animate({
                    marginRight : _panelWidth
                }, params.speed);

                _panel.animate({
                    right : 0
                }, params.speed);

            }

        });

        return el;

    };

})(jQuery);
