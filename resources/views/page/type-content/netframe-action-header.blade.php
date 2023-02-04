        {{ trans('netframe_actions.'.$Taction->type_action) }}

        @if ($Taction->type_action == 'like')
            {{ trans('netframe_actions.'.$Taction->author_type) }}
            @include("page.type-content.netframe-actions-owner-header")
        @endif

        @if ($Taction->type_action == 'like_post')
            {{ trans('netframe_actions.'.$Taction->author_type) }}
        @endif

        @if ($Taction->type_action == 'post_on')
            {{ trans('netframe_actions.'.$Taction->author_type) }}
            @include("page.type-content.netframe-actions-owner-header",array('profile'=>'on'))
        @endif

        @if ($Taction->type_action == 'follow')
            {{ trans('netframe_actions.'.$Taction->author_type) }}
            @include("page.type-content.netframe-actions-owner-header")
        @endif

        @if ($Taction->type_action == 'new_profile')
            {{ trans('netframe_actions.'.$Taction->author_type.'_profile') }} :
            @include("page.type-content.netframe-actions-owner-header")
        @endif

        @if ($Taction->type_action == 'new_friend')
            @include("page.type-content.netframe-actions-owner-header")
        @endif

        @if ($Taction->type_action == 'userNewreference')
             :
             {{ $Taction->author->reference->name }}
        @endif

        @if ($Taction->type_action == 'joinProject' || $Taction->type_action == 'joinCommunity' || $Taction->type_action == 'joinHouse' || $Taction->type_action == 'joinChannel')
             @include("page.type-content.netframe-actions-owner-header")
        @endif