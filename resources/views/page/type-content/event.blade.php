
@if($activeMap && $gdpr_agrement && $post->post->location != null)
    <div class="panel-event-map" data-latitude="{{ $post->post->latitude }}" data-longitude="{{ $post->post->longitude }}"></div>
@endif

<div class="panel-event-head">
    <div class="panel-event-date">
        <span class="top">{{ \App\Helpers\DateHelper::eventPartialDate($post->post->date, $post->post->time, 'month') }}</span>
        <span class="bottom">{{ \App\Helpers\DateHelper::eventPartialDate($post->post->date, $post->post->time, 'day') }}</span>
    </div>
    <div class="panel-event-info">
        <h3 class="panel-event-title">{{ $post->post->title }}</h3>
        <div class="panel-event-subtitle">
            @if($post->post->location != null)
                {{ $post->post->location }}
            @endif
            {{ \App\Helpers\DateHelper::eventDate($post->post->date, $post->post->time, $post->post->date_end, $post->post->time_end) }}
        </div>
    </div>
    <ul class="nf-actions">
        <li class="nf-action">
            <a class="nf-btn fn-event-participate {{ ($post->post->hasParticipant(auth()->guard('web')->user()->id)) ? 'active' : '' }}" data-event="{{ $post->post->id }}">
                <span class="btn-txt">
                    @php
                        if(($post->post->hasParticipant(auth()->guard('web')->user()->id))){
                            $classLeave = '';
                            $classParticipate = 'd-none';
                        }
                        else{
                            $classLeave = 'd-none';
                            $classParticipate = '';
                        }
                    @endphp
                    <span class="fn-leave-participation {{ $classLeave }}">
                        {{ trans('event.leaveParticipeButton') }}
                    </span>
                    <span class="fn-enter-participation {{ $classParticipate }}">
                        {{ trans('event.participeButton') }}
                    </span>
                </span>
            </a>
        </li>
    </ul>

    <div class="nf-post-actions @if($post->post->participants == 0) d-none @endif">
        <a href="{{ url()->route('event.participants', ['eventId' => $post->post->id]) }}" class="nf-btn" data-toggle="modal" data-target="#modal-ajax-comment" >
            <span class="btn-img svgicon">
                @include('macros.svg-icons.participants')
            </span>
            <span class="btn-txt btn-digit">
                {{ $post->post->participants }}
            </span>
        </a>
    </div>
</div>

@if($post->post->description != null)
    <div class="panel-event-body">
        {!! \App\Helpers\StringHelper::collapsePostText($post->post->description) !!}
    </div>
@endif
