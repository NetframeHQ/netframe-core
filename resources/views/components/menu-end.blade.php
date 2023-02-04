<div class="navigation-user">
    <div class="nav-fx">
        @include('macros.svg-icons.search-bg')
    </div>
    <ul class="navigation-user-links">
        @if(session('hasMessages'))
            <li class="notifs">
                <a href="{{ url()->route('messages_inbox') }}" class="wrap-badge btn-notif notifications" title="{{ trans('netframe.navMessages') }}">
                    <span class="svgicon icon-inbox">
                        @include('macros.svg-icons.inbox')
                    </span>
                    @if(HTML::messagesNumber() > 0)
                        <span class="badge-notif">{{ HTML::messagesNumber() }}</span>
                    @endif
                </a>
            </li>
        @endif
        <li>
            <a title="{{ trans('netframe.notifications') }}" class="wrap-badge btn-notif notifications fn-tl-popover" role="button" data-toggle="popover" data-url="{{ url()->route('notifications.lasts') }}" data-html="true" data-trigger="focus" data-placement="bottom" data-content="" title="Notifications" >
                <span class="svgicon icon-notifs">
                    @include('macros.svg-icons.notifs')
                </span>
                @if(HTML::notifyNumber() > 0)
                    <span class="badge-notif">{{ HTML::notifyNumber() }}</span>
                @endif
            </a>
        </li>
    </ul>

    <a href="{{ auth()->guard('web')->user()->getUrl() }}" class="nf-btn btn-nobg" title="{{ auth()->guard('web')->user()->getNameDisplay() }}">
        @if(auth()->guard('web')->user()->profileImage != null)
            <span class="btn-img avatar">
                {!! HTML::thumbnail(
                    auth()->guard('web')->user()->profileImage,
                    30,
                    30,
                    [],
                    asset('assets/img/avatar/user.jpg'),
                    null,
                    'user'
                ) !!}
            </span>
        @else
            {{--
            <span class="btn-img svgicon">
                @include('macros.svg-icons.user')
            </span>
            --}}
            {!! HTML::userAvatar(auth()->guard('web')->user(), 30) !!}
        @endif
        {{--<span class="btn-txt">
            {{ auth()->guard('web')->user()->getNameDisplay() }}
        </span>--}}
    </a>

    <a href="#" class="nf-btn btn-submenu btn-ico btn-nobg">
        <span class="svgicon btn-img">
            @include('macros.svg-icons.arrow-down')
        </span>
    </a>
   <div class="submenu-container submenu-right">
        <ul class="submenu">
            <li>
                <a class="nf-btn" title="{{ trans('netframe.navUser') }}" href="{{ auth()->guard('web')->user()->getUrl() }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.user')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.navUser') }}
                    </span>
                </a>
            </li>
            <li>
                <a class="nf-btn" title="{{ trans('netframe.myDocuments') }}" href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.doc')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.myDocuments') }}
                    </span>
                </a>
            </li>
            <li>
                <a class="nf-btn" title="{{ trans('netframe.directory') }}" href="{{ url()->route('directory.home') }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.directory')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.directory') }}
                    </span>
                </a>
            </li>
            {{--
            {{ url()->route('friends.results') }}
            <li>
                <a class="nf-btn" title="{{ trans('netframe.directory') }}" href="{{ url()->route('directory.home') }}">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.doc')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.directory') }}
                    </span>
                </a>
            </li>
            --}}
            <li class="sep"></li>
            <li>
                <a class="nf-btn" title="{{ trans('netframe.myAccount') }}" href="{{ url()->route('account.account') }}">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.settings')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.myAccount') }}
                    </span>
                </a>
            </li>
            @if(session()->has('instanceRoleId') && session('instanceRoleId') <= 2)
                <li>
                    <a class="nf-btn" title="{{ trans('netframe.myInstance') }}" href="{{ url()->route('instance.boarding') }}">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.settings')
                        </span>
                        <span class="btn-txt">
                            <!-- {{ trans('netframe.myInstance') }} -->
                            {{ $globalInstanceName }}
                        </span>
                    </a>
                </li>
            @endif
            <li class="sep"></li>
            <li>
                <a class="nf-btn" title="{{ trans('netframe.logout') }}" href="{{ url()->route('auth.logout') }}" title="{{ trans('netframe.logout') }}">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.leave')
                    </span>
                    <span class="btn-txt">
                    {{ trans('netframe.logout') }}
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>


