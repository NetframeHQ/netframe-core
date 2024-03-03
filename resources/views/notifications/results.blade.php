@extends('layouts.master-header')

@section('title')
    {{ trans('notifications.results') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/search_results.css') }}">
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.notifs')
        </span>
        <h3 class="main-header-title">
            {{ trans('notifications.results') }}
        </h3>
    </div>
@endsection

@section('content')
    <div id="nav_skipped" class="main-scroller">
        <div class="notifications-content">
            <ul class="nf-list-settings" id="notifications-results">
                @if(0 === count($results))
                    <li class="p-3">
                        {{ trans('notifications.no_matching_results') }}
                    </li>
                @endif

                @include('notifications.results-details')
            </ul>
        </div>
    </div>

    {{-- @include('components.sidebar.toggle') --}}

@stop


@section('sidebar')
    @include('components.sidebar-user')
@stop


@section('javascripts')
@parent
<script>
(function($) {
    //INFINITE SCROLL
    var currentPageInfinite = 1;
    var scrolling = 0;

    $(".main-scroller").scroll(function() {
        if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight -10 ) && scrolling == 0) {
            scrolling = 1;
            $.post(laroute.route('notifications.results', {limit: currentPageInfinite }))
                .success(function (data) {
                    if(data.length > 0){
                        scrolling = 0;
                        $("#notifications-results").append(data);
                        currentPageInfinite = currentPageInfinite+1;

                        new PlayMediaModal({
                            $modal: $modal,
                            $modalTitle: $modal.find('.modal-title'),
                            $modalContent: $modal.find('.modal-body'),
                            $media: $('.viewMedia'),
                            baseUrl: baseUrl
                            });
                    }
                });
            }
    });

  //for modal media view
    var $modal = $('#viewMediaModal');

    new PlayMediaModal({
       $modal: $modal,
       $modalTitle: $modal.find('.modal-title'),
       $modalContent: $modal.find('.modal-body'),
       $media: $('.viewMedia'),
       baseUrl: baseUrl
    });

})(jQuery);


</script>
@stop

