function geometa_editor_initialize_fields() {
	jQuery('.geometa_editor_map_wrap').not('.geometa-editor-setup').each( geometa_editor_make_maps ).addClass('geometa-editor-setup');
	jQuery('.geometa_editor_ll_wrap').on( 'keyup change', geometa_editor_make_ll_to_geojson );
}

function geometa_editor_make_maps(i,wrapperdiv) {
	var div = jQuery(wrapperdiv).find('.geometa_editor_map');
	if ( div.data( 'map_loaded' ) === true ) {
		return;
	}
	div = div[0];
	div.innerHTML = '';
	var map = L.map(div).setView([0,0],1);
	div._geometa_map = map;

	// Basemap
	L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}).addTo(map);

	// Location control
	L.control.locate({ 
		icon: 'pointer_marker',
		iconLoading: 'pointer_marker_loading'
	}).addTo(map);

	// Draw control layer
	var curgeojson = '';
	var drawnItems = new L.GeoJSON();
	try {
		curgeojson = JSON.parse(jQuery(this).find('input[data-name="geojson"]').val());
		drawnItems.addData(curgeojson);
	} catch(e) {}
	map.addLayer( drawnItems );

	// Draw control
	var drawControl = new L.Control.Draw({
		draw: {
			circle: false
		},
		edit: {
			featureGroup: drawnItems
		}
	});

	map.addControl( drawControl );

	// Make a function that will have access to drawnItems.
	var savevalfunc = (function(thegeojson){
		return function(){
			thegeojson.val( JSON.stringify( drawnItems.toGeoJSON() ) );
		};
	})(jQuery(this).find('input[data-name="geojson"]'));

	map.on(L.Draw.Event.CREATED, function (e) {
		console.log("Created layer");
		drawnItems.addLayer(e.layer);
		savevalfunc(e);
	});

	map.on( L.Draw.Event.EDITED, function(e){
		console.log("Edited layer!");
		savevalfunc( e );
	});

	map.on( L.Draw.Event.EDITSTOP, function(e){
		console.log("Edit stop layer!");
		savevalfunc( e );
	});

	map.on( L.Draw.Event.DELETESTOP, function(e){
		console.log("Deleted layer!");
		savevalfunc( e );
	});

	// If we have existing geojson, fit bounds
	if ( drawnItems.getLayers().length > 0 ) {
		map.fitBounds(drawnItems.getBounds());
	}

	jQuery( div ).data( 'map_loaded', true );
}

function geometa_editor_make_ll_to_geojson(e){
		var lat = jQuery(this).find('input[data-name="lat"]').val();
		var lng = jQuery(this).find('input[data-name="lng"]').val();

		if ( (parseFloat( lat ) + "") !== lat || (parseFloat( lng ) + "") !== lng ) {
			// Something's not numeric!
			return;
		}

		if ( lng < -180 || lng > 180 || lat > 90 || lat < -90 ) {
			// Out of range!
			return;
		}

		var geojson = {
			"type" : "Feature",
			"geometry" : { 
				"type" : "Point",
				"coordinates" : [ parseFloat(lng), parseFloat(lat) ]
			},
			"properties" : {}
		};

		jQuery(this).find('input[data-name="geojson"]').val(JSON.stringify(geojson));
	}

jQuery(document).ready(function(){
	geometa_editor_set_up_geocode_buttons_handler();
});

function geometa_editor_set_up_geocode_buttons_handler() {
	jQuery('.geometa_editor_geocode_button').not('.geometa-editor-setup').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();

		e.target.disabled = true;

		var origE = e;
		var callback = function(success){

			success = success || '';

			if ( typeof success === 'object' && success.hasOwnProperty('geometry') && success.hasOwnProperty('type') && success.type == 'Feature' ) {
				// We've got GeoJSON, call it a success	
				jQuery(origE.target).addClass('has_geojson');
				success = JSON.stringify( success );
			} else {
				// No GeoJSON, call it failure
				jQuery(origE.target).removeClass('has_geojson');
				success = '';
			}

			jQuery(origE.target.parentElement).find('input[data-name="geojson"]').val( success );

			origE.target.disabled = false;
		};

		jQuery(document).trigger('leaflet-editor/byo-geocode', [
			e, callback
		]);

		return false;
	}).addClass('geometa-editor-setup');
}
