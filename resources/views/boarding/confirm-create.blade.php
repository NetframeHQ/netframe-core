@extends('layouts.boarding')

@section('content')
<h1 class="mb-2 mb-md-5"><span>{{ trans('boarding2020.step4.title1') }}</span><br/>{{ trans('boarding2020.step4.title2') }}</h1>

<div class="box">
    <a href="{{ $nextRoute }}" class="btn btn--primary btn--full">{{ trans('boarding2020.next') }}</a>
</div>

@stop