@extends('admin.layout')


@section('content')
<div class="col-md-12">
    <h1 class="Hn-title">Statistiques</h1>

    <div class="row mg-bottom">
        <div class="col-md-12">
            @include("admin.statistics.navbar")
        </div>
    </div>

    <div class="row mg-bottom">
        <div class="col-md-12">
            @include('admin.statistics.'.$subView)
        </div>
    </div>
</div>
@stop


<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">


@section('javascript')
@parent
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
@stop