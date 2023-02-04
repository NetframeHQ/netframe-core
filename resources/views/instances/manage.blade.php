@extends('instances.main')
@section('stylesheets')
    <style>
        .dropdown-toggle::after{display: none}
        .status.active{background: #e8e9eb;}
        .status.hide{display: none;}
        .switch-on::before{background: #333}
    </style>
@stop

@section('title')
    @if($profileType=="projects")
        {{$profile->title}} • {{ $globalInstanceName }}
    @else
        {{$profile->name}} • {{ $globalInstanceName }}
    @endif
@stop

@section('content-header')
    <div class="main-header-infos">
        <h2 class="main-header-title">
            <a href="{{route('instance.profiles',['profileType'=>$profileType])}}">
                {{ trans('instances.profiles.titles.'.$profileType) }}
            </a>
            >
            @if($profileType=="projects")
                {{$profile->title}}
            @else
                {{$profile->name}}
            @endif
        </h2>
    </div>
@stop

@section('subcontent')
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" id="manageTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ (request()->has('page') || isset($fromSearch)) ? '' : 'active' }}" id="profile-members-tab" data-toggle="tab" href="#profile-members" role="tab" aria-controls="home" aria-selected="true">
                    {{trans('instances.manage.members')}} ({{count($members)}})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ (request()->has('page') || isset($fromSearch)) ? 'active' : '' }}" id="add-profile-members-tab" data-toggle="tab" href="#add-profile-members" role="tab" aria-controls="profile" aria-selected="false">
                    {{ trans('instances.manage.addMembers') }}
                </a>
            </li>
        </ul>

        <div class="tab-content" id="manageTabContent">
            <div class="tab-pane fade {{ (request()->has('page') || isset($fromSearch)) ? '' : 'show active' }}" id="profile-members" role="tabpanel" aria-labelledby="profile-members-tab">
                <div class="card">
                    <ul class="nf-list-settings">
                        @foreach($members as $member)
                            @include('join.member-card')
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="tab-pane fade {{ (request()->has('page') || isset($fromSearch)) ? 'show active' : '' }}" id="add-profile-members" role="tabpanel" aria-labelledby="add-profile-members-tab">
                <div class="card">
                    <div class="card-heading">
                        {{ Form::open(['id' => 'searchProfiles']) }}
                            <div class="input-group">
                                {{ Form::text('query', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => trans('instances.manage.searchUsers')]) }}
                                <span class="input-group-btn">
                                    <button class="btn btn-default" name="search" type="submit">
                                        <span class="svgicon">
                                            @include('macros.svg-icons.search')
                                        </span>
                                    </button>
                                </span>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <ul class="nf-list-settings">
                        @foreach($profiles as $member)
                            @if(!in_array($member->id, $membersIds))
                                @include('join.member-card', ['form' => true])
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="nf-pagination">
                    @if($fromSearch == 0)
                        {{ $profiles->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
@parent
<script>
var disableTxt = '{{ trans('instances.profiles.disable') }}';
var enableTxt = '{{ trans('instances.profiles.enable') }}';
</script>
@stop