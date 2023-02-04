@section('stylesheets')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/bootstrap-slider.min.css') }}">
@stop

@section('customCssContent')
    contentToTop
@stop

<div class="col-xs-12 col-md-12 ">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="filter-ui-mosaic visible-lg">
                @include('home.advanced-form-mosaic')
            </div>

            <div class="filter-ui-mosaic-xs col-xs-12 hidden-lg">
                @include('home.advanced-form-xs')
            </div>
        </div>
    </div>
</div>

<ul class="block-mosaic mosaic-container" id="fn-result-view">
    @if(count($profiles) == 0)
        <li class="col-md-8 offset-md-2 col-xs-12 mg-top">
            <div class="panel panel-default">
                <div class="panel-body">
                    {{ trans('search.noProfilesMatch') }}
                </div>
            </div>
        </li>
    @endif

    @include('home.mosaic-less')
    <li class="mosaic-last-item col-xs-12 col-md-12"></li>
</ul>

@section('javascripts')
@parent
{{ HTML::script('/assets/js/plugins/jquery.touchSwipe.min.js') }}
{{ HTML::script('/assets/js/plugins/bootstrap-slider.min.js') }}

<script>

(function($) {
    var profileLoaded = [];
    var nextProfileIdentity = "";
    var mosaicLastItemCode = '<li class="mosaic-last-item col-xs-12 col-md-12"></li>';

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

    // include function for infinitescroll
    var stopScroll = 0;
    var formSearch = $('#searchProfileFilter');
    var disableGeoSearch = {{ (isset($disableGeoSearch) && $disableGeoSearch) ? 'true' : 'false' }};

    window.onscroll = function(ev) {

        ev.stopPropagation();
        bottomPosition = $('.mosaic-last-item').position();

        if((window.innerHeight + window.pageYOffset + 1) >= bottomPosition.top && stopScroll == 0) {
            stopScroll = 1;

            var arrayParameters = [];

            var formData = formSearch.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

            //merge in formData profilesloaded
            profileLoadedJson = multiDimensionArray2JSON(profileLoaded);
            for(profileType in profileLoaded){
                formData.push({name: profileType+'Loaded', value: profileLoaded[profileType]});
            }

            if(disableGeoSearch){
                formData.push({name: 'geosearch', value: false});
            }

            $.post('{{ url()->to('/') }}' + laroute.route('{{ $searchParameters['route'] }}'), formData)
                .success(function (data) {
                    if(data.view.length > 0) {
                        stopScroll = 0;
                        $('.mosaic-last-item').remove();
                        $('#fn-result-view').append(data.view);
                        $('#fn-result-view').append(mosaicLastItemCode);
                        $('#currentPage').val(data.currentPage);
                        getProfilesId();
                        $("img.lazy").lazyload();
                    }
                });
        }
    };

})(jQuery);
</script>
@stop
