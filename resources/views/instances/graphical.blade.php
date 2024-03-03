@extends('instances.main')

@section('title')
    {{ trans('instances.graphical.title') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    @parent
    {{ HTML::style('assets/vendor/spectrum/spectrum.css') }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@stop

@section('subcontent')
    <!-- COVER PICTURE  -->
    <div class="nf-form-cell nf-cell-cover nf-cell-full cover_image"  style="{{ (!empty($coverImage) ? 'background-image:url(\'' . $coverImage . '\')' : '')}}" id="cover_image">
        <!-- IF COVER  -->
        <ul class="nf-actions {{ ((empty($coverImage)) ? 'd-none' : '')}}" id="updel-cover">
            <li class="nf-action">
                <a class="nf-btn btn-ico" id="fn-replace-cover">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.edit')
                    </span>
                </a>
            </li>
            <li class="nf-action">
                <a class="nf-btn btn-ico" id="fn-delete-instance-img" data-image-type="cover_image">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.trash')
                    </span>
                </a>
            </li>
        </ul>

        <!-- ELSE PLACEHOLDER -->
        <div class="cover-placeholder {{ ((!empty($coverImage)) ? 'd-none' : '') }}">
            <ul class="nf-actions">
                <li class="nf-action">
                    <label class="nf-btn btn-nobg">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.plus')
                        </span>
                        <span class="btn-txt">
                            {{ trans('profiles.manage.addCoverImage') }}
                        </span>
                        @include('instances.partials.form-upload', [
                            'image' => 'cover_image',
                            'finalField' => 'menuLogoFile',
                            'customButton' => '',
                            'inBackground' => 1
                        ])
                    </label>
                </li>
                {{--
                <li class="sep">
                    <i></i>
                </li>
                <li class="nf-action">
                    <label class="nf-btn btn-nobg">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.search')
                        </span>
                        <input type="text" class="btn-input" placeholder="{{ trans('profiles.manage.searchFromUnslpash') }}">
                        <input type="submit" class="btn-input-submit" value="{{ trans('profiles.manage.searchUnslpashTxtButton') }}">
                    </label>
                </li>
                --}}
            </ul>
        </div>
    </div>
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            <h2>{{ trans('instances.graphical.title') }}</h2>
        </div>
        <div class="nf-form-informations">
            <fieldset class="custom-instance">
                <div class="{{ ((!$themeParameters['switchable']) ? 'd-none' : '') }}">
                    <div class="test-mode-light d-none">
                        <label class="nf-form-cell nf-form-checkbox" for="test-mode-light">
                            {{ Form::checkbox('test-mode-light', 'light', '', ['id' => 'test-mode-light', 'class' => 'nf-form-input']) }}
                            <span class="nf-form-label">
                                {{ trans('instances.graphical.testLightMode') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                    </div>
                    <div class="test-mode-dark d-none">
                        <label class="nf-form-cell nf-form-checkbox" for="test-mode-dark">
                            {{ Form::checkbox('test-mode-dark', 'dark', '', ['id' => 'test-mode-dark', 'class' => 'nf-form-input']) }}
                            <span class="nf-form-label">
                                {{ trans('instances.graphical.testDarkMode') }}
                            </span>
                            <div class="nf-form-cell-fx"></div>
                        </label>
                    </div>
                </div>
                <div class="theme-light mb-4">
                    <h3>{{ trans('instances.graphical.titleLight') }}</h3>
                    @include('instances.partials.logos-uploads', [
                        'typeTheme' => 'light',
                        'menuLogo' => $menuLogo,
                        'mainLogo' => $mainLogo,
                        'defaultMainLogo' => $defaultMainLogo,
                    ])
                    @include('instances.partials.color-theme', [
                        'typeTheme' => 'light',
                        'additionnalType' => ''
                    ])
                </div>

                <div class="theme-dark {{ ((!$themeParameters['switchable']) ? 'd-none' : '') }}  mb-4">
                    <h3>{{ trans('instances.graphical.titleDark') }}</h3>
                    @include('instances.partials.logos-uploads', [
                        'typeTheme' => 'dark',
                        'menuLogo' => $menuLogoDark,
                        'mainLogo' => $mainLogoDark,
                        'mainLogoLight' => $mainLogo,
                        'defaultMainLogo' => $defaultMainLogoDark,
                    ])
                    @include('instances.partials.color-theme', [
                        'typeTheme' => 'dark',
                        'additionnalType' => 'Dark'
                    ])
                </div>
                <div class="text-center mg-top-10 col-md-12">
                    {{ Form::open(['route' => ['instance.graphical', 'customType' => 'colors'], 'id' => 'allColors']) }}
                        {{-- Form to store all fields values --}}
                        {{ Form::hidden('primaryColor', $cssColors['primaryColor']) }}
                        {{ Form::hidden('accentColor', $cssColors['accentColor']) }}
                        {{ Form::hidden('bgColor', $cssColors['bgColor']) }}
                        {{ Form::hidden('baseColor', $cssColors['baseColor']) }}
                        {{ Form::hidden('primaryColorDark', $cssColors['primaryColorDark']) }}
                        {{ Form::hidden('accentColorDark', $cssColors['accentColor']) }}
                        {{ Form::hidden('bgColorDark', $cssColors['bgColor']) }}
                        {{ Form::hidden('baseColorDark', $cssColors['baseColorDark']) }}
                        {{ Form::hidden('disableMode', '') }}
                        {{ Form::submit(trans('instances.graphical.validBgLogo'), ['class' => 'button primary fn-submit-colors']) }}
                    {{ Form::close() }}
                </div>
                <div class="text-center mt-2">
                    <a href="#" class="fn-reset-colors">{{ trans('instances.graphical.resetColors') }}</a>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            <h2>{{ trans('instances.graphical.bgScreen') }}</h2>
        </div>
        <div class="nf-form-cell nf-cell-cover nf-cell-full background_login_2018"  style="{{ (!empty($bgScreen) ? 'background-image:url(\'' . $bgScreen . '\')' : '')}}" id="background_login_2018">
            <!-- Background  -->
            <ul class="nf-actions {{ ((empty($bgScreen)) ? 'd-none' : '')}}" id="updel-cover">
                <li class="nf-action">
                    <a class="nf-btn btn-ico" id="fn-replace-background">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.edit')
                        </span>
                    </a>
                </li>
                <li class="nf-action">
                    <a class="nf-btn btn-ico" id="fn-delete-instance-img" data-image-type="background_login_2018">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.trash')
                        </span>
                    </a>
                </li>
            </ul>

            <!-- ELSE PLACEHOLDER -->
            <div class="cover-placeholder {{ ((!empty($bgScreen)) ? 'd-none' : '') }}">
                <ul class="nf-actions">
                    <li class="nf-action">
                        <label class="nf-btn btn-nobg">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.plus')
                            </span>
                            <span class="btn-txt">
                                {{ trans('instances.graphical.addBackgroundImage') }}
                            </span>
                            @include('instances.partials.form-upload', [
                                'image' => 'background_login_2018',
                                'finalField' => 'menuLogoFile',
                                'customButton' => '',
                                'inBackground' => 1
                            ])
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            <h2>{{ trans('instances.graphical.themes.title') }}</h2>
        </div>
        <div class="nf-form-informations">
            @foreach($allThemes['groups'] as $groupKey=>$group)
                <h3 class="theme">{{ trans('instances.graphical.themes.groups.' . $groupKey) }}</h3>
                <div class="row themes-group">
                    @foreach($group as $themeSlug)
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3 theme">
                            <label class="nf-form-cell nf-form-checkbox" for="selected_theme_{{ $themeSlug }}">
                                {{ Form::checkbox(
                                    'selected_theme_' . $themeSlug,
                                    $themeSlug,
                                    ($themeSlug == $currentTheme),
                                    [
                                        'id' => 'selected_theme_' . $themeSlug,
                                        'class' => 'nf-form-input fn-select-theme',
                                        'data-url' => url()->route('instance.graphical.theme', ['slug' => $themeSlug]),
                                    ]
                                ) }}
                                <span class="nf-form-label">
                                    <strong>{{ trans('instances.graphical.themes.themes.' . $themeSlug) }}</strong>
                                </span>
                                <div class="nf-form-cell-fx"></div>
                            </label>
                            @if(File::exists(public_path('css/theme/' . $themeSlug . '/preview.png')))
                                <img src="/css/theme/{{ $themeSlug }}/preview.png" class="img-fluid">
                            @endif
                        </div>
                    @endforeach
                </div>
                <hr>
            @endforeach

            <!-- {{-- -->
            <fieldset class="custom-instance">
                <legend>
                    {{ trans('instances.graphical.likeButton') }}
                </legend>
                <div class="form-group row">
                    <div class="col-md-12">
                        {{ Form::open(['route' => ['instance.graphical', 'customType' => 'buttons']]) }}
                            <div class="text-center mg-top-10">
                                {{ Form::label('emoji', trans('instances.graphical.reactions')) }}
                                <br>
                                {{ Form::text('reactions', request()->old('reactions'),
                                    $attributes = $errors->has('reactions') ? ['class' => 'form-control is-invalid', 'id'=>'emojis-reactions'] : ['class' => 'form-control', 'id'=>'emojis-reactions']) }}
                                <small class="invalid-feedback">{{trans('instances.graphical.'.$errors->first('reactions'))}}</small>
                                <br/>
                            </div>
                            @include('components.emojis.emojis', ['emojiTarget' => '#emojis-reactions', 'fromAjax' => false])
                            <div class="text-center mg-top-10">
                                {{ Form::Submit(trans('instances.graphical.valid'), ['class' => 'button primary']) }}
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </fieldset>
            <!-- --}} -->
        </div>
    </div>

@stop

@section('javascripts')
@parent
    {{ HTML::script('packages/netframe/media/js/instances-medias.js') }}
    {{ HTML::script('assets/vendor/spectrum/spectrum.js') }}
    {{ HTML::script('assets/vendor/spectrum/i18n/jquery.spectrum-'.Lang::locale().'.js') }}

    <script>
        @php
            $themeModeForced = '';
            if (!$themeParameters['switchable']) {
                $themeModeForced = 'light';
            } else {
                if (isset($paramCss['disableMode']) && !empty($paramCss['disableMode'])) {
                    if ($paramCss['disableMode'] == 'dark') {
                        $themeModeForced = 'light';
                    } elseif ($paramCss['disableMode'] == 'light') {
                        $themeModeForced = 'dark';
                    }
                }
            }
        @endphp
        var themeMode = '';
        var themeModeForced = '{{ $themeModeForced }}';
        var disableLight = false;
        var disableDark = {{ ($themeParameters['switchable']) ? 'false' : 'true' }};

        $(document).on('change', 'input.fn-select-theme', function(e) {
            $('input.fn-select-theme').not($(this)).prop("checked", false);
            window.location.href = $(this).data('url');
        });

        $(document).on('change', 'input.fn-image-choice', function(e){
            if($(this).is(':checked')){
                var checkedState = $(this).val();
                var concernedImage = 'image-'+$(this).attr('name');
                if(checkedState == 1){
                    $('.'+concernedImage).removeClass('d-none');
                }
                else if(checkedState == 0){
                    $('.'+concernedImage).addClass('d-none');
                }
            }
        });

        $(document).on('change', '.fn-change-navtheme', function(e){
            var theme = $(this).val();
            if(theme == 'dark'){
                $('#navigation').addClass('dark');
            }
            else{
                $('#navigation').removeClass('dark');
            }
        });

        var themeColors = {!! json_encode($cssColorsTheme) !!};

        $(".color-picker").each(function(elem){
            var picker = $(this);
            var color = $(this).val();
            picker.spectrum({
                preferredFormat: "hex",
                showInput: true,
                color: color,
                change: function(color) {
                    var newColor = color.toHexString();
                    picker.val(newColor);
                },
                hide: function(color){
                    changeColors(picker, color)
                },
                move: function(color){
                    changeColors(picker, color)
                }
            });
        });

        function changeColors(picker, color) {
            var primaryColor = hexToRgb($('input[name="primaryColor"]').val());
            var primaryColorDark = hexToRgb($('input[name="primaryColorDark"]').val());
            var accentColor = hexToRgb($('input[name="accentColor"]').val());
            var accentColorDark = hexToRgb($('input[name="accentColorDark"]').val());
            var bgColor = hexToRgb($('input[name="bgColor"]').val());
            var bgColorDark = hexToRgb($('input[name="bgColorDark"]').val());
            var baseColor = hexToRgb($('input[name="baseColor"]').val());
            var baseColorDark = hexToRgb($('input[name="baseColorDark"]').val());

            // get current modified color to override preview var
            if (picker != false) {
                eval(picker.attr('name') + " = color.toRgbString().replace('rgb(', '').replace(')', '')");
            }

            // manage light / dark mode force preview
            if (themeMode != themeModeForced){
                if (themeModeForced == 'light') {
                    primaryColorDark = primaryColor;
                    accentColorDark = accentColor;
                    bgColorDark = bgColor;
                    baseColorDark = baseColor;
                } else if (themeModeForced == 'dark') {
                    primaryColor = primaryColorDark;
                    accentColor = accentColorDark;
                    bgColor = bgColorDark;
                    baseColor = baseColorDark;
                }
            }

            $('head style').remove();
            var styleString = '@media (prefers-color-scheme: light) {' +
                ':root {' +
                '    --nf-primaryColor: ' + primaryColor + ' !important;' +
                '    --nf-bgColor: ' + bgColor + ' !important;' +
                '    --nf-accentColor: ' + accentColor + ' !important;' +
                '    --nf-baseColor: ' + baseColor + ' !important;' +
                '}' +
            '}' +
            '@media (prefers-color-scheme: dark) {' +
                ':root {' +
                '    --nf-primaryColor: ' + primaryColorDark + ' !important;' +
                '    --nf-bgColor: ' + bgColorDark + ' !important;' +
                '    --nf-accentColor: ' + accentColorDark + ' !important;' +
                '    --nf-baseColor: ' + baseColorDark + ' !important;' +
                '}' +
            '}';

            $('head').append('<style type="text/css">' + styleString + '</style>');
        }

        function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if (result) {
                return parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16);
            } else {
                return null;
            }
        }

        // test dark mode on init and when change
        function testDarkMode() {
            let matched = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (matched) {
                // dark mode on
                themeMode = 'dark';
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', testDarkMode);
                $('.test-mode-light').removeClass('d-none');
                $('.test-mode-dark').addClass('d-none');
            } else {
                // dark mode off
                themeMode = 'light';
                window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', testDarkMode);
                $('.test-mode-dark').removeClass('d-none');
                $('.test-mode-light').addClass('d-none');
            }
            changeColors(false, false);
        }
        testDarkMode();

        // preview for light / dark mode
        $(document).on('change', 'input[type="checkbox"][name^="test-mode-"]', function(e) {
            if ($(this).is(':checked')) {
                themeModeForced = $(this).val();
            } else {
                themeModeForced = '';
            }
            changeColors(false, false);
        });

        // manage disable mode
        $(document).on('change', 'input[name="disable_mode"]', function(e) {
            if ($(this).is(':checked')) {
                // uncheck other checkbox for disabling mode
                $('input[name="disable_mode"][value!="' + $(this).val() + '"]').prop("checked", false );
                // preset live preview
                if ($(this).val() == 'disable_mode_light') {
                    themeModeForced = 'dark';
                    $('form#allColors input[name="disableMode"]').val('light');
                }
                if ($(this).val() == 'disable_mode_dark') {
                    themeModeForced = 'light';
                    $('form#allColors input[name="disableMode"]').val('dark');
                }
                $('div.test-mode-light').addClass('d-none');
            } else if (!$('input[name="disable_mode"][value!="' + $(this).val() + '"]').is(":checked")){
                themeModeForced = '';
                $('form#allColors input[name="disableMode"]').val('');
                $('div.test-mode-light').removeClass('d-none');
            }
            changeColors(false, false);
        });

        // reset default theme colors
        $(document).on('click', '.fn-reset-colors', function(e){
            e.preventDefault();
            $.each(themeColors, function(key,value) {
                $('input[name="'+ key +'"]').spectrum('set', value);
            });
            changeColors(false, false);
        });

        // submit color form
        $(document).on('submit', 'form#allColors', function(e) {
            // get fields from others forms
            $('form#allColors input[name="primaryColor"]').val($('form#colors input[name="primaryColor"]').val());
            $('form#allColors input[name="accentColor"]').val($('form#colors input[name="accentColor"]').val());
            $('form#allColors input[name="bgColor"]').val($('form#colors input[name="bgColor"]').val());
            $('form#allColors input[name="primaryColorDark"]').val($('form#colorsDark input[name="primaryColorDark"]').val());
            $('form#allColors input[name="accentColorDark"]').val($('form#colorsDark input[name="accentColorDark"]').val());
            $('form#allColors input[name="bgColorDark"]').val($('form#colorsDark input[name="bgColorDark"]').val());
        });

        // manage cover image
        $(document).on('click', '#fn-replace-cover', function(e){
            e.preventDefault();
            $('#cover_image .fileinput-button').trigger('click');
        });

        $(document).on('click', 'a#fn-delete-instance-img', function(e){
            e.preventDefault();
            var deleteLink = $(this);

            var mediaType = $(this).data('image-type');

            var ajaxData = {
                mediaType: mediaType
            };

            $.ajax({
                url: laroute.route('instances.remove.media'),
                data: ajaxData,
                type: "POST",
                success: function (data) {
                    var container = $("#"+mediaType);
                    console.log(container);
                    if(mediaType == 'cover_image'){
                        container.css('background-image', 'none');
                        container.find('#updel-cover').addClass('d-none');
                        container.find('.cover-placeholder').removeClass('d-none');
                    } else if (mediaType == 'background_login_2018'){
                        container.css('background-image', 'none');
                        container.find('#updel-cover').addClass('d-none');
                        container.find('.cover-placeholder').removeClass('d-none');
                    }
                    else{
                        var defaultImg = container.find('img').data('default-img');
                        container.find('img').attr('src', defaultImg);
                        container.find('.fn-remove-avatar').addClass('d-none');
                    }
                }
            });
        });

        // manage background image
        $(document).on('click', '#fn-replace-background', function(e){
            e.preventDefault();
            $('#background_login_2018 .fileinput-button').trigger('click');
        });

    </script>
@stop