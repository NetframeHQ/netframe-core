
<li class="nf-action">
    <a href="#" class="nf-btn btn-submenu">
        <span class="btn-txt">
            @if($status == 3)
                {{ trans('members.change.isBan') }}
            @elseif($right != -1)
                {{ trans($profile->rolesLangKey . $right) }}
            @else
                {{ trans('instances.manage.chooseRole') }}
            @endif
        </span>
        <span class="btn-img svgicon">
            @include('macros.svg-icons.arrow-down')
        </span>
    </a>
    <div class="submenu-container submenu-right">
        <ul class="submenu fn-right-management" data-target-return="{{$domTargetRelaod}}" data-invite="{{ $fromInvite }}">
            @foreach($profile->listRoles() as $roleKey=>$roleSlug)
                <li>
                    <a href="#" class="nf-btn @if($right == $roleKey) active @endif" data-status="{{$roleKey}}" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                        <span class="btn-txt">
                            {{ trans($profile->rolesLangKey . $roleKey) }}
                        </span>
                    </a>
                </li>
            @endforeach

            @if(class_basename($profile) != 'Instance' && $right != -1)
                <li class="sep"></li>
                <li>
                    <a href="#" class="nf-btn exclude @if($right == -1) hide @endif" data-status="-1" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                        <span class="btn-txt">
                            {{ trans('members.change.exclude') }}
                        </span>
                    </a>
                </li>
                <li>
                    @if($status == 3)
                        <a href="#" class="nf-btn ban" data-status="-3" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                            <span class="btn-txt">
                                {{ trans('members.change.release') }}
                            </span>
                        </a>
                    @else
                        <a href="#" class="nf-btn ban" data-status="-2" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                            <span class="btn-txt">
                                {{ trans('members.change.blacklist') }}
                            </span>
                        </a>
                    @endif
                </li>
            @elseif(class_basename($profile) == 'Instance')
                {{-- manage enable/disables for profiles --}}
                <li class="sep"></li>
                @if($user->active == 1)
                    <a href="#" class="nf-btn"  data-status="disable" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                        <span class="btn-txt">
                            {{ trans('instances.profiles.disable') }}
                        </span>
                    </a>
                @else
                    <a href="#" class="nf-btn"  data-status="enable" data-id="{{$profile->id}}" data-type="{{$profile->getType()}}" data-user="{{$user->id}}" data-from="{{$fromProfile}}">
                        <span class="btn-txt">
                            {{ trans('instances.profiles.enable') }}
                        </span>
                    </a>
                @endif
            @endif
        </ul>
    </div>
</li>
