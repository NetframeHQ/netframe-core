@extends('layouts.master')

@section('stylesheets')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/bootstrap-slider.min.css') }}">
@stop


@section('content')
    <div class="col-xs-12 col-md-6 offset-md-3 column">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <h1>{{ trans('offer.searchOffer') }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">

            @include('offers.search-form')
        </div>
    </div>

    <div id="offers-results" class="row">
        @include('offers.offers-results')
    </div>
</div>
@stop

@section('javascripts')
@parent

<!-- Start Select media modal -->
<script src="{{ asset('packages/netframe/media/vendor/jquery-bootpag/jquery.bootpag.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/handlebars/handlebars.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/js/select-modal.js') }}"></script>
<!-- End Select media modal -->

{{ HTML::script('/assets/js/plugins/bootstrap-slider.min.js') }}

<script type="text/javascript">
(function($) {
    var baseUrl = '{{ url()->to('/') }}';

    distanceElement = $('#offersForm').find('input[name="distance"]');
    var mySlider = distanceElement.bootstrapSlider({
        tooltip: 'hide',
        ticks: [1, 5, 100, 200, 500, 35000],
        ticks_positions: [0, 20, 40, 60, 80, 100],
        ticks_labels: ['1 km', '5 km', '100', '200', '500', '{{ trans('search.allDistance') }}'],
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


        //limit distance to place searched
        var limitNE;
        var limitSW;
        $.ajax({
            url: 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latitude+','+longitude+'&key={{$apiKeyGoogle}}',
            headers: '',
            success: function(dataloc){
                resultsloc = dataloc.results;
                resultsloc.forEach(function(places) {
                    if(places.types[0] == 'locality'){
                        //get bounds
                        limitNE = places.geometry.viewport.northeast;
                        limitSW = places.geometry.viewport.southwest;
                    }
                });
            }
        }).then(function() { //success
            lat1 = limitNE.lat;
            lon1 = limitNE.lng;
            lat2 = limitSW.lat;
            lon2 = limitSW.lng;

            var R = 6371; // Radius of the earth in km
            var dLat = (lat2-lat1).toRad();  // Javascript functions in radians
            var dLon = (lon2-lon1).toRad();
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var d = R * c; // Distance in km
            distanceSearch = Math.round(d);
            if(distanceSearch == 0){
                distanceSearch = 5;
            }

            //affect distance to distance slider
            distanceElement.bootstrapSlider('setValue', distanceSearch);

        }, function(raison) {
            // Erreur
        });
    });

    $('#offersForm').on('change', 'input[name="offer_choice"]', function(e){
        if($(this).val() == 'demand'){
            //load skills with mp_search_name
            var params = {
                    refName: 'mp_proposal_name',
                    offerType: $(this).val(),
                    multi: 1
                    };
        }
        else{
            //load skills with mp_proposal_name
            var params = {
                    refName: 'mp_search_name',
                    offerType: $(this).val(),
                    multi: 1
                    };
        }

        _form = $(this).closest('form');

        $.post('{{ url()->to('/') }}' + laroute.route('skills_offers', params))
        .success(function (data) {
            _form.find('#offer-skills').html(data.viewSkills);
            _form.find('#offer-type').html(data.viewType);
        });

    });

    $(document).on('submit', '#offersForm', function(event) {
        event.preventDefault();
        var actionUrl = '{{ url()->to('/') }}' + laroute.route('search_offers');
        var _form = $(this);
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();
        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                $('#offers-results').html(data.view);
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    });

    // include function for infinitescroll
    var stopScroll = 0;
    var formSearch = $('#offersForm');

    window.onscroll = function(ev){
        ev.stopPropagation();
        if((window.innerHeight + window.pageYOffset + 1) >= document.body.offsetHeight) {
            var lastPostDate = $('#offers-results article').last().data('time');
            var formData = formSearch.find('input, hidden, select, textarea, radio, checkbox').serializeArray();
            formData.push({name: 'last_time', value: lastPostDate});
            $.post('{{ url()->to('/') }}' + laroute.route('search_offers'), formData)
                .success(function (data) {
                    $("#offers-results").append(data.view);
                    new PlayMediaModal({
                        $modal: $modal,
                        $modalTitle: $modal.find('.modal-title'),
                        $modalContent: $modal.find('.modal-body'),
                        $media: $('.viewMedia'),
                        baseUrl: baseUrl
                        });
                });
            }
    };

  //for modal media view
    var $modal = $('#viewMediaModal');

    new PlayMediaModal({
       $modal: $modal,
       $modalTitle: $modal.find('.modal-title'),
       $modalContent: $modal.find('.modal-body'),
       $media: $('.viewMedia'),
       baseUrl: baseUrl
    });
})(jQuery);
</script>
@stop