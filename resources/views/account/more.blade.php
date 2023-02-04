@extends('account.main')

@section('subcontent')

    <div class="card no-bg">
        <div class="card-body">
            @if(session()->has('successRecord'))
            <div class="alert alert-success">
                {{ trans('form.successRecord') }}
            </div>
            @endif
        	@if($fields != null)
            {{ Form::open(['id' => 'post-user']) }}
            	@foreach($fields as $slug => $value)
            	<div class="row">
			        <div class="form-group col-md-12">
			            <label>{{ucfirst($value['name'])}}</label>
			            
			            	@include('account.partials.'.$value['type'],[
			            		'name'=> $slug, 
			            		'value'=>isset($values[$slug]) ? $values[$slug] : ''
			            	])
			            
			        </div>
			    </div>
			    @endforeach

			    <br>

			    <div class="form-group">
			        {{ Form::submit(trans('form.save'), ['class' => 'button primary float-right']) }}
			    </div>
            {{ Form::close() }}
			@else
				{{trans('user.nomore')}}
			@endif
        </div>
    </div>
@stop