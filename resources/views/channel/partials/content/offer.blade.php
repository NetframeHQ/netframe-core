<a href="{{ $post->getUrl() }}" class="nf-invisiblink" alt="{{ trans('channels.display.viewOffer') }}"></a>

@if(class_basename($post) != "NetframeAction" && class_basename($post) != "App\TaskTable")
    @if($post->medias != null && $post->medias->count() > 0)
        @if($post->onlyImages())
            @include("page.type-content.medias.multi-medias")
        @else
            @include("page.type-content.medias.medias", ['medias' => $post->medias])
        @endif
    @endif
@endif

<div class="panel-event">

    {{-- @if($activeMap && $gdpr_agrement && $post->location != null)
        <div class="panel-event-map" data-latitude="{{ $post->post->latitude }}" data-longitude="{{ $post->post->longitude }}"></div>
    @endif --}}

    <div class="panel-event-head">
        <div class="panel-event-date">
            <span class="top">{{ \App\Helpers\DateHelper::eventPartialDate($post->date, $post->time, 'month') }}</span>
            <span class="bottom">{{ \App\Helpers\DateHelper::eventPartialDate($post->date, $post->time, 'day') }}</span>
        </div>
        <div class="panel-event-info">
            <h3 class="panel-event-title">
                {{ $post->getNameDisplay() }}
            </h3>
            <div class="panel-event-subtitle">
                {{ \App\Helpers\DateHelper::eventDate($post->date, $post->time, $post->date_end, $post->time_end) }} — {{ $post->location }}
            </div>
        </div>
    </div>
    {{--
        <div class="panel-event-body">
            {!! \App\Helpers\StringHelper::formatPostText($post->content) !!}
        </div>
    --}}
    <a href="{{ $post->getUrl() }}" class="nf-invisiblink" alt="{{ trans('channels.display.viewOffer') }}"></a>
</div>

@if($post->content != null)
    <div class="panel-body">
        {!! \App\Helpers\StringHelper::formatPostText($post->content) !!}
    </div>
@endif

@if(in_array(class_basename($post), config('netframe.model_taggables')) && $post->tags()->count() > 0)
    <div class="panel-tags">
        <span class="tags-icon">
            <span class="svgicon">
                @include('macros.svg-icons.tags')
            </span>
        </span>
        @include('tags.element-display', ['tags' => $post->tags])
    </div>
@endif


<footer class="shared-content-origin">
    <a href="{{ $author->getUrl() }}">
        {!! HTML::thumbImage($author->profile_media_id, 60, 60, [], $author->getType()) !!}
        {{ $author->getNameDisplay() }}
    </a>
</footer>
