@extends('instances.main')

@section('title')
    {{ trans('instances.manage.title') }} • {{ $globalInstanceName }}
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
    <div class="nf-form">
        <div class="nf-settings-title">
            <h2>{{ trans('instances.manage.title') }}</h2>
            <div class="nf-actions">
                <ul class="nav nav-tabs">
                    @if(!session('instanceMonoProfile'))
                        <li class="nav-item">
                            <a class="nf-btn btn-nobg nav-link px-2 @if(request()->get('pane') == null || request()->get('pane') == 'houses') active @endif" data-toggle="tab" href="#houses">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.house')
                                </span>
                                <span class="btn-txt">
                                    {{trans('instances.manage.titles.houses')}}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nf-btn btn-nobg nav-link px-2 @if(request()->get('pane') == 'projects') active @endif" data-toggle="tab" href="#projects">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.project')
                                </span>
                                <span class="btn-txt">
                                    {{trans('instances.manage.titles.projects')}}
                                </span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nf-btn btn-nobg nav-link px-2 @if(request()->get('pane') == 'communities' || (session('instanceMonoProfile') && request()->get('pane') == null)) active @endif" data-toggle="tab" href="#communities">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.community')
                            </span>
                            <span class="btn-txt">
                                {{trans('instances.manage.titles.communities')}}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nf-btn btn-nobg nav-link px-2 @if(request()->get('pane') == 'channels') active @endif" data-toggle="tab" href="#channels">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.channel')
                            </span>
                            <span class="btn-txt">
                                {{trans('instances.manage.titles.channels')}}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            @if(!session('instanceMonoProfile'))
                <div class="tab-pane @if(request()->get('pane') == null || request()->get('pane') == 'houses') active @endif" id="houses">
                    <ul class="nf-list-settings">
                        @foreach($houses as $profile)
                            @include('join.member-card', ['member' => $profile, 'profile' => $profile->member])
                        @endforeach
                    </ul>

                    <div class="nf-pagination">
                        {{ $houses->appends(['pane'=>'houses'])->links('vendor.pagination.bootstrap-4', ['foo'=>'bar']) }}
                    </div>
                </div>
                <div class="tab-pane @if(request()->get('pane') == 'projects') active @endif" id="projects">
                    <ul class="nf-list-settings">
                        @foreach($projects as $profile)
                            @include('join.member-card', ['member' => $profile, 'profile' => $profile->member])
                        @endforeach
                    </ul>

                    <div class="nf-pagination">
                        {{ $houses->appends(['pane'=>'projects'])->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            @endif
            <div class="tab-pane @if(request()->get('pane') == 'communities'  || (session('instanceMonoProfile') && request()->get('pane') == null)) active @endif" id="communities">
                <ul class="nf-list-settings">
                    @foreach($communities as $profile)
                        @include('join.member-card', ['member' => $profile, 'profile' => $profile->member])
                    @endforeach
                </ul>

                <div class="nf-pagination">
                    {{ $communities->appends(['pane'=>'communities'])->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
            <div class="tab-pane @if(request()->get('pane') == 'channels') active @endif" id="channels">
                <ul class="nf-list-settings">
                    @foreach($channels as $profile)
                        @include('join.member-card', ['member' => $profile, 'profile' => $profile->member])
                    @endforeach
                </ul>

                <div class="nf-pagination">
                    {{ $channels->appends(['pane'=>'channels'])->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascripts')
@parent
<script>
var disableTxt = '{{ trans('instances.profiles.disable') }}';
var enableTxt = '{{ trans('instances.profiles.enable') }}';

</script>
@stop