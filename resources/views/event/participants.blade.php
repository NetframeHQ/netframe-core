<div id="modal-widget-content">
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>

    <div class="participant-list">
        @if(count($participants) > 0)
            <h2 class="widget-title">{{ trans('event.participantTitle') }}</h2>
            {{--!! HTML::thumbMosaic($participants) !!--}}
            <ul class="list-unstyled">
                @foreach($participants as $participant)
                    <li>
                        <a href="{{ $participant->getUrl() }}">
                            {!! HTML::thumbImage($participant->profile_media_id, 60, 60, [], 'user', 'avatar') !!}
                            <p class="name">{{ $participant->getNameDisplay() }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>