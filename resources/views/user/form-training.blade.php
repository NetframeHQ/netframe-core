<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('form.user.editTraining') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
        {{ Form::open(['url'=> '/netframe/publish-training-user', 'id' => 'form-training-user', 'files' => true]) }}

    <div class="form-group clearfix">
    {{ Form::hidden("id_user", $user->id ) }}

    {{ Form::message() }}
    <div class="form-group">
        {{ Form::textarea( 'training', (isset($inputOld['training']) ? $inputOld['training'] : $user->training), ['rows'=>'15', 'class'=>'form-control '.(($errors->has('training')) ? 'is-invalid' : '')] ) }}
        {{ $errors->first('training', '<p class="invalid-feedback">:message</p>') }}
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