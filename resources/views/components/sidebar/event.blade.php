@if((isset($nextEvent) && $nextEvent !== null))
    <section id="userEvent" class="block-widget">
    @if($nextEvent !== null)
        <h2 class="widget-title">{{ trans('netframe.nextEvent') }}</h2>

        @if($nextEvent->medias()->first() != null)
            {{ HTML::thumbnail($nextEvent->medias()->first(), '60', '60', ['class' => 'img-fluid profile-image float-left mg-right-5'],
                            asset('/assets/img/blank.png'))
                        }}
        @endif
        <p>
            <a href="{{ url()->route('post.modal', [$nextEvent->posts->first()->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                <span class="title1">{{ $nextEvent->title }}</span>
                <span class="title2">{{ date("d / m / Y", strtotime($nextEvent->date)) }} - {{ $nextEvent->time }}</span>
            </a>
            <p>
                {{ \App\Helpers\StringHelper::collapsePostText($nextEvent->description) }}
            </p>
        </p>
    @endif
    </section>
@endif
