@extends('layouts.fullpage')

@section('content')
    <div class="card">
        <div class="card-header text-center">
            <a href="{{ session('landingDrivePage') }}" class="float-right">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('form.close') }}</span>
            </a>
            <h4>
                {{ trans('task.task.title') }}
            </h4>
        </div>

        <div class="main-container">
            <div id="nav_skipped" class="main-scroller search">
                <div class="tasks">
                    {!! Form::open() !!}
                    <div class="row">
                        <div class="form-group col-12">
                            <label>{{ trans('task.task.name') }}</label>
                            <div class="input-group">
                                <input type="text" name="task_name" required value="{{$name ?? ''}}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label>{{ trans('task.task.user') }}</label>
                            <div class="input-group">
                                <select name="task_user" class="select-user form-control">
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label>{{ trans('task.task.deadline') }}</label>
                            <div class="input-group">
                                <input type="date" name="deadline" required class="form-control" min="{{-- date('Y-m-d') --}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label>{{ trans('task.task.parent') }}</label>
                            <div class="input-group">
                                @if($tasks!=[])
                                {!! Form::select('parent', [null=>trans('task.task.select')]+$tasks->toArray(), null, ['class' => 'form-control']) !!}&nbsp;
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <div class="input-group">
                                <button class="button primary float-right">{{ trans('form.save') }}</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
    $('.select-user').select2({
        // dropdownParent: $("#modal-ajax"),
        placeholder: "Saisir un user",
        // minimumInputLength: 1,
        templateResult: format,
        templateSelection: format,
        ajax: {
            url: "{{route('task.users')}}",
            dataType: "json",
            type: "POST",
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function(m) { return m; },
    });
    // $(document).ajaxSuccess(function(){

    // })
})
function format(state) {
    // alert(JSON.stringify(state))
    if (!state.image) return state.text;
    //var originalOption = state.element;
    return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}
</script>
@stop