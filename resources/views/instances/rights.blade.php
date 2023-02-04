@extends('instances.main')

@section('title')
    {{ trans('instances.rights.title') }} • {{ $globalInstanceName }}
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
    @if(!isset($action) || $action == '')

        <div class="nf-form nf-col-2">
            <div class="nf-settings-title">
                {{ trans('instances.rights.title') }}
            </div>
            <div class="nf-form-informations">
                <label class="nf-checkbox">
                    {{ Form::checkbox('ban_post_on_timeline', 1, $refusePostTimeline) }}
                    <span> {{ trans('instances.rights.banPostTimeline') }}</span>
                </label>
            </div>
            <div class="nf-form-informations">
                {{ trans('instances.rights.authProfiles.intro') }}
            </div>

            {{ Form::open(['route' => ['instance.rights', 'action' => 'authProfiles']]) }}
                <div class="nf-table-custom">
                    <div class="table-col table-head-y">
                        <div class="table-line table-head-x">
                        </div>
                        @foreach($rightsProfiles as $profiles=>$profile)
                            <div class="table-line">
                                <div class="table-cell">
                                    <div class="table-cell-content">
                                        <div class="svgicon">
                                            @include('macros.svg-icons.'.$profile)
                                        </div>
                                        {{ trans('netframe.'.$profiles) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="table-col">
                        <div class="table-line table-head-x">
                            @if(!session('instanceMonoProfile'))
                                <div class="table-cell">
                                    <div class="table-cell-content">
                                        <div class="nf-checkbox">
                                            {{ trans('netframe.houses') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="table-cell">
                                <div class="table-cell-content">
                                    <div class="nf-checkbox">
                                        {{ trans('netframe.communities') }}
                                    </div>
                                </div>
                            </div>
                            @if(!session('instanceMonoProfile'))
                                <div class="table-cell">
                                    <div class="table-cell-content">
                                        <div class="nf-checkbox">
                                            {{ trans('netframe.projects') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="table-cell">
                                <div class="table-cell-content">
                                    <div class="nf-checkbox">
                                        {{ trans('netframe.channels') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach($rightsProfiles as $profiles=>$profile)
                            <div class="table-line">
                                @if(!session('instanceMonoProfile'))
                                    <div class="table-cell">
                                        <div class="table-cell-content">
                                            <div class="nf-checkbox">
                                                {{ Form::checkbox($profile.'-create-house', '1', $instanceRightsProfiles[$profile]['house']) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="table-cell">
                                    <div class="table-cell-content">
                                        <div class="nf-checkbox">
                                            {{ Form::checkbox($profile.'-create-community', '1', $instanceRightsProfiles[$profile]['community']) }}
                                        </div>
                                    </div>
                                </div>
                                @if(!session('instanceMonoProfile'))
                                    <div class="table-cell">
                                        <div class="table-cell-content">
                                            <div class="nf-checkbox">
                                                {{ Form::checkbox($profile.'-create-project', '1', $instanceRightsProfiles[$profile]['project']) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="table-cell">
                                    <div class="table-cell-content">
                                        <div class="nf-checkbox">
                                            {{ Form::checkbox($profile.'-create-channel', '1', $instanceRightsProfiles[$profile]['channel']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

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
                {{ trans('instances.rights.god.title') }}
            </div>
            <div class="nf-form-informations">
                @if(session()->has('godMode') && session('godMode') == 1)
                    {{ trans('instances.rights.god.youAreGod') }}
                    <hr>
                    <a class="nf-btn btn-xxl btn-full" href="{{ url()->route('instance.rights', ['action' => 'disableGod']) }}">
                        <span class="btn-txt">
                            {{ trans('instances.rights.god.disableGod') }}
                        </span>
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.arrow-right')
                        </span>
                    </a>
                @else
                    {{ trans('instances.rights.god.intro') }}
                    <hr>
                    <a class="nf-btn btn-xxl btn-full" href="{{ url()->route('instance.rights', ['action' => 'becomeGod']) }}">
                        <span class="btn-txt">
                            {{ trans('instances.rights.god.becomeGod') }}
                        </span>
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.arrow-right')
                        </span>
                    </a>
                @endif

            </div>
        </div>
    @elseif($action == 'godPassword')
        {{ Form::open() }}
            {{ Form::label('password', trans('instances.rights.god.godPassword')) }}
            {{ Form::password('password', '', ['class' => 'form-control']) }}
            {{ Form::submit(trans('instances.rights.god.becomeGod'), ['class' => 'button primary']) }}
        {{ Form::close() }}
    @endif

@stop

@section('javascripts')
@parent
<script>
(function($){
    $(document).on('change', 'input[name="ban_post_on_timeline"]', function(e){
        var postData = {
            'ban_post_on_timeline': ($(this).is(':checked'))
        };

        $.ajax({
            url: '{{ url()->route('instance.rights', ['action' => 'updateTimelinePost']) }}',
            data: postData,
            type: "POST",
            success: function (data) {

            },
            error: function (textStatus, errorThrown) {
                requestSended = false;
            }
        });
    });
})(jQuery);
</script>
@endsection