@extends('layouts.master-header')

@section('favicon')
  {{url()->route('netframe.svgicon', ['name' => 'localisation'])}}
@endsection

@section('title')
  {{ trans('netframe.navMap') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
  <div class="main-header-infos">
    <span class="svgicon icon-talkgroup">
        @include('macros.svg-icons.localisation')
    </span>
    <h2 class="main-header-title">
      {{ trans('netframe.navMap') }}
    </h2>
  </div>
@endsection

@section('content')
    @include('components.mapcontent', array('zoomMapBox'=>$zoomMapBox))
@stop