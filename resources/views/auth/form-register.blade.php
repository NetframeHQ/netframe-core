@extends('layouts.boarding')

@section('content')
<h1 class="mb-2 mb-md-5"><span>{{ trans('boarding2020.step3.title1') }}</span><br/>{{ trans('boarding2020.step3.title2') }}</h1>
@if(isset($messageRegister) && $messageRegister != null)
    <div class="alert alert-info text-center" role="alert">
        {{ trans($messageRegister) }}
    </div>
@endif

{{ Form::open(['action' =>'AuthController@register', 'id' => 'auth_register', 'role' => 'form', 'class' => 'box']) }}
{{ Form::hidden('instanceId', $instanceId) }}
    <div class="form__col">
        <div class="input mb-2">
            {{ Form::label('firstname', trans('auth.label_firstName')) }}
            {{ Form::text('firstname', '', ['class' => '', 'placeholder' => trans('boarding2020.register.yourFirstname')]) }}
            {!! $errors->first('firstname', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
        </div>
        <div class="input mb-2">
            {{ Form::label('name', trans('auth.label_name')) }}
            {{ Form::text('name', '', ['class' => '', 'placeholder' => trans('boarding2020.register.yourName')]) }}
            {!! $errors->first('name', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
        </div>
    </div>
    <div class="input mb-2">
        {{ Form::label('email', trans('auth.label_email')) }}
        {{ Form::email('email', (isset($email)) ? $email : request()->old('email'), ['class' => '', 'placeholder' => trans('boarding2020.register.yourEmail')]) }}
        {!! $errors->first('email', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
    </div>
    <div class="mb-2">
        <div class="form__col mb-0">
            <div class="input mb-2">
                {{ Form::label('password', trans('auth.label_password_subscribe')) }}
                {{ Form::password('password', ['placeholder' => '• • • • •']) }}
            </div>
            <div class="input mb-2">
                {{ Form::label('password', trans('auth.label_password_confirm')) }}
                {{ Form::password('password_confirmation', ['placeholder' => '• • • • •']) }}
            </div>
        </div>
        {!! $errors->first('password', '<p class="input__error mb-0 mt-0 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
    </div>
    @if(session()->has('boarding.main-user'))
        <div class="checkbox mb-2">
            {{ Form::checkbox('cgv', '1', (request()->old('cgv') == '1'), ['class' => 'custom-checkbox', 'id' => 'cgv' ]) }}
            <label for="cgv">
                {{ trans('auth.accept_cgu') }} <a href="{{ url()->route('static_cgv') }}" target="_blank">{{ trans('auth.accept_cgv_link') }}</a>
            </label>
        </div>
        <div class="mb-2">
            {!! $errors->first('cgv', '<p class="input__error mb-0 mt-0 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
        </div>
    @endif
    <div class="checkbox mb-0">
        {{ Form::checkbox('cgu', '1', (request()->old('cgu') == '1'), ['class' => 'custom-checkbox', 'id' => 'cgu' ]) }}
        <label for="cgu">
            {{ trans('auth.accept_cgu')}} <a href="{{ url()->route('static_cgu') }}" target="_blank">{{ trans('auth.accept_cgu_link')}}</a>
        </label>
    </div>
    <div class="mb-2">
        {!! $errors->first('cgu', '<p class="input__error mb-0 mt-0 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
    </div>
    <div class="checkbox mb-2">
        {{ Form::checkbox('gdpr', '1', (request()->old('gdpr') == '1'), ['class' => 'custom-checkbox', 'id' => 'gdpr']) }}
        {{ Form::label('gdpr', trans('auth.gdpr_text')) }}
    </div>

    <button class="btn btn--primary btn--full" type="submit" >{{ trans('auth.subscription') }}</button>

{{ Form::close() }}
<p class="mt-3 ft-center ft-secondary"><a href="{{ url()->route('login') }}" title="{{ trans('auth.btn_login2') }}">{{ trans('auth.btn_login2') }}</a></p>

@stop