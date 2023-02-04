@extends('accountent.menu.navigation')

@section('subcontent')
<div class="panel panel-default">
    <div class="panel-heading">
        <h2>{{ trans('form.infos.title') }}</h2>
    </div>
    <div class="panel-body">   

    @if(Session::has('flash_message'))
        <div class="alert alert-success"><em> {!! session('flash_message') !!}</em></div>
    @endif 
    @if(Session::has('error_flash_message'))
        <div class="alert alert-danger"><em> {!! session('error_flash_message') !!}</em></div>
    @endif 
    @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div><br />
      @endif
        <div>
            {{ Form::open(['method'=>'POST', 'id'=>'infos-form']) }}
                <div class="row">
                    <div class="form-group col-md-12 error hide alert alert-danger">
                        <span class="text-alert"></span>
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('designation', trans('form.infos.designation')) }}
                        {{ Form::text( 'designation', $designation, ['class'=>'form-control'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('address', trans('form.infos.address')) }}
                        {{ Form::text( 'address', $address, ['class'=>'form-control'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('codepostal', trans('form.infos.codepostal')) }}
                        {{ Form::text( 'codepostal', $codepostal, ['class'=>'form-control'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('city', trans('form.infos.city')) }}
                        {{ Form::text( 'city', $city, ['class'=>'form-control'] ) }}
                    </div> 
                </div>

                <div class="form-group col-md-12">
                    {{ Form::submit(trans('form.send'), ['class' => 'btn btn-border-default pull-right']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop



