    {!! HTML::subscribeBtnProfile($profile, $followed, $profile->followers()->count()) !!}

    <div class="content-sidebar-action">
        {!! HTML::joinProfileBtn($profile->id, $profile->getType(), auth()->guard('web')->user()->id, $joined, $profile->free_join, $profile->nbUsers()) !!}
    </div>

    <div class="content-sidebar-action sidebar-actions">
        @if($confidentiality == 1)
            {!! HTML::likeBtnProfile($profile, $liked, $profile->like) !!}
            {!! HTML::shareBtnProfile($profile) !!}
        @endif
    </div>

    <div class="content-sidebar-block">

        <!-- <div class="content-sidebar-line content-sidebar-titline">
            <span class="svgicon icon-creator">
                @include('macros.svg-icons.creator')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('page.createdBy') }}">{{ trans('page.createdBy') }}</h4>
            <p>
                <strong>
                    @if(!auth()->user()->visitor)
                        <a href="{{ $profile->owner->getUrl() }}">{{ $profile->owner->getNameDisplay() }}</a>
                    @else
                        {{ $profile->owner->getNameDisplay() }}
                    @endif
                </strong>
            </p>
        </div> -->

        @if($profile->description != '')
            <div class="content-sidebar-line">
                <span class="svgicon icon-infos">
                    @include('macros.svg-icons.infos')
                </span>
                <h4 class="content-sidebar-title" title="{{ trans('page.description') }}">{{ trans('page.description') }}</h4>
                <p>{!! \App\Helpers\StringHelper::collapsePostText($profile->description, 1000) !!}</p>
            </div>
        @endif

        <div class="content-sidebar-line">
            <span class="svgicon icon-tags">
                @include('macros.svg-icons.tags')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('tags.tags') }}">{{ trans('tags.tags') }}</h4>
            @if(in_array(class_basename($profile), config('netframe.model_taggables')))
                @include('tags.element-display', ['tags' => $profile->tags])
            @endif
        </div>

        <a href="{{ url()->to('messages/form-message', [$profile->getType(), $profile->id, 'user', auth()->guard('web')->user()->id, 5]) }}" class="nf-btn btn-xl" data-toggle="modal" data-target="#modal-ajax">
            <span class="svgicon btn-img">
                @include('macros.svg-icons.talk')
            </span>
            <span class="btn-txt">
                {{ trans('page.leave_message') }}
            </span>
        </a>
    </div>

    @if($confidentiality == 1)
        @if($profile->medias->count() > 0)
            <div class="content-sidebar-block">
                <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}" class="nf-invisiblink"></a>
                <div class="content-sidebar-line content-sidebar-titline">
                    <span class="svgicon icon-docs">
                        @include('macros.svg-icons.doc')
                    </span>
                    <h4 class="content-sidebar-title" title="{{ trans('widgets.documents') }} ">{{ trans('widgets.documents') }} </h4>
                </div>
                <ul class="netframe-list">
                    @foreach($profile->lastMedias()->take(3)->get() as $media)
                        <li>
                            @if (!$media->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item">
                            @else
                                <a href="" class="viewMedia item"
                                    data-media-name="{{ $media->name }}"
                                    data-media-id="{{ $media->id }}"
                                    data-media-type="{{ $media->type }}"
                                    data-media-platform="{{ $media->platform }}"
                                    data-media-mime-type="{{ $media->mime_type }}"

                                    @if ($media->platform !== 'local')
                                        data-media-file-name="{{ $media->file_name }}"
                                    @endif
                                >
                            @endif
                                {!! HTML::thumbnail($media, '32', '32', array('class' => 'preview')) !!}
                                <span>{{ $media->name }}</span>
                            </a>
                        </li>
                    @endforeach
                    @if($profile->medias->count() > 3)
                        <li>
                            <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}" class="item">
                                <span class="others">+{{ $profile->medias->count()-3 }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

        @else
            <div class="content-sidebar-block">
                <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}" class="nf-invisiblink"></a>
                <div class="content-sidebar-line content-sidebar-titline">
                    <span class="svgicon icon-docs">
                        @include('macros.svg-icons.doc')
                    </span>
                    <h4 class="content-sidebar-title" title="{{ trans('widgets.documents') }}">{{ trans('widgets.documents') }}</h4>
                </div>
            </div>
        @endif
    @endif

    @if(!auth()->user()->visitor)
        @foreach($sidebarProfiles as $sidebarProfile)
            @if(in_array($sidebarProfile['type'], ['channel']) && count($sidebarProfile['profiles']) > 0)
                <div class="content-sidebar-block">
                    <div class="content-sidebar-line content-sidebar-titline">
                        <span class="svgicon">
                            @include('macros.svg-icons.channel')
                        </span>
                        <h4 class="content-sidebar-title" title="{{ ucfirst(trans_choice('page.feeds', count($sidebarProfile['profiles']))) }}">{{ ucfirst(trans_choice('page.feeds', count($sidebarProfile['profiles']))) }}</h4>
                    </div>
                    <ul class="list-unstyled sidebar-users">
                        @foreach($sidebarProfile['profiles'] as $profileChannel)
                            <li>
                                <a href="{{ $profileChannel->getUrl() }}" class="sidebar-users-line">
                                    {!! HTML::thumbImage($profileChannel->profile_media_id, 30, 30, [], $profileChannel->getType()) !!}
                                    <p class="name">
                                        {{ $profileChannel->getNameDisplay() }}
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    @endif

    @if(!auth()->user()->visitor)
        @foreach($sidebarProfiles as $sidebarProfile)
            @if(in_array($sidebarProfile['type'], ['task']) && count($sidebarProfile['profiles']) > 0)
                <div class="content-sidebar-block">
                    <div class="content-sidebar-line content-sidebar-titline">
                        <span class="svgicon">
                            @include('macros.svg-icons.tasks')
                        </span>
                        <h4 class="content-sidebar-title" title="{{ ucfirst(trans_choice('page.tasks', count($sidebarProfile['profiles']))) }}">{{ ucfirst(trans_choice('page.tasks', count($sidebarProfile['profiles']))) }}</h4>
                    </div>
                    <ul class="list-unstyled sidebar-users">
                        @foreach($sidebarProfile['profiles'] as $profileChannel)
                            <li>
                                <a href="{{ $profileChannel->getUrl() }}" class="sidebar-users-line">
                                    {!! HTML::thumbImage($profileChannel->profile_media_id, 30, 30, [], $profileChannel->getType(), 'avatar') !!}
                                    <p class="name">
                                        {{ $profileChannel->getNameDisplay() }}
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    @endif

    @if(!auth()->user()->visitor)
        @foreach($sidebarProfiles as $sidebarProfile)
            @if(in_array($sidebarProfile['type'], ['house', 'community', 'project']) && count($sidebarProfile['profiles']) > 0)
                <div class="content-sidebar-block">
                    <div class="content-sidebar-line content-sidebar-titline">
                        <span class="svgicon">
                            @include('macros.svg-icons.' . $sidebarProfile['type'])
                        </span>
                        <h4 class="content-sidebar-title" title="{{ ucfirst(trans_choice('page.' . $sidebarProfile['type'], count($sidebarProfile['profiles']))) }}">{{ ucfirst(trans_choice('page.' . $sidebarProfile['type'], count($sidebarProfile['profiles']))) }}</h4>
                    </div>
                    <ul class="list-unstyled sidebar-users">
                        @foreach($sidebarProfile['profiles'] as $profileChannel)
                            <li>
                                <a href="{{ $profileChannel->getUrl() }}" class="sidebar-users-line">
                                    {!! HTML::thumbImage($profileChannel->profile_media_id, 30, 30, [], $profileChannel->getType(), 'avatar') !!}
                                    <p class="name">
                                        {{ $profileChannel->getNameDisplay() }}
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    @endif

    @if(!auth()->user()->visitor && $confidentiality == 1)
        <div class="content-sidebar-block">
            <div class="content-sidebar-line content-sidebar-titline">
                <span class="svgicon icon-members">
                    @include('macros.svg-icons.members')
                </span>
                <h4 class="content-sidebar-title" title="{{ ucfirst(trans_choice('page.members', $profile->users()->count())) }}">{{ ucfirst(trans_choice('page.members', $profile->users()->count())) }}</h4>
            </div>
            <ul class="list-unstyled sidebar-users">
                @foreach($profile->validatedUsers as $member)
                    <li>
                        <a href="{{ $member->getUrl() }}" class="sidebar-users-line">
                            {!! HTML::thumbImage(
                                $member->profile_media_id,
                                30,
                                30,
                                [],
                                'user',
                                'avatar',
                                $member
                            ) !!}
                            <p class="name">{{ $member->getNameDisplay() }}</p>
                            @if($member->pivot->roles_id == 1)
                                <span class="label">Admin</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($activeCalendar) && $activeCalendar)
        <div class="content-sidebar-block">
            <a class="nf-invisiblink" href="{{ url()->route('calendar.home', ['profile_type' => $profile->getType(), 'profile_id' => $profile->id]) }}" ></a>
            <div class="content-sidebar-line content-sidebar-titline">
                <span class="svgicon icon-cal">
                    @include('macros.svg-icons.calendar')
                </span>
                <h4 class="content-sidebar-title" title="{{ trans('netframe.navEvent') }}">{{ trans('netframe.navEvent') }}</h4>
            </div>

            <ul class='list-unstyled sidebar-users'>
                @foreach($nextEvents as $event)
                    <li>
                        <a class="sidebar-users-line" href="{{ $event->post->getUrl() }}">
                            <span class="svgicon">
                                @include('macros.svg-icons.calendar')
                            </span>
                            <p class="name">
                                {{ $event->post->getName() }}
                            </p>
                            <p class="date">
                                {{ \App\Helpers\DateHelper::eventDate($event->post->date, $event->post->time, $event->post->date_end, $event->post->time_end) }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{--
    @if($confidentiality == 1)
        @if(isset($activeCalendar) && $activeCalendar)
            <div class="content-sidebar-block">
                <a class="nf-invisiblink" href="{{ url()->route('calendar.home') }}"></a>
                <div class="content-sidebar-line content-sidebar-titline">
                    <span class="svgicon icon-cal">
                        @include('macros.svg-icons.calendar')
                    </span>
                    <h4 class="content-sidebar-title" title="{{ trans('netframe.navEvent') }}">{{ trans('netframe.navEvent') }}</h4>
                </div>
                <div class='sidebar-calendar'></div>
            </div>
        @endif
    @endif
    --}}

@section('javascripts')
@parent
@if($confidentiality == 1)
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
                url: laroute.route('calendar.dates', { type : 'profile', profile_type: '{{$profile->getType()}}', profile_id: {{ $profile->id }} }),
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
@endif
@stop
