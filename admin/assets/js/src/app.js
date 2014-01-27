/* globals ko:{}, google:{}, alert:{} */
( function( $, ko ) {
	'use strict';
	var geocoder,
		map;
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

	google.maps.event.addDomListener(window, 'load', initialize);

	$(function () {
		$( '.mapper-find' ).on( 'click.google-mapper', function( e ) {
			e.preventDefault();
			codeAddress();
		});
		var tmp = ko;
		tmp++;
	});
})( jQuery, ko );