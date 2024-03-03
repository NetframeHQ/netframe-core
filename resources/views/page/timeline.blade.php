@extends('layouts.page')
@section('title')
    {{ $globalInstanceName }} • {{ trans('netframe.welcome') }}
@stop
@section('stylesheets')
    <link href="{{ asset('packages/netframe/media/vendor/videojs/video-js.min.css') }}" rel="stylesheet">
    <!-- Start Select media modal -->
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/select-modal.css') }}">
    <!-- End Select media modal -->
@stop

@section('content')
@include('page.timeline-header')

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">

        <section class="feed" id="newsFeed">
            @if(isset($instanceCoverUrl))
                <div class="nf-form-cell nf-cell-cover" style="background-image:url('{{ $instanceCoverUrl }}')">
                </div>
            @endif

            @if($canPostOnTimeline)
                @include('posting.init')
            @endif
            @foreach($newsfeed as $post)
                @if($post->post != null)
                    @include('page.post-content-loader')
                @endif
            @endforeach
        </section>

    </div>
</div>


@if((session()->has('justCreated') && !$need_local_consent))
    @include('welcome.boarding-modals')
@endif
{{-- @include('components.sidebar.toggle') --}}
@stop


@section('sidebar')
    @include('components.sidebar-tout-netframe')
@stop

@section('javascripts')
@parent
<script>
(function($){
    // load posts
    loadTimelinePosts();

    //include function for infinitescroll
    var profileIdFeed = '{{ $dataUser->id }}';
    var profileTypeFeed = 'user';
    var scrolling = 0;

    $(".main-scroller").scroll(function() {
        if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight -10 ) && scrolling == 0) {
            scrolling = 1;
            var lastPostDate = $('#newsFeed article').last().data('time');
            $.post('{{ url()->to('/') }}' + laroute.route('infinite_timeline', {last_time: lastPostDate }))
                .success(function (data) {
                    $("#newsFeed").append(data.view);
                    scrolling = 0;
                    loadTimelinePosts();

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

    //for modal media view
    var $modal = $('#viewMediaModal');

    playMediaModal = new PlayMediaModal({
        $modal: $modal,
        $modalTitle: $modal.find('.modal-title'),
        $modalContent: $modal.find('.modal-carousel .carousel-item'),
        $media: $('.viewMedia'),
        baseUrl: baseUrl
    });
})(jQuery);
</script>
@stop
