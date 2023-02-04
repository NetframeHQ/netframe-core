<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('form.user.editDescription') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
        {{ Form::open(['url'=> '/netframe/publish-description-user', 'id' => 'form-description-user', 'files' => true]) }}

    <div class="form-group clearfix">
    {{ Form::hidden("id_user", $user->id ) }}

    {{ Form::message() }}
    <div class="form-group">
        {{ Form::textarea( 'description', (isset($inputOld['description']) ? $inputOld['description'] : $user->description), ['rows'=>'15', 'class'=>'form-control '.(($errors->has('description')) ? 'is-invalid' : '')] ) }}
        {{ $errors->first('description', '<p class="invalid-feedback">:message</p>') }}
    </div>

    </div>

</div>
<!-- End MODAL-BODY -->

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('form.close') }}</button>
    <button type="submit" class="btn btn-primary">
        {{ trans('form.publish') }}
    </button>
</div>
{{ Form::close() }}
<!-- End MODAL-FOOTER -->