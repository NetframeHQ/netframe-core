@if(count($projects) > 0)
<section id="userProject" class="block-widget">
    @if(count($projects) > 0)
        @if(isset($routeMore))
            <a href="{{ $routeMore }}" class="btn-xs btn-default float-right modal-hidden" data-toggle="modal" data-target="#modal-ajax">{{ trans('netframe.viewAll') }}</a>
        @endif
    <h2 class="widget-title">{{ trans('widgets.'.$prefixTranslate.'project') }}</h2>

    <ul class="list-unstyled">
        @foreach($projects as $project)
        <li class="media">
            <a href="{{ $project->getUrl() }}">
				{!! HTML::thumbImage($project->profile_media_id, '60', '60', ['class' => 'img-fluid profile-image'], 'project', 'media-left') !!}
                <div class="media-body">
                    <h4>{{ $project->title }}</h4>
                    <small>{{ $project->description }}</small>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
    @endif
</section>
@endif