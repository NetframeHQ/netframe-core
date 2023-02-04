<li class="nf-list-setting member-{{ $profile->id }} @if($profile->active == 0) disabled @endif">
    <a class="nf-invisiblink" href="{{ $profile->getUrl() }}"></a>
    @if($profile->mosaicImage() != null)
        <span class="avatar">
            {!! HTML::thumbnail($profile->mosaicImage(), '30', '30', array(), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
        </span>
    @else
        <span class="svgicon">
            @include('macros.svg-icons.'.$profile->getType())
        </span>
    @endif

    <div class="nf-list-infos">
        <div class="nf-list-title" @if($profile->getType()=='user') id="user-{{$profile->id}}" @endif>
            {{ $profile->getNameDisplay() }}
            @if($profile->getType() == 'user')
                <span class="nf-list-title-infos">
                    ({{ trans('instances.profiles.roles.'.$profile->pivot->roles_id) }})
                </span>
            @endif
        </div>
        <span class="nf-list-subtitle">
            {{ trans('instances.profiles.createdAt') }} : {{ \App\Helpers\DateHelper::feedDate($profile->created_at) }}
        </span>
    </div>
    <ul class="nf-actions">
        <!-- TODO : REPARER HREF DES BOUTONS SETTINGS -->
        {{--
        @if(($profile->getType() == 'user' && $profile->id != auth()->guard('web')->user()->id) || $profile->getType() != 'user')
            <li class="nf-action">
                <a class="nf-btn btn-ico" title="{{ trans('netframe.myInstance') }}" href="{{ url()->route($profileType.'.edit', ['id' => $profile->id])  }}">
                <a class="nf-btn btn-ico" title="{{ trans('netframe.myInstance') }}" href="{{url()->route('instance.manageRights', ['id'=>$profile->id])}}">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.settings')
                    </span>
                </a>
            </li>
        @endif
        --}}

        {{-- CUSTOM LINKS MANAGE ENTITY/GROUP/PROJECTS --}}
        {{--
        <li class="nf-action nf-custom-nav">
            <a class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    <li>
                        <a class="nf-btn" href="{{ url()->route($profileType.'.edit', ['id' => $profile->id])  }}">

                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.settings')
                            </span>
                            <span class="btn-txt">
                                {{ trans('netframe.myInstance') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        --}}

        @if($profile->active == 0)
            <li class="nf-action">
                <div class="nf-lbl">
                    <span class="lbl-txt">
                        {{ trans('instances.profiles.disabled') }}
                    </span>
                </div>
            </li>
        @endif
        {{-- ••• MENU --}}
        <li class="nf-action">
            <a href="#" class="nf-btn btn-submenu btn-ico">
                <span class="btn-img svgicon">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">


                    @if($profile->getType() == 'user')
                        <li>
                            <a class="nf-btn" href="{{url()->route('instance.manageRights', ['id'=>$profile->id])}}">
                                <span class="btn-txt">
                                    {{trans('instances.profiles.manage-user')}}
                                </span>
                            </a>
                        </li>
                        @if(isset($activeVirtualUsers) && $activeVirtualUsers)
                            <li>
                                <a class="nf-btn" href="{{url()->route('instance.virtualuser.list', ['userId'=>$profile->id])}}">
                                    <span class="btn-txt">
                                        {{trans('instances.profiles.manageVirtualUser')}}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @else
                        <li>
                            <a class="nf-btn" href="{{url()->route('instance.manage', ['profileType'=>$profileType, 'id'=>$profile->id])}}">
                                <span class="btn-txt">
                                    {{trans('instances.profiles.manage')}}
                                </span>
                            </a>
                        </li>
                    @endif
                    @if(($profile->getType() == 'user' && $profile->id != auth()->guard('web')->user()->id) || $profile->getType() != 'user')
                        <li>
                            @if($profile->active == 1)
                                <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.profile.activation', ['profileType' => $profileType]) }}" data-toggle-state="0" data-profile-id="{{ $profile->id }}">
                                    <span class="btn-txt">
                                        {{ trans('instances.profiles.disable') }}
                                    </span>
                                </a>
                            @else
                                <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.profile.activation', ['profileType' => $profileType]) }}" data-toggle-state="1" data-profile-id="{{ $profile->id }}">
                                    <span class="btn-txt">
                                        {{ trans('instances.profiles.enable') }}
                                    </span>
                                </a>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>
        </li>
    </ul>
</li>
