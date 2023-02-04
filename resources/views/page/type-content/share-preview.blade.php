<div class="panel-exhibit panel-gallery shared-content">
    @if($medias !== null && ((isset($post) && !in_array(class_basename($post->post), config('netframe.shareProfilesTypes'))) || !isset($post)))
        @include("page.type-content.medias.share")
    @endif


    @if(isset($post) && class_basename($post->post) == 'Media')
        @include("page.type-content.medias.share", ['medias' => ['0' => $post->post ]] )
    @endif

    @if(isset($post))
        @if(class_basename($post->post) === "News")
            @if($post->post->content != null)
                <div class="panel-post">
                    @include("page.type-content.news")
                </div>
            @endif
            @include("page.type-content.links", ['post' => $post->post])

        @elseif(class_basename($post->post) === "TEvent")
            <div class="panel-event">
                @include('page.type-content.event')
            </div>
            @include("page.type-content.links", ['post' => $post->post])

        @elseif(class_basename($post->post) === "Offer")
            <div class="panel-event">
                @include("page.type-content.offer" )
            </div>

        @elseif(class_basename($post->post) === "NetframeAction")
            @include("page.type-content.netframe-actions", ['Taction'=>$post->post])

        @elseif(in_array(class_basename($post->post), config('netframe.shareProfilesTypes')))
            @include("page.type-content.profile", ['profile' => $post->post])
        @endif
    @elseif(isset($profile))
        @include("page.type-content.profile", ['profile' => $profile])
    @endif

</div>