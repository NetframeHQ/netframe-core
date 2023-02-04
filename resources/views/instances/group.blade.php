@extends('instances.main')

@section('title')
    {{ trans('instances.groups.addUsers') }} • {{ $globalInstanceName }}
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
            <h2 class="main-header-title">{{ trans('instances.groups.title') }}</h2>
            <h2 class="main-header-title">{{ trans('instances.groups.titleSingle') }} {{ $group->name }}</h2>

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#create">
                        {{trans('instances.groups.users')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#import">
                        {{trans('instances.groups.profiles')}}
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <br/>
            <div class="tab-content">
                <div class="tab-pane container active" id="create">
                    <div class="card">
                        <div class="card-heading">
                            <h3>{{ trans('instances.groups.addUsers') }}</h3>
                        </div>
                        <div class="card-body">
                            {{ Form::open(['route' => ['join.search.users'], 'id' => 'inviteUsers']) }}
                                <div class="input-group">
                                    {{ Form::text('query', '', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit">
                                            <span class="svgicon">
                                                @include('macros.svg-icons.search')
                                            </span>
                                        </button>
                                    </span>
                                </div>

                            {{ Form::close() }}

                            <div id="search-results">
                                {{--
                                @include('join.search-results')
                                --}}
                            </div>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        @foreach($group->users as $user)
                            <li class="bd-bottom padding-5 clearfix member-card container-element group-{{ $group->id }}-user-{{ $user->id }}">
                                {{ $user->getNameDisplay() }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tab-pane container fade" id="import">
                    @todo : gérer les profils avec membership auto et role
                </div>
            </div>
        </div>
    </div>
@stop