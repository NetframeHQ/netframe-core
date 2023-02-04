@extends('layouts.boarding')

@section('content')
<h1 class="mb-2 mb-md-5"><span>{{ trans('boarding2020.step1.title1') }}</span><br/>{{ trans('boarding2020.step1.title2') }}</h1>

{{ Form::open(['route' => 'boarding.sendcode','id' => 'boarding', 'role' => 'form', 'class' => 'box']) }}
    <div class="form--inline">
        <div class="input input--only mb-2 mb-md-0">
            {{ Form::label('email', trans('boarding2020.email')) }}
            {{ Form::email('email', (isset($email)) ? $email : \App\Helpers\InputHelper::get('email'), ['class' => 'form-control', 'placeholder' => trans('boarding2020.register.yourEmail')]) }}
        </div>
        <button class="btn btn--primary btn--full" type="submit">{{ trans('boarding2020.next') }}</button>
    </div>
    @if(isset($errorCode))
        <div class="input__error mb-0 mt-2 ft-600">
            <img src="{{ asset('assets/img/boarding/alert-circle.svg') }}" alt="icon erreur" class="is-inline" /> {{ trans('boarding.error.'.$errorCode) }}
            @if($errorCode == "emailExists")
                <p class="text-center">
                    <a href="{{ URL::route('boarding.newcode', ['boardingId' => session('boarding.boarding')]) }}">{{ trans('boarding.receiveNewCode') }}</a>
                </p>
            @elseif($errorCode == "userExists")
                <a href="{{ URL::route('login') }}">> {{ trans('boarding.login') }}</a>
            @endif
        </div>
    @endif
{{ Form::close() }}

@stop
