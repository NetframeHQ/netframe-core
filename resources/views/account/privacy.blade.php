@extends('account.main')

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('user.menu.privacySettings') }}
        </div>

        {{ Form::open() }}

            <div class="nf-form-informations">
                {{ trans('auth.gdpr_text') }}
                <label class="nf-form-gdpr">
                    <div class="nf-checkbox">
                        {{ Form::checkbox('gdpr', '1', ($gdpr == '1')) }}
                    </div>
                    <span class="text">{{ trans('auth.accept_gdpr') }}</span>
                </label>
            </div>

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