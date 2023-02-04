@extends('instances.main')

@section('title')
    {{ trans('instances.visitors.title') }} • {{ $globalInstanceName }}
@stop

@section('subcontent')
    @if ($visitors->count() >= 1)
        <div class="nf-form nf-col-2">
            <div class="nf-settings-title">
                {{ trans('instances.visitors.title') }}
            </div>

            @foreach($visitors as $profile)
                <ul class="nf-list-settings">
                    @include('join.member-card', ['profile' => $instance, 'member' => $profile])
                    {{--@include('instances.partials.visitor-card')--}}
                </ul>
            @endforeach
        </div>
    @endif

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.profiles.addVisitor') }}
        </div>

        {{ Form::open(['id' => 'addProfile']) }}
            <!-- FIRST NAME -->
            <label class="nf-form-cell @if($errors->has('firstname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="firstname" name="firstname" value="{{ request()->old('firstname') }}" placeholder="{{trans('form.placeholder.firstname')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.firstname') }}
                </span>
                {!! $errors->first('firstname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- LAST NAME -->
            <label class="nf-form-cell @if($errors->has('lastname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="lastname" name="lastname" value="{{ request()->old('lastname') }}" placeholder="{{trans('form.placeholder.lastname')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.name') }}
                </span>
                {!! $errors->first('lastname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <!-- EMAIL -->
            <label class="nf-form-cell nf-cell-full @if($errors->has('email')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="email" name="email" value="{{ request()->old('email') }}" placeholder="{{trans('form.placeholder.email')}}">
                <span class="nf-form-label">
                    {{ trans('form.setting.email') }}
                </span>
                {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('instances.profiles.add') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
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
</script>
@stop