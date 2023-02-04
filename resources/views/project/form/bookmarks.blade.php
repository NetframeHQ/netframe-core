@extends('project.form.main')

@section('subcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ trans('project.bookmarks') }}</h3>
        </div>

        <div class="panel-body">
        <ul id="bookmark-list">
            @foreach($project->bookmarks as $bookmark)
                <li id="bookmark-{{ $bookmark->id }}">
                    <p class="float-right">
                        <a href="{{ url()->route('project_bookmark_delete', ['idProject' => $project->id, 'idBookmark' => $bookmark->id]) }}" class="fn-confirm-delete fn-ajax-delete"><span class="label label-danger" aria-hidden="true">&times;</span></a>
                        &nbsp;
                        <a href="{{ url()->route('project_bookmark_form', ['idProject' => $project->id, 'idBookmark' => $bookmark->id]) }}" data-target="#modal-ajax" data-toggle="modal">
                            {{ trans('project.modify') }}
                        </a>
                    </p>
                    <h4><a href="{{ $bookmark->url }}" target="_blank">{{ $bookmark->name }}</a></h4>
                    <p>{{ $bookmark->description }}</p>
                </li>
            @endforeach
        </ul>
        </div>
    </div>

    <center>
        <a href="{{ url()->route('project_bookmark_form', ['project' => $project->id]) }}" class="button primary" data-target="#modal-ajax" data-toggle="modal">
            {{ trans('project.addBookmark') }}
        </a>
    </center>
@stop