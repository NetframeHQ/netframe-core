@extends('instances.main')

@section('title')
    {{ trans('instances.groups.title') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@stop

@section('subcontent')
    <div class="card">
        <div class="card-body">
            <h2>{{ trans('instances.groups.title') }}</h2>
            {{ Form::open() }}
                @if(Session::has('group.created'))
                    <div class="alert alert-success">{{ Session::get('group.created') }}</div>
                @endif
                <div class="form-group">
                    <label>{{trans('instances.groups.name')}}</label>
                    <div class="input-group">
                        {{ Form::text('name', '', $attributes = $errors->has('name') ? ['class' => 'form-control is-invalid'] : ['class' => 'form-control']) }}
                        <small class="invalid-feedback">{{$errors->first('name')}}</small>
                    </div>
                </div>
                {{-- <div class="form-group">
                    <label>{{trans('instances.groups.owner')}}</label>
                    <div class="input-group">
                        <select name="owner" class="form-control select-user"></select>
                        <small class="invalid-feedback">{{$errors->first('owner')}}</small>
                    </div>
                </div> --}}
                <div>
                    {{ Form::Submit(trans('form.save'), ['class' => 'button primary float-right']) }}
                </div>
                <button type="submit" name="add" class="button primary float-right">{{ trans('instances.groups.addButton') }}</button>
            {{ Form::close() }}
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script>
var disableTxt = '{{ trans('instances.profiles.disable') }}';
var enableTxt = '{{ trans('instances.profiles.enable') }}';

(function($){
    $('.select-user').select2({
        placeholder: "Saisir un user",
        // minimumInputLength: 1,
        templateResult: format,
        templateSelection: format,
        ajax: {
            url: "{{url()->route('instance.user')}}",
            dataType: "json",
            type: "POST",
            data: function (params) {
                return {
                    query: params.term
                };
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function(m) { return m; },
    });
    function format(state) {
        if (!state.image) return state.text;
        return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
    }
})(jQuery);
</script>
@stop