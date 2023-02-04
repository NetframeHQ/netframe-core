@extends('channel.form.main')

@section('subcontent')
    <div class="nf-form nf-col-2">

        <!-- AVATAR -->
        <!-- <div class="nf-form-cell nf-cell-avatar">
            <div class="nf-form-select" id="js-profile-picture">
                <a class="nf-btn btn-submenu" id="profile-image-container">
                    @if($channel->profileImage != null)
                        {!! HTML::thumbImage($channel->profileImage, 80, 80, [], $channel->getType(), 'avatar profile-image') !!}
                    @endif
                    <span class="svgicon btn-img @if($channel->profileImage != null) d-none @endif">
                        @include('macros.svg-icons.channel')
                    </span>

                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.arrow-down')
                    </span>
                </a>
                <div class="submenu-container submenu-left">
                    <ul class="submenu">
                        {{--
                        <li>
                            <a class="nf-btn">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.emoji')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('profiles.manage.useEmoji') }}
                                </span>
                            </a>
                        </li>
                        --}}
                        <li>
                            <label class="nf-btn" id="profile-picture">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.plus')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('profiles.manage.uploadAvatar') }}
                                </span>

                                @include('page.partials.form-profile-picture', [
                                    'profile_type' => 'Channel',
                                    'profile_id' => $channel->id,
                                    'disable_extra_links' => 1
                                ])
                                <input type="file" class="btn-input">
                            </label>
                        </li>
                        <li class="sep fn-remove-avatar {{ (($channel->profileImage != null) ? '' : 'd-none' ) }}">
                            <i></i>
                        </li>
                        <li class="fn-remove-avatar {{ (($channel->profileImage != null) ? '' : 'd-none' ) }}">
                            <a class="nf-btn">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.trash')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('profiles.manage.resetAvatar') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <span class="nf-form-label">
                {{ trans('profiles.manage.avatar') }}
            </span>
        </div> -->

        {{ Form::open(['id' => 'form-channel-edit']) }}
            <div id="publish-as-hidden-ch">
                {{ Form::hidden("id", $channel->id) }}
                {{ Form::hidden("id_foreign", ($channel->profile_id) ? $channel->profile_id : auth()->guard('web')->user()->id ) }}
                {{ Form::hidden("type_foreign", ($channel->profile_type) ? strtolower(class_basename($channel->profile)) : 'user' ) }}
            </div>


            <label class="nf-form-cell nf-cell-full nf-cell-name @if($errors->has('name')) nf-cell-error @endif">
                {{ Form::text('name', $channel->name, ['class'=>'nf-form-input '.(($errors->has('name')) ? 'is-invalid' : '')] ) }}
                <span class="nf-form-label">
                    {{ trans('channels.edit.name') }}
                </span>
                {!! $errors->first('name', '<p class="nf-form-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>
            


            @if(!isset($channel->updateMode) || (isset($channel->updateMode) && $channel->updateMode == 1 && isset($NetframeProfiles[$channel->ownerProfil()->get()->first()->pivot->type_profil][$channel->ownerProfil()->get()->first()->pivot->profils_has_project_id])))
                <!-- PUBLIC PRIVATE -->
                <label class="nf-form-cell nf-form-checkbox nf-cell-privacy @if($errors->has('confidentiality')) nf-cell-error @endif ">
                    <span class="nf-form-label">
                        {{ trans('netframe.public') }}
                    </span>
                    {{ Form::checkbox('confidentiality', '1', ($channel->confidentiality == 0), ['class' => 'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('netframe.private') }}
                    </span>
                    <div class="nf-form-cell-fx"></div>
                </label>
            @endif


            <hr>

            <!-- DESCRIPTION -->
            <label class="nf-form-cell nf-cell-full @if($errors->has('description')) nf-cell-error @endif">
                {{ Form::textarea('description', $channel->description, ['rows'=>'1', 'class'=>'nf-form-input '.(($errors->has('content')) ? 'is-invalid' : '')] ) }}
                <!-- <textarea class="nf-form-input" placeholder="Écrivez ici…"></textarea> -->
                <span class="nf-form-label">
                    {{ trans('channels.edit.description') }}
                </span>
                {!! $errors->first('description', '<div class="nf-form-feedback">:message</div>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            @include('components.forms.tags', ['element' => $channel])

            @if ($errors->has('id_foreign') || $errors->has('type_foreign'))
                <div class="nf-form-feedback">{{ trans('playlist.publish_as_not_selected_error') }}</div>
            @endif

            <div class="nf-form-validation">
                <div class="nf-form-validactions">
                    <div class="nf-form-cell nf-cell-full">
                        @if(!isset($channel->updateMode) || (isset($channel->updateMode) && $channel->updateMode == 1 && isset($NetframeProfiles[$channel->ownerProfil()->get()->first()->pivot->type_profil][$channel->ownerProfil()->get()->first()->pivot->profils_has_project_id])))

                            {!! HTML::publishAs('#form-channel-edit',
                                $NetframeProfiles,
                                [
                                    'id'=>'id_foreign',
                                    'type'=>'type_foreign',
                                    'postfix'=>'ch'
                                ],
                                true,
                                ($channel->profile_id !== null) ? [
                                    'id' => $channel->profile_id,
                                    'type' => strtolower(class_basename($channel->profile))
                                ] :
                                    ((isset($channel->profile)) ? [
                                    'id' => $channel->profile->id,
                                    'type' => $channel->profile->getType()
                                    ] : null),
                                'channel' ) !!}
                            <span class="nf-form-label">
                                {{ trans('channels.edit.publishAs') }}
                            </span>
                         @endif
                    </div>
                </div>

                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        @if($channel->id != null)
                        {{ trans('form.save') }}
                        @else
                        {{ trans('form.create') }}
                        @endif
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>

        {{ Form::close() }}
    </div>
@stop
