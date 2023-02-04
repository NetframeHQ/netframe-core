<a href="{{ $post->getUrl() }}" class="nf-invisiblink" alt="{{ trans('channels.display.viewNews') }}"></a>

@include('page.type-content.medias.full-size', ['medias' => [$post]])

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
        <!-- {{ trans('page.share.MediaSharedFrom') }} -->
    </a>
</footer>