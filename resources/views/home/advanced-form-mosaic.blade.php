{{ Form::open(['method' => 'GET', 'route' => $searchParameters['route'], 'id' => 'searchProfileFilter']) }}
    <div class="row">
        <div class="col-sm-12 col-md-12 profile-filters-mosaic">
            <div class="form-inline text-center">
                    @foreach($listProfilesFilter as $profileFilter=>$active)
                        @if($active == 1)
                    <label class="btn btn-{{ $profileFilter }} fn-profile-filter @if(isset($searchParameters['targetsProfiles'][$profileFilter]) ) active @endif">
                        <span class="icon ticon-{{ str_singular($profileFilter) }}"></span>
                        <span class="hidden-xs">{{ trans('netframe.'.$profileFilter) }}</span>
                        {{ Form::checkbox('profile[]', $profileFilter, (isset($searchParameters['targetsProfiles'][$profileFilter])), ['class' => '']) }}
                    </label>

                        @endif
                    @endforeach

                {{ Form::input('text', 'query', request()->get('query'), ['class' => 'form-control', 'placeholder' => trans('search.label')]) }}
                <button type="submit" class="btn btn-default">{{ trans('form.filter') }}</button>
            </div>
        </div>
    </div>
{{ Form::input('hidden', 'currentPage', $searchParameters['currentPage'], ['id' => 'currentPage'] ) }}
{{ Form::close() }}