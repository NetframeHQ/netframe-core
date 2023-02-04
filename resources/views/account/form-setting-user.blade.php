<div class="nf-form nf-col-2" id="profile-edit-form">

    <!-- AVATAR -->
    <div class="nf-form-cell nf-cell-avatar nf-cell-avatarBIG">
        <div class="nf-form-select" id="js-profile-picture">
            <a href="#" class="nf-btn btn-submenu" id="profile-image-container">
                @if($user->profileImage != null)
                    {!! HTML::thumbImage($user->profileImage, 80, 80, [], $user->getType(), 'avatar profile-image') !!}
                @endif
                <span class="svgicon btn-img @if($user->profileImage != null) d-none @endif">
                    @include('macros.svg-icons.user')
                </span>

                <span class="btn-img svgicon">
                    @include('macros.svg-icons.arrow-down')
                </span>
            </a>
            <div class="submenu-container submenu-left">
                <ul class="submenu">
                    <li>
                        <label class="nf-btn" id="profile-picture">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.plus')
                            </span>
                            <span class="btn-txt">
                                {{ trans('profiles.manage.uploadAvatar') }}
                            </span>

                            @include('page.partials.form-profile-picture', [
                                'profile_type' => 'User',
                                'profile_id' => $user->id,
                                'disable_extra_links' => 1
                            ])
                            <input type="file" class="btn-input">
                        </label>
                    </li>
                    <li class="sep fn-remove-avatar  {{ (($user->profileImage != null) ? '' : 'd-none' ) }}">
                        <i></i>
                    </li>
                    <li class="fn-remove-avatar {{ (($user->profileImage != null) ? '' : 'd-none' ) }}">
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

    <!-- COVER PICTURE  -->
    <div class="nf-form-cell nf-cell-cover" style="{{ (($user->coverImage != null) ? 'background-image:url(\''.$user->coverImage->getUrl().'\')' : '')}}">
        <!-- IF COVER  -->
        <ul class="nf-actions {{ ((is_null($user->coverImage)) ? 'd-none' : '')}}" id="updel-cover">
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
        <div class="cover-placeholder {{ (($user->coverImage != null) ? 'd-none' : '') }}">
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
                            'profile_type' => 'User',
                            'profile_id' => $user->id,
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
        <span class="nf-form-label">
            {{ trans('profiles.manage.coverImage') }}
        </span>
    </div>
    <hr>
    {{ Form::open(['id' => 'post-user']) }}
        {{ Form::hidden('profileMediaId', ((!is_null($user->profile_media_id)) ? $user->profile_media_id : '')) }}
        {{ Form::hidden('coverMediaId', ((!is_null($user->cover_media_id)) ? $user->cover_media_id : '')) }}
        <!-- FIRST NAME -->
        <label class="nf-form-cell @if($errors->has('firstname')) nf-cell-error @endif">
            {{ Form::text( 'firstname', $user->firstname, ['class'=>'nf-form-input', 'autofocus'=>'autofocus', 'placeholder' => trans('form.placeholder.firstname')] ) }}
            <span class="nf-form-label">
                {{ trans('form.setting.firstname') }}
            </span>
            {!! $errors->first('firstname', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>

        <!-- LAST NAME -->
        <label class="nf-form-cell @if($errors->has('name')) nf-cell-error @endif">
            {{ Form::text( 'name', $user->name, ['class'=>'nf-form-input', 'placeholder' => trans('form.placeholder.lastname')] ) }}
            <span class="nf-form-label">
                {{ trans('form.setting.name') }}
            </span>
            {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>

        <!-- EMAIL -->
        <label class="nf-form-cell @if($errors->has('email')) nf-cell-error @endif">
            {{ Form::email( 'email', (empty($user->email) ? request()->old() : $user->email), ['class'=>'nf-form-input', 'placeholder' => trans('form.placeholder.email')] ) }}
            <span class="nf-form-label">
                {{ trans('form.setting.email') }}
            </span>
            {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>

        <!-- PASSWORD -->
        <label class="nf-form-cell @if($errors->has('password')) nf-cell-error @endif">
            <input type="password" value="••••••••" disabled class="nf-form-input">
            <span class="nf-form-label">
                {{ trans('form.setting.password') }}
            </span>
            {!! $errors->first('password', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
            <ul class="nf-actions">
                <li class="nf-action">
                    <a href="{{route('account.editPassword')}}" class="nf-btn btn-ico" data-toggle="modal" data-target="#modal-ajax">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.edit')
                        </span>
                    </a>
                </li>
            </ul>
        </label>

        <!-- LANGUAGE -->
        <label class="nf-form-cell nf-cell-full @if($errors->has('lang')) nf-cell-error @endif">
            {{ Form::select('lang', $listLanguageNetframe, (empty($user->lang) ? null : $user->lang), ['class'=>'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('form.setting.lang') }}
            </span>
            {!! $errors->first('lang', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>


        @include('location.minimap-form', ['profile' => $user])

        @if($customFields != null)
            <!-- INFORMATIONS -->
            <h3 class="nf-form-hr-title">
                <span>
                    INFORMATIONS
                </span>
                <hr>
            </h3>

            @foreach($customFields as $slug => $value)
                @include('account.partials.'.$value['type'],[
                    'name'=> 'custom_field['.$slug.']',
                    'label'=> ''.$value['name'].'',
                    'value' => isset($customFieldsValues[$slug]) ? $customFieldsValues[$slug] : ''
                ])
            @endforeach
        @endif

        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('form.save') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>

@section('javascripts')
@parent
<script>
(function () {
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

    var miniMap = $('#post-user');
    new MiniMapForm({
        $wrapper: miniMap,
        $latitude: {!! ($user->latitude != '') ? $user->latitude : '\'\'' !!},
        $longitude: {!! ($user->longitude != '') ? $user->longitude : '\'\'' !!},
        $displayMap: {{ ($user->id != null || ($user->latitude && $user->longitude) ) ? 'true' : 'false' }},
        $placeName: '',
        $elementType: '{{ get_class($user) }}'
    });
})();


    // $(document).ready(function(){
    //     jQuery('.validatedForm').validate({
    //         rules : {
    //             password : {
    //                 minlength : 5
    //             },
    //             password_confirm : {
    //                 minlength : 5,
    //                 equalTo : '[name="password"]'
    //             }
    //         }
    //     })
    // })

    $( document ).ajaxSuccess(function( event, xhr, settings ) {
        if(xhr.responseJSON.errors){
            let errors = xhr.responseJSON.errors
            $('.validatedForm').find('span.error').text("")
            $('.validatedForm input').removeClass('is-invalid')
            if(errors.password){
                $('.password').addClass('is-invalid')
                $('.password').parent().parent().find('span.error').text(errors.password[0])
            }
            if(errors.old_password){
                $('.old_password').addClass('is-invalid')
                $('.old_password').parent().parent().find('span.error').text(errors.old_password[0])
            }
            if(errors.password_confirmation){
                $('.password_confirmation').addClass('is-invalid')
                $('.password_confirmation').parent().parent().find('span.error').text(errors.password_confirmation[0])
            }
        }
    });
</script>
@stop
