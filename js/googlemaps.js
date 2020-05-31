jQuery( document ).ready( function( $ ) {
	$( '#map' ).css( 'height', '60vh' );

	/* global DATA */
	/* global google */

	let styles;
	if ( DATA.mapjson && DATA.mapjson.length > 2 ) {
		styles = JSON.parse( DATA.mapjson );
	} else {
		// edit style settings at https://mapstyle.withgoogle.com/
		styles = [
			{
				elementType: 'labels.icon',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'administrative.land_parcel',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'administrative.neighborhood',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'poi',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'poi.business',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'poi.park',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'road',
				elementType: 'labels',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
			{
				featureType: 'road.arterial',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'simplified',
					},
				],
			},
			{
				featureType: 'road.highway',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'simplified',
					},
				],
			},
			{
				featureType: 'road.local',
				elementType: 'geometry.stroke',
				stylers: [
					{
						visibility: 'on',
					},
				],
			},
			{
				featureType: 'road.local',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'simplified',
					},
				],
			},
			{
				featureType: 'water',
				elementType: 'labels.text',
				stylers: [
					{
						visibility: 'off',
					},
				],
			},
		];
	}

	const myLatLng = new google.maps.LatLng( DATA.location.lat, DATA.location.lng );

	const options = {
		mapTypeControlOptions: {
			mapTypeIds: [ 'Styled' ],
		},
		center: myLatLng,
		zoom: parseInt( DATA.zoomlevel ),
		mapTypeId: 'Styled',
		scrollwheel: false,
		draggable: ! ( 'ontouchend' in document ),
	};

	const div = document.getElementById( 'map' );
	const map = new google.maps.Map( div, options );
	const styledMapType = new google.maps.StyledMapType( styles );
	map.mapTypes.set( 'Styled', styledMapType );

	const marker = new google.maps.Marker( {
		position: myLatLng,
		map,
		title: DATA.location.address,
	} );

	const infowindow = new google.maps.InfoWindow( {
		content: DATA.infowindow,
		maxWidth: 300,
	} );
	marker.addListener( 'click', function() {
		infowindow.open( map, marker );
	} );
	infowindow.open( map, marker );
} );
