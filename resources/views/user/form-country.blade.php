<div class="modal-header">
    <h4 class="modal-title">
        <span class="glyphicon glyphicon-pencil"></span> {{ trans('form.account.completeInfos') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <p>
        {{ trans('form.setting.explainNationality') }}
    </p>
    {{ Form::open(['route'=> 'account.wallet.create', 'id' => 'form-nationality-user']) }}

    {{ Form::hidden("id_user", $user->id ) }}

    {{ Form::message() }}
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            {{ Form::label('pays', trans('form.setting.country')) }}
            {{ Form::select( 'pays', $listCountries, (empty($user->pays) ? 'null' : $user->pays), ['class'=>'form-control '.(($errors->has('country')) ? 'is-invalid' : '')] ) }}
            {{ $errors->first('pays', '<p class="invalid-feedback">:message</p>') }}
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
            {{ Form::label('nationality', trans('form.setting.nationality')) }}
            {{ Form::select( 'nationality', $listCountries, (empty($user->pays) ? 'null' : $user->nationality), ['class'=>'form-control '.(($errors->has('nationality')) ? 'is-invalid' : '')] ) }}
            {{ $errors->first('nationality', '<p class="invalid-feedback">:message</p>') }}
        </div>
    </div>

</div>
<!-- End MODAL-BODY -->

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">{{ trans('form.save') }}</button>
</div>
{{ Form::close() }}
<!-- End MODAL-FOOTER -->
