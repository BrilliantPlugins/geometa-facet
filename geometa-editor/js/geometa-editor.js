function geometa_editor_make_maps(i,wrapperdiv) {
	console.log('Make maps' + i + wrapperdiv);
}

function geometa_editor_make_ll_to_geojson(e){
	var lat = jQuery(this).find('input[data-name="lat"]').val();
	var lng = jQuery(this).find('input[data-name="lng"]').val();

	if ( (parseFloat( lat ) + '') !== lat || (parseFloat( lng ) + '') !== lng ) {
		// Something's not numeric!
		return;
	}

	if ( lng < -180 || lng > 180 || lat > 90 || lat < -90 ) {
		// Out of range!
		return;
	}

	var geojson = {
		'type' : 'Feature',
		'geometry' : { 
			'type' : 'Point',
			'coordinates' : [ parseFloat(lng), parseFloat(lat) ]
		},
		'properties' : {}
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

			if ( typeof success === 'object' && success.hasOwnProperty('geometry') && success.hasOwnProperty('type') && success.type === 'Feature' ) {
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
