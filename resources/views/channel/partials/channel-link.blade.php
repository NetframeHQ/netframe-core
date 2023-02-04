<li id="channel-{{ $channel->id }}" class="{{ (isset($loop) && $loop->iteration > 3 && session('activeChannels') == false) ? 'd-none' : '' }}">
    <a href="{{ url()->route('channels.home', ['id' => $channel->id]) }}" data-action="load-channel" data-channel-id="{{ $channel->id }}"
        class="{{(session()->has('channelDisplayId') && session('channelDisplayId') == $channel->id) ? 'active' : '' }}" title="{{ $channel->getNameDisplay() }}">
        @if($channel->personnal == 0)
            <span class="svgicon icon-talktalk  fn-notifiable">
                @include('macros.svg-icons.channel')
            </span>
        @elseif($channel->getUserPhoto() != null)
            {!! HTML::thumbImage($channel->getUserPhoto(), 30, 30, [], 'user', 'avatar fn-notifiable') !!}
        @else
            {{--
            <span class="svgicon fn-notifiable">
                @include('macros.svg-icons.user')
            </span>
            --}}
            {!! HTML::userAvatar($channel->otherUser(), 24, 'avatar fn-notifiable') !!}
        @endif
        @if($channel->personnal == 0 && $channel->confidentiality == 0)
            <span class="svgicon private">
                @include('macros.svg-icons.private')
            </span>
        @endif
        <span class="txt">
            {{ $channel->getNameDisplay() }}
        </span>
        @if($channel->personnal == 1)
            <span class="status {{ ($channel->getUserStatus()) ? 'online' : '' }}"></span>
        @endif



        <span class="notif-ctn d-none">
            <span class="badge-notif num"></span>
        </span>
    </a>
</li>