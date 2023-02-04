@extends('admin.layout')


@section('content')
<div class="col-md-12">
<h1 class="Hn-title">{{ trans('admin.titles.instances') }}</h1>

    <div class="row">
        <div class="col-md-6">
            {{ Form::open(['route' => 'admin.instances.search', 'id' => 'searchInstance', 'role' => 'form', 'class' => 'form-inline']) }}
                <div class="form-group mx-sm-3">
                    {{ Form::label('search', trans('admin.form.search_instance'), array('class' => 'sr-only')) }}
                    {{ Form::text('search', '', ['required', 'class' => 'form-control', 'id' => 'search', 'placeholder' => trans('admin.form.search_instance')]) }}
                </div>
                {{ Form::submit(trans('admin.form.search'), ['class' => 'btn btn-primary']) }}
            {{ Form::close() }}
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-primary" href="">{{ trans('admin.form.add_instance') }}</a>
        </div>
    </div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th>{{ trans('admin.tableau.name') }}</th>
                <th>{{ trans('admin.tableau.slug') }}</th>
                <th>{{ trans('admin.tableau.date') }}</th>
                <th>{{ trans('admin.tableau.number_user') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($instances as $instance)
                <tr>
                    <td><a href="{{ url()->route('admin.instances.details', ['id' => $instance->id]) }}">{{ $instance->name }}</a></td>
                    <td>{{ $instance->slug }}</td>
                    <td>{{ \Carbon\Carbon::parse($instance->created_at)->format('d/m/Y')}}</td>
                    <td>{{ $instance->users()->count() }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($hide_pagin != 1)
        <div class="nf-pagination">
            {{ $instances->links('vendor.pagination.bootstrap-4') }}
        </div>
    @endif
</div>
@stop
