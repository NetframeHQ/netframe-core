<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('instances.edit.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>


<div class="modal-body nf-form">
    {!! Form::open(['class'=>'validatedForm']) !!}
        @if(Session::has('status'))
            <div class="alert alert-success">{{trans('instances.create.success')}}{{ Session::get('status') }}</div>
        @endif
        <label class="nf-form-cell nf-cell-full @if($errors->has('name')) nf-cell-error @endif">
            {{ Form::text('name', $user->name, ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{trans('instances.create.name')}}
            </span>
            {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell nf-cell-full @if($errors->has('firstname')) nf-cell-error @endif">
            {{ Form::text('firstname', $user->firstname, ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{trans('instances.create.firstname')}}
            </span>
            {!! $errors->first('firstname', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell nf-cell-full @if($errors->has('email')) nf-cell-error @endif">
            {{ Form::email('email', $user->email, ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{trans('instances.create.email')}}
            </span>
            {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>
        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('instances.profiles.change.edit') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>
