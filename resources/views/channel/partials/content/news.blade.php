<a href="{{ $post->getUrl() }}" class="nf-invisiblink" alt="{{ trans('channels.display.viewNews') }}"></a>

@if(class_basename($post) != "NetframeAction" && class_basename($post) != "App\TaskTable")
    @if($post->medias != null && $post->medias->count() > 0)
        @if($post->onlyImages())
            @include("page.type-content.medias.multi-medias")
        @else
            @include("page.type-content.medias.medias", ['medias' => $post->medias])
        @endif
    @endif
@endif

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
        <!-- {{ trans('page.share.NewsSharedFrom') }} -->
    </a>
</footer>