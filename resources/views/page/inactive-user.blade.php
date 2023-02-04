@extends('layouts.page')

@section('stylesheets')
    <!-- Start Select media modal -->
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/select-modal.css') }}">
    <!-- End Select media modal -->
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-heading">
                {{ trans('user.inactiveUserTitle') }}
            </div>

            <div class="card-body">
                {{ trans('user.inactiveUser') }}
            </div>

        </div>

    </div>
</div>

@stop

@section('sidebar')

@stop
