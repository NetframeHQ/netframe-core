@foreach($newsfeed as $post)
    @if($post->post != null)
        @if($post->post_type == 'App\\NetframeAction' && $post->post->type_action == 'new_profile' && auth()->user()->visitor)
            @continue
        @endif

        @include('page.post-content')
    @endif
@endforeach