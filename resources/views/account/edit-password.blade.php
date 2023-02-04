<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('form.editPassword.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>


<div class="modal-body nf-form">
    {!! Form::open(['class'=>'validatedForm']) !!}
        <label class="nf-form-cell nf-cell-full @if($errors->has('old_password')) nf-cell-error @endif">
            {{ Form::password('old_password', ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('form.editPassword.oldPassword') }}
            </span>
            {!! $errors->first('old_password', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell nf-cell-full @if($errors->has('password')) nf-cell-error @endif">
            {{ Form::password('password', ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('form.editPassword.newPassword') }}
            </span>
            {!! $errors->first('password', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell nf-cell-full" @if($errors->has('password_confirmation')) nf-cell-error @endif">
            {{ Form::password('password_confirmation', ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('form.editPassword.confirmPassword') }}
            </span>
            {!! $errors->first('password_confirmation', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>

        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('form.editPassword.edit') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>
