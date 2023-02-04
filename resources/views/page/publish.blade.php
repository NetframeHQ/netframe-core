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
            {{--
            @if(!$rights)
                <div class="card mg-bottom-10">
                    <div class="card-body">
                        {{ trans('netframe.memberProfile.'.$profile->getType()) }}
                    </div>
                </div>
            @endif
            --}}

            @if($profile->coverImage != null)
                <div class="nf-form-cell nf-cell-cover" style="{{ (($profile->coverImage != null) ? 'background-image:url(\''.$profile->coverImage->getUrl().'\')' : '')}}">
                    @if($rights && $rights < 3 && $profile->getType() != 'user')
                        <ul class="nf-actions">
                            <li>
                                <a class="nf-btn btn-ico" href="{{ url()->route($profile->getType().'.edit', [$profile->id]) }}">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.edit')
                                    </span>
                                </a>
                            </li>
                        </ul>
                    @endif
                </div>
            @endif

            @if(!$unitPost && $rights)
                @include('posting.init')
            @endif
            @if($confidentiality == 1)
                @if($topPost != null && !$unitPost)
                    @include('page.post-content', ['post' => $topPost, 'pintop' => true])
                @endif

                @foreach($newsfeed as $post)
                    @if($post->post != null)
                        @if($post->post_type == 'App\\NetframeAction' && $post->post->type_action == 'new_profile' && auth()->user()->visitor)
                            @continue
                        @endif
                        @include('page.post-content')
                    @endif
                @endforeach
            @else
                <div class="card">
                    <div class="card-body">
                        {{ trans('netframe.privateProfile.'.$profile->getType()) }}
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>

@stop

@section('sidebar')
        @include('components.sidebar')
@stop


@if(session()->has('autoFireMediaModalView'))
     @include( session('autoFireMediaModalView'), session('autoFireMediaModalViewData') )
@endif

@section('javascripts')
@parent
<script>
current_profile_type = '{{ $profile->getType() }}';
current_profile_id = {{ $profile->id }};

(function($){
    //include function for infinitescroll
    var profileIdFeed = {{ $profile->id }};
    var profileTypeFeed = '{{ $profile->getType() }}';
    var scrolling = 0;

    @if(!$unitPost)
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
    @endif

    //for modal media view
    var $modal = $('#viewMediaModal');

    new PlayMediaModal({
       $modal: $modal,
       $modalTitle: $modal.find('.modal-title'),
       $modalContent: $modal.find('.modal-carousel .carousel-item'),
       $media: $('.viewMedia'),
       baseUrl: baseUrl
    });

    // Setup select media modal
    /*
    $('#modal-ajax').on('shown.bs.modal', function () {
        SelectModal({
             baseUrl: '{{ url()->to('/') }}',
             $modal: $('#postSelectMediaModal'),
             $input: $('#postSelectedMediasId'),
             $thumbPreview: $('#selectedMediasPreview')
         });
    });
    */

    // load events maps
    loadMapEvents("#newsFeed");

})(jQuery);
</script>
@stop