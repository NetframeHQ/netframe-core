<div class="form-group">
    {{ Form::open(['id' => 'colors' . $additionnalType]) }}
    <div class="row">
            <div class="text-center mg-top-10 col-md-4">
                {{ Form::label('primaryColor' . $additionnalType, trans('instances.graphical.principalColor')) }}
                <br>
                {{ Form::text('primaryColor' . $additionnalType, (isset($cssColors)) ? $cssColors['primaryColor' . $additionnalType] : '', ['class' => 'form-control color-picker']) }}
            </div>
            <div class="text-center mg-top-10 col-md-4">
                {{ Form::label('accentColor' . $additionnalType, trans('instances.graphical.actionColor')) }}
                <br>
                {{ Form::text('accentColor' . $additionnalType, (isset($cssColors)) ? $cssColors['accentColor' . $additionnalType] : '', ['class' => 'form-control color-picker']) }}
            </div>
            <div class="text-center mg-top-10 col-md-4">
                {{ Form::label('bgColor' . $additionnalType, trans('instances.graphical.backgroundColor')) }}
                <br>
                {{ Form::text('bgColor' . $additionnalType, (isset($cssColors)) ? $cssColors['bgColor' . $additionnalType] : '', ['class' => 'form-control color-picker']) }}
            </div>
    </div>
    <div class="disable-mode-selector {{ ((!$themeParameters['switchable']) ? 'd-none' : '') }} mt-2">
        <label class="nf-form-cell nf-form-checkbox" for="disable_mode_{{ $additionnalType }}">
            {{ Form::checkbox(
                'disable_mode',
                'disable_mode_' . (($additionnalType == '') ? 'light' : 'dark'),
                (isset($paramCss['disableMode']) &&
                    !empty($paramCss['disableMode']) &&
                    (($additionnalType == '' && $paramCss['disableMode'] == 'light') ||
                        ($additionnalType == 'Dark' && $paramCss['disableMode'] == 'dark'))
                    ) ? 1 : 0,
                [
                    'id' => 'disable_mode_' . $additionnalType,
                    'class' => 'nf-form-input'
                ]
            ) }}
            <span class="nf-form-label">
                {{ trans('instances.graphical.disableMode' . $additionnalType ) }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>
    </div>
    {{ Form::close() }}
</div>