(function ($) {
  // $('select').dropdown({maxItems: 5});
  // $('input[type=file]').inputfile();

  $('.sidebar-toggle').on('click', function (e) {
    e.preventDefault();
    
    if ($('body').hasClass('show-sidebar')) {
      $('body').removeClass('show-sidebar');
    } else {
      $('body').addClass('show-sidebar');
    }
  });

  /*
  $('.content-sidebar-toggle').on('click', function(e){
    e.preventDefault();
    $('body').toggleClass('show-content-sidebar');
  });
  */

  $('.navigation-search .search-input').on('focus', function () {
    $(this).closest('.navigation-search').addClass('focus');
  }).on('blur', function () {
    $(this).closest('.navigation-search').removeClass('focus');
  });

  // Submenus
  $(document).on('click', '.menu-wrapper .fn-menu', function (e) {
    e.preventDefault();
    var parent = $(this).parent('.menu-wrapper');

    if (!parent.hasClass('active')) {
      $('.menu-wrapper').removeClass('active');
      parent.addClass('active');
    } else {
      $('.menu-wrapper').removeClass('active');
    }
  });

  $(document).on('click', 'body', function (e) {
    if ($(e.target).closest('.menu-wrapper').length < 1) {
      $('.menu-wrapper').removeClass('active');
    }
  });

  // Inputs
  $('#panel-textarea').on('keyup', function () {
    if ($(this).val().length >= 1) {
      $(this).closest('.panel').find('button[type=submit]').prop('disabled', false);
    } else {
      $(this).closest('.panel').find('button[type=submit]').prop('disabled', true);
    }
  });

  // search filters
  $('.search-filters-toggle input[type=checkbox]').on('change', function (e) {
    var input = $(this);
    if (input.prop('checked') == true) {
      input.parent('label').addClass('active');
    } else {
      input.parent('label').removeClass('active');
    }
  });
  $('.search-filters-types-toggle input[type=checkbox]').on('change', function (e) {
    var input = $(this);
    if (input.prop('checked') == true) {
      input.parent('label').addClass('active');
    } else {
      input.parent('label').removeClass('active');
    }
  });

  // Talk height
  var talk = {
    input: $('.talk-input'),
    scroller: $('.main-scroller-talk'),
    content: $('.talk'),

    init: function () {
      var self = this;

      self.resizeScroller();
      //self.scrollerBottom();

      $(window).on('resize', function () {
        self.resizeScroller();
      });

      self.input.on('resize', function () {
        self.resizeScroller();
      });
    },
    scrollerBottom: function () {
      var self = this;

      var bottom = self.content.outerHeight();
      self.scroller.animate({ 'scrollTop': bottom + 'px' }, 0);
    },
    resizeScroller: function () {
      var self = this;
      var inputHeight = self.input.outerHeight();

      self.scroller.css('bottom', inputHeight);
    }
  };
  talk.init();

  //Custom checkboxes
  $('input[type=checkbox]').customCheckbox();

  // TMP (sidebar click)
  $('#talkgroups a:not(.more)').on('click', function (e) {
    e.preventDefault();
    $('#talkgroups a:not(.more)').removeClass('active');
    $(this).addClass('active');
  });

  // Toggle options
  $(document).on('click', '.panel-options-toggle', function (e) {
    $(this).parent('.panel-options-wrapper').toggleClass('show-options');
  });

  $('body').on('click', function (e) {
    if ($(e.target).closest('.panel-options-wrapper').length < 1) {
      $('.panel-options-wrapper').removeClass('show-options');
    }
  });

  //Panel tabs dropdown
  $(document).on('click', '.panel-tabs-mobile', function (e) {
    $(this).parent().toggleClass('show-mobile-tabs');
  });

  $(document).on('click', '.panel-tabs-mobile', function (e) {
    var content = $(this).html();
    console.log(content);
    $(this).closest('.panel-tabs-wrapper').find('.panel-tabs-mobile-content').html(content);
  });

  $('body').on('click', function (e) {
    if ($(e.target).closest('.panel-tabs-mobile').length < 1) {
      $('.panel-tabs-mobile').parent('.panel-tabs-wrapper').removeClass('show-mobile-tabs');
    }
  });

  //Offer type buttons
  $(document).on('change', '.panel-post-offer-type input[type="radio"]', function () {
    $(this).closest('.panel-post-offer-type').find('label').removeClass('active');
    $(this).parent('label').addClass('active');
  });

})(jQuery);
