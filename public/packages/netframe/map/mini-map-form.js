 (function () {
    'use strict';

	function MiniMapForm(options) {
        this.$wrapper = options.$wrapper;
		this.$latitude = options.$latitude || (this.$wrapper.find("input[name=latitude]").val() != '') ? parseFloat(this.$wrapper.find("input[name=latitude]").val()) : '';
		this.$longitude = options.$longitude || (this.$wrapper.find("input[name=longitude]").val() != '') ? parseFloat(this.$wrapper.find("input[name=longitude]").val()) : '';
		this.$displayMap = options.$displayMap || 0;
		this.$placeName = options.$placeName;
		this.$elementType = options.$elementType;
		this.$markerYou = '';
		this.$userMarker = '';
		this.$map = '';
    }

	MiniMapForm.prototype.initMiniMapPosition = function () {
		var that = this;

		// Create map.

		that.$map = new google.maps.Map(document.getElementById('mini-map-form'), {
			zoom: 15,
			center: {lat: that.$latitude, lng: that.$longitude},
			disableDefaultUI: false
		  });

		that.$markerYou = new google.maps.LatLng(that.$latitude, that.$longitude);

		//init marker for user position
		that.$userMarker = that.addThisMarkerMiniMap(that.$markerYou);
		that.$userMarker.setMap(that.$map);
		google.maps.event.addListener(that.$userMarker, 'dragend', function(event){
			$('input[name=latitude]').val(event.latLng.lat());
			$('input[name=longitude]').val(event.latLng.lng());
		});
	}

	MiniMapForm.prototype.initializeMiniMap = function () {
		var that = this;

		this.initMiniMapPosition();

	    //implement search box
		var inputForm = (document.getElementById('pac-input-form'));
		var searchBoxForm = new google.maps.places.SearchBox((inputForm));

		// Listen for the event fired when the user selects an item from the pick list. Retrieve the matching places for that item.
		google.maps.event.addListener(searchBoxForm, 'places_changed', function() {
			var placesForm = searchBoxForm.getPlaces();

			if (placesForm.length == 0) {return;}
			// For each place, get the icon, place name, and location.
			var bounds = new google.maps.LatLngBounds();
			for (var i = 0, place; place = placesForm[i]; i++) {
				var image = {
					url: place.icon,
					size: new google.maps.Size(71, 71),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};
				bounds.extend(place.geometry.location);
			}

			var zoom = that.$map.getZoom();
			that.$map.setZoom(zoom);
			var newCenter = bounds.getCenter();
			that.$userMarker.setPosition(newCenter);

			$("#mini-map-form").css('position', 'relative');
			google.maps.event.trigger(that.$map, 'resize');

			// update inputs hidden for new postion
			$('input[name=latitude]').val(newCenter.lat());
			$('input[name=longitude]').val(newCenter.lng());

			that.$map.setCenter(newCenter);
		});

		if(that.$latitude != '' && that.$longitude != '' && that.$displayMap){
			$("#mini-map-form").css('height', '300');
			setTimeout(function () {
				google.maps.event.trigger(that.$map, 'resize');
				var displayCenter = new google.maps.LatLng(that.$latitude, that.$longitude)
				that.$map.setCenter(displayCenter);
			}, 500);
		}
	}

	/* Fonction qui affiche un marker sur la carte reprenant la position de l'utilisateur*/
	MiniMapForm.prototype.addThisMarkerMiniMap = function (point){
		var that = this;
		var marker = new google.maps.Marker({
			position: point,
			map: that.$map,
			draggable:true,
			icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
			});
		return marker;
	}


	window.MiniMapForm = function (options) {
        var miniMap = new MiniMapForm(options);

		//load map initialization
		google.maps.event.addDomListener(window, 'load', miniMap.initializeMiniMap);
		//$(document).ajaxStop(miniMap.initializeMiniMap());

		miniMap.initializeMiniMap();

        return miniMap;
    };

})();