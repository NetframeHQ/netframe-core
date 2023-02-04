@section('stylesheets')
<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.css' rel='stylesheet' />
<link href='https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css' rel='stylesheet' />
<link href='https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css' rel='stylesheet' />
@stop

@section('content')
    <div id='filters' class='filter-ui'></div>
    <div id="map"></div>
    <div id="history">
        <input type="hidden" id="lat-history" />
        <input type="hidden" id="lng-history" />
        <input type="hidden" id="dist-history" />
    </div>
@stop


@section('javascripts')
@parent
<script>
$(function() {

    L.mapbox.accessToken = "{{ config('location.token_key') }}";

    var latit = {{ session("lat") }};
    var longit = {{ session("lng") }};

    $("#lat-history").val(latit);
    $("#lng-history").val(longit);

    myLayer = L.mapbox.featureLayer();
    var map = L.mapbox.map('map')
               .setView([latit, longit], {{ config('mapbox.zoom-map') }})
               .addLayer(L.mapbox.tileLayer('examples.map-i86nkdio'))
               .addControl(L.mapbox.geocoderControl('mapbox.places'));

    var markerYou = L.marker([latit, longit], {
        icon: L.mapbox.marker.icon({
            'marker-size': 'large',
            'marker-color': '#f86767',
            'marker-symbol': 'marker-stroked'
        })
    }).addTo(map);

    map.on('moveend', function() {
        newCenterLat = map.getCenter().lat;
        newCenterLng = map.getCenter().lng;
        /*
        borders=map.getBounds();
        SW = borders['_southWest'];
        NE = borders['_northEast'];
        newDistance = Math.round(SW.distanceTo(NE)/1000);
        */
        newDistance = getTotalDistanceMap();
        newDistanceDb = newDistance/2;

        //get latlng history and calcul move ratio
        newCenter = map.getCenter();
        oldCenter = new L.LatLng(parseFloat($("#lat-history").val()), parseFloat($("#lng-history").val()));
        moveDist = Math.round(newCenter.distanceTo(oldCenter) / 1000);
        moveRatio = moveDist/newDistance;

        //get dis history and get zoom ratio
        zoomRatio = Math.abs(parseFloat($("#dist-history").val()) / newDistance);

        if(moveRatio > 0.28 || zoomRatio < 0.5 || zoomRatio > 10) {
            $("#lat-history").val(newCenterLat);
            $("#lng-history").val(newCenterLng);
            $("#dist-history").val(newDistance);
            loadNewFile(newCenterLat,newCenterLng, newDistanceDb);
        }
    });

    function loadMapFunctions(jsonUrl){
        $("#filters").html('');
        myLayer = L.mapbox.featureLayer()
            .on('ready', function(e) {
            // create a new MarkerClusterGroup that will show special-colored
            // numbers to indicate the type of rail stations it contains
            /*
            function makeGroup(color,profil) {
              return new L.MarkerClusterGroup({
                iconCreateFunction: function(cluster) {
                  return new L.DivIcon({
                    iconSize: [20, 20],
                    html: '<div class="MG_'+profil+'" style="text-align:center;color:#fff;background:' +
                    color + '">' + cluster.getChildCount() + '</div>'
                  });
                }
              }).addTo(map);
            }
            */
            // create a marker cluster group for each type of rail station
            /*
            var groups = {
              houses: makeGroup('green','houses'),
              community: makeGroup('yellow','community'),
              events: makeGroup('orange','events'),
              projects: makeGroup('pink','projects')
            };
            e.target.eachLayer(function(layer) {
              // add each point to its specific group.
              groups[layer.feature.properties.profil].addLayer(layer);
            });
            */
        });

        myLayer.loadURL(jsonUrl).addTo(map);

        var filters = document.getElementById('filters');
        myLayer.on('ready', function() {
          // Collect the types of symbols in this layer. you can also just
          // hardcode an array of types if you know what you want to filter on,
          // like var types = ['foo', 'bar'];
          var typesObj = {}, types = [];
          var features = myLayer.getGeoJSON().features;

          for (var i = 0; i < features.length; i++) {
              typesObj[features[i].properties['profil']] = true;
          }

          for (var k in typesObj) types.push(k);

          var checkboxes = [];
          // Create a filter interface.
          for (var i = 0; i < types.length; i++) {
            // Create an an input checkbox and label inside.
            createMarker = true;
            if(createMarker==true){
                var item = filters.appendChild(document.createElement('div'));
                var checkbox = item.appendChild(document.createElement('input'));
                var label = item.appendChild(document.createElement('label'));
                checkbox.type = 'checkbox';
                checkbox.id = types[i];
                checkbox.checked = true;
                // create a label to the right of the checkbox with explanatory text
                label.innerHTML = types[i];
                label.setAttribute('for', types[i]);
                // Whenever a person clicks on this checkbox, call the update().
                checkbox.addEventListener('change', update);
                checkboxes.push(checkbox);
            }
          }

          // This function is called whenever someone clicks on a checkbox and changes
          // the selection of markers to be displayed.
          function update() {
            var enabled = {};
            // Run through each checkbox and record whether it is checked. If it is,
            // add it to the object of types to display, otherwise do not.
            for (var i = 0; i < checkboxes.length; i++) {
                if(checkboxes[i].checked) enabled[checkboxes[i].id] = true;
            }
            myLayer.setFilter(function(feature) {
              // If this symbol is in the list, return true. if not, return false.
              // The 'in' operator in javascript does exactly that: given a string
              // or number, it says if that is in a object.
              // 2 in { 2: true } // true
              // 2 in { } // false
              return (feature.properties['profil'] in enabled);
            });
          }
        });

        myLayer.on('layeradd', function(e) {
            var container = $('<div class="map-popup" />');
            var marker = e.layer,
                feature = marker.feature;

            container.on('click', '.bookmark-map', function() {
                profileType = $(this).attr('data-profile');
                profileId = $(this).attr('data-profile-id');
                var button = $(this);
                $.post('{{ url()->to('/') }}' + laroute.route('playlist_user_profile_bookmark', {profileType: profileType, profileId: profileId }))
                    .success(function () {
                        button.prop("disabled", true);
                    });
            });

            // Create custom popup content
            var popupContent = '<h3><a href="'+ feature.properties.url +'">'+ feature.properties.name +'</a>'+
            ' - <strong>'+ feature.properties.profil +'</strong>'+
            '</h3>';
            if(feature.properties.image != '')
            {
                popupContent = popupContent+'<img class="thumb-map float-left" src="' + feature.properties.image + '" />';
            }

            popupContent = popupContent+'<p>'+ feature.properties.description +'</p>';

            disabled = "";
            if(feature.properties.isBookmarked == 1){
                disabled = "disabled";
                }
            popupContent = popupContent+'<button ' + disabled + ' class="bookmark-map btn btn-default" data-profile="'+ feature.properties.bookmarkProfile +'" data-profile-id="' +feature.properties.id+ '">'+
                '<span class="glyphicon glyphicon-list"></span>'+
                '{{ trans("netframe.bookmark") }}'+
                '</button>';

            container.html(popupContent);

            // http://leafletjs.com/reference.html#popup
            marker.bindPopup(container[0],{
                closeButton: true,
                minWidth: 320
            });
        });
    }

    loadMapFunctions('{{ url()->to("netframe/big-map-json") }}');
    $("#dist-history").val(getTotalDistanceMap());

    function loadNewFile(centerLat,centerLng,distance){
        map.removeLayer(myLayer);
        myLayer.clearLayers();
        loadMapFunctions('{{ url()->to("netframe/big-map-json") }}?centerLat='+centerLat+'&centerLng='+centerLng+'&distance='+distance);
        }

    function distance(lon1, lat1, lon2, lat2) {
        var R = 6371; // Radius of the earth in km
        var dLat = (lat2-lat1).toRad();  // Javascript functions in radians
        var dLon = (lon2-lon1).toRad();
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c; // Distance in km
        return d;
      }

    function getTotalDistanceMap(){
        borders=map.getBounds();
        SW = borders['_southWest'];
        NE = borders['_northEast'];
        return Math.round(SW.distanceTo(NE)/1000);
    }

    /** Converts numeric degrees to radians */
    if (typeof(Number.prototype.toRad) === "undefined") {
      Number.prototype.toRad = function() {
        return this * Math.PI / 180;
      }
    }

    function dump(obj) {
        var out = '';
        for (var i in obj) {
            out += i + ": " + obj[i] + "\n";
        }

        alert(out);
    }

});
</script>
@stop