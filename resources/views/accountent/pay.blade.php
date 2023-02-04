@extends('accountent.menu.navigation')

@section('subcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{ trans('accountent.pay') }}</h2>
        </div>

        <div class="panel-body">
            {{ Form::open(['method'=>'POST', 'data-stripe-publishable-key'=>'pk_test_gvlGsOspsppx2aRgtERdFVyh', 'id'=>'payment-form']) }}

            <div class="row">
                <div class="form-group col-md-6 error hide alert alert-danger">
                    <span class="text-alert"></span>
                </div> 
            </div>
            @if(Session::has('error_message'))
                <div class="alert alert-danger"><em> {!! session('error_message') !!}</em></div>
            @endif 

            <div class="row">
                <div class="col-md-12">
                    <p>{{trans('accountent.bill.number')}}: {{$bill->number}}</p>
                    <p>{{trans('accountent.bill.card-name')}}: {{$card_name}}</p>
                    <p>{{trans('accountent.bill.type')}}: {{trans('accountent.bill.'.$type)}}</p>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <div class="alert alert-success pull-right">
                        {{ Form::label('total', trans('form.pay.total')) }}: 
                        <span>{{$bill->total}} {{trans('accountent.bills.currency')}}</span>
                    </div>
                </div> 
            </div>

            <div class="form-group col-md-12">
                @if($type!="card")
                    {{ Form::submit(trans('form.pay.title'), ['class' => 'btn btn-border-default pull-right', 'disabled'=>"disabled", 'title'=>trans('form.pay.not_allowed')]) }}
                @elseif(!$pay)
                    {{ Form::submit(trans('form.pay.title'), ['class' => 'btn btn-border-default pull-right', 'disabled'=>"disabled", 'title'=>trans('form.pay.expired_card')]) }}
                @else
                    {{ Form::submit(trans('form.pay.title'), ['class' => 'btn btn-border-default pull-right']) }}
                @endif
            </div>

        {{ Form::close() }}
        </div>
    </div>
    
    {{ HTML::script('assets/js/jquery.min.js') }}
    
@stop