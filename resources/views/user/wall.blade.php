@extends('layouts.page')

@section('content')
<div class="col-md-9 offset-md-3">
	<h1>Bienvenue sur votre mur Netframe <i>
   {{ auth()->guard('web')->user()->getNameDisplay() }}</i> !
	</h1>
</div>

@stop


