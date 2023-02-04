@extends('accountent.menu.navigation')

@section('subcontent')
<div class="panel panel-default">
    <div class="panel-heading">
        <h2>{{ trans('accountent.menu.payMode') }}</h2>
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
        @if(isset($card) && $card!='')
            {{Form::open()}}
            <strong>{{ trans('welcome.actual-card') }}</strong>
            <div class="form-group"><br>
                {{$card}}
                <button type="submit" class="btn btn-border-default pull-right" name="delete">{{ trans('welcome.delete') }}</button>
            </div>
            {{Form::close()}}
        @endif
        <div class="row">
            <div class="form-group col-md-12">
                 {{ Form::label('payment_mode', trans('form.billingPayment.payment_mode')) }}
                {{ Form::select( 'payment_mode', ['card'=>trans('form.billingPayment.card'), 'iban'=>trans('form.billingPayment.iban')], null, ['class'=>'form-control payment_mode'] ) }}
             </div> 
        </div>
        <div class="card">
            {{ Form::open(['method'=>'POST', 'id'=>'payment-form']) }}
                <div class="row">
                    <div class="form-group col-md-12 error hide alert alert-danger">
                        <span class="text-alert"></span>
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('card-name', trans('form.pay.cardName')) }}
                        {{ Form::text( 'card-name', '', ['class'=>'form-control card-name'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('card-number', trans('form.pay.cardNumber')) }}
                        {!! $errors->first('card-number', '<p class="help-block">Ce champ est obligatoire</p>') !!}
                        {{ Form::text( 'card-number', '', ['class'=>'form-control card-number', 'inputMask' => "'mask': '9999 9999 9999 9999'"] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('card-expiry-month', trans('form.pay.month')) }}
                        {{ Form::text( 'card-expiry-month', '', ['class'=>'form-control card-expiry-month', 'placeholder'=>'MM'] ) }}
                    </div> 
                    <div class="form-group col-md-6">
                        {{ Form::label('year', trans('form.pay.year')) }}
                        {{ Form::text( 'card-expiry-year', '', ['class'=>'form-control card-expiry-year', 'placeholder'=>'YYYY'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('card-crypto', trans('form.pay.crypto')) }}
                        {{ Form::text( 'card-crypto', '', ['class'=>'form-control card-crypto', 'placeholder'=> 'ex: 123'] ) }}
                    </div> 
                </div>

                <div class="form-group col-md-12">
                    {{ Form::submit(trans('form.send'), ['class' => 'btn btn-border-default pull-right']) }}
                </div>
            {{ Form::close() }}
        </div>
        <div class="iban hide">
            {{ Form::open(['method'=>'POST', 'id'=>'iban-payment-form']) }}
                <div class="row">
                    <div class="form-group col-md-12 error hide alert alert-danger">
                        <span class="text-alert"></span>
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('iban-name', trans('form.pay.ibanName')) }}
                        {{ Form::text( 'iban-name', '', ['class'=>'form-control iban-name'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {{ Form::label('iban', trans('form.pay.iban')) }}
                        {!! $errors->first('iban', '<p class="help-block">Ce champ est obligatoire</p>') !!}
                        {{ Form::text( 'iban', '', ['class'=>'form-control iban'] ) }}
                    </div> 
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        {!! $errors->first('bic', '<p class="help-block">Ce champ est obligatoire</p>') !!}
                        {{ Form::label('bic', trans('form.pay.bic')) }}
                        {{ Form::text( 'bic', '', ['class'=>'form-control bic'] ) }}
                    </div> 
                </div>

                <div class="form-group col-md-12">
                    {{ Form::submit(trans('form.send'), ['class' => 'btn btn-border-default pull-right']) }}
                </div>
            {{ Form::close() }}
        </div>  

    </div>
</div>
    </div>
</div>  
@stop

@section('javascripts')
@parent
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>
            $(function() {
                var $select = $('.payment_mode');
                $select.val("{{$type}}");
                if($select.val()=="{{$type}}"){
                    $('.{{$type}}').removeClass('hide');
                    $other = ("card" == "{{$type}}") ? $(".iban") : $(".card");
                    $other.addClass("hide");
                }
                $select.on('change',function(){
                    if($(this).val()=="card"){
                        $('.card').removeClass('hide');
                        $('.iban').addClass('hide');
                    }
                    if($(this).val()=="iban"){
                        $('.card').addClass('hide');
                        $('.iban').removeClass('hide');
                    }

                });
                var $form = $("#payment-form");
                $form.on('submit', function(e){
                    $form.append("<input type='hidden' name='type' value='" + $('.payment_mode').val() + "'/>");
                    $form.get(0).submit();
                });
                var $ff = $("#iban-payment-form");
                $ff.on('submit', function(e){
                    $ff.append("<input type='hidden' name='type' value='" + $('.payment_mode').val() + "'/>");
                    $ff.get(0).submit();
                });
            });
        </script>
    <script>
        
    </script>
@stop