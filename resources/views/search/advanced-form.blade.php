<div class="search-topbar">
    <ul class="list-unstyled search-tabs d-flex">
        <li class="@if($searchParameters['searchType'] == 'profile') active @endif">
            <a href="{{url()->route('search_results').'?query='.urlencode(request()->get('query')).'&loadFilters=0'}}">
                <span class="svgicon">
                    @include('macros.svg-icons.user')
                </span>
                {{trans('netframe.profile')}}
            </a>
        </li>
        <li class="@if($searchParameters['searchType'] == 'media') active @endif">
            <a href="{{url()->route('search_results').'?query='.urlencode(request()->get('query')).'&loadFilters=0&type=media'}}">
                <span class="svgicon">
                    @include('macros.svg-icons.doc')
                </span>
                {{trans('netframe.media')}}
            </a>
        </li>
        <li class="@if($searchParameters['searchType'] == 'post') active @endif">
            <a href="{{url()->route('search_results').'?query='.urlencode(request()->get('query')).'&loadFilters=0&type=post'}}">
                <span class="svgicon">
                    @include('macros.svg-icons.channel')
                </span>
                {{trans('netframe.post')}}
            </a>
        </li>
    </ul>
    <a class="search-filters-toggle" data-toggle="collapse" href="#search-filters-collapse" aria-expanded="true">
        <span class="svgicon icon-filters">
            @include('macros.svg-icons.filters')
        </span>
        <span class="text">{{ trans('search.filter') }}</span>
    </a>
</div>
<div class="search-filters collapse" id="search-filters-collapse" style="">
    {{ Form::open(array('method' => 'GET', 'route' => $searchParameters['route'], 'id' => 'searchProfileFilter')) }}
        {{ Form::hidden('type', (request()->get('type') != '') ? request()->get('type') : 'profile' ) }}
        {{ Form::hidden('query', request()->get('query')) }}

        {{-- Form::input('search', 'query', request()->get('query'), array('class' => 'input-search', 'placeholder' => trans('netframe.search'))) --}}

        <hr class="d-none d-md-block">


    @if($searchParameters['searchType'] == 'profile')
    <div class="row">
        <div class="col-md-6">
            <div class="place-search search-filters-section">
                <h4 class="search-filters-title">
                    {{ Form::label('placeSearch', trans('map.searchPlace')) }}
                </h4>
                <div class="form-group search-filters-geo">
                    {{ Form::input('text', 'placeSearch', $searchParameters['placeSearch'], array('id' => 'pac-input', 'class' => 'form-control', 'placeholder' => trans('map.searchPlaceHolder'))) }}
                    {{ Form::input('hidden', 'latitude', $searchParameters['latitude'], ['id' => 'latitude'] ) }}
                    {{ Form::input('hidden', 'longitude', $searchParameters['longitude'], ['id' => 'longitude'] ) }}
                    <span class="svgicon icon-loc">
                        @include('macros.svg-icons.localisation')
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group search-filters-section">
                <h4 class="search-filters-title">
                    {{ Form::label('distanceSlider', trans('search.distance')) }}
                </h4>
                <input name="distance" class="distance-selector" data-slider-id='distanceSlider' type="text" style="width: 80%; margin-bottom: 24px;" data-slider-value="{{ $searchParameters['distance'] }}" />
            </div>
        </div>
    </div>
    @endif
        <div class="search-filters-section">
            <h4 class="search-filters-title">{{ trans('search.typoFilter') }}</h4>

            <div class="form-group search-filters-types-toggle">
                @foreach($listProfilesFilter as $profileFilter=>$active)
                    @if($active == 1)
                        <label class="@if(isset($searchParameters['targetsProfiles'][$profileFilter]) && $searchParameters['targetsProfiles'][$profileFilter] == 1) active @endif @if($profileFilter == 'media') hidden @endif">
                            @if(in_array(ucfirst($profileFilter), config('search.profilesModels')))
                                <span class="svgicon">
                                    @include('macros.svg-icons.'.str_singular($profileFilter).'_big')
                                </span>
                            @endif
                            {{ trans('netframe.'.$profileFilter) }}
                            {{ Form::checkbox('profile[]', $profileFilter, (isset($searchParameters['targetsProfiles'][$profileFilter]) && $searchParameters['targetsProfiles'][$profileFilter] == 1)) }}
                        </label>
                    @endif
                @endforeach
            </div>

            {{ Form::submit(trans('form.search'), ['class' => 'btn btn-sm float-right']) }}
        </div>
        {{ Form::input('hidden', 'currentPage', $searchParameters['currentPage'], ['id' => 'currentPage'] ) }}
    {{ Form::close() }}
</div>

@if($searchParameters['toggleFilter'] == true)
<button class="btn btn-border-default fn-toggle-filters-link float-left offMenu">
    {{ trans('map.showFilter') }}
</button>
@endif

@section('javascripts')
    @parent

{{ HTML::script('/assets/js/plugins/bootstrap-slider.min.js') }}
<script>
$(document).ready(function () {
@if($searchParameters['toggleFilter'] == true)
    //---------------------toggle filter menu------------------------//
    showFiltersCaption = '{{ trans('map.showFilter') }}';
    hideFiltersCaption = '{{ trans('map.hideFilter') }}';

    setTimeout(function(){
        originalTopPosition = $('.navbar-fixed-top').height();
        hiddenHeight = $('.filter-ui').height() - ($('.navbar-fixed-top').height() + $('.fn-toggle-filters').height() + 30);
        $('.filter-ui').css('top','-'+hiddenHeight+'px');
    }, 2000);

    $('.fn-toggle-filters-link').on('click', function(){
        if($(this).hasClass('offMenu')){
            showFilters($(this));
        }
        else{
            hideFilters($(this));
        }
    });

    $('.fn-toggle-filters-xs').on('click', function(){
        hideFilters($(this), false);
    });

    function hideFilters(element, updateElement){
        if(typeof updateElement != 'undefined'){
            element = $('.fn-toggle-filters-link');
        }
        hiddenHeightLive = $('.filter-ui').height() - ($('.navbar-fixed-top').height() + $('.fn-toggle-filters').height() + 30);
        element.addClass('offMenu');
        $('.filter-ui').css('top','-'+hiddenHeightLive+'px');
        element.html(showFiltersCaption);
    }

    function showFilters(element){
        element.removeClass('offMenu');
        $('.filter-ui').css('top',originalTopPosition+'px');
        element.html(hideFiltersCaption);
    }
@endif

});

(function($) {
    var profileLoaded = [];

    getProfilesId();

    function getProfilesId(){
        profileLoaded = [];
        $('.profile-container').each(function(i){
            profileType = $(this).data('profile-type');
            if(typeof profileLoaded[profileType] == 'undefined'){
                profileLoaded[profileType] = [];
            }
            profileLoaded[profileType].push($(this).data('profile-id'))
        });
    }

    distanceElement = $('#searchProfileFilter').find('input[name="distance"]');
    var mySlider = distanceElement.bootstrapSlider({
        tooltip: 'hide',
        ticks: [1, 5, 100, 200, 500, 35000],
        ticks_positions: [0, 20, 40, 60, 80, 100],
        ticks_labels: ['1KM', '5KM', '100', '200', '500', '{{ trans('search.allDistance') }}'],
        ticks_snap_bounds: 30
    });

    //implement google searchplaces
    var input = (document.getElementById('pac-input'));
    var searchBox = new google.maps.places.SearchBox((input));
    google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {return;}
        // For each place, get the icon, place name, and location.
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0, place; place = places[i]; i++) {
            var image = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25)
            };
            bounds.extend(place.geometry.location);
        }

        newCenter = bounds.getCenter();
        latitude = newCenter.lat();
        longitude = newCenter.lng();
        $('#latitude').val(latitude);
        $('#longitude').val(longitude);
    });

    $('#searchProfileFilter').submit(function(ev){
    	$('#currentPage').val(1);
    });

    /*// include function for infinitescroll
    var stopScroll = 0;
    var formSearch = $('#searchProfileFilter');

    window.onscroll = function(ev) {
        ev.stopPropagation();

        if((window.innerHeight + window.pageYOffset + 1) >= document.body.offsetHeight && stopScroll == 0) {
            stopScroll = 1;
            var arrayParameters = [];
            alert()

            var formData = formSearch.find('input, hidden, select, textarea, radio, checkbox').serializeArray();
            for(profileType in profileLoaded){
                formData.push({name: profileType+'Loaded', value: profileLoaded[profileType]});
            }

            $.post('{{ url()->to('/') }}' + laroute.route('{{ $searchParameters['route'] }}'), formData)
                .success(function (data) {
                    if(data.view.length > 0) {
                        stopScroll = 0;
                        $('#fn-result-view').append(data.view);
                        $('#currentPage').val(data.currentPage);
                        getProfilesId();
                    }
                });
        }
    };*/
})(jQuery);

</script>
@stop