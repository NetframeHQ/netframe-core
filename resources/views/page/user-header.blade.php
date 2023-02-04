@section('favicon')
{{$profile->profileImageSrc()}}
@endsection


{{-- HEADER OF USER & MY PROFILE --}}

<div class="main-header">
    <div class="main-header-infos">
        @if($profile->profileImage)
            <span class="avatar">
                {!! HTML::thumbnail(
                    $profile->profileImage,
                    '40',
                    '40',
                    array('class' => 'float-left'),
                    asset('assets/img/avatar/'.$profile->getType().'.jpg'),
                    null,
                    'user',
                ) !!}
            </span>
        @else
            {{--
            <span class="svgicon icon-talkgroup">
                @include('macros.svg-icons.user_big')
            </span>
            --}}
            {!! HTML::userAvatar($profile, 40, 'avatar') !!}
        @endif
        <h2 class="main-header-title">{{ $dataUser->getNameDisplay() }}</h2>
    </div>

    {{-- CUSTOM LINKS PAGE USER & MY PROFILE --}}
    <ul class="nf-actions">
        {{-- ••• MENU --}}
        <li class="nf-action nf-custom-nav">
            <a href="#" class="nf-btn btn-submenu btn-ico">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    @if($profile->getType() == 'user' && $profile->id != auth('web')->user()->id)
                        {{-- CUSTOM LINK DISCUSSION --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('channels.messenger', ['userId' => $profile->id]) }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.talk')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.channel') }}
                                </span>
                            </a>
                        </li>
                        {{--
                        <li>
                            <a class="nf-btn" href="{{url()->route('instance.manageRights', ['id'=>$profile->id])}}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.settings')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myInstance') }}
                                </span>
                            </a>
                        </li>
                        --}}
                    @endif
                    @if($rights && $rights < 3 && $profile->getType() === 'user')
                        {{-- CUSTOM LINK MY DOCUMENTS --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.doc')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myDocuments') }}
                                </span>
                            </a>
                        </li>

                        {{-- CUSTOM LINK DIRECTORY
                        <li>
                            <a class="nf-btn" href="{{ url()->route('directory.home') }}">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.members')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myFriends') }}
                                </span>
                            </a>
                        </li>
                        --}}

                        {{-- CUSTOM LINK SETTINGS MY PROFILE --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('account.account') }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.settings')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myInstance') }}
                                </span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        {{-- SIDEBAR TOGGLE --}}
        <li class="nf-action">
            <a href="#" class="content-sidebar-toggle nf-btn btn-ico">
                <span class="btn-img svgicon fn-close">
                    @include('macros.svg-icons.sidebar-close2')
                </span>
                <span class="btn-txt fn-close">
                    {{ trans('netframe.close_sidebar') }}
                </span>
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.sidebar-open')
                </span>
                <span class="btn-txt">
                    {{ trans('netframe.open_sidebar') }}
                </span>
            </a>
        </li>
    </ul>
</div>