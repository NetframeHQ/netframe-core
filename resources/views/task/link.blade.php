@extends('layouts.fullpage')

@section('content')
    <div class="card">
        <div class="card-body">
            {!! Form::open() !!}
            <div class="row">
                <div class="col-12">
                <div class="row">
                    <div class="form-group col-12">
                        <label>{{ trans('task.link') }}</label>
                        <div class="input-group">
                            @if($tasks!=[])
                            {!! Form::select('parent', $tasks->toArray(), null, ['class' => 'form-control']) !!}&nbsp;
                            @endif
                        </div>
                    </div>
                </div>
                    <div class="form-group offset-10">
                        <div class="input-group" style="float: right;">
                            <button class="button primary">{{ trans('form.save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
@stop
 