<p class="post-content">
    @if(!$unitPost)
        {!! \App\Helpers\StringHelper::collapsePostText($post->post->content, 500) !!}
    @else
        {!! \App\Helpers\StringHelper::formatPostText($post->post->content) !!}
    @endif
</p>
