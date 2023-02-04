@extends('instances.main')

@section('title')
    {{ trans('instances.create.title') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.create.title') }}</h1>
    </div>
@stop

@section('subcontent')
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.create.title') }}
        </div>

        {{ Form::open() }}
            @if(Session::has('status'))
                <div class="alert alert-success nf-form-cell nf-cell-full">{{trans('instances.create.success')}}{{ Session::get('status') }}</div>
            @endif

            <label class="nf-form-cell @if($errors->has('firstname')) nf-cell-error @endif">
                {{ Form::text('firstname', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.firstname')]) }}
                <span class="nf-form-label">
                    {{trans('instances.create.firstname')}}
                </span>
                {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell @if($errors->has('name')) nf-cell-error @endif">
                {{ Form::text('name', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.lastname')]) }}
                <span class="nf-form-label">
                    {{trans('instances.create.name')}}
                </span>
                {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell nf-cell-full @if($errors->has('email')) nf-cell-error @endif">
                {{ Form::text('email', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                <span class="nf-form-label">
                    {{trans('instances.create.email')}}
                </span>
                {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            @foreach($customFields as $slug => $value)
                @include('instances.partials.custom-fields.'.$value['type'],[
                    'name'=> 'custom_field['.$slug.']',
                    'label'=> ''.$value['name'].'',
                    'value' => ''
                ])
            @endforeach

            <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('form.save') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        {{ Form::close() }}
    </div>

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.create.importFrom') }}
        </div>
        {{ Form::open(['files' => true]) }}
            @if(Session::has('error.header'))
                <div class="nf-form-informations bg-danger">
                    {{trans('instances.create.error.title')}}: {{trans("instances.create.cols.".Session::get('error.header'))}}
                </div>
            @elseif(Session::has('success.file'))
                <div class="nf-form-informations bg-success">
                    {{trans('instances.create.file-success')}}
                </div>
                <table class="table table-bordered">
                    <tbody>
                        @foreach(Session::get('success.file') as $line)
                            @if($line!=null)
                            <tr>
                                @foreach($line as $col)
                                <td>{{$col}}</td>
                                @endforeach
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @elseif(Session::has('error.file'))
                <div class="nf-form-informations bg-danger">
                    {{trans('instances.create.file-error')}}
                </div>
                <table class="table table-bordered">
                    <tbody>
                        @foreach(Session::get('error.file') as $line)
                            @if($line!=null)
                            <tr>
                                @foreach($line as $col)
                                <td>{{$col}}</td>
                                @endforeach
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
            <div class="nf-form-informations">
                {{trans('instances.create.description')}}
            </div>
            <div class="nf-form-validation">
                <div class="nf-form-validactions">
                    <!-- EMAIL -->
                    <label class="nf-form-cell nf-cell-full @if($errors->has('file')) nf-cell-error @endif">
                        <input type="file" name="file" class="nf-form-input" accept=".csv">
                        <span class="nf-form-label">
                            {{trans('instances.create.file')}}
                        </span>
                        {!! $errors->first('file', '<p class="invalid-feedback">:message</p>') !!}
                        <div class="nf-form-cell-fx"></div>
                    </label>
                    <small class="invalid-feedback">{{$errors->first('file')}}</small>
                </div>
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.import')
                    </div>
                    <div class="btn-txt">
                        {{ trans('instances.create.import') }}
                    </div>
                </button>
            </div>
            {{ Form::close() }}
    </div>
@stop

@section('javascripts')
@parent
<script>
var disableTxt = '{{ trans('instances.profiles.disable') }}';
var enableTxt = '{{ trans('instances.profiles.enable') }}';

(function($){
    $(document).on('click', '.fn-active-profile', function(e){
        e.preventDefault();
        var el = $(this);
        var link = el.attr('href');
        var newState = el.data('toggle-state');
        var profileId = el.data('profile-id');
        var params = {
                stateTo: newState,
                profileId: profileId
        };

        var jqXhr = $.post(link, params);

        jqXhr.success(function(data) {
            if(data.active == 1){
                el.removeClass('btn-danger');
                el.addClass('btn-success');
                el.data('toggle-state', 0);
                el.html(disableTxt);
            }
            else if(data.active == 0){
                el.removeClass('btn-success');
                el.addClass('btn-danger');
                el.data('toggle-state', 1);
                el.html(enableTxt);
            }

        }).error(function(xhr) {

        });
    });
})(jQuery);
</script>
@stop
