@if(!auth()->user()->visitor)
    <div class="content-sidebar-block">
        <a class="nf-invisiblink" href="{{ url()->route('directory.home') }}"></a>
        <div class="content-sidebar-line content-sidebar-titline">
            <span class="svgicon icon-members">
                @include('macros.svg-icons.members')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('widgets.newProfiles') }}">{{ trans('widgets.newProfiles') }}</h4>
        </div>
    </div>

    <ul class="sidebar-profiles">
        @foreach($newProfiles as $user)
            <li class="sidebar-profile">
                <a href="{{ $user->getUrl() }}" class="nf-invisiblink"></a>
                <div class="sidebar-profile-av">
                    {!! HTML::thumbImage(
                        $user->profile_media_id,
                        50,
                        50,
                        [],
                        'user',
                        'avatar',
                        $user
                    ) !!}
                </div>
                <div class="sidebar-profile-infos">
                    <h4 class="sidebar-profile-title">{{ $user->getNameDisplay() }}</h4>
                </div>
            </li>
        @endforeach
    </ul>
@endif

{{--
@if(isset($activeCalendar) && $activeCalendar)
    <div class="content-sidebar-block">
        <a href="{{ url()->route('calendar.home') }}" class="nf-invisiblink"></a>
        <div class="content-sidebar-line content-sidebar-titline">
            <span class="svgicon icon-cal">
                @include('macros.svg-icons.calendar')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('netframe.navEvent') }}">{{ trans('netframe.navEvent') }}</h4>
        </div>
        <div class='sidebar-calendar'></div>
    </div>
@endif

@if(isset($activeCalendar) && $activeCalendar)
    @section('javascripts')
        @parent
        <script>
        $(document).ready(function(){
            $('.sidebar-calendar').fullCalendar({
                //theme: true,
                header: {
                    left: 'today',
                    center: 'title',
                    right: 'prev,next'
                },
                defaultView: 'agendaWeek',
                //height: 'auto',
                theme: 'bootstrap3',
                defaultDate: moment().format("YYYY-MM-DD"),
                navLinks: true,
                editable: false,
                eventLimit: true,
                events: {
                    url: laroute.route('calendar.dates', { type : '{{ $calendarView }}'}),
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
@endif
--}}