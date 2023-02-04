@extends('accountent.menu.navigation')


@section('subcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{ trans('accountent.menu.bills') }}</h2>
        </div>
        
    @if(Session::has('success_message'))
        <div class="alert alert-success"><em> {!! session('success_message') !!}</em></div>
    @endif 
    @if(Session::has('error_message'))
        <div class="alert alert-danger"><em> {!! session('error_message') !!}</em></div>
    @endif 
        <div class="panel-body table-hover">
            @if($bills!=null)
                <table class="table">
                    <thead>
                        <th>{{ trans('accountent.bills.header.number') }}</th>
                        <th>{{ trans('accountent.bills.header.total') }}</th>
                        <th>{{ trans('accountent.bills.header.paid') }}</th>
                        <th>{{ trans('accountent.bills.header.actions') }}</th>
                    </thead>
                    <tbody>
                        @foreach($bills as $bill)
                            <tr>
                                <td>{{$bill->number}}</td>
                                <td>{{$bill->total}}</td>
                                @if($bill->paid)
                                    <td>{{trans('accountent.bills.paid')}}</td>
                                    <td>
                                        <a class="alert alert-warning" href="{{ url()->route('accountent.pdf', ['number'=>$bill->number]) }}" target="_blank"><i class="glyphicon glyphicon-print" title="{{trans('accountent.bills.print')}}"></i></a>
                                        <a class="alert alert-warning" href="{{ url()->route('accountent.billing', ['number'=>$bill->number]) }}" target="_blank"><i class="glyphicon glyphicon-eye-open" title="{{trans('accountent.bills.visualize')}}"></i></a>
                                    </td>
                               @else
                                    <td>{{trans('accountent.bills.not_paid')}}</td>
                                    <td><a class="alert alert-warning" href="{{ url()->route('accountent.pay', ['number'=>$bill->number]) }}" title="Payer la facture"><i class="glyphicon glyphicon-eur"></i></a></td>
                                    
                                @endif
                                
                            </tr>
                        @endforeach
                </tbody>
            </table>
            @else
                <p>{{ trans('instances.subscription.bills.noBill') }}</p>
            @endif
        </div>
    </div>
@stop