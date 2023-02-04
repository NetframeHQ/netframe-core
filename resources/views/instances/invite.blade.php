@extends('instances.main')

@section('title')
    {{ trans('instances.invite.title') }} • {{ $globalInstanceName }}
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
            {{ trans('instances.invite.title') }}
        </div>
        @if($userQuotaReach)
            <div class="nf-form-informations">
                {{ trans('instances.invite.userLimitReach') }}
            </div>
        @else
            @if(session()->has('tabEmails'))
                @if(session()->has('tabEmails.notSended') && count(session('tabEmails.notSended')) > 0)
                    <div class="nf-form-informations bg-warning">
                        {{ trans('instances.invite.notSended') }}
                        <ul>
                            @foreach(session('tabEmails.notSended') as $notSended)
                                <li>
                                    {{ $notSended }}
                                </li>
                            @endforeach
                        </ul>
                    </div> 
                @endif
                @if(session()->has('tabEmails.sended') & count(session('tabEmails.sended')) > 0)
                    <div class="nf-form-informations bg-success">
                        {{ trans('instances.invite.sended') }}
                        <ul>
                            @foreach(session('tabEmails.sended') as $sended)
                                <li>
                                    {{ $sended }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
            <div class="nf-form-informations">
                {{ trans('instances.boarding.publicKeyLink') }}
                <code>
                    {{ $instance->getBoardingUrl() }}
                    
                </code>
                <ul class="nf-actions">
                    <!-- <li class="nf-action">
                        <a href="#" class="nf-btn">
                            <span class="btn-txt">
                                {{ trans('instances.boarding.publicKeyLinkCopy') }}
                            </span>
                        </a>
                    </li> -->
                    <li class="nf-action">
                        <a href="{{ url()->route('instance.boarding', ['action' => 'disable-key']) }}" class="nf-btn">
                            <span class="btn-txt">
                                @if($instance->getParameter('boarding_on_key_disable') == null || $instance->getParameter('boarding_on_key_disable') == 0)
                                    {{ trans('instances.boarding.createWithKeyDisable') }}
                                @else
                                    {{ trans('instances.boarding.createWithKeyEnable') }}
                                @endif
                            </span>
                        </a>
                    </li>
                </ul>
                
                <hr>
                {{ trans('instances.invite.intro') }}
            </div>
            {{ Form::open() }}
                {{ Form::hidden('nbFields', '5') }}

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email1')) nf-cell-error @endif">
                    {{ Form::text('email1', '', ['class' => 'nf-form-input', 'class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email1', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email2')) nf-cell-error @endif">
                    {{ Form::text('email2', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email2', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email3')) nf-cell-error @endif">
                    {{ Form::text('email3', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email3', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email4')) nf-cell-error @endif">
                    {{ Form::text('email4', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email4', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email5')) nf-cell-error @endif">
                    {{ Form::text('email5', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email5', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <!-- EMAIL -->
                <label class="nf-form-cell @if($errors->has('email6')) nf-cell-error @endif">
                    {{ Form::text('email6', '', ['class' => 'nf-form-input', 'placeholder' => trans('form.placeholder.email')]) }}
                    <span class="nf-form-label">
                        {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email6', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <div class="nf-form-validation">
                    <button type="submit" class="nf-btn btn-primary btn-xxl">
                        <div class="btn-txt">
                            {{ trans('instances.invite.send') }}
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