
    @if ($Taction->type_action == 'userNewreference')
         <div class="panel-post">
            #{{ $Taction->author->reference->name }}
         </div>

    @elseif ($Taction->type_action == 'participant_event')

        <div class="panel-event">
            @include("page.type-content.event",  ['post' => $Taction->author->posts[0], 'medias' => null])
        </div>

    @elseif ($Taction->type_action == 'like' || $Taction->type_action == 'follow' || $Taction->type_action == 'new_profile' || $Taction->type_action == 'joinProject' || $Taction->type_action == 'joinCommunity' || $Taction->type_action == 'joinHouse' || $Taction->type_action == 'joinChannel')
        @include("page.type-content.netframe-actions-owner")
    @endif

    {{--
    @if ($Taction->type_action == 'like_post')
        {{ trans('netframe_actions.'.$Taction->author_type) }}
    @endif

    @if ($Taction->type_action == 'post_on')
        @include("page.type-content.netframe-actions-owner",array('profile'=>'on'))
    @endif
    --}}
