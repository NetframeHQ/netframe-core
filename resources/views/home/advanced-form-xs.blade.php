{{ Form::open(array('method' => 'GET', 'route' => $searchParameters['route'], 'id' => 'searchProfileFilterXs')) }}

    <div class="row">
        <div class="col-xs-12 profile-filters-xs">
            <div class="form-inline">
                <div class="text-center mg-bottom-5 col-xs-12 col-sm-7">
                    @foreach($listProfilesFilter as $profileFilter=>$active)
                        @if($active == 1)
                    <label class="btn btn-{{ $profileFilter }} fn-profile-filter @if(isset($searchParameters['targetsProfiles'][$profileFilter]) ) active @endif">
                        <span class="icon ticon-{{ str_singular($profileFilter) }}"></span>
                        {{ Form::checkbox('profile[]', $profileFilter, (isset($searchParameters['targetsProfiles'][$profileFilter])), ['class' => '']) }}
                    </label>
                        @endif
                    @endforeach
                </div>

                <div class="input-group col-xs-12 col-sm-5">
                    {{ Form::input('text', 'query', request()->get('query'), ['class' => 'form-control input-sm', 'placeholder' => trans('search.label')]) }}
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-xs ">{{ trans('form.filter') }}</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
{{ Form::input('hidden', 'currentPage', $searchParameters['currentPage'], ['id' => 'currentPage'] ) }}
{{ Form::close() }}