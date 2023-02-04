@extends('instances.main')

@section('title')
    {{ trans('instances.virtualUsers.create') }} {{ $user->getNameDisplay() }} • {{ $globalInstanceName }}
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
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.virtualUsers.create') }} {{ $user->getNameDisplay() }}
        </div>

        {{ Form::open(['id' => 'addProfile']) }}
            <!-- FIRST NAME -->
            <label class="nf-form-cell @if($errors->has('firstname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="firstname" name="firstname" value="{{ $virtualUser->firstname }}" placeholder="{{trans('form.placeholder.firstname')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.firstname') }}
                </span>
                {!! $errors->first('firstname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- LAST NAME -->
            <label class="nf-form-cell @if($errors->has('lastname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="lastname" name="lastname" value="{{ $virtualUser->lastname }}" placeholder="{{trans('form.placeholder.lastname')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.name') }}
                </span>
                {!! $errors->first('lastname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- EMAIL -->
            <label class="nf-form-cell nf-cell-full @if($errors->has('email')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="email" name="email" value="{{ $virtualUser->email }}" placeholder="{{trans('form.placeholder.email')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.email') }}
                </span>
                {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- PASSWORD -->
            <label class="nf-form-cell @if($errors->has('password')) nf-cell-error @endif">
                {{ Form::password('password', ['placeholder' => '• • • • •', 'class' => 'nf-form-input']) }}
                <span class="nf-form-label">
                    {{ trans('auth.label_password_subscribe') }}
                </span>
                {!! $errors->first('password', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- PASSWORD CONFIRM -->
            <label class="nf-form-cell @if($errors->has('password_confirmation')) nf-cell-error @endif">
                {{ Form::password('password_confirmation', ['placeholder' => '• • • • •', 'class' => 'nf-form-input']) }}
                <span class="nf-form-label">
                    {{ trans('auth.label_password_confirm') }}
                </span>
                {!! $errors->first('password_confirmation', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- ACTIVATION -->
            <label class="nf-form-cell nf-form-checkbox @if($errors->has('active')) nf-cell-error @endif ">
                <span class="nf-form-label">
                    {{ trans('instances.virtualUsers.active') }}
                </span>
                {{ Form::checkbox('active', '1', ($virtualUser->active == 1), ['class' => 'nf-form-input']) }}
                <div class="nf-form-cell-fx"></div>
            </label>

            <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        @if(empty($virtualUser->id))
                            {{ trans('instances.profiles.add') }}
                        @else
                            {{ trans('instances.profiles.edit') }}
                        @endif
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        {{ Form::close() }}
    </div>
@stop