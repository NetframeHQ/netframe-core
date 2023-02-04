/*
 * jQuery Form Elements
 *
 * Author : @starfennec
 * Version: 0.1
 * Date: mar 22 2017
 */

(function($) {

  var customRadio = function(radio, options){
    var settings = $.extend({
    }, options || {});

    this.settings = settings;

    var $radio = $(radio);

    var el = '<span class="radio-btn"></span>';

    if($radio.closest('.form-element').length == 1){
      $(el).insertAfter($radio);
    }
  }

  $.fn.customRadio = function(options){
    return this.each(function(i,e){
      var fe = new customRadio(e, options);
    });
  };

  var customCheckbox = function(checkbox, options){
    var settings = $.extend({

    }, options || {});

    this.settings = settings;

    var $checkbox = $(checkbox);

    var svg = '';

    if($checkbox.closest('.form-element').length == 1){
      $(svg).insertAfter($checkbox);
    }
  }

  $.fn.customCheckbox = function(options){
    return this.each(function(i,e){
      var fe = new customCheckbox(e, options);
    });
  };

  var dropDown = function(dropdown, options) {

    var settings = $.extend({
        maxItems: 5
    }, options || {});

    this.settings = settings;

    var $dropdown = $(dropdown);

    if($dropdown.parent('.form-element').length == 1){
      var activeVal = $dropdown.find('option:selected').text();
      var options = '';
      $dropdown.find('option').each(function(i,e){
        if($(e).text() == activeVal){
          options += '<li class="selected">'+$(e).text()+'</li>';
        } else {
          options += '<li>'+$(e).text()+'</li>';
        }
      });

      var newDropdown = $('<div class="form-element-dropdown">'+
      '<a href="#">'+activeVal+'</a>'+
      '<ul data-max-items="'+this.settings.maxItems+'">'+options+'</ul>'+
      '</div>');

      $dropdown.hide().parent('.form-element').append(newDropdown);
    }
  }
  function initDropdown(){
    $('body').on('click touchend', '.form-element-dropdown>a', function(e){
      e.preventDefault();
      var wrap = $(this).parent('.form-element-dropdown');
      var isOpen = false;

      if(wrap.hasClass('active')){
        isOpen = true;
      }

      $('.form-element-dropdown').each(function(i,e){
        if($(this).hasClass('active')){
          closeDropdown(this);
        }
      });

      if(!isOpen){
        openDropdown(wrap);
      }
    }).on('click touchend', '.form-element-dropdown ul li', function(e){
      e.preventDefault();
      var index = $(this).index(),
        val = $(this).text(),
        wrap = $(this).closest('.form-element-dropdown');

      wrap.children('ul')
        .find('li')
        .removeClass('selected')
        .eq(index)
        .addClass('selected');

      wrap.siblings('select')
        .find('option')
        .eq(index)
        .prop("selected",true);
      wrap.find('>a').text(val);

      wrap.siblings('select').trigger('change');

      closeDropdown(wrap);
    });

    $('body').on('click touchend', function(e){
      if($(e.target).closest('.form-element-dropdown').length == 0){

        $('.form-element-dropdown').each(function(i,e){
          if($(this).hasClass('active')){
            closeDropdown(this);
          }
        });
      }
    });
  }
  function openDropdown(dropdown){
    var $dropdown = $(dropdown);
    $dropdown.addClass('active');

    var itemHeight = $dropdown.find('li').eq(0).height(),
      itemNum = $dropdown.find('li').length,
      maxItems = $dropdown.find('ul').attr('data-max-items');

    //console.log(itemNum+'*'+itemHeight+'(max '+maxItems+')');
    if(itemNum > maxItems){
      $dropdown.find('ul').css({ 'height' : maxItems*itemHeight });
    }
  }

  function closeDropdown(dropdown){
    var $dropdown = $(dropdown);
    $dropdown.removeClass('active');
  }

  function log(){
    if (window.console && console.log)
      console.log('[FormElements] ' + Array.prototype.join.call(arguments,' '));
  }

  $.fn.dropdown = function(options){
    initDropdown();

    return this.each(function(i,e){
      var fe = new dropDown(e, options);
   });
  };


  var inputFile = function(input, options) {
    var settings = $.extend({

    }, options || {});

    this.settings = settings;

    var $input = $(input);

    if($input.parent('.form-element').length == 1){
      var label = $input.attr('data-label');
      var $btn = $('<div><span>'+label+'</span></div>');
      var $cross = $('<a href="#"></a>');
      $cross.on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $input.val('');
        $btn.removeClass('form-element-inputfile-filled');
        $(this).siblings('span').text(label);
      });

      $cross.addClass('form-element-inputfile-reset').appendTo($btn);
      $btn.addClass('form-element-inputfile');
      $btn.on('click', function(){
          $input.trigger('click');
      });

      $input.on('change', function(e){
        var fileName = '';

        if( this.files && this.files.length > 1 )
          fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{n}', this.files.length );
        else if( e.target.value )
          fileName = e.target.value.split( '\\' ).pop();

        if( fileName )
          $btn.addClass('form-element-inputfile-filled').find('span').text(fileName);
        else
          $btn.removeClass('form-element-inputfile-filled').find('span').text(label);
      });

      // Firefox bug fix
      $input
        .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
        .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });

      $input.addClass('input-file-hidden');
      $btn.appendTo($input.parent('.form-element'));
    }
  }

  $.fn.inputfile = function(options){
    return this.each(function(i,e){
      var fe = new inputFile(e, options);
    });
  };

})(jQuery);
