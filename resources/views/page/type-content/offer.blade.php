@if($activeMap && $gdpr_agrement && $post->post->location != null)
    <div class="panel-event-map" data-latitude="{{ $post->post->latitude }}" data-longitude="{{ $post->post->longitude }}"></div>
@endif

<div class="panel-event-head">
    <div class="panel-event-date">
        <span class="top">{{ \App\Helpers\DateHelper::eventPartialDate($post->post->date, $post->post->time, 'month') }}</span>
        <span class="bottom">{{ \App\Helpers\DateHelper::eventPartialDate($post->post->date, $post->post->time, 'day') }}</span>
    </div>
    <div class="panel-event-info">
        <h3 class="panel-event-title">{{ $post->post->name }}</h3>
        <div class="panel-event-subtitle">
            @if($post->post->location != null)
                {{ $post->post->location }}
            @endif
            {{ \App\Helpers\DateHelper::eventDate($post->post->date, $post->post->time, $post->post->date_end, $post->post->time_end) }}
        </div>
    </div>
</div>
@if($post->post->content != null)
    <div class="panel-event-body">
        <strong>{{ trans('offer.type.'.$post->post->offer_type) }} :</strong>
        {!! \App\Helpers\StringHelper::collapsePostText($post->post->content) !!}
    </div>
@endif


{{--
    <span class="label label-offer text-capitalize float-right">
        {{ $post->post->getSkillName->commercial_name }} {{ trans('offer.type.'.$post->post->offer_type) }}
    </span>
--}}