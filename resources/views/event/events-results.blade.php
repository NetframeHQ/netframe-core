@if(count($events) == 0 && $autoScroll == 0)
    <div class="col-md-12">
        <div class="well well-sm text-center">
            <strong>{{ trans('event.noResults') }}</strong>
        </div>
    </div>
@endif

@foreach($events as $event)
    <div class="col-md-12">
        @include('page.post-content', ['unitPost'=>false, 'post' => $event ])
    </div>
@endforeach
