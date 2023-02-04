{{ trans('page.share.shared') }} {{ trans('page.share.'.class_basename($post->post)) }}

@if(get_class($post->post) != 'App\Media')
    <a href="{{ $post->post->posts()->first()->author->getUrl() }}">
        {{ $post->post->posts()->first()->author->getNameDisplay() }}
    </a>
@else
    <a href="{{ $post->post->author->first()->getUrl() }}">
        {{ $post->post->author->first()->getNameDisplay() }}
    </a>
@endif