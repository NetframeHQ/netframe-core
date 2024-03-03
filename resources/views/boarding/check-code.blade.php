@extends('layouts.boarding')

@section('content')

@if(session()->has('boarding_code'))
    <span class="boarding-code">{{ session('boarding_code') }}</span>
@endif

<h1 class="mb-2"><span>{{ trans('boarding2020.step2.title1') }} </span>{{ trans('boarding2020.step2.title2') }}</h1>
        <p class="mb-4 mb-md-5">{{ trans('boarding2020.step2.emailSentTo') }} <strong>{{ $boarding->email }}</strong> <a href="{{ url()->route('boarding.home') }}">{{ trans('boarding2020.step2.update') }}</a></p>
        {{ Form::open(['route' => 'boarding.checkcode','id' => 'boarding-checkcode', 'role' => 'form', 'class' => "box"]) }}
            <div class="input input--only input--code mb-2">
                <label>{{ trans('boarding2020.step2.confirmationCode') }}</label>
                {{ Form::text('n1', '', ["class" => "input-step", "data-next" => "input[name='n2']", "placeholder" => "_", "maxlength" => "1"]) }}
                {{ Form::text('n2', '', ["class" => "input-step", "data-next" => "input[name='n3']", "placeholder" => "_", "maxlength" => "1"]) }}
                {{ Form::text('n3', '', ["class" => "input-step", "data-next" => "input[name='n4']", "placeholder" => "_", "maxlength" => "1"]) }}
                <span class="input__separator"></span>
                {{ Form::text('n4', '', ["class" => "input-step", "data-next" => "input[name='n5']", "placeholder" => "_", "maxlength" => "1"]) }}
                {{ Form::text('n5', '', ["class" => "input-step", "data-next" => "input[name='n6']", "placeholder" => "_", "maxlength" => "1"]) }}
                {{ Form::text('n6', '', ["class" => "input-step","placeholder" => "_", "maxlength" => "1"]) }}
            </div>
            <button class="btn btn--primary btn--full" type="submit" >{{ trans('boarding2020.next') }}</button>
            @if(isset($errorCode))
                <div class="input__error mb-0 mt-2 ft-600">
                    <p class="text-center">
                        <img src="{{ asset('assets/img/boarding/alert-circle.svg') }}" alt="icon erreur" class="is-inline" /> {{ trans('boarding.error.'.$errorCode) }}
                    </p>
                    @if($errorCode == "codeMatch")
                        <p class="text-center">
                            <a href="{{ URL::route('boarding.newcode', ['boardingId' => session('boarding.boarding')]) }}">{{ trans('boarding.receiveNewCode') }}</a>
                        </p>
                    @endif
                </div>
            @endif
        {{ Form::close() }}
@stop

@section('javascripts')
@parent
<script type="text/javascript">
    var capterra_vkey = 'aabc5d92cbfa2fd3bd967264f8943e4f',
    capterra_vid = '2117005',
    capterra_prefix = (('https:' == document.location.protocol) ? 'https://ct.capterra.com' : 'http://ct.capterra.com');

    (function() {
        var ct = document.createElement('script'); ct.type = 'text/javascript'; ct.async = true;
        ct.src = capterra_prefix + '/capterra_tracker.js?vid=' + capterra_vid + '&vkey=' + capterra_vkey;
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ct, s);
    })();
</script>
<script type="text/javascript">
(function (w, d, id) {
    var ts = new Date().getTime();
    w.avURL = w.avURL || 'https://www.appvizer.fr';
    w.avPool = w.avPool || [];
    w.avPool.push({start: ts, id: id});
    w.av = function () { w.avPool.push(arguments) };
    var e = document.createElement("script");
    e.async = true;
    e.src = w.avURL + '/ariadne/v1/ariadne.js?ts=' + ts;
    d.getElementsByTagName("head")[0].appendChild(e);
})(window, document, 'AP-25130');
av("visit");
</script>
<script>
(function($) {
    $(document).on('paste', "input[name='n1']", function(e){
        var valPasted = e.originalEvent.clipboardData.getData('Text');

        if(valPasted.indexOf('-') !== -1){
            var pastedCode = valPasted.split('-');
            var codePart1 = pastedCode[0].split('');
            var codePart2 = pastedCode[1].split('');

            $("input[name='n1']").val(codePart1[0]);
            $("input[name='n2']").val(codePart1[1]);
            $("input[name='n3']").val(codePart1[2]);
            $("input[name='n4']").val(codePart2[0]);
            $("input[name='n5']").val(codePart2[1]);
            $("input[name='n6']").val(codePart2[2]);
        }
    });

    $(document).on('focus', '.input-step', function(e){
        $(this).val('');
    });

    $(document).on('keyup', '.input-step', function(e){
        const isNumber = isFinite(e.key);
        if($(this)[0].hasAttribute('data-next')){
            var code1 = $(this).val();
            var nextInput = $(this).data('next');
            if(code1.length == 1 && isNumber){
                $(nextInput).focus();
            }
        }
    });
})(jQuery);
</script>
@stop