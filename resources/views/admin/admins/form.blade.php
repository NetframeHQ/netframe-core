

@extends('admin.layout')


@section('content')
<div class="col-md-12">
    <h1 class="Hn-title">
        {{ ($admin->id != null) ? "Modification d'un administrateur" : "Ajout d'un administrateur" }}
    </h1>

    <div class="row mg-bottom">
        <div class="col-md-6 col-md-offset-3">
            {{ Form::open(['id' => 'admins']) }}

                {{ Form::hidden('id', $admin->id) }}

                <div class="form-group @if ($errors->has('username')) has-error @endif">
                    {{ Form::label('username', 'Nom') }}
                    {{ Form::text('username', $admin->username, ['class' => 'form-control']) }}
                    {{ $errors->first('username', '<p class="help-block">:message</p>') }}
                </div>

                <div class="form-group @if ($errors->has('email')) has-error @endif">
                    {{ Form::label('email', 'Email') }}
                    {{ Form::email('email', $admin->email, ['class' => 'form-control']) }}
                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                </div>

                <div class="form-group @if ($errors->has('password')) has-error @endif">
                    {{ Form::label('password', 'Mot de passe') }}
                    {{ Form::password('password', ['class' => 'form-control']) }}
                    {{ $errors->first('password', '<p class="help-block">:message</p>') }}
                </div>

                {{ Form::submit('Valider', ['class' => 'btn btn-block btn-success']) }}

            {{ Form::close() }}
        </div>
    </div>
</div>
@stop