<span class="d-block d-sm-none" id="xsTest"></span>

<script>
    var baseUrl = '{{ url()->to('/') }}';
    var requestUrl = '{{ Request::url() }}';

    @if(auth()->guard('web')->check())
        var instanceId = {{ session('instanceId') }};
    @endif
</script>
@if(auth()->guard('web')->check())
    {{ HTML::script('js/app.js?v=' . env('ASSETS_VERSION', 5)) }}

    <!-- PUSH NOTIFICATIONS -->
    {{ HTML::script('/packages/firebase/app.js?v=' . env('ASSETS_VERSION', 5)) }}
@endif


{{ HTML::script('assets/js/jquery.min.js') }}
{{ HTML::script('assets/js/plugins/moment.min.js') }}
{{ HTML::script('assets/js/bootstrap/bootstrap.bundle.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-growl.min.js') }}
{{ HTML::script('js/laroute.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('assets/js/plugins/jquery.lazyload.min.js') }}
{{ HTML::script('assets/js/holder.js') }}
{{ HTML::script('assets/vendor/select2/js/select2.full.min.js') }}
{{ HTML::script('assets/vendor/select2/js/i18n/'.Lang::locale().'.js') }}
{{ HTML::script('assets/js/plugins/polyfiller.js') }}
{{ HTML::script('assets/js/plugins/jquery-scrolltofixed-min.js') }}
{{ HTML::script('assets/js/plugins/jquery.setCaret.js') }}
{{ HTML::script('assets/vendor/fullcalendar/js/fullcalendar.min.js') }}
{{ HTML::script('assets/vendor/fullcalendar/js/locale/'.Lang::locale().'.js') }}
{{ HTML::script('assets/vendor/netframe/4-jquery-form-elements.js') }}
{{ HTML::script('assets/vendor/netframe/5-popper.js') }}
{{-- HTML::script('assets/vendor/netframe/7-jquery-ui.js') --}}
{{ HTML::script('assets/vendor/netframe/jquery-ui.min.js') }}

{{ HTML::script('assets/vendor/jquery-mentions/jquery.mentions.js') }}
{{ HTML::script('assets/js/plugins/devices.js') }}


@if(isset($googleMapsKey))
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places"></script>
@endif
<!-- Start Attachment modal and bloc post -->
{{ HTML::script('packages/netframe/media/vendor/jquery-ui/jquery-ui.min.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/tmpl.min.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/load-image.all.min.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.iframe-transport.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload-process.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload-image.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload-audio.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload-video.js') }}
{{ HTML::script('packages/netframe/media/vendor/fileupload/jquery.fileupload-ui.js') }}
{{ HTML::script('packages/netframe/media/js/attachment-system.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('packages/netframe/media/js/modal-media.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('packages/netframe/post/js/jquery.autogrowtextarea.js') }}
{{ HTML::script('packages/netframe/media/vendor/jquery-bootpag/jquery.bootpag.min.js') }}
{{ HTML::script('packages/netframe/media/vendor/handlebars/handlebars.min.js') }}
{{ HTML::script('packages/netframe/post/js/posting.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('packages/netframe/media/js/explorer.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('packages/netframe/channels/js/channels.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('packages/netframe/map/mini-map-form.js') }}

<!-- End Attachment modal and bloc post -->


<!-- Start audio and video players -->
{{ HTML::script('packages/netframe/media/vendor/audiojs/audio.min.js') }}
{{ HTML::script('packages/netframe/media/vendor/videojs/video.js') }}
<script>videojs.options.flash.swf = "{{ asset('packages/netframe/media/vendor/videojs/video-js.swf') }}";</script>
<!-- End audio and video players -->

{{ HTML::script('assets/js/functions.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('assets/js/app.js?v=' . env('ASSETS_VERSION', 5)) }}
{{ HTML::script('assets/js/script.js?v=' . env('ASSETS_VERSION', 5)) }}

@if(auth()->guard('web')->check() && isset($activeChannel) && $activeChannel)
    <!--  socket io for global instance -->
    {{ HTML::script('assets/js/broadcasting.js?v=' . env('ASSETS_VERSION', 5)) }}
@endif

<script>

window.translations = {!! Cache::get('translations') !!};

{{--
// for custom crisp button
$crisp.push(['do', 'chat:hide']);
<button onclick="$crisp.push(['do', 'chat:open']); $crisp.push(['do', 'chat:show']);">Open chat</button>
<button onclick="$crisp.push(['do', 'chat:close']); $crisp.push(['do', 'chat:hide']);">Close chat</button>
--}}

if(!mobile){
    @if(in_array(Route::currentRouteName(), ['user.timeline', 'netframe.anynews']))
        @if(auth()->guard('web')->check() && isset($activeHelpDesk) and $activeHelpDesk)
            // crsip chat
            window.$crisp=[];
            window.CRISP_WEBSITE_ID="779a5d02-715b-48b1-8341-a2b8090bb56a";
            (function(){
                d=document;
                s=d.createElement("script");
                s.src="https://client.crisp.chat/l.js";
                s.async=1;
                d.getElementsByTagName("head")[0].appendChild(s);
            })();
        @endif
    @endif
}

// gdpr and local consent vars
var gdprConsent = {{ (isset($gdpr_agrement)) ? $gdpr_agrement : 0 }};
var gdprContentBlockedTxt = "{{ trans('netframe.privacyContent') }}";

var localeLang = "{{ Lang::locale() }}";
var current_profile_type = "";
var current_profile_id = "";
var channels = "";
var modalPosting = "";

$(document).ready(function() {
    @if(isset($modal_gdpr))
        $(window).on('load',function(){
            $('#modal-ajax').find('.modal-body').replaceWith('{!! $modal_gdpr !!}');
            $('#modal-ajax').modal('show');
        });

        $(document).on('click', '.fn-accept-gdpr', function(e){
            e.preventDefault();
            var modalBody = $(this).closest('.modal-body');
            var data = {
                gdpr: 1,
                fromModal: true
            };
            $.ajax({
                url: laroute.route('account.privacy'),
                data: data,
                type: "POST",
                success: function(data) {
                    modalBody.replaceWith(data.view);
                }
            });
        });
    @endif

    @if(isset($need_local_consent_content))
        $(window).on('load',function(){
            $('#modal-ajax-charter').modal('show');
        });
    @endif


    var postingSystem = $('#modal-ajax');
    modalPosting = new PostingSystem({
        $wrapper: postingSystem,
        $defaultTemplate: $('#template-post'),
        $defaultRoute: '{{ url()->route('posting.default') }}',
        $initFirstLoad: false,
        $modal: $('#modal-ajax'),
    });

    //========= GROWL NOTIFICATION SEE MACRO
    @if(session()->has('growl'))
    $.bootstrapGrowl("{{ HTML::growl(1) }}", {
        type: '{{ HTML::growl(0) }}',
        offset: {from: 'top', amount: 70},
        align: 'left',
        delay: 6000
    });
    @endif
});
@if(auth()->guard('web')->check() && $activeChannel)
    var channelSystem = $('channels');
    channels = new ChannelSystem({
        $wrapper: channelSystem,
        $channelId: {{ (isset($channel)) ? $channel->id : 0 }}
    });
    @endif
</script>

@if(auth()->guard('web')->check() && $activeChannel)
    @include('channel.js-views')
@endif

@if(isset($activeWorkflow) && $activeWorkflow)
    {{ HTML::script('packages/netframe/workflow/workflow.js?v=' . env('ASSETS_VERSION', 5)) }}
@endif

<!-- Templates for media upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-download mosaic-item col-md-2 col-3">
        <div class="mosaic-content">
            <img src="{{ asset('assets/img/no-media.jpg') }}" class="img-fluid">
        </div>
        <div class="mosaic-footer">
            <div class="progress progress-upload active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-upload" style="width:0%;"></div>
            </div>
        </div>
    </li>
{% } %}
</script>

<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    {% if (typeof file.fromXplorer != "undefined") { %}
        <li class="clearfix">
            <ul class="list-inline row">
            {% if(typeof file.error == "undefined") { %}
                <li class="list-inline-item template-download mosaic-item col-12 col-md-2 file-{%=file.id%} {% if (typeof file.fromXplorer != "undefined") { %}in{% } %} text-center" data-file-id="{%=file.id%}" data-media-type="local">
                    {% if (typeof file.fromXplorer == "undefined") { %}
                        <a class="fn-remove-media">
                            <span class="svgicon">
                                @include('macros.svg-icons.cancel')
                            </span>
                        </a>
                    {% } %}
                    {% if (file.mediaPlatform=="local") { %}
                        {% if (file.type=="0") { %}
                            <img src="/media/download/{%=file.id%}?thumb=1" class="img-fluid" />
                        {% } %}

                        {% if (file.type=="1") { %}
                            <img src="{{  asset('assets/img/icons/video.png') }}">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="2") { %}
                            <img src="{{  asset('assets/img/icons/audio.png') }}">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="3") { %}
                            <img src="{{  asset('assets/img/icons/file.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="4") { %}
                            <img src="{{  asset('assets/img/icons/folder.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="5") { %}
                            <img src="{{  asset('assets/img/icons/file.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="6") { %}
                            <img src="{{  asset('assets/img/icons/file.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="7") { %}
                            <img src="{{  asset('assets/img/icons/file.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}

                        {% if (file.type=="8") { %}
                            <img src="{{  asset('assets/img/icons/file.png') }}" class="img-fluid">
                            <p class="text-center">{%=file.name%}</p>
                        {% } %}
                    {% } %}

                    {% if (file.mediaPlatform=="vimeo") { %}
                        <img src="/media/download/{%=file.id%}?thumb=1" class="img-fluid" />
                    {% } %}

                    {% if (file.mediaPlatform=="youtube") { %}
                        <img src="/media/download/{%=file.id%}?thumb=1" class="img-fluid" />
                    {% } %}

                    {% if (file.mediaPlatform=="dailymotion") { %}
                        <img src="/media/download/{%=file.id%}?thumb=1" class="img-fluid" />
                    {% } %}

                    {% if (file.mediaPlatform=="soundcloud") { %}
                        <img src="/media/download/{%=file.id%}?thumb=1" class="img-fluid" />
                    {% } %}
                </li>
                {% if (typeof file.fromXplorer != "undefined") { %}
                    <input type="hidden" name="file-id[]" value="{%=file.id%}">
                    <input type="hidden" name="replace-{%=file.id%}" value="{%=file.replaceFile%}">
                    <input type="hidden" name="originalId-{%=file.id%}" value="{%=file.originalId%}">
                    <li class="list-inline-item col-12 col-md-10 nf-form nf-col-2 file-{%=file.id%}" data-file-id="{%=file.id%}">
                        {% if (1 == 0) { %}
                            <a class="fn-remove-media float-right">
                                <span class="svgicon">
                                    @include('macros.svg-icons.cancel')
                                </span>
                            </a>
                        {% } %}
                        <label class="nf-form-cell">
                            <input type="text" name="filename-{%=file.id%}" value="{%=file.name%}" class="nf-form-input">
                            <span class="nf-form-label">
                                {{ trans('xplorer.file.add.filename') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                        <label class="nf-form-cell">
                            <input type="text" name="author-{%=file.id%}" class="nf-form-input">
                            <span class="nf-form-label">
                                {{ trans('xplorer.file.add.author') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                        <label class="nf-form-cell">
                            <textarea name="description-{%=file.id%}" class="nf-form-input"></textarea>
                            <span class="nf-form-label">
                                {{ trans('xplorer.file.add.description') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                        <label class="nf-form-cell tags-add">
                            <select name="tags-{%=file.id%}[]" class="nf-form-input fn-select2-tag" id="tags{%=file.id%}" multiple="multiple"></select>
                            <span class="nf-form-label">
                                {{ trans('xplorer.file.add.tags') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                    </li>
                </ul>
                </li>
            {% } %}
        {% } %}
        {% if (typeof file.error != "undefined") { %}
            <li class="alert alert-danger col-md-12 text-center">
                <strong>{%=file.name%}</strong> : {%=file.error%}
            </li>
        {% } %}
    {% } %}
    {% if (typeof file.fromXplorer == "undefined") { %}
        {% if(typeof file.error == "undefined") { %}
            <li class="file-{%=file.id%}" data-file-id="{%=file.id%}" data-media-type="local">
                {% if (file.mediaPlatform=="local") { %}
                    {% if (file.type=="0") { %}
                        <img src="/media/download/{%=file.id%}?thumb=1" />
                    {% } %}

                    {% if (file.type=="1") { %}
                        <img src="{{  asset('assets/img/icons/video.png') }}">
                    {% } %}

                    {% if (file.type=="2") { %}
                        <img src="{{  asset('assets/img/icons/audio.png') }}">
                    {% } %}

                    {% if (file.type=="3") { %}
                        <img src="{{  asset('assets/img/icons/file.png') }}">
                    {% } %}

                    {% if (file.type=="4") { %}
                        <img src="{{  asset('assets/img/icons/folder.png') }}">
                    {% } %}

                    {% if (file.type=="5") { %}
                        <img src="{{  asset('assets/img/icons/file.png') }}">
                    {% } %}

                    {% if (file.type=="6") { %}
                        <img src="{{  asset('assets/img/icons/file.png') }}">
                    {% } %}

                    {% if (file.type=="7") { %}
                        <img src="{{  asset('assets/img/icons/file.png') }}">
                    {% } %}

                    {% if (file.type=="8") { %}
                        <img src="{{  asset('assets/img/icons/file.png') }}">
                    {% } %}
                {% } %}

                {% if (file.mediaPlatform=="vimeo") { %}
                    <img src="/media/download/{%=file.id%}?thumb=1" />
                {% } %}

                {% if (file.mediaPlatform=="youtube") { %}
                    <img src="/media/download/{%=file.id%}?thumb=1" />
                {% } %}

                {% if (file.mediaPlatform=="dailymotion") { %}
                    <img src="/media/download/{%=file.id%}?thumb=1" />
                {% } %}

                {% if (file.mediaPlatform=="soundcloud") { %}
                    <img src="/media/download/{%=file.id%}?thumb=1" />
                {% } %}
                <p>{%=file.name%}</p>
                <a class="close fn-remove-media">
                    <span class="svgicon">
                        @include('macros.svg-icons.close')
                    </span>
                </a>
            </li>
        {% } %}

        {% if (typeof file.error != "undefined") { %}
            <li class="alert alert-danger col-md-12 text-center">
                <strong>{%=file.name%}</strong> : {%=file.error%}
            </li>
        {% } %}
    {% } %}
{% } %}
</script>

<!-- Template to display a single media imported-->
<script id="template-media-import-loader" type="text/x-handlebars-template">
    <li class="import-loader" id="import-loader-@{{ elementId }}">
        <span class="svgicon">
            @include('macros.svg-icons.loader')
        </span>
    </li>
</script>

<script id="template-media-import" type="text/x-handlebars-template">
    <li class="file-@{{ media.id }}" data-file-id="@{{ media.id }}" data-media-type="import">
        @{{#if_eq media.mediaPlatform "vimeo"}}
            <img src="/media/download/@{{ media.id }}?thumb=1" class="img-fluid" />
        @{{/if_eq}}

        @{{#if_eq media.mediaPlatform "youtube"}}
            <img src="/media/download/@{{ media.id }}?thumb=1" class="img-fluid" />
        @{{/if_eq}}

        @{{#if_eq media.mediaPlatform "dailymotion"}}
            <img src="/media/download/@{{ media.id }}?thumb=1" class="img-fluid" />
        @{{/if_eq}}

        @{{#if_eq media.mediaPlatform "soundcloud"}}
            <img src="/media/download/@{{ media.id }}?thumb=1" class="img-fluid" />
        @{{/if_eq}}
        <p>@{{ media.mediaPlatform }}</p>
        <a class="close fn-remove-media">
            <span class="svgicon">
                @include('macros.svg-icons.close')
            </span>
        </a>
    </li>
</script>

<!-- Template to display a media when selected by GED finder in task add media modal-->
<script id="template-media-select-tasks" type="text/x-handlebars-template">
    @{{#each files}}
        <li class="clearfix">
            <ul class="list-inline row">
                <li class="list-inline-item template-download mosaic-item col-md-2 col-3 file-@{{ id }} in" data-file-id="@{{ id }}" data-media-type="local">
                    <img src="/media/download/@{{ id }}?thumb=1" class="img-fluid" />
                </li>
                <input type="hidden" name="file-id[]" value="@{{ id }}">
                <input type="hidden" name="replace-@{{ id }}" value="0">
                <input type="hidden" name="originalId-@{{ id }}" value="@{{ id }}">
                <li class="list-inline-item col-md-10 col-9 file-@{{ id }}" data-file-id="@{{ id }}">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ trans('xplorer.file.add.filename') }}
                                </div>
                            </div>
                            <input type="text" name="filename-@{{ id }}" value="@{{ name }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ trans('xplorer.file.add.tags') }}
                                </div>
                            </div>
                            <select name="tags-@{{ id }}[]" class="form-control fn-select2-tag" id="tags@{{ id }}" multiple="multiple"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ trans('xplorer.file.add.author') }}
                                </div>
                            </div>
                            <input type="text" name="author-@{{ id }}" class="form-control" value="@{{ meta_author }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    {{ trans('xplorer.file.add.description') }}
                                </div>
                            </div>
                            <textarea name="description-@{{ id }}" class="form-control">@{{ description }}</textarea>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
    @{{/each}}
</script>

<!-- Template to display a media when selected by GED finder in post content-->
<script id="template-media-select" type="text/x-handlebars-template">
    <li class="file-@{{ media.id }}" data-file-id="@{{ media.id }}" data-media-type="local">
        <img src="/media/download/@{{ media.id }}?thumb=1" class="img-fluid" />
        <p>@{{ media.name }}</p>
        <a class="close fn-remove-media">
            <span class="svgicon">
                @include('macros.svg-icons.close')
            </span>
        </a>
    </li>
</script>

<!-- Template to display a link near the textarea, when typed in-->
<script id="template-link-preview" type="text/x-handlebars-template">
    <div class="link-preview" id="import-link-@{{linkId}}" data-id="@{{linkId}}">
        <div class="link-visual import-loader" style="background-image:url('/link-preview/download/@{{linkId}}');">
            @{{#if_eq screenPath "pending"}}
                <span class="svgicon">
                    @include('macros.svg-icons.loader')
                </span>
            @{{/if_eq}}
        </div>
        <div class="link-infos">
            <div class="nf-close fn-remove-link">
            </div>
            <div class="link-info">
                <h4 class="info-title">@{{ title }}</h4>
                <p class="info-desc">@{{ desc }}</p>
            </div>
            <div class="link-links">
                <a href="@{{ final_url }}" target="_blank" class="link-info-url">@{{ url }}</a>
            </div>
        </div>
    </div>
</script>
