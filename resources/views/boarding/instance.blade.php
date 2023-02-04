@extends('layouts.boarding')


@section('content')
<h1 class="mb-2 mb-md-5"><span>{{ trans('boarding2020.step5.title1') }}</span><br/>{{ trans('boarding2020.step5.title2') }}</h1>
@if(isset($messageLogin))
    {{ trans($messageLogin) }}
@endif
{{ Form::open(['route' => 'boarding.create.instance','id' => 'boarding-instance', 'role' => 'form', 'class' => 'box form--inline']) }}
    <div class="input mb-2 mb-md-0">
        {{ Form::label('instance_name', trans('boarding2020.step5.chooseName')) }}
        {{ Form::text('instance_name', '', ['class' => 'form-control', 'autocomplete' => 'false', 'placeholder' => trans('boarding2020.step5.placeholder')]) }}
    </div>
    {!! $errors->first('instance_name', '<p class="input__error mb-0 mt-0 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" />:message</p>') !!}
    <button class="btn btn--primary btn--full" type="submit">{{ trans('boarding2020.next') }}</button>
{{ Form::close() }}

</div>


@stop