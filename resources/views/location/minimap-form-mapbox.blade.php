<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.css' rel='stylesheet' />

<div class="form-group">
{{ Form::label('mappicker', trans('form.mappicker')) }}
<i>{{ Form::label('mappicker', trans('form.selectAddress')) }}</i>

<?php
    // Store result value input latitude & longitude
    $valueLatitude = (count($errors) > 0) ? \App\Helpers\InputHelper::get('latitude') : (($profile->latitude) ? $profile->latitude : $NetframeGeoip->lat);
    $valueLongitude = (count($errors) > 0) ? \App\Helpers\InputHelper::get('longitude') : (($profile->longitude) ? $profile->longitude : $NetframeGeoip->lon);
?>

{{ Form::hidden('latitude', $valueLatitude, ['class'=>'input-latitude']) }}
{{ Form::hidden('longitude', $valueLongitude, ['class'=>'input-longitude']) }}
</div>

<div id="mini-map-form"></div>

@section('javascripts')
    @parent

<script>
(function($) {

    $(document).ready(function() {

        var latit = {{ $valueLatitude }};
        var longit = {{ $valueLongitude }};

        L.mapbox.accessToken = '{{ config("location.token_key") }}';
        var map = L.mapbox.map('mini-map-form', 'examples.map-i86nkdio').setView([latit, longit], 15).addControl(L.mapbox.geocoderControl('mapbox.places'));
        var marker = L.marker([latit, longit], {
            icon: L.mapbox.marker.icon({
                'marker-size': 'large',
                'marker-color': '#f86767'
            }),
            draggable: true
        }).addTo(map);

        ondragend = function () {
            var m = marker.getLatLng();
            $('input[name=latitude]').val(m.lat);
            $('input[name=longitude]').val(m.lng);
        }

        // every time the marker is dragged
        marker.on('dragend', ondragend);

        // Set the initial marker coordinate on load
        ondragend();

        map.on('moveend',function(){
            newCenter = map.getCenter();
            marker.setLatLng(newCenter);
        });
    });

})(jQuery);
</script>
@stop

