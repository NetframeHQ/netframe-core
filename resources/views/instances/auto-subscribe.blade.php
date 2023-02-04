@extends('instances.main')

@section('title')
    {{ trans('instances.autoSubscribe.title') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon btn-img">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@stop

@section('subcontent')
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.autoSubscribe.title') }}
        </div>
        <div class="nf-form-informations">
            {!! trans('instances.autoSubscribe.intro') !!}
            <div class="nf-form-tabs">
                @if(!session('instanceMonoProfile'))
                    <a class="nf-btn btn-nobg {{ ($profileType == 'houses') ? 'active' : ''}}" href="{{ url()->route('instance.auto.subscribe', ['profileType' => 'houses'] ) }}">
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.house')
                        </span>
                        <span class="btn-txt">
                            {{ trans('netframe.house') }}
                        </span>
                    </a>
                    <a class="nf-btn btn-nobg {{ ($profileType == 'projects') ? 'active' : ''}}" href="{{ url()->route('instance.auto.subscribe', ['profileType' => 'projects'] ) }}">
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.project')
                        </span>
                        <span class="btn-txt">
                            {{ trans('netframe.project') }}
                        </span>
                    </a>
                @endif
                <a class="nf-btn btn-nobg {{ ($profileType == 'communities') ? 'active' : ''}}" href="{{ url()->route('instance.auto.subscribe', ['profileType' => 'communities'] ) }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.community')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.community') }}
                    </span>
                </a>
                <a class="nf-btn btn-nobg {{ ($profileType == 'channels') ? 'active' : ''}}" href="{{ url()->route('instance.auto.subscribe', ['profileType' => 'channels'] ) }}">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.channel')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.channel') }}
                    </span>
                </a>
            </div>
        </div>
        @if($display == 'profile')
            @if(isset($success) && $success)
                <div class="nf-form-informations bg-success">
                    {{ trans('instances.autoSubscribe.updatedDone') }}
                </div>
            @endif
            {{ Form::open() }}
                <ul class="nf-list-settings">
                    @foreach($profiles as $profile)
                        <li class="nf-list-setting">
                            @if($profile->getType() != 'channel' && $profile->mosaicImage() != null)
                                <span class="avatar">
                                    {!! HTML::thumbnail($profile->mosaicImage(), '40', '40', array('class' => 'img-fluid float-left'), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
                                </span>
                            @else
                                <span class="svgicon">
                                    @include('macros.svg-icons.'.$profile->getType())
                                </span>
                            @endif
                            <div class="nf-list-infos">
                                <div class="nf-list-title">
                                    {{ $profile->getNameDisplay() }}
                                </div>
                                <div class="nf-list-subtitle">
                                    {{ trans('instances.profiles.createdAt') }} {{ \App\Helpers\DateHelper::feedDate($profile->created_at) }}
                                </div>
                            </div>
                            <ul class="nf-actions">
                                <li class="nf-action">
                                    <div class="nf-checkbox">
                                        {{ Form::checkbox('autoProfiles[]', $profile->id, ($profile->auto_subscribe == 1) ? true : false) }}
                                    </div>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
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
        @endif
    </div>

@stop
