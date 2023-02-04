@if(!auth()->user()->visitor)
    @include('search.form')
@endif
<div class="navigation-menu">
    <ul class="list-unstyled">
        <li class="nav-mobile">
            <a href="{{ url()->route('netframe.workspace.home') }}" class="{{(in_array(url()->current(), [url()->route('user.timeline'), url()->route('netframe.anynews')])) ? 'active' : '' }}" title="{{ trans('netframe.timeline') }}">
                <span class="svgicon icon-home">
                    @include('macros.svg-icons.home')
                </span>
            </a>
        </li>
        @if(!auth()->user()->visitor)
            <li class="nav-mobile">
                <a href="{{ url()->route('channels.main') }}" class="nav-messenger @if(Route::currentRouteName() == 'channels.home') active @endif" title="{{ trans('channels.feeds.title') }}" >
                    <span class="svgicon icon-talk">
                        @include('macros.svg-icons.talk')
                    </span>
                    <span class="badge-notif channels-notifs"  data-nb="0">0</span>
                </a>
            </li>
            <li>
                <a href="{{ url()->route('medias_explorer-general') }}" class="@if(strpos(Route::currentRouteName(), 'medias_explorer') !== false) active @endif" title="{{ trans('netframe.documents') }}">
                    <span class="svgicon icon-docs">
                        @include('macros.svg-icons.doc')
                    </span>
                </a>
            </li>
            @if($activeCollab)
                <li>
                    <a href="/collab" class="{{(url()->current() == url()->route('collab.home')) ? 'active' : '' }}" title="{{ trans('netframe.leftMenu.notes') }}" >
                        <span class="svgicon icon-notes">
                            @include('macros.svg-icons.notes')
                        </span>
                    </a>
                </li>
            @endif
            @if($activeTasks)
                <li>
                    <a href="{{ url()->route('task.home') }}" class="{{(url()->current() == url()->route('task.home')) ? 'active' : '' }}" title="{{ trans('netframe.leftMenu.task') }}">
                        <span class="svgicon icon-tasks">
                            @include('macros.svg-icons.tasks')
                        </span>
                    </a>
                </li>
            @endif
            @if(isset($activeCalendar) && $activeCalendar)
                <li>
                    <a href="{{ url()->route('calendar.home') }}" class="{{(url()->current() == url()->route('calendar.home')) ? 'active' : '' }}" title="{{ trans('netframe.navEvent') }}">
                        <span class="svgicon icon-cal">
                            @include('macros.svg-icons.calendar')
                        </span>
                    </a>
                </li>
            @endif
            @if($activeMap && $gdpr_agrement)
                <li>
                    <a href="{{ url()->route('profile.map.location') }}" class="{{(url()->current() == url()->route('profile.map.location')) ? 'active' : '' }}" title="{{ trans('netframe.navMap') }}">
                        <span class="svgicon icon-loc">
                            @include('macros.svg-icons.localisation')
                        </span>
                    </a>
                </li>
            @endif
        @endif
    </ul>
  </div>