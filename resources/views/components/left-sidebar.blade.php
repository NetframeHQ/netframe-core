<nav id="sidebar-wrapper" class="sidebar-wrapper">
    <a href="{{ url()->route('netframe.workspace.home') }}" title="{{ trans('netframe.leftMenu.home') }}" class="navigation-logo">
        @if(isset($menuLogo))
            <div style="background-image: url('{{ $menuLogo }}')" class="logo-img nf-logosquare nf-logorect menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @else
            <div style="background-image: url('{{ asset('assets/img/logo.png') }}')" class="logo-img nf-logosquare nf-logorect menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @endif

        @if(isset($menuLogoDark))
            <div style="background-image: url('{{ $menuLogoDark }}')" class="logo-img nf-logosquare nf-logorect menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @else
            <div style="background-image: url('{{ asset('assets/img/logo.png') }}')" class="logo-img nf-logosquare nf-logorect menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @endif
    </a>

    <div id="skip">
        <a class="nf-btn" href="#nav_skipped">
            <span class="svgicon icon-home">
                @include('macros.svg-icons.arrow-right')
            </span>
            <span class="btn-txt">Skip navigation</span>
        </a>
    </div>

    <ul class="sidebar-links">
        @if(isset($activeHomeLink) && $activeHomeLink)
            <li>
                <a href="{{ url()->route('user.timeline') }}" title="{{ trans('netframe.timeline') }}" class="{{(url()->current() == url()->route('user.timeline')) ? 'active' : '' }}">
                    <span class="svgicon icon-home">
                        @include('macros.svg-icons.home')
                    </span>
                    <span>{{ trans('netframe.leftMenu.home') }}</span>
                </a>
            </li>
        @endif
        @if(isset($active24h) && $active24h)
            <li>
                <a href="{{ url()->route('netframe.anynews') }}" title="{{ trans('netframe.leftMenu.24h') }}" class="{{(url()->current() == url()->route('netframe.anynews')) ? 'active' : '' }}">
                    <span class="svgicon icon-last24">
                        @include('macros.svg-icons.last24')
                    </span>
                    <span>{{ trans('netframe.leftMenu.24h') }}</span>
                </a>
            </li>
        @endif
        @if(!auth()->user()->visitor)
            <li>
                <a href="{{ url()->route('medias_explorer-general') }}" title="{{ trans('netframe.documents') }}" class="@if(strpos(Route::currentRouteName(), 'medias_explorer') !== false) active @endif">
                    <span class="svgicon icon-docs">
                        @include('macros.svg-icons.doc')
                    </span>
                    <span>{{ trans('netframe.documents') }}</span>
                </a>
            </li>
            @if($activeCollab)
                <li>
                    <a href="/collab" title="{{ trans('netframe.leftMenu.notes') }}" class="{{(url()->current() == url()->route('collab.home')) ? 'active' : '' }}">
                        <span class="svgicon icon-last24">
                            @include('macros.svg-icons.notes')
                        </span>
                        <span>{{ trans('netframe.leftMenu.notes') }}</span>
                    </a>
                </li>
            @endif
            @if($activeTasks)
                <li>
                    <a href="{{ url()->route('task.home') }}" title="{{ trans('netframe.leftMenu.task') }}" class="{{(url()->current() == url()->route('task.home')) ? 'active' : '' }}">
                        <span class="svgicon icon-last24">
                            @include('macros.svg-icons.tasks')
                        </span>
                        <span>{{ trans('netframe.leftMenu.task') }}</span>
                    </a>
                </li>
            @endif
            @if(isset($activeCalendar) && $activeCalendar)
                <li>
                    <a href="{{ url()->route('calendar.home') }}" title="{{ trans('netframe.navEvent') }}"  class="{{(url()->current() == url()->route('calendar.home')) ? 'active' : '' }}">
                        <span class="svgicon icon-cal">
                            @include('macros.svg-icons.calendar')
                        </span>
                        <span>{{ trans('netframe.navEvent') }}</span>
                    </a>
                </li>
            @endif
            @if($activeMap && $gdpr_agrement)
                <li>
                    <a href="{{ url()->route('profile.map.location') }}" title="{{ trans('netframe.navMap') }}"  class="{{(url()->current() == url()->route('profile.map.location')) ? 'active' : '' }}">
                        <span class="svgicon icon-loc">
                            @include('macros.svg-icons.localisation')
                        </span>
                        <span>{{ trans('netframe.navMap') }}</span>
                    </a>
                </li>
            @endif
        @endif
    </ul>

    <!-- ENTITEE / PROJETS / GROUPES / DISCUSSIONS / USERS -->

    @foreach($profilesTypes as $keyProfile)
        <div class="sidebar-title" title="{{ trans('netframe.leftMenu.'.$keyProfile) }}">
            <h3>{{ trans('netframe.leftMenu.'.$keyProfile) }}</h3>
            @if(!auth()->user()->visitor && session('profileAuth.userCanCreate.'.$keyProfile))
                <a class="nf-btn btn-ico btn-nobg" href="{{ url()->route($keyProfile.'.edit') }}" alt="{{ trans('netframe.createYour'.ucfirst($keyProfile)) }}" title="{{ trans('netframe.createYour'.ucfirst($keyProfile)) }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.plus')
                    </span>
                </a>
                <a class="nf-btn btn-ico btn-nobg" href="{{ url()->route($keyProfile.'.manage') }}" alt="{{ trans('netframe.manageYour'.ucfirst($keyProfile)) }}" title="{{ trans('netframe.manageYour'.ucfirst($keyProfile)) }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.settings-xs')
                    </span>
                </a>
            @endif
        </div>

    <!-- ENTITEE / PROJETS / GROUPES -->

        @if(isset($userProfiles[$keyProfile]))
            <ul class="sidebar-links" id="{{$keyProfile}}-list">
                @foreach($userProfiles[$keyProfile] as $profile)
                    <li class="{{ ($loop->iteration > 3 && (session()->has('profileDisplay') && session('profileDisplay') != $keyProfile || !session()->has('profileDisplay'))) ? 'd-none' : '' }}">
                        <a href="{{ $profile->getUrl() }}"  class="{{(session()->has('profileDisplay') && session('profileDisplay') == $keyProfile && session('profileDisplayId') == $profile->id) ? 'active' : '' }}" title="{{ $profile->getNameDisplay().'' }}">
                            @if($profile->profileImage)
                                <span class="svgicon icon-talkentity">
                                    {!! HTML::thumbnail($profile->profileImage, '30', '30', array('class' => 'float-left'), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
                                </span>
                            @else
                                {!! HTML::profileIcon($profile->getType()) !!}
                            @endif
                            @if($profile->confidentiality == 0)
                                <span class="svgicon private">
                                    @include('macros.svg-icons.private')
                                </span>
                            @endif
                            <span class="txt">{{ $profile->getNameDisplay()."" }}</span>

                        </a>
                    </li>
                @endforeach
                @if(!auth()->user()->visitor && session('profileAuth.userCanCreate.'.$keyProfile))
                    <li>
                        <a href="{{ url()->route($keyProfile.'.edit') }}" title="{{ trans('netframe.createYour'.ucfirst($keyProfile)) }}">
                            <span class="svgicon icon-talkentity">
                                @include('macros.svg-icons.add')
                            </span>
                            <span class="txt">{{ trans('netframe.createYour'.ucfirst($keyProfile)) }}</span>

                        </a>
                    </li>
                @endif
                @if($userProfiles[$keyProfile]->count() > 3
                    && (session()->has('profileDisplay') && session('profileDisplay') != $keyProfile || !session()->has('profileDisplay'))
                    )
                    <li>
                        <a class="more fn-more-sidebar-link" data-target="{{$keyProfile}}-list" href="#" title="{{trans('page.viewMore'.ucfirst($keyProfile))}} ({{$userProfiles[$keyProfile]->count() - 3}})">
                            <span>{{ trans('page.viewMore'.ucfirst($keyProfile)) }} (<span class="num">{{ $userProfiles[$keyProfile]->count() - 3 }}</span>)</span>
                        </a>
                    </li>
                @endif
            </ul>
        @endif
    @endforeach

    @include('channel.main')

</nav>