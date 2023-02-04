@extends('layouts.master-header')

@section('favicon')
{{url()->route('netframe.svgicon', ['name' => 'calendar'])}}
@endsection

@section('title')
    {{ trans('netframe.navEvent') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-calendar">
            @include('macros.svg-icons.calendar_big')
        </span>
        <h2 class="main-header-title">
            {{ trans('netframe.navEvent') }}
            @if(isset($profile))
                : {{ $profile->getNameDisplay() }}
            @endif
        </h2>
    </div>
    <ul class="nf-actions">
        <li class="nf-action">
            <a class="nf-btn" href="{{ url()->route('posting.default', ['post_type' => 'event']) }}" data-toggle="modal" data-target="#modal-ajax" >
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.plus')
                </span>
                <span class="btn-txt">
                    {{ trans('posting.TEvent.new') }}
                </span>
            </a>
        </li>
        <li class="nf-action">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    <li>
                        <a class="nf-btn" href="{{ url()->route('calendar.import') }}">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.import')
                            </span>
                            <span class="btn-txt">
                                {{ trans('posting.TEvent.import') }}
                            </span>
                        </a>
                    </li>
                    {{--
                    <li>
                        <a class="nf-btn" href="{{ url()->route('calendar.export') }}" data-toggle="modal" data-target="#modal-ajax">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.share')
                            </span>
                            <span class="btn-txt">
                                {{ trans('posting.TEvent.export') }}
                            </span>
                        </a>
                    </li>
                    --}}
                </ul>
            </div>
        </li>
    </ul>
@stop


@section('content')
    <div class="main-container no-side">
        <div id="nav_skipped" class="main-scroller">
            <div id='calendar' class='calendar-example'>
            </div>
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script>
@if(isset($profile))
    var calendarUrl = "{{ url()->route('calendar.dates', ['type' => 'profile', 'profile_type' => $profile->getType(), 'profile_id' => $profile->id]) }}";
@else
    var calendarUrl = "{{ url()->route('calendar.dates', ['type' => 'timeline']) }}";
@endif

$(document).ready(function(){
    $('#calendar').fullCalendar({
        //theme: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        eventLimit: true,
        views: {
            agenda: {
              eventLimit: 1 // adjust to 6 only for agendaWeek/agendaDay
            }
        },
        theme: 'bootstrap3',
        defaultDate: moment().format("YYYY-MM-DD"),
        navLinks: true,
        editable: false,

        events: {
            url: calendarUrl,
            error: function() {
                $('#script-warning').show();
            },
            success: function(){
                //alert("successful: You can now do your stuff here. You dont need ajax. Full Calendar will do the ajax call OK? ");
            }
        },
        loading: function(bool) {
            $('#loading').toggle(bool);
        },
        eventRender: function(event, el) {
            // render the timezone offset below the event title
            if (event.start.hasZone()) {
              el.find('.fc-title').after(
                $('<div class="tzo"/>').text(event.start.format('Z'))
              );
            }
          },
    });
});
</script>
@stop