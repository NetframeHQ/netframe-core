@extends('admin.layout')


@section('content')
<div class="col-md-12">
    <h1 class="Hn-title">Gestion des administrateurs</h1>

    <div class="row mg-bottom">
        <div class="col-md-12">
            <a href="{{ url()->route('admin.edit') }}" class="btn btn-xs btn-info float-right">Ajouter un administrateur</a>

            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Créé le</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr id="admin-{{ $admin->id }}">
                            <td>{{ $admin->username }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ date('d / m / Y', strtotime($admin->created_at)) }}</td>
                            <td>
                                <a href="{{ url()->route('admin.edit', ['id' => $admin->id]) }}" class="btn btn-xs btn-info">Modifier</a>
                                @if($admin->id != 1)
                                    <a href="{{ url()->route('admin.delete', ['id' => $admin->id]) }}" class="btn btn-xs btn-danger fn-confirm-delete" data-txtconfirm="Etes vous sûr de supprimer cet administrateur ?">Supprimer</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop