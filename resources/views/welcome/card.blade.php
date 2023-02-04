@extends('layouts.boarding')

@section('stylesheets')

@stop
@section('content')

<h1 class="mb-2"><span>{{ trans('boarding2020.step7.title1') }}</span> {{ trans('boarding2020.step7.title2') }}</h1>
<p class="h3 ft-white ft-700 mb-1">{{ trans('boarding2020.step7.pricing') }}</p>
<p class="mb-4 mb-md-5">{{ trans('boarding2020.step7.intro') }}</p>

{{ Form::open(['class' => 'box']) }}
    {{ Form::hidden('instanceId', $instanceId) }}

    @if(isset($errorCb))
        <p class="input__error mb-0 mt-2 ft-600"><img src="{{ asset('assets/img/boarding/alert-circle.svg') }}" class="is-inline" />
            {{ trans('stripe.'.$errorCb) }}
        </p>
    @endif

    <div class="input mb-2">
        {{ Form::label('card_number', trans('welcome.card-number')) }}
        {{ Form::text('card_number', '', ['placeholder' => 'XXXX ...']) }}
        {!! $errors->first('card_number', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" class="is-inline" />:message</p>') !!}
    </div>
    <div class="form__cb">
        <div class="input form__date mb-2">
            {{ Form::label('month', trans('welcome.card-expiry')) }}
            {{ Form::text('month', '', ['placehodler' => '', 'min' => '01', 'max' => '12']) }}
            <span class="ft-700">/</span>
            {{ Form::text('year', '', ['placehodler' => '', 'min' => '00', 'max' => '99']) }}
            {{ Form::hidden('card_expiry') }}
            {!! $errors->first('card_expiry', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" class="is-inline" />:message</p>') !!}
        </div>

        <div class="input form__cvv mb-2 mb-md-3">
            {{ Form::label('card_crypto', trans('welcome.card-crypto')) }}
            {{ Form::text('card_crypto', '', ['placeholder' => '', 'min' => '000', 'max' => '999']) }}
            {!! $errors->first('card_crypto', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" class="is-inline" />:message</p>') !!}
        </div>
    </div>

    <button class="btn btn--primary btn--full" type="submit" >{{ trans('welcome.send') }}</button>

    <p class="mt-2 mt-md-3 mb-0 form__mention ft-secondary">
        @include('macros.svg-icons.lock-stripe')
        {{ trans('welcome.stripeText') }}
    </p>
{{ Form::close()}}
@stop

@section('javascripts')
@parent
<script>
(function($) {
    $(document).on('keyup', "input[name='month']", function(e){
        const isNumber = isFinite(e.key);
        var month = $(this).val();
        if(month.length == 2 && isNumber){
            $("input[name='year']").focus();
        }
        $("input[name='card_expiry']").val($("input[name='month']").val()+'/'+$("input[name='year']").val())
    });
    $(document).on('keyup', "input[name='year']", function(e){
        $("input[name='card_expiry']").val($("input[name='month']").val()+'/'+$("input[name='year']").val())
    });
})(jQuery);
</script>
@stop