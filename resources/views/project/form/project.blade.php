@extends('project.form.main')

@section('subcontent')
<div id="profile-edit-form">
    <!-- COVER PICTURE  -->
    <div class="nf-form-cell nf-cell-cover" style="{{ (($project->coverImage != null) ? 'background-image:url(\''.$project->coverImage->getUrl().'\')' : '')}}">

        <!-- IF COVER  -->
        <ul class="nf-actions {{ ((is_null($project->coverImage)) ? 'd-none' : '')}}" id="updel-cover">
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
        <div class="cover-placeholder {{ (($project->coverImage != null) ? 'd-none' : '') }}">
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
                            'profile_type' => 'Project',
                            'profile_id' => $project->id,
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
                    @if($project->profileImage != null)
                        {!! HTML::thumbImage($project->profileImage, 80, 80, [], $project->getType(), 'avatar profile-image') !!}
                    @else
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.project')
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
                                    'profile_type' => 'Project',
                                    'profile_id' => $project->id,
                                    'disable_extra_links' => 1
                                ])
                                <input type="file" class="btn-input">
                            </label>
                        </li>
                        <li class="sep fn-remove-avatar {{ (($project->profileImage != null) ? '' : 'd-none' ) }}">
                            <i></i>
                        </li>
                        <li class="fn-remove-avatar {{ (($project->profileImage != null) ? '' : 'd-none' ) }}">
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


        {{ Form::open(['id' => 'post-project', 'files' => true]) }}
            {{ Form::hidden('profileMediaId', ((!is_null($project->id) && !is_null($project->profile_media_id)) ? $project->profile_media_id : '')) }}
            {{ Form::hidden('coverMediaId', ((!is_null($project->id) && !is_null($project->cover_media_id)) ? $project->cover_media_id : '')) }}
            <!-- NAME -->
            <label class="nf-form-cell nf-cell-name @if($errors->has('name')) nf-cell-error @endif">
                {{ Form::text( 'title', (isset($project->id) ? $project->title : \App\Helpers\InputHelper::get('title')), ['class'=>'nf-form-input', 'autofocus'=>'autofocus', 'placeholder'=>'Écrivez ici…'] ) }}
                <span class="nf-form-label">
                    {{ trans('project.project_title') }}
                </span>
                {!! $errors->first('title', '<div class="nf-form-feedback">:message</div>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- PUBLIC/PRIVATE -->
            <label class="nf-form-cell nf-form-checkbox nf-cell-privacy @if($errors->has('confidentiality')) nf-cell-error @endif ">
                <span class="nf-form-label">
                    {{ trans('netframe.public') }}
                </span>
                {{ Form::checkbox('confidentiality', '1', ($project->confidentiality == 0), ['class' => 'nf-form-input']) }}
                <span class="nf-form-label">
                    {{ trans('netframe.private') }}
                </span>
                <div class="nf-form-cell-fx"></div>
            </label>

            <hr>

            <!-- DESCRIPTION -->
            <label class="nf-form-cell nf-cell-full @if($errors->has('description')) nf-cell-error @endif">
                {{ Form::textarea( 'description', (isset($project->id) ? $project->description : \App\Helpers\InputHelper::get('description')), ['rows'=>'1', 'class'=>'nf-form-input', 'placeholder'=>'Écrivez ici…'] ) }}
                <!-- <textarea class="nf-form-input" placeholder="Écrivez ici…"></textarea> -->
                <span class="nf-form-label">
                    {{ trans('form.description') }}
                </span>
                {!! $errors->first('description', '<div class="nf-form-feedback">:message</div>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- TAGS -->
            @include('components.forms.tags', ['element' => $project])

            <!-- TITLE -->
            <!-- <h3 class="nf-form-hr-title">
                <span>
                    Localisation
                </span>
                <hr>
            </h3> -->

            <!-- LOCATION -->
            <!-- TODO : Cacher l'encart de la map tant qu'aucune adresse n'y est affiché -->
            @include('location.minimap-form', ['profile' => $project])

            <section class="nf-form-col">
                <!-- SETTINGS -->
                <h3 class="nf-form-col-title">
                    <span>
                        Utilisateurs
                    </span>
                </h3>

                <!-- VALIDATE USERS  -->
                <label class="nf-form-cell nf-form-checkbox @if($errors->has('free_join')) nf-cell-error @endif">
                    {{ Form::checkbox('free_join', '0', ($project->free_join !== null && $project->free_join == 0), ['class' => 'nf-form-input']) }}
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
                    {{ Form::checkbox('with_personnal_folder', 1, ($project->with_personnal_folder != 0), ['id' => 'with_personnal_folder','class'=>'nf-form-input']) }}
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
                    @include('profiles.partials.auto-members', ['profile' => $project, 'roles' => $roles])
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
                @if($project->has_defaultChannel()->first() != null)
                    <div class="nf-form-cell nf-form-checkbox">
                        <input type="checkbox" class="nf-form-input" checked disabled />
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultChannel') }} :
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </div>
                    <div class="nf-form-cell">
                        <a class="nf-btn" href="{{ $project->has_defaultChannel()->first()->getUrl() }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.channel')
                            </span>
                            <span class="btn-txt">
                                {{ $project->has_defaultChannel()->first()->getNameDisplay() }}
                            </span>
                        </a>
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.defaultChannelLink') }}
                        </span>
                    </div>
                @else
                    <label class="nf-form-cell nf-form-checkbox @if($errors->has('default_channel')) nf-cell-error @endif">
                        {{ Form::checkbox('default_channel', 1, ($project->has_defaultChannel()->first() != null), ['id' => 'default_channel', 'class' => 'nf-form-input']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultChannel') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                @endif

                <hr>

                <!-- CREATE DEFAULT TASKS  -->
                @if($project->has_defaultTasks()->first() != null)
                    <div class="nf-form-cell nf-form-checkbox">
                        <input type="checkbox" class="nf-form-input" checked disabled />
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultTasks') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </div>
                    <div class="nf-form-cell">
                        <a class="nf-btn" href="{{ $project->has_defaultTasks()->first()->getUrl() }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.tasks')
                            </span>
                            <span class="btn-txt">
                                {{ $project->has_defaultTasks()->first()->name }}
                            </span>
                        </a>
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.defaultTaskLink') }}
                        </span>
                    </div>
                @elseif(count($tasks) > 0)
                    <label class="nf-form-cell nf-form-checkbox fn-profile-default-tasks @if($errors->has('default_tasks')) nf-cell-error @endif">
                        {{ Form::checkbox('default_tasks', 1, ($project->has_defaultTasks()->first() != null), ['id' => 'default_tasks', 'class' => 'nf-form-input', 'data-target' => '.fn-profile-default-tasks-template']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.addDefaultTasks') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                    <label class="nf-form-cell d-none fn-profile-default-tasks-template">
                        {{ Form::select('default_tasks_template', $tasks, ($project->has_defaultTasks()->first() != null) ? $project->has_defaultTasks()->first()->id : '', ['class' => 'nf-form-input']) }}
                        <span class="nf-form-label">
                            {{ trans('profiles.manage.createTasksTitle') }}
                        </span>
                        <div class="nf-form-cell-fx"></div>
                    </label>
                @else
                    {{ trans('profiles.manage.createTasksBefore') }}
                @endif
            </section>

            <div class="nf-form-validation">
                <div class="nf-form-validactions">
                    <div class="nf-form-cell nf-cell-full">
                        @if(isset($project->id))
                            {!! HTML::publishAs('#post-project',
                                $NetframeProfiles, [
                                    'id'=>'id_foreign',
                                    'type'=>'type_foreign',
                                    'postfix'=>'hs'
                                ],
                                true,
                                ($project->id_foreign !== null) ? [
                                    'id'=>$project->id_foreign,
                                    'type'=>$project->type_foreign
                                    ] : [
                                    'id' => $project->owner->id,
                                    'type' => strtolower(class_basename($project->owner))
                                ],
                                'project' ) !!}
                        @else
                            {!! HTML::publishAs('#post-project',
                                $NetframeProfiles, [
                                    'id'=>'id_foreign',
                                    'type'=>'type_foreign',
                                    'postfix'=>'hs'],
                                true,
                                ($project->id_foreign !== null) ? ['id' => $project->id_foreign, 'type' => $project->type_foreign] : null,
                                'project') !!}
                        @endif
                        <span class="nf-form-label">
                            {{ trans('form.create') }} {{ trans('netframe.publishAs') }}
                        </span>
                    </div>


                    <div id="publish-as-hidden-hs">
                        @if(isset($project->id))
                            {{ Form::hidden("id_foreign", ($project->id_foreign) ? $project->id_foreign : $project->owner->id ) }}
                            {{ Form::hidden("type_foreign", ($project->type_foreign) ? $project->type_foreign : strtolower(class_basename($project->owner)) ) }}
                        @else
                            {{ Form::hidden("id_foreign", ($project->id_foreign) ? $project->id_foreign : auth()->guard('web')->user()->id ) }}
                            {{ Form::hidden("type_foreign", ($project->type_foreign) ? $project->type_foreign : 'user' ) }}
                        @endif
                    </div>
                </div>

                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        @if($project->id != null)
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

        var miniMap = $('#post-project');
        new MiniMapForm({
            $wrapper: miniMap,
            $latitude: {!! ($project->latitude != '') ? $project->latitude : '\'\'' !!},
            $longitude: {!! ($project->longitude != '') ? $project->longitude : '\'\'' !!},
            $displayMap: {{ ($project->id != null || ($project->latitude && $project->longitude) ) ? 'true' : 'false' }},
            $placeName: '',
            $elementType: '{{ get_class($project) }}'
        });
    })();
</script>
@stop
