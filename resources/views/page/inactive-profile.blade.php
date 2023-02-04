@extends('layouts.page')
@section('title')
    {{ $profile->getNameDisplay() }} • {{ $globalInstanceName }}
@stop
@section('stylesheets')
    <!-- Start Select media modal -->
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/select-modal.css') }}">
    <!-- End Select media modal -->
@stop

@section('content')

@include('page.profile-header')

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <section class="feed feed-{{ class_basename($profile) }}-{{ $profile->id }}" id="newsFeed">
            <div class="nf-pageholder">{{ trans('page.inactiveProfileTitle') }}</div>
        </section>
    </div>
</div>

@stop

@section('sidebar')

@stop
