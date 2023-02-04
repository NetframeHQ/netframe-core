@extends('layouts.page')
@section('title')
    {{ trans('task.title') }} • {{ $globalInstanceName }}
@stop
@section('content')
<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.user')
        </span>
        <h2 class="main-header-title">
            {{ trans('task.title') }}
        </h2>
    </div>
</div>

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <div class="search">
            <div>
                <a href="{{ route('task.detailsProject',['projectId' => $project->id]) }}" class="button primary">{{ trans('task.details') }}</a>
                <a style="float: right;" href="{{ route('task.addTask',['project' => $project->id]) }}" data-toggle="modal" data-target="#modal-ajax" class="button primary">{{ trans('task.task.title') }}</a>
            </div>
            <br>
        <div class="bloc">
            <div class="tasks">
                <p>
                    <h6>{{ trans('task.template.name') }}: {{ $project->name }}</h6>
                </p>
                @foreach($template_cols as $key=>$value)
                    <p>
                        <h6>
                            @if(isset($value[$key]))
                            <span style="float: left;">{{ucfirst($value['name'])}}:&nbsp;</span>
                            @endif
                            @if($value['type']=="tag")
                            <ul class="list-unstyled tags-list" id="userReferenceList">
                                @if( $project->tags->count() > 0)
                                    @foreach($project->tags as $tag)
                                        <li>
                                            <a href="{{ url()->route('tags.page', ['tagId' => $tag->id, 'tagName' => str_slug($tag->name)]) }}">
                                                #{{ $tag->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                            @elseif($value['type']=="user")
                                @if(isset($cols[$key]))
                                <ul class="list-unstyled profiles">
                                @foreach($cols[$key] as $userId)
                                @php
                                    $user = \App\User::find($userId);
                                @endphp
                                    <li>
                                        <a href="{{$profile->getUrl()}}">
                                            <span class="user-pic">
                                                {!! HTML::thumbnail($user->profileImage, 60, 60, [], asset('assets/img/avatar/user.jpg')) !!}
                                            </span>

                                            <p class="name">{{$user->getNameDisplay()}}</p>
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                                @endif
                            @elseif($value['type']=="file")
                            @elseif(!empty($cols[$key]))
                            {{ $cols[$key] }}
                            @endif
                        </h6>

                    </p>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</div>
@stop

@section('sidebar')
    @include('components.sidebar-user')
@stop