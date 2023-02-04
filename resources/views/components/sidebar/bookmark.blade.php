@if(count($project->bookmarks) > 0)
<section id="widget-bookmark" class="block-widget">
    <h2 class="widget-title">{{ trans('project.bookmarks') }}</h2>

    <ul class="list-unstyled">
    @foreach($project->bookmarks as $bookmark)
        <li>
            <a href="{{ $bookmark->url }}" target="_blank" class="text-capitalize">
                {{ $bookmark->name }}
            </a>
            <p>
            	<em>{{ $bookmark->description }}</em>
            </p>
        </li>
    @endforeach
    </ul>
</section>
@endif