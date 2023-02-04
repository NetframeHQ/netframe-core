
@if($displayUserPages)
    {{--
    @if($profile->getType() == 'user' && $profile->id = auth('web')->user()->id)
        <div class="content-sidebar-block">
            <div class="content-sidebar-line content-sidebar-user">
            @if($profile->profileImage != null)
                {!! HTML::thumbImage($profile->profileImage, 80, 80, [], $profile->getType(), 'avatar') !!}
            @else
                <span class="svgicon">
                    @include('macros.svg-icons.user')
                </span>
            @endif
            <span class="name">{{ $profile->getNameDisplay() }}</span>
                @if($profile->isOnline())
                    <span class="status online">online</span>
                @else
                    <span class="status offline">offline</span>
                @endif
            </div>
        </div>
    @endif
    --}}

    @if($profile->id != auth('web')->user()->id)
        <div class="content-sidebar-action">
            {!! HTML::addFriendBtn(['author_id' => $profile->id,'user_from' => auth()->guard('web')->user()->id,'author_type'  => 'user'], \App\Friends::relation($profile->id) ) !!}
        </div>
    @endif
    @if($activeChannel && $profile->id != auth()->guard('web')->user()->id)
        @if(!isset($channel))
            {{--<div class="content-sidebar-action">
                <a href="{{ url()->route('channels.messenger', ['userId' => $profile->id]) }}" class="button primary counter button-discuss">
                    <span class="default">
                        visio
                    </span>
                    <span class="content-link">
                        <span class="svgicon">
                            @include('macros.svg-icons.channel')
                        </span>
                        {{ trans('channels.sidebarUserLink') }}
                    </span>
                </a>
            </div>--}}
            <div class="content-sidebar-action bloc-access-livechat">
                <a href="{{ url()->route('channels.livechat', ['channelId' => $profile->id, 'from' => 0, 'fromUser' => 1]) }}" class="button primary counter button-visio" target="_blank">
                    <span class="default">
                        visio
                    </span>
                    <span class="content-link">
                        <span class="svgicon">
                            @include('macros.svg-icons.visio')
                        </span>
                        {{ trans('channels.startLive') }}
                    </span>
                    {{--
                    <span class="num" data-members="{{ $channel->live_members }}">{{ $channel->live_members }}</span>
                    --}}
                </a>
            </div>
        @else
            <div class="content-sidebar-action bloc-access-livechat">
                <a href="{{ url()->route('channels.livechat', ['channelId' => $channel->id]) }}" class="button primary counter button-visio" target="_blank">
                    <span class="default">
                        visio
                    </span>
                    <span class="content-link">
                        <span class="svgicon">
                            @include('macros.svg-icons.visio')
                        </span>
                        {{ trans('channels.startLive') }}
                    </span>
                    {{--
                    <span class="num" data-members="{{ $channel->live_members }}">{{ $channel->live_members }}</span>
                    --}}
                </a>
            </div>
        @endif
    @endif

    @include('components.sidebar.user-reference', [ 'profile' => $profile ])

    @if(count($profile->validCustomFields()) > 0 || (!$rights && $dataUser->id != auth('web')->user()->id))
        <div class="content-sidebar-block bloc-send-message">
            @foreach($profile->validCustomFields() as $values)
                @if($values['value'] != '')
                    <div class="content-sidebar-line">
                        <span class="svgicon icon-infos">
                            @include('macros.svg-icons.infos')
                        </span>
                        <h4 class="content-sidebar-title" title="{{ $values['name'] }}">{{ $values['name'] }}</h4>
                        <p>
                            {!! \App\Helpers\StringHelper::collapsePostText($values['value']) !!}
                        </p>
                    </div>
                @endif
            @endforeach

            @if(!$rights && $dataUser->id != auth('web')->user()->id)
                <a href="{{ url()->route('channels.messenger', ['userId' => $dataUser->id]) }}" class="nf-btn btn-xl">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.talk')
                    </span>
                    <span class="btn-txt">
                        {{ trans('page.leave_message') }}
                    </span>
                </a>
            @endif
        </div>
    @endif

    @foreach($sidebarPages as $mosaic)
        @if(count($mosaic['profiles']) > 0)
            <div class="content-sidebar-block">
                <div class="content-sidebar-line content-sidebar-titline">
                    <span class="svgicon icon-members">
                        @include('macros.svg-icons.'.$mosaic['type'])
                    </span>
                    <h4 class="content-sidebar-title" title="{{ trans('widgets.'.$mosaic['type']) }}">{{ trans('widgets.'.$mosaic['type']) }}</h4>
                </div>
                <ul class="list-unstyled sidebar-users">
                    @foreach($mosaic['profiles'] as $userProfile)
                        <li>
                            <a href="{{ $userProfile->getUrl() }}" class="sidebar-users-line">
                                {!! HTML::thumbImage($userProfile->profile_media_id, 60, 60, [], $userProfile->getType(), 'avatar') !!}
                                <p class="name">{{ $userProfile->getNameDisplay() }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach


    @foreach($sidebarProfiles as $mosaic)
        @if(count($mosaic['profiles']) > 0)
            <div class="content-sidebar-block">
                <div class="content-sidebar-line content-sidebar-titline">
                    <span class="svgicon icon-members">
                        @include('macros.svg-icons.members')
                    </span>
                    <h4 class="content-sidebar-title" title="{{ trans('widgets.'.$mosaic['type']) }}">{{ trans('widgets.'.$mosaic['type']) }}</h4>
                </div>
                <ul class="list-unstyled sidebar-users">
                    @foreach($mosaic['profiles'] as $userProfile)
                        <li>
                            <a href="{{ $userProfile->getUrl() }}" class="sidebar-users-line">
                                {!! HTML::thumbImage($userProfile->profile_media_id, 30, 30, [], $userProfile->getType(), 'avatar') !!}
                                <p class="name">{{ $userProfile->getNameDisplay() }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach

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
    --}}
@endif


@section('javascripts')
@parent
{{--
@if(isset($activeCalendar) && $activeCalendar)
    <script>
    $(document).ready(function(){
        $('.sidebar-calendar').fullCalendar({
            //theme: true,
            header: {
                left: 'today',
                center: 'title',
                right: 'prev,next'
            },
            theme: 'bootstrap3',
            defaultView: 'agendaWeek',
            //height: 'auto',
            defaultDate: moment().format("YYYY-MM-DD"),
            navLinks: true,
            editable: false,
            eventLimit: true,
            events: {
                url: laroute.route('calendar.dates', { type : 'profile', profile_type: 'user', profile_id: {{ $dataUser->id }} }),
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
@endif
--}}
@stop
