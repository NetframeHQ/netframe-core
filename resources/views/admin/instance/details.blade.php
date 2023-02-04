@extends('admin.layout')


@section('content')
<div class="col-md-12">
<h1 class="Hn-title">{{ trans('admin.titles.instances') }}</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.instance_name') }} : </div>
                <div class="col-md-10">{{ $instance->name }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_users') }} : </div>
                <div class="col-md-10">{{ $instance->users()->count() }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_news') }} : </div>
                <div class="col-md-10">{{ $instance->news()->count() }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_events') }} : </div>
                <div class="col-md-10">{{ $instance->events()->count() }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_offers') }} : </div>
                <div class="col-md-10">{{ $instance->offers()->count() }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_profiles') }} : </div>
                <div class="col-md-10">
                    <div>{{ trans('admin.detail.houses') }} : {{ $instance->houses()->count() }}</div>
                    <div>{{ trans('admin.detail.communities') }} : {{ $instance->communities()->count() }}</div>
                    <div>{{ trans('admin.detail.projects') }} : {{ $instance->projects()->count() }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_channels') }} : </div>
                <div class="col-md-10">{{ $instance->channels()->where('personnal', '=', 0)->count() }}</div>
            </div>
            <div class="row">
                <div class="col-md-2 font-weight-bold">{{ trans('admin.detail.number_channels_personnal') }} : </div>
                <div class="col-md-10">{{ $instance->channels()->where('personnal', '=', 1)->count() }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Applications</h2>
            {{ Form::open(['route' => 'admin.instances.apps']) }}
                {{ Form::hidden('instance_id', $instance->id) }}
                <ul>
                    @foreach($apps as $app)
                        <li>
                            {{ Form::checkbox('app_'.$app->id, 1, $instance->apps->contains($app->id)) }} {{ $app->name }}
                        </li>
                    @endforeach
                </ul>
                {{ Form::submit('Valider') }}
            {{ Form::close() }}
        </div>
    </div>



    <div>
        <a href="{{ url()->route('admin.instances.details', ['id' => $instance->id, 'type' => 'usersList']) }}" class="btn btn-primary">
            {{ trans('admin.detail.list_users') }}
        </a>
        <a href="{{ url()->route('admin.instances.details', ['id' => $instance->id, 'type' => 'invitationSent']) }}" class="btn btn-primary">
            {{ trans('admin.detail.invitations_sent') }}
        </a>
    </div>

    @isset($users)
        <h2>{{ trans('admin.detail.list_users') }}</h2>
        <table class="table table-condensed">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>{{ trans('admin.detail.name') }}</th>
                <th>{{ trans('admin.detail.firstname') }}</th>
                <th>{{ trans('admin.detail.email') }}</th>
                <th>{{ trans('admin.detail.created') }}</th>
                <th>{{ trans('admin.detail.lastLogin') }}</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->firstname }}</td>
                    <td>
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        @if($user->pivot->roles_id <= 2)
                            / {{ $user->phone }}
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($user->last_connexion != null)
                            {{ date('d/m/Y H:i', strtotime($user->last_connexion)) }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ url()->route('admin.instances.userpass', ['id' => $user->id]) }}">
                            {{ trans('admin.detail.changePassword') }}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endisset

    @isset($boardings)
        <h2>{{ trans('admin.detail.invitations_sent') }}</h2>
        <table class="table table-condensed">
        <thead>
            <tr>
                <th>{{ trans('admin.detail.boarding_email') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($boardings as $boarding)
                <tr>
                    <td>{{ $boarding->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endisset

</div>

@stop