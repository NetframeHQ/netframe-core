@if (count($events) > 0)
    <section id="widget-profile-medias" class="block-widget event">
        <h2 class="widget-title">{{ trans('widgets.newEvents') }}</h2>

        <ul class="list-unstyled">
        @foreach($events as $event)
            <li class="media">
                <a href="{{ $event->author->getUrl() }}/{{ $event->posts()->first()->id }}">
                    <div class="media-left">
                        {!! HTML::thumbnail($event->author->profileImage, '60', '60', ['class' => 'img-fluid profile-image'],
                            asset('/assets/img/avatar/'.$event->author->getType().'.jpg'))
                        !!}
                    </div>
                    <div class="media-body">
                        <h3>{{ $event->title }}</h3>
                        <p>
                            {{ \App\Helpers\DateHelper::eventDate($event->date, $event->time, $event->date_end, $event->time_end) }}
                        </p>
                        @if($event->location != null)
                            <p> {{ $event->location }}</p>
                        @endif
                    </div>
                </a>
            </li>
        @endforeach
        </ul>
    </section>
@endif
