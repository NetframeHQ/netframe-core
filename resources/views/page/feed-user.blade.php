@extends('layouts.page')
@section('title')
    {{ $dataUser->getNameDisplay() }} • {{ $globalInstanceName }}
@stop
@section('stylesheets')
    <link href="{{ asset('packages/netframe/media/vendor/videojs/video-js.min.css') }}" rel="stylesheet">
    <!-- Start Select media modal -->
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/select-modal.css') }}">
    <!-- End Select media modal -->
@stop

@section('content')

@include('page.user-header') 

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <section class="feed" id="newsFeed">

            @if($profile->coverImage != null)
                <div class="nf-form-cell nf-cell-cover" style="{{ (($profile->coverImage != null) ? 'background-image:url(\''.$profile->coverImage->getUrl().'\')' : '')}}">
                    
                </div>
            @endif

            @if(!$unitPost)
                @if($rights && $rights < 3)
                    @include('posting.init')
                @endif

                @if(session()->has('justCreated') && count($newsfeed) == 0 && $rights && $rights < 3)
                    @include('lang.'.auth()->guard('web')->user()->lang.'.user-empty-feed')
                @endif

            @endif

            @foreach($newsfeed as $post)
                @include('page.post-content')
            @endforeach
        </section>

    </div>
</div>
@stop

@section('sidebar')
    @include('components.sidebar-user', ['profile' => $profile])
@stop

@if(session()->has('autoFireMediaModalView'))
     @include( session('autoFireMediaModalView'), session('autoFireMediaModalViewData') )
@endif

@section('javascripts')
@parent
<script>
(function($){
    //include function for infinitescroll
    var profileIdFeed = '{{ $dataUser->id }}';
    var profileTypeFeed = 'user';
    var scrolling = 0;

    $(".main-scroller").scroll(function() {
        if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10 ) && scrolling == 0) {
            scrolling = 1;
            var lastPostDate = $('#newsFeed article').last().data('time');
            $.post('{{ url()->to('/') }}' + laroute.route('infinite_feed', {profile_type: profileTypeFeed, profile_id: profileIdFeed, last_time: lastPostDate }))
                .success(function (data) {
                    $("#newsFeed").append(data.view);
                    scrolling = 0;
                    
                    new PlayMediaModal({
                        $modal: $modal,
                        $modalTitle: $modal.find('.modal-title'),
                        $modalContent: $modal.find('.modal-carousel .carousel-item'),
                        $media: $('.viewMedia'),
                        baseUrl: baseUrl
                    });

                    $(document).find('.video-js').each(function(){
                        newVideoId = 'video-'+$(this).closest('article').attr('id');
                        $(this).attr('id', newVideoId);
                        videojs(newVideoId);
                    });
                });
            }
    });

    $(document).on('click', '.calendar', function(e){
        e.preventDefault();
        var el = $(this);
        var panel = el.closest(".calendar");
        $('.synchronizing').show()

        var calendar = el.data('email');
        var id = el.data('event');

        var jqXhr = $.post("{{route('calendar.synchronize')}}" , {
                postData : {event_id: id, email: calendar}
        });

        jqXhr.success(function(data) {
            $('.synchronizing').addClass('alert alert-success').html("Synchronisation terminée.")
            setTimeout(function(){
                $('#modal-ajax').modal('hide');
            },1000);
        });
        return false;
    });

    //for modal media view
    var $modal = $('#viewMediaModal');

    new PlayMediaModal({
        $modal: $modal,
        $modalTitle: $modal.find('.modal-title'),
        $modalContent: $modal.find('.modal-carousel .carousel-item'),
        $media: $('.viewMedia'),
        baseUrl: baseUrl
    });

     // load events maps
    loadMapEvents("#newsFeed");
})(jQuery);
</script>
@stop
