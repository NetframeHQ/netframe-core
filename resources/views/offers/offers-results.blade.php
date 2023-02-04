@if(count($offers) == 0 && $autoScroll == 0)
    <div class="col-md-12">
        <div class="well well-sm text-center">
            <strong>{{ trans('offer.noResults') }}</strong>
        </div>
    </div>
@endif

@foreach($offers as $offer)
    <div class="col-md-12">
        @include('page.post-content', ['unitPost'=>false, 'post' => $offer->posts->first() ])
    </div>
@endforeach
