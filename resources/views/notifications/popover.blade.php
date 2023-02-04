<div class="popover-notificatons">
    <ul class="list-unstyled">
        @foreach($notifications as $result)
                <li class="" id="notif-{{ $result->id }}">
                    @if(!empty($result->notifLink))
                        @if(is_array($result->notifLink))
                            <a
                            @foreach($result->notifLink as $key=>$value)
                                {{$key}}="{{$value}}"
                            @endforeach
                            >
                        @else
                            <a href="{{ $result->notifLink }}">
                        @endif
                    @endif
                        {!! $result->notifImg !!}
                        <p class="content">
                            {!! $result->notifTitle !!} {!! $result->notifTxt !!}
                        </p>
                    @if(!empty($result->notifLink))
                        </a>
                    @endif
                </li>
        @endforeach
    </ul>
</div>

<footer class="text-center">
    <a href="{{ url()->route('notifications.results') }}">
        {{ trans('netframe.navAllNotifs') }}
    </a>
</footer>