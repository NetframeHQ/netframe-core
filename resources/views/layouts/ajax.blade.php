<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <title>@yield('title', ":: Netframe Page ::")</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{ HTML::script('assets/js/modernizr.custom-2.8.3.js') }}
    {{ HTML::style('assets/css/bootstrap.css') }}
    {{ HTML::style('assets/css/animate.css') }}
    {{ HTML::style('assets/css/socicon.css') }}
    {{ HTML::style('assets/css/bootstrap-glidpanel.css') }}
    {{ HTML::style('assets/css/netframe.css') }}
    {{ HTML::style('assets/vendor/flexslider/flexslider.css') }}
    {{ HTML::style('assets/css/page-slide.css') }}
    {{ HTML::style('assets/css/page-picture.css') }}

    <!-- Start Attachment modal -->
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/attachment-modal.css') }}">
    <!-- End Attachment modal -->

    @yield('stylesheets')
    <!-- [if lt IE 9]>
    {{ HTML::script('assets/js/html5shiv.min.js') }}
    <![endif]-->
</head>
<body>
@include('netframe.modal-ajax')

<div id="wrap" class="clearfix">
    <div class="container-fluid">

        <section class="col-md-12">
            @yield('content')
        </section>

    </div>
</div>

<!-- Javascript -->
{{ HTML::script('assets/js/jquery.min.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-growl.min.js') }}
{{ HTML::script('js/laroute.js') }}
{{ HTML::script('assets/js/plugins/jquery.glidpanel.js') }}
{{ HTML::script('assets/js/holder.js') }}
{{ HTML::script('assets/vendor/flexslider/jquery.flexslider-min.js') }}
{{ HTML::script('assets/js/app.js') }}

<!-- Start Attachment modal -->
<script src="{{ asset('packages/netframe/media/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/tmpl.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/load-image.all.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload-process.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload-image.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload-audio.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload-video.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/fileupload/jquery.fileupload-ui.js') }}"></script>
<script src="{{ asset('packages/netframe/media/js/attachment-modal.js') }}"></script>
<!-- End Attachment modal -->

<script>
$(document).ready(function() {

    //========= GROWL NOTIFICATION SEE MACRO
    @if (session()->has('growl'))
    $.bootstrapGrowl("{{ HTML::growl(1) }}", {
        type: '{{ HTML::growl(0) }}',
        offset: {from: 'top', amount: 70},
        align: 'left',
        delay: 5000
    });
    @endif

    //========= Setup the Attachment Modal in the navigation.
    (function () {
        var attachmentModal = $('#navigationAttachMediaModal');
        new AttachmentModal({
            $wrapper: attachmentModal,
            $fileUpload: attachmentModal.find('#fileupload'),
            $postAs: attachmentModal.find('#post-has'),
            $importForm: attachmentModal.find('#import'),
            $importHelp: attachmentModal.find('#importUrl .help-block'),
            $importUrlGroup: attachmentModal.find('#importUrl'),
            $importUrl: attachmentModal.find('input[name=url]'),
            $profileMedia: 0,
            $autoUpload: 1,
            $confidentiality: attachmentModal.find('input:radio[name=navigationAttachMediaModalConfidentiality]')
        });
     })();


    //========= Select Profile & Set in Element Publish AS
    $(document).on('click', ".f-select-profile", function(e) {
        e.preventDefault();

        var el = $(this);
        var targetID = el.attr('href');
        var dataProfileType = el.data('profile');
        var dataProfileId = el.data('profile-id');
        var targetForm = el.parents('#modal-ajax').find('form:first');

        var inputHiddenIdForeign = targetForm.find('input[name="id_foreign"]');
        var inputHiddenTypeForeign = targetForm.find('input[name="type_foreign"]');

        $(targetID).text(dataProfileType + ' : ' + el.text());
        inputHiddenIdForeign.val(dataProfileId);
        inputHiddenTypeForeign.val(dataProfileType);

    });

    //========= Fixed & Hack Modal Bootstrap, Reload Content & Remove Content of modal when click open
    $(document).on('hidden.bs.modal', '#modal-ajax', function (e) {
        $(e.target).removeData('bs.modal');
    });

    //========= Post Ajax Form for Modal publish and return response
    $('#modal-ajax').on('click', 'button[type="submit"]', function(event) {
        event.preventDefault();

        var modalId = '#modal-ajax';
        var modalContent = $('#modal-ajax .modal-content');
        var _form = $(this).parents('.modal-content').find('form');
        var actionUrl = _form.attr('action');
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: "{{ Request::url() }}"
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

                if(typeof data.reload != 'undefined' && data.reload===true) {
                    document.location.reload();
                }

                // If publish model is A COMMENTARY, getback response
                if(typeof data.viewComment != 'undefined') {
                    $(modalId).modal('hide');

                    var elTarget = $(data.targetId);

                    // stop duplicate add when is in Ajax
                    event.stopPropagation();
                    // If is COMMENT in Insert Mode
                    if(typeof data.edit != "undefined") {

                        elTarget.fadeOut('slow', function() {
                            $(this).replaceWith(data.viewComment);
                            elTargetId.fadeIn('slow');
                        });

                    } else {
                    // If is COMMENT in Edit Mode
                        $(data.viewComment).appendTo($('.comments', elTarget)).hide().slideDown('normal');
                    }
                }

            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });

    });
});
</script>

@yield('javascripts')

</body>
</html>
