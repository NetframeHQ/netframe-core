<div class="identity-map-card">
@if($eventMedia != null)
        {!! HTML::thumbnail($eventMedia, '60', '60', array('class' => 'img-fluid img-thumbnail profile-image float-left mg-right-5'),asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
    @endif
<div class="padding-5">
    <h4>
        <a href="{{ $author->getUrl() }}/{{ $profile->posts[0]->id }}">
            <span class="icon ticon-event"></span>
            {{ $profile->title }}
        </a>

        <h5>
            <p><span class="icon ticon-event"></span>
                {{ \App\Helpers\DateHelper::eventDate($event->date, $event->time, $event->date_end, $event->time_end) }}
            </p>
            @if($event->location != null)
                <p><span class="icon ticon-geoloc"></span> {{ $event->location }}</p>
            @endif
        </h5>

        @if(in_array(class_basename($profile), config('netframe.model_taggables')))
            @include('tags.element-display', ['tags' => $profile->tags])
        @endif

        <button id="nextProfile" class="btn btn-netframe skip-map btn-arrow-card float-right" data-toggle="tooltip" title="{{ trans('netframe.next') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </button>
        <p>
            {!! \App\Helpers\StringHelper::formatMetaText($event->description, 100) !!}
        </p>

    </h4>
</div>



</div>
