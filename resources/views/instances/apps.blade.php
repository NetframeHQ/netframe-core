@extends('instances.main')

@section('title')
    {{ trans('instances.apps.title') }} • {{ $globalInstanceName }}
@stop

@section('subcontent')
    <div class="nf-form">
        {{ Form::open() }}
            <div class="nf-settings-title">
                {{ trans('instances.apps.title') }}
                <ul class="nf-actions">
                    <li class="nf-action">
                        <button type="submit" class="nf-btn btn-primary">
                            <div class="btn-txt">
                                {{ trans('form.save') }}
                            </div>
                            <div class="svgicon btn-img">
                                @include('macros.svg-icons.arrow-right')
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
            @foreach($apps as $app)
                <label class="nf-form-box">
                    {{ Form::checkbox('app_'.$app->id, '1', ($instance->apps->contains($app->id)) ? true : false) }}
                    <div class="nf-box-title">{{ trans('apps.' . $app->slug . '.title') }}</div>
                    <div class="nf-box-desc">{{ trans('apps.' . $app->slug . '.desc') }}</div>
                </label>
            @endforeach

            <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('form.save') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        {{ Form::close() }}
    </div>
@stop