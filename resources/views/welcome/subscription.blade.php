@extends('layouts.boarding')

@section('content')
<h1 class="mb-2 mb-md-5"><span>{{ trans('boarding2020.step6.title1') }}</span><br/>{{ trans('boarding2020.step6.title2') }}</h1>

<div class="card-offer">
    <div class="card box mb-7">
        <h2 class="mb-5 mb-md-6">{{ trans('boarding2020.step6.freeTry') }}</h2>
        <img class="mb-3" src="{{ asset('assets/img/boarding/boat.jpg') }}" width="24" height="20" />
        <p class="mb-8 mb-md-6"><strong>{{ trans('boarding2020.step6.30j') }}</strong> {{ trans('boarding2020.step6.freeTryTxt') }}</p>
        <a href="{{ (App::isLocale('fr')) ? 'https://www.netframe.co/fr/tarifs' : 'https://www.netframe.co/pricing' }}" target="_blank">{{ trans('boarding2020.step6.moreDetails') }}</a>
        <a href="{{url()->route('boarding.admin.step2')}}" class="btn btn--primary btn--full" type="submit">{{ trans('boarding2020.step6.start') }}</a>
    </div>

    <div class="card box mb-7">
        <h2 class="mb-2">{{ trans('boarding2020.step6.normalTry') }}</h2>
        <img class="mb-2" src="{{ asset('assets/img/boarding/rocket.jpg') }}" width="24" height="24" />
        <p class="mb-6"><strong>{{ trans('boarding2020.step6.45j') }}</strong> {{ trans('boarding2020.step6.45jfree') }}<br/><strong>5€</strong> {{ trans('boarding2020.step6.priceFor') }}</p>
        <a href="{{ (App::isLocale('fr')) ? 'https://www.netframe.co/fr/tarifs' : 'https://www.netframe.co/pricing' }}" target="_blank">{{ trans('boarding2020.step6.moreDetails') }}</a>
        <a href="{{url()->route('boarding.admin.stepCB')}}" class="btn btn--primary btn--full" type="submit">{{ trans('boarding2020.step6.continue') }}</a>
    </div>
</div>

@stop

@section('javascripts')
@parent
<script type="text/javascript">
// appvizer
(function (w, d, id) {
    var ts = new Date().getTime();
    w.avURL = w.avURL || 'https://appvizer.one';
    w.avPool = w.avPool || [];
    w.avPool.push({start: ts, id: id});
    w.av = function () { w.avPool.push(arguments) };
    var e = document.createElement("script");
    e.async = true;
    e.src = w.avURL + '/ariadne/v1/ariadne.js?ts=' + ts;
    d.getElementsByTagName("head")[0].appendChild(e);
})(window, document, 'AP-25130');  av("conversion");
</script>
@stop