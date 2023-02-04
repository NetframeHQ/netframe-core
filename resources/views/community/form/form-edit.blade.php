@extends('community.form.main')

@section('subcontent')
<div id="profile-edit-form">
    <!-- COVER PICTURE  -->
    <div class="nf-form-cell nf-cell-cover" style="{{ (($community->coverImage != null) ? 'background-image:url(\''.$community->coverImage->getUrl().'\')' : '')}}">

        <!-- IF COVER  -->
        <ul class="nf-actions {{ ((is_null($community->coverImage)) ? 'd-none' : '')}}" id="updel-cover">
            <li class="nf-action">
                <a class="nf-btn btn-ico" id="fn-replace-cover">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.edit')
                    </span>
                </a>
            </li>
            <li class="nf-action">
                <a class="nf-btn btn-ico" id="fn-delete-cover">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.trash')
                    </span>
                </a>
            </li>
        </ul>

        <!-- ELSE PLACEHOLDER -->
        <div class="cover-placeholder {{ (($community->coverImage != null) ? 'd-none' : '') }}">
            <ul class="nf-actions">
                <li class="nf-action">
                    <label class="nf-btn btn-nobg">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.plus')
                        </span>
                        <span class="btn-txt">
                            {{ trans('profiles.manage.addCoverImage') }}
                        </span>
                        @include('page.partials.form-profile-cover', [
                            'profile_type' => 'Community',
                            'profile_id' => $community->id,
                            'disable_extra_links' => 1
                        ])
                    </label>
                </li>
                {{--
                <li class="sep">
                    <i></i>
                </li>
                <li class="nf-action">
                    <label class="nf-btn btn-nobg">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.search')
                        </span>
                        <input type="text" class="btn-input" placeholder="{{ trans('profiles.manage.searchFromUnslpash') }}">
                        <input type="submit" class="btn-input-submit" value="{{ trans('profiles.manage.searchUnslpashTxtButton') }}">
                    </label>
                </li>
                --}}
            </ul>
        </div>
    </div>
    <div class="nf-form nf-col-2">
        <!-- AVATAR -->
        <div class="nf-form-cell nf-cell-avatar">
            <div class="nf-form-select" id="js-profile-picture">
                <a href="#" class="nf-btn btn-submenu" id="profile-image-container">
                    @if($community->profileImage != null)
                        {!! HTML::thumbImage($community->profileImage, 80, 80, [], $community->getType(), 'avatar profile-image') !!}
                    @else
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.community')
                        </span>
                    @endif

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
                                    'profile_type' => 'Community',
                                    'profile_id' => $community->id,
                                    'disable_extra_links' => 1
                                ])
                                <input type="file" class="btn-input">
                            </label>
                        </li>
                        <li class="sep fn-remove-avatar {{ (($community->profileImage != null) ? '' : 'd-none' ) }}">
                            <i></i>
                        </li>
                        <li class="fn-remove-avatar {{ (($community->profileImage != null) ? '' : 'd-none' ) }}">
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
        </div>


        {{ Form::open(['id' => 'post-community', 'files' => true]) }}
            {{ Form::hidden('profileMediaId', ((!is_null($community->id) && !is_null($community->profile_media_id)) ? $community->profile_media_id : '')) }}
            {{ Form::hidden('coverMediaId', ((!is_null($community->id) && !is_null($community->cover_media_id)) ? $community->cover_media_id : '')) }}
            <!-- NAME -->
            <label class="nf-form-cell nf-cell-name @if($errors->has('name')) nf-cell-error @endif">
                {{ Form::text( 'name', (isset($community->id) ? $community->name : \App\Helpers\InputHelper::get('name')), ['class'=>'nf-form-input', 'autofocus'=>'autofocus', 'placeholder'=>'Écrivez ici…'] ) }}
                <span class="nf-form-label">
                    {{ trans('community.form.name') }}
                </span>
                {!! $errors->first('name', '<div class="nf-form-feedback">:message</div>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- PUBLIC/PRIVATE -->
            <label class="nf-form-cell nf-form-checkbox nf-cell-privacy @if($errors->has('confidentiality')) nf-cell-error @endif ">
                <span class="nf-form-label">
                    {{ trans('netframe.public') }}
                </span>
                {{ Form::checkbox('confidentiality', '1', ($community->confidentiality == 0), ['class' => 'nf-form-input']) }}
                <span class="nf-form-label">
                    {{ trans('netframe.private') }}
                </span>
                <div class="nf-form-cell-fx"></div>
            </label>

            <hr>

            <!-- DESCRIPTION -->
            <label class="nf-form-cell nf-cell-full @if($errors->has('description')) nf-cell-error @endif">
                {{ Form::textarea( 'description', (isset($community->id) ? $community->description : \App\Helpers\InputHelper::get('description')), ['rows'=>'1', 'class'=>'nf-form-input', 'placeholder'=>'Écrivez ici…'] ) }}
                <!-- <textarea class="nf-form-input" placeholder="Écrivez ici…"></textarea> -->
                <span class="nf-form-label">
                    {{ trans('form.description') }}
                </span>
                {!! $errors->first('description', '<div class="nf-form-feedback">:message</div>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- TAGS -->
            @include('components.forms.tags', ['element' => $community])


            <!-- LOCATION -->
            @include('location.minimap-form', ['profile' => $community])

            <section class="nf-form-col">
                <!-- SETTINGS -->
                <h3 class="nf-form-col-title">
                    <span>
                        Utilisateurs
                    </span>
                </h3>

                <!-- VALIDATE USERS  -->
                <label class="nf-form-cell nf-form-checkbox @if($errors->has('free_join')) nf-cell-error @endif">
                    {{ Form::checkbox('free_join', '0', ($community->free_join !== null && $community->free_join == 0), ['class' => 'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('profiles.manage.freeJoin') }}
                    </span>
                    @if ($errors->has('free_join'))
                        <span class="nf-form-feedback">{{ $errors->first('free_join') }}</span>
                    @endif
                    <div class="nf-form-cell-fx"></div>
                </label>
                <!-- CREATE PERSONAL FOLDER -->
                <label class="nf-form-cell nf-form-checkbox @if($errors->has('withPersonalFolder')) nf-cell-error @endif">
                    {{ Form::checkbox('with_personnal_folder', 1, ($community->with_personnal_folder != 0), ['id' => 'with_personnal_folder','class'=>'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('profiles.manage.withPersonalFolder') }}
                    </span>
                    @if ($errors->has('with_personnal_folder'))
                        <span class="nf-form-feedback">{{ $errors->first('with_personnal_folder') }}</span>
                    @endif
                    <div class="nf-form-cell-fx"></div>
                </label>
                <hr>

                <!-- MEMBERS AUTO JOIN  -->
                @if(session('instanceRoleId') <= 2)
                    @include('profiles.partials.auto-members', ['profile' => $community, 'roles' => $roles])
                @endif
            </section>

            <section class="nf-form-col">
                <!-- SETTINGS -->
                <h3 class="nf-form-col-title">
                    <span>
                        Options
                    </span>
                </h3>

                <!-- CREATE DEFAULT CHANNEL  -->
                @if($community->has_defaultChannel()->first() != null)
                    <div class="nf-form-cell nf-form-checkbox">
                        <input type="checkbox" class="nf-form-input" checked disabled />
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.defaultChannelLink') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </div>
                    <div class="nf-form-cell">
                        <a class="nf-btn" href="{{ $community->has_defaultChannel()->first()->getUrl() }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.channel')
                            </span>
                            <span class="btn-txt">
                                {{ $community->has_defaultChannel()->first()->getNameDisplay() }}
                            </span>
                        </a>
                    </div>
                @else
                    <label class="nf-form-cell nf-form-checkbox @if($errors->has('default_channel')) nf-cell-error @endif">
                        {{ Form::checkbox('default_channel', 1, ($community->has_defaultChannel()->first() != null), ['id' => 'default_channel', 'class' => 'nf-form-input']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultChannel') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                @endif

                <hr>

                <!-- CREATE DEFAULT TASKS  -->
                @if($community->has_defaultTasks()->first() != null)
                    <div class="nf-form-cell nf-form-checkbox">
                        <input type="checkbox" class="nf-form-input" checked disabled />
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.defaultTaskLink') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </div>
                    <div class="nf-form-cell">
                        <a class="nf-btn" href="{{ $community->has_defaultTasks()->first()->getUrl() }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.tasks')
                            </span>
                            <span class="btn-txt">
                                {{ $community->has_defaultTasks()->first()->name }}
                            </span>
                        </a>
                    </div>
                @elseif(count($tasks) > 0)
                    <label class="nf-form-cell nf-form-checkbox fn-profile-default-tasks @if($errors->has('default_tasks')) nf-cell-error @endif">
                        {{ Form::checkbox('default_tasks', 1, ($community->has_defaultTasks()->first() != null), ['id' => 'default_tasks', 'class' => 'nf-form-input', 'data-target' => '.fn-profile-default-tasks-template']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultTasks') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                    <label class="nf-form-cell d-none fn-profile-default-tasks-template">
                        {{ Form::select('default_tasks_template', $tasks, ($community->has_defaultTasks()->first() != null) ? $community->has_defaultTasks()->first()->id : '', ['class' => 'nf-form-input']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.createTasksTitle') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                @else
                    <label class="nf-form-cell">
                        <a class="nf-btn" data-toggle="modal" data-target="#modal-ajax" href="{{ url()->route('task.addTemplate') }}">
                            <span class="svgicon btn-img">
                            @include('macros.svg-icons.plus')
                            </span>
                            <span class="btn-txt">
                            {{ trans('task.createTemplate') }}
                            </span>
                        </a>
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.createTasksBefore') }}
                        </span>
                    </label>
                @endif
            </section>



            <div class="nf-form-validation">
                <div class="nf-form-validactions">
                    <div class="nf-form-cell nf-cell-full">
                        @if(isset($community->id))
                            {!! HTML::publishAs('#post-community',
                                $NetframeProfiles, [
                                    'id'=>'id_foreign',
                                    'type'=>'type_foreign',
                                    'postfix'=>'hs'
                                ],
                                true,
                                ($community->id_foreign !== null) ? [
                                    'id'=>$community->id_foreign,
                                    'type'=>$community->type_foreign
                                    ] : [
                                    'id' => $community->owner->id,
                                    'type' => strtolower(class_basename($community->owner))
                                ],
                                'community' ) !!}
                        @else
                            {!! HTML::publishAs('#post-community',
                                $NetframeProfiles, [
                                    'id'=>'id_foreign',
                                    'type'=>'type_foreign',
                                    'postfix'=>'hs'],
                                true,
                                ($community->id_foreign !== null) ? ['id' => $community->id_foreign, 'type' => $community->type_foreign] : null,
                                'community') !!}
                        @endif
                        <span class="nf-form-label">
                            {{ trans('form.create') }} {{ trans('netframe.publishAs') }}
                        </span>
                    </div>


                    <div id="publish-as-hidden-hs">
                        @if(isset($community->id))
                            {{ Form::hidden("id_foreign", ($community->id_foreign) ? $community->id_foreign : $community->owner->id ) }}
                            {{ Form::hidden("type_foreign", ($community->type_foreign) ? $community->type_foreign : strtolower(class_basename($community->owner)) ) }}
                        @else
                            {{ Form::hidden("id_foreign", ($community->id_foreign) ? $community->id_foreign : auth()->guard('web')->user()->id ) }}
                            {{ Form::hidden("type_foreign", ($community->type_foreign) ? $community->type_foreign : 'user' ) }}
                        @endif
                    </div>
                </div>

                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        @if($community->id != null)
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
</div>
@stop

@section('javascripts')
@parent
<script>
    (function () {
        $(document).on('change', 'input[name="confidentiality"]', function() {
            if($(this).is(':checked')) {
                $('input[name="free_join"]').prop('checked', true);
            }
        });

        $(document).on('click', '#fn-replace-cover', function(e){
            e.preventDefault();
            $('#fileuploadCover .fileinput-button').trigger('click');
        });

        $(document).on('click', 'a#fn-delete-cover', function(e){
            e.preventDefault();
            var deleteLink = $(this);

            var ajaxData = {
                mediaId: $('input[name="coverMediaId"]').val(),
                mediaType: 'media'
            };

            $.ajax({
                url: laroute.route('xplorer_delete_element'),
                data: ajaxData,
                type: "POST",
                success: function (data) {
                    deleteLink.closest('#updel-cover').addClass('d-none');
                    deleteLink.closest('div.nf-cell-cover').css('background-image','none');
                    deleteLink.closest('div.nf-cell-cover').find('.cover-placeholder').removeClass('d-none');
                    $('input[name="coverMediaId"]').val('');
                }
            });
        });

        $(document).on('click', '.fn-remove-avatar a', function(e){
            e.preventDefault();
            var deleteLink = $(this);

            var ajaxData = {
                mediaId: $('input[name="profileMediaId"]').val(),
                mediaType: 'media'
            };

            $.ajax({
                url: laroute.route('xplorer_delete_element'),
                data: ajaxData,
                type: "POST",
                success: function (data) {
                    deleteLink.addClass('d-none');
                    $('.nf-cell-avatar #js-profile-picture #profile-image-container span.avatar.profile-image').remove();
                    $('.nf-cell-avatar #js-profile-picture #profile-image-container .svgicon.btn-img').removeClass('d-none');
                    $('input[name="profileMediaId"]').val('');
                }
            });
        });


        $(document).on('change', 'input[name="default_tasks"]', function(e){
            if($(this).is(':checked')){
                $($(this).data('target')).removeClass('d-none');
            }
            else{
                $($(this).data('target')).addClass('d-none');
            }
        });

        var miniMap = $('#post-community');
        new MiniMapForm({
            $wrapper: miniMap,
            $latitude: {!! ($community->latitude != '') ? $community->latitude : '\'\'' !!},
            $longitude: {!! ($community->longitude != '') ? $community->longitude : '\'\'' !!},
            $displayMap: {{ ($community->id != null || ($community->latitude && $community->longitude) ) ? 'true' : 'false' }},
            $placeName: '',
            $elementType: '{{ get_class($community) }}'
        });
    })();
</script>
@stop
