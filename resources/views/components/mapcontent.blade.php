@section('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/bootstrap-slider.min.css') }}">
@stop

@section('customCssContent')
    contentToTop
@stop

<div class="map-overlay-loader">

</div>

@if(isset($minimap))
    <a href="{{ url()->route('profile.map.location') }}" class="map-overlay"></a>
@endif

<section @if(!isset($minimap)) class="map" @else  class="mini-map" @endif>
    <div class="full-map" id="map"></div>
</section>
<div id="history">
    <input type="hidden" id="lat-history" />
    <input type="hidden" id="lng-history" />
    <input type="hidden" id="dist-history" />
</div>

@section('javascripts')
    @parent
{{ HTML::script('assets/js/plugins/jquery.touchSwipe.min.js') }}
{{ HTML::script('/assets/js/plugins/bootstrap-slider.min.js') }}

<script>
(function($) {
    var baseUrl = "{{ url()->to('/') }}";


    if($('#mapProfileFilterXs').is(':visible')){
        distanceElement = $('#mapProfileFilterXs').find('input[name="distance"]');
    }
    else{
        distanceElement = $('#mapProfileFilterMap').find('input[name="distance"]');
    }

    @if(!isset($minimap))

        var mySlider = distanceElement.bootstrapSlider({
            tooltip: 'hide',
            ticks: [1, 5, 100, 200, 500, 2000],
            ticks_positions: [0, 20, 40, 60, 80, 100],
            ticks_labels: ['1 km', '5 km', '100 km', '200 km', '500 km', '2000 km'],
            ticks_snap_bounds: 30
        });

        //---------------------toggle filter menu------------------------//
        var showFiltersCaption = '{{ trans('map.showFilter') }}';
        var hideFiltersCaption = '{{ trans('map.hideFilter') }}';

        var elNavbar = $('#navigation .tl-main-nav');
        var elContentMarginTop = parseInt($('#content').css('marginTop'));

        //--------------------now button function--------------------------
        $('.fn-map-now').on('click', function(){
            $('.map-overlay-loader').show();
            newCenterLat = map.getCenter().lat();
            newCenterLng = map.getCenter().lng();
            newDistance = getTotalDistanceMap();
            newDistanceDb = newDistance/2;

            //empty map
            var filteredResult = map.markers.filter(function(obj) {
                obj.setMap(null);
            });

            setMapOnAll(null);
            map.markers = [];

            loadNewFile(newCenterLat,newCenterLng,newDistanceDb,'now');
        });

        /*
        $('form').on( 'submit', function(e) {
            $('.map-overlay-loader').show();
            var formDatas = $( this ).serializeArray();
            var formDatasJson = JSON.stringify(formDatas, null, 2);
            e.preventDefault();

            var postFilters = [];
            $(this).find('input[name="filter[]"]:checked').each(function(){
                postFilters.push($(this).val())
            });

            var postBuzz = [];
            $(this).find('input[name="buzz"]:checked').each(function(){
                postBuzz.push($(this).val())
            });

            newProfile = 0;
		        if($(this).find('input[name=newProfile]').is(':checked')){
		        	newProfile = 1;
		        }

            subjects = $(this).find('input[name=subject]:checked').val();
            categories = $(this).find('input[name=category]').val();

            search = '1&filters='+postFilters+'&subjects='+subjects+'&categories='+categories+'&buzz='+postBuzz+'&newProfile='+newProfile;
            newCenterLat = map.getCenter().lat();
            newCenterLng = map.getCenter().lng();
            newDistance = getTotalDistanceMap();
            newDistanceDb = newDistance/2;

            newDistanceDb = distanceElement.val();

            var filteredResult = map.markers.filter(function(obj) {
                obj.setMap(null);
            });

            setMapOnAll(null);
            map.markers = [];

            if($(this).find('button[type="submit"]').is(':visible')){
                $(this).find('.collapse').collapse('hide');
            }

            loadNewFile(newCenterLat,newCenterLng,newDistanceDb,search);

            circle.setRadius(newDistanceDb * 800);
            circle.setCenter(map.getCenter());
            map.fitBounds(circle.getBounds());
            map.circleRadius = newDistanceDb;
        });
        */

        {{--
        $(document).on('click', '.fn-search-carto', function(){
            var _form = $(this).closest('form');
            _form.submit();
        });
        --}}

        $(document).on('change', '.fn-profile-filter', function(){
            var _form = $(this).closest('form');
            if(_form.find('button[type="submit"]').is(':visible')){
            }
            else{
                // @ TODO discard distance selector
                _form.find('button[type=submit]').trigger('click');
            }
        });

        $('[data-toggle="collapse"]').on('click', function(e) {
            collapseElement = $($(this).data('target'));
        });


    @endif


    var latit = {{ session("lat") }};
    var longit = {{ session("lng") }};
    var positionAccuracy = false;


    var map;
    window.netframeMap = map;
    var userMarker;
    var markers = [];
    var nearests = [];
    var prev_infowindow =false;

    var circle;

    var newCenterLat = 0;
    var newCenterLng = 0;
    var newDistanceDb = 0;

    var inLoad = 0;
    var distTester = 0;
    var centerLatTester = 0;
    var centerLngTester = 0;

    function initialize() {
        // Create map.
        @if(isset($minimap))
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: {{ $zoomMapBox }},
                disableDefaultUI: true
              });
        @else
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: {{ $zoomMapBox }},
              });
        @endif

        map.markers = [];

        //get browser geoloc or geolocalise by IP
        /*
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(successLocation, errorLocation,{maximumAge:600,enableHighAccuracy:true, timeout:27000});
        }
        */
        initMapPosition(true);

        google.maps.event.addListenerOnce(map, 'idle', function(){
            $("#dist-history").val(getTotalDistanceMap());
        });


        google.maps.event.addListener(map, 'idle', reloadMarkers);

        @if(!isset($minimap))
            //implement search box
            if($('.filter-ui-map').is(":visible")){
                var input = (document.getElementById('pac-input'));
            }
            else{
                var input = (document.getElementById('pac-input-xs'));
            }
            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var searchBox = new google.maps.places.SearchBox((input));

            // Listen for the event fired when the user selects an item from the pick list. Retrieve the matching places for that item.
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

                zoom = map.getZoom();
                map.setZoom(12);
                newCenter = bounds.getCenter();
                userMarker.setPosition(newCenter);
                map.setCenter(newCenter);
            });

            // Bias the SearchBox results towards places that are within the bounds of the current map's viewport.
            google.maps.event.addListener(map, 'bounds_changed', function() {
                var bounds = map.getBounds();
                searchBox.setBounds(bounds);
            });
        @endif
    }

    //load map initialization
    google.maps.event.addDomListener(window, 'load', initialize);

    function loadMapFunctions(data){

        //loading marker by parsing geoJson mannualy
        $.each( data, function( key, val ) {
            if(key == 'features'){
                $.each(val, function(k, point){
                    if (point.geometry.type === 'Point') {
                         displayMarker = 1;
                         $.each(map.markers, function(l, existingMarker){
                             if(point.properties.uniqueId == existingMarker.uniqueId){
                                 displayMarker = 0;
                             }
                         });
                         if(displayMarker == 1){
                             if(point.properties.buzz > 0){
                                 iconMarker = point.properties.markerName+'-buzz';
                             }
                             else{
                                 iconMarker = point.properties.markerName;
                             }
                             var marker = new google.maps.Marker({
                                position: new google.maps.LatLng(point.geometry.coordinates[1],point.geometry.coordinates[0]),
                                title: point.properties.name,
                                //icon: pinSymbol(point.properties.markerColor),
                                //icon : "/assets/img/icons/map-"+iconMarker+".png",
                                icon : "/netframe/svg-icon/"+iconMarker,
                                map: map,
                                uniqueId : point.properties.uniqueId,
                                category: point.properties.typeProfil,
                                profileType: point.properties.profileType,
                                profileId: point.properties.profileId

                            });

                            //-----------------------------------creating popup infos-------------------------------------------//
                            var container = $('<div class="map-popup" />');

                        // Create custom popup content
                        var popupContent = point.properties.completeView;

                        google.maps.event.addListener(marker, 'click', function (target, elem) {
                            loadInfoWindowsContent(marker);
                        });

                        marker.info = new google.maps.InfoWindow({
                            content: '',
                            maxWidth: 350
                        });


                            //add event listener for clip click and skip
                            google.maps.event.addListener(marker.info, 'domready', function() {
                                $('.skip-map').on('click', function() {
                                    find_closest_marker(marker);
                                    });
                            });


                            google.maps.event.addListener(marker.info, 'domready', function() {
                                // Reference to the DIV which receives the contents of the infowindow using jQuery
                                var iwOuter = $('.gm-style-iw');

                                //iwOuter.parent().css({'margin-top': '400px'});
                            });


                            // open the infoBox when the marker is clicked
                            google.maps.event.addListener(marker, 'click', function (marker, e) {
                                return function () {
                                    if( prev_infowindow ) {
                                        prev_infowindow.close();
                                    }
                                    prev_infowindow = marker.info;
                                    marker.info.open(map, marker);
                                };

                            }(marker));

                            //markerClusterer.addMarker(marker);
                            marker.setMap(map);
                            map.markers.push(marker);
                         }
                    }
                });
            }
        });

        //disable unselected profiles
        $('#mapProfileFilter').find('input:checkbox').each(function(){
            if(!$(this).prop('checked')){
                profileFilter = $(this).val();
                var filteredResult = map.markers.filter(function(obj) {
                    if(obj.category === profileFilter){
                        obj.setMap(null);
                    }
                });
            }
        });

        //var markerClusterer = new MarkerClusterer(map, markers);
        $('.map-overlay-loader').hide();
    }

    function successLocation(position) {
        latit = position.coords.latitude;
        longit = position.coords.longitude;
        positionAccuracy = true;

        initMapPosition(positionAccuracy);
    }

    function errorLocation(err) {
        @if(!isset($minimap))
            //alert('{{ addslashes(trans('map.mustActivateGeoloc')) }}');
        @endif
        //console.warn('ERROR(' + err.code + '): ' + err.message);
        initMapPosition(false);
    }

    function initMapPosition(accuracy){
        $("#lat-history").val(latit);
        $("#lng-history").val(longit);

        newCenter = new google.maps.LatLng(latit, longit);
        map.setCenter(newCenter);
        circle = new google.maps.Circle({radius: 10, center: newCenter})

        if(accuracy){
            //var markerYou = new google.maps.LatLng(latit, longit);
            userMarker = addThisMarker(newCenter);
            userMarker.setMap(map);
        }
    }

    function loadNewFile(centerLat,centerLng,distance,search){
        if($('.fn-map-now').hasClass('btn-success')){
            search = 'now';
        }
        if(inLoad == 0){
            inLoad = 1;
            getMapInfos('{{ url()->to("netframe/big-map-json") }}?centerLat='+centerLat+'&centerLng='+centerLng+'&distance='+distance+'&query='+search).then(function(data){
                loadMapFunctions(data);
                inLoad = 0;
                if(distTester != newDistance || centerLatTester != newCenterLat || centerLngTester != newCenterLng){
                    reloadMarkers();
                }
            });
        }
    }

    function getMapInfos(jsonUrl){
        return $.getJSON( jsonUrl, function( data ) { return {data: data} });
    }

    /* Fonction qui affiche un marker sur la carte reprenant la position de l'utilisateur*/
    function addThisMarker(point){
        var marker = new google.maps.Marker({
            position: point,
            map: map,
            icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
            });
        return marker;
    }

    function setMapOnAll(map){
        for (var i = 0; i < markers.length; i++){
            markers[i].setMap(map);
        }
    }

    function reloadMarkers(){
        //$('.map-overlay-loader').show();
        newCenterLat = map.getCenter().lat();
        newCenterLng = map.getCenter().lng();
        newDistance = getTotalDistanceMap();
        newDistanceDb = newDistance;


/*
        @if(!isset($minimap))
            //compute distance selector
            mySlider.bootstrapSlider('setValue', newDistanceDb);
        @endif
*/

        //get latlng history and calcul move ratio
        newCenter = map.getCenter();
        oldCenter = new google.maps.LatLng(parseFloat($("#lat-history").val()),parseFloat($("#lng-history").val()));
        moveDist = distance(newCenter,oldCenter);
        moveRatio = moveDist/newDistance;

        if(inLoad == 0){
            distTester = newDistanceDb;
            centerLatTester = newCenterLat;
            centerLngTester = newCenterLng;
        }

        var formId;

        if($('.filter-ui-map').is(":visible")){
            formId = '#mapProfileFilterMap';
        }
        else{
            formId = '#mapProfileFilterXs';
        }

        var formDatas = $(formId).serializeArray();
        var formDatasJson = JSON.stringify(formDatas, null, 2);

        var postFilters = [];
        $(formId).find('input[name="filter[]"]:checked').each(function(){
            postFilters.push($(this).val())
        });

        var postBuzz = [];
        $(formId).find('input[name="buzz"]:checked').each(function(){
            postBuzz.push($(this).val())
        });

        newProfile = 0;
        if($(formId).find('input[name=newProfile]').is(':checked')){
        	newProfile = 1;
        }

        subjects = $(formId).find('input[name=subject]:checked').val();
        categories = $(formId).find('input[name=category]').val();

        search = '1&filters='+postFilters+'&subjects='+subjects+'&categories='+categories+'&buzz='+postBuzz+'&newProfile='+newProfile;


        //get dis history and get zoom ratio
        zoomRatio = Math.abs(parseFloat($("#dist-history").val())/newDistance);

        //if(moveRatio > 0.28 || zoomRatio < 0.5 || zoomRatio > 10){
            $("#lat-history").val(newCenterLat);
            $("#lng-history").val(newCenterLng);
            $("#dist-history").val(newDistance);
            loadNewFile(newCenterLat,newCenterLng,newDistanceDb,search);
        //}
    }

    //function to close all infowindow
    function loadInfoWindows(markerLoad){
        if( prev_infowindow ) {
            prev_infowindow.close();
        }
        prev_infowindow = markerLoad.info;
        loadInfoWindowsContent(markerLoad);
        markerLoad.info.open(map, markerLoad);
    }

    function loadInfoWindowsContent(marker){
    	$.post('{{ url()->to('/') }}' + laroute.route('profile.map.card', {profileId: marker.profileId, profileType: marker.profileType }))
      	.success(function (data) {
        	//infowindow.setContent(data)
            marker.info.setContent(data);
        });
    }

    //search closest marker from an existing  marker
    function rad(x) {return x*Math.PI/180;}

    function find_closest_marker( marker ) {
        var lat = marker.position.lat();
        var lng = marker.position.lng();
        var R = 6371; // radius of earth in km
        var distances = [];
        var closest = -1;
        for(i=0; i<map.markers.length; i++) {
            if(!isInArray(i, nearests) && map.markers[i] != marker){
                var mlat = map.markers[i].position.lat();
                var mlng = map.markers[i].position.lng();
                var dLat  = rad(mlat - lat);
                var dLong = rad(mlng - lng);
                var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(rad(lat)) * Math.cos(rad(lat)) * Math.sin(dLong/2) * Math.sin(dLong/2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                var d = R * c;
                distances[i] = d;
                if(closest == -1 || d < distances[closest]) {
                    closest = i;
                }
            }
        }
        if(closest != -1){
            loadInfoWindows(map.markers[closest]);
            nearests.push(closest);
        }
    }

    //get distance in km betwen 2 points
    function distance(point1, point2) {
        lat1 = point1.lat();
        lon1 = point1.lng();
        lat2 = point2.lat();
        lon2 = point2.lng();

        var R = 6371; // Radius of the earth in km
        var dLat = (lat2-lat1).toRad();  // Javascript functions in radians
        var dLon = (lon2-lon1).toRad();
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c; // Distance in km
        return Math.round(d);
    }

    //get total distance display on map from corners
    function getTotalDistanceMap(){
        var borders=map.getBounds();
        SW = borders.getSouthWest();
        NE = borders.getNorthEast();
        diag = distance(SW, NE);
        return diag;
    }

    function pinSymbol(color) {
        return {
            path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
            fillColor: color,
            fillOpacity: 1,
            strokeColor: '#000',
            strokeWeight: 2,
            scale: 1,
       };
    }

    function isInArray(value, array) {
        return array.indexOf(value) > -1;
      }

    function dump(obj) {
        var out = '';
        for (var i in obj) {
            out += i + ": " + obj[i] + "\n";
        }

        alert(out);

        // or, if you wanted to avoid alerts...

        var pre = document.createElement('pre');
        pre.innerHTML = out;
        document.body.appendChild(pre)
    }
})(jQuery);
</script>
@stop
