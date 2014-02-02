/* globals ajaxurl:{}, GoogleMapper:{}, ko:{}, google:{}, alert:{}, pager:{} */
var locationsApp = ( locationsApp || {} );
( function( $, ko ) {
	'use strict';
	var geocoder,
		map,
		Location,
		LocationsViewModel,
		Map;

	Map = function( data ) {
		ko.mapping.fromJS( data, {}, this );
	};

	Location = function( data ) {
		ko.mapping.fromJS( data, {}, this );
	};

	LocationsViewModel = function( data ) {
		var self = this;

		self.appMode = ko.observable( 'edit' );

		self.maps = ko.observableArray( ko.utils.arrayMap( data.maps, function( map ) {
			return new Map( map );
		}));

		self.locations = ko.observableArray( ko.utils.arrayMap( data.locations, function( location ) {
			return new Location( location );
		}));

	};

	function initialize() {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(-34.397, 150.644),
			mapOptions = {
				zoom: 8,
				center: latlng
			};
		map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	}

	function codeAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
			// map.setCenter(results[0].geometry.location);
			// var marker = new google.maps.Marker({
			//     map: map,
			//     position: results[0].geometry.location
			// });
				console.log( results[0].geometry );
				document.getElementById( 'latitude' ).value = results[0].geometry.location.d;
				document.getElementById( 'longitude' ).value = results[0].geometry.location.e;
			} else {
				alert('Geocode was not successful for the following reason: ' + status);
			}
		});
	}

	function get_locations() {
		jQuery.post( ajaxurl, {
			action: 'google_mapper_get_locations',
			security: GoogleMapper.get_locations_nonce,
			js_data_for_php: 'Some Javascript data to pass to PHP AJAX handler'},
			function(php_data){
				alert(php_data.result);
			},
			'json'
			);
	}

	google.maps.event.addDomListener(window, 'load', initialize);

	$(function () {
		$( '.mapper-find' ).on( 'click.google-mapper', function( e ) {
			e.preventDefault();
			codeAddress();
		});
		var tmp = ko;
		tmp++;

		locationsApp = new LocationsViewModel( GoogleMapper );

		pager.extendWithPage( locationsApp );

		ko.applyBindings( locationsApp, document.getElementById( 'googleMapperApp' ) );

		pager.start();

		get_locations();
	});
})( jQuery, ko );