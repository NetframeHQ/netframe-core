@if($joined == null)
    <a href="{{ url()->route('join.ask', ['profile_id' => $profile_id, 'profile_type' => $profile_type, 'users_id' => $users_id]) }}"
        class="button primary button-subscribe @if($members > 0) counter @endif"
        @if($free_join == 0)
            data-toggle="modal" data-target="#modal-ajax"
        @endif
        data-profile="{{$profile_type}}-{{$profile_id}}"
        >

        <span class="default">{{ trans('netframe.join') }}</span>
@elseif($joined == 1)
    <a href="#" class="button primary button-subscribe fn-remove-join  @if($members > 0) counter @endif status-subscribed show-leave" data-tl-join='{{ $dataJson }}' data-confirm="{{ trans('members.quit.'.$profile_type) }}" data-profile="{{$profile_type}}-{{$profile_id}}">
        <span class="default">{{ trans('netframe.join') }}</span>
        <span class="subscribed">
            <span class="svgicon icon-check">
                @include('macros.svg-icons.check')
            </span>
            {{ trans('members.member') }}
        </span>
        <span class="leave">
            <span class="svgicon icon-leave">
                @include('macros.svg-icons.leave')
            </span>
            {{ trans('members.quitGroup') }}
        </span>
@elseif(in_array($joined, [0,2]))
    <a href="#" class="fn-remove-join button primary button-subscribe @if($members > 0) counter @endif status-subscribed show-leave"  data-tl-join='{{ $dataJson }}' data-confirm="{{ trans('members.quit.'.$profile_type) }}" data-profile="{{$profile_type}}-{{$profile_id}}">
        <span class="default">{{ trans('netframe.join') }}</span>
        <span class="subscribed">
            <span class="svgicon icon-check">
                @include('macros.svg-icons.leave')
            </span>
            {{ trans('members.inProgress') }}
        </span>
@endif
    <span class="num @if($members == 0) d-none @endif">{{ $members }}</span>

</a>