<li class="help-block clearfix">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>{{ trans('project.bookmarks') }}</h2>
                @foreach($project->bookmarks as $bookmark)
                    <h3><a href="{{ $bookmark->url }}" target="_blank">{{ $bookmark->name }}</a></h3>
                    <p>{{ $bookmark->description }}</p>
                @endforeach
        </div>
    </div>
</li>