<li class="nf-list-setting member-{{ $member->id }} @if($member->active == 0) disabled @endif">
    <a class="nf-invisiblink" href="{{ $member->getUrl() }}"></a>
    {!! HTML::thumbImage(
        $member->profile_media_id,
        30,
        30,
        [],
        $member->getType(),
        'avatar float-left',
        $member
    ) !!}
    <div class="nf-list-infos">
        <div class="nf-list-title">
            {{ $member->getNameDisplay() }}
        </div>
        <div class="nf-list-subtitle">
            {{ trans('instances.profiles.createdAt') }} {{ \App\Helpers\DateHelper::feedDate($member->created_at) }}
        </div>
    </div>
    @php
        if (class_basename($member) == 'User') {
            $concernedMember = $member;
        } else {
            $concernedMember = $profile;
        }
    @endphp
    @if(($concernedMember->id != auth()->guard('web')->user()->id || class_basename($concernedMember != 'User')) && !isset($fromStats))
        <ul class="nf-actions">
            @if((class_basename($concernedMember) == 'User' && $profile->active == 0) || $member->active == 0)
                <li class="nf-action">
                    <div class="nf-lbl">
                        <span class="lbl-txt">
                            {{ trans('instances.profiles.disabled') }}
                        </span>
                    </div>
                </li>
            @endif
            @if(isset($concernedMember->pivot) && isset($concernedMember->pivot->status) && $concernedMember->pivot->status == 0)
                <li class="nf-action">
                    <div class="nf-lbl">
                        <span class="lbl-txt">
                            {{ trans('members.inviteSended') }}
                        </span>
                    </div>
                </li>
            @endif
            @if(class_basename($concernedMember) == 'User' &&
                (
                    (isset($concernedMember->pivot) && $concernedMember->pivot->status != 2) ||
                    !isset($concernedMember->pivot)
                ))
                {!! HTML::userRights($profile, $member, '.member-' . $member->id, (isset($fromInvite) && $fromInvite)) !!}
            @endif
            {{-- $form : manage exception when attach members from instance manager --}}
            @if( !isset($form) &&
                (
                    (class_basename($profile) != 'Instance' && isset($concernedMember->pivot) && $concernedMember->pivot->status != 1 && $concernedMember->pivot->status != 3) ||
                    (class_basename($profile) == 'Instance' && $concernedMember->getType() == 'user' && $concernedMember->id != auth()->guard('web')->user()->id) ||
                    (class_basename($concernedMember) != 'User')
                )
            )
                <li class="nf-action">
                    <a href="#" class="nf-btn btn-submenu btn-ico">
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.menu')
                        </span>
                    </a>
                    <div class="submenu-container submenu-right">
                        <ul class="submenu" data-target-return=".member-{{ $member->id }}">
                            @if(class_basename($profile) != 'Instance' && isset($concernedMember->pivot))
                                @if($concernedMember->pivot->status == 0)
                                    {{--
                                    <li class="nf-action">
                                        <a href="#" class="nf-btn fn-resend-invitation" data-tl-user="{{ json_encode([
                                            'profile_id' => $profile->id,
                                            'friend_id' => $concernedMember->id,
                                            'users_id'  => auth()->guard('web')->user()->id,
                                            'type_profile' => $profile->getType(),
                                            'user_role' => $concernedMember->pivot->roles_id
                                        ]) }}" data-tl-action="resend">
                                            <span class="btn-txt">
                                                {{trans('members.resend')}}
                                            </span>
                                        </a>
                                    </li>
                                    --}}
                                    {!! HTML::removeInviteBtn( [
                                        'profile_id' => $profile->id,
                                        'friend_id' => $concernedMember->id,
                                        'users_id'  => auth()->guard('web')->user()->id,
                                        'type_profile' => $profile->getType(),
                                        'user_role' => $concernedMember->pivot->roles_id
                                    ]) !!}
                                @elseif($concernedMember->pivot->status == 2)
                                    {!! HTML::joinAnswerBtn( [
                                        'profile_id' => $profile->id,
                                        'friend_id' => $concernedMember->id,
                                        'users_id'  => auth()->guard('web')->user()->id,
                                        'type_profile' => $profile->getType()
                                    ]) !!}
                                @endif
                            @elseif(class_basename($profile) == 'Instance' && $concernedMember->getType() == 'user' && $concernedMember->id != auth()->guard('web')->user()->id)
                                    {!! HTML::instanceRoleAction( [
                                        'instance_id' => $profile->id,
                                        'user_id' => $concernedMember->id,
                                        'user_role' => $concernedMember->pivot->roles_id
                                    ]) !!}
                            @elseif(class_basename($concernedMember) != 'User')
                                {{-- Sous menu pour la gestion des profils --}}
                                <li>
                                    <a class="nf-btn" href="{{url()->route('instance.manage', ['profileType' => $member->getInstanceRelation(), 'id' => $member->id])}}">
                                        <span class="btn-txt">
                                            {{trans('instances.profiles.manage')}}
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    @if($member->active == 1)
                                        <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.profile.activation', ['profileType' => $member->getInstanceRelation()]) }}" data-toggle-state="0" data-profile-id="{{ $member->id }}">
                                            <span class="btn-txt">
                                                {{ trans('instances.profiles.disable') }}
                                            </span>
                                        </a>
                                    @else
                                        <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.profile.activation', ['profileType' => $member->getInstanceRelation()]) }}" data-toggle-state="1" data-profile-id="{{ $member->id }}">
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
            @endif

        </ul>
    @endif
</>