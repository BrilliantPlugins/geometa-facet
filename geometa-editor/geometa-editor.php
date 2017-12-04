<?php

function geometa_editor_scripts( $geometa_editor_url = '' ) {
	// vars
	$geometa_editor_url =  ( !empty( $geometa_editor_url ) ? $geometa_editor_url : plugin_dir_url( __FILE__ ) );
	$version = filemtime( dirname( __FILE__ ) );

	$src = '';
	if ( !empty( $_GET['DEBUG_SCRIPT'] ) ) {
		$src = '-src';
	}

	// register & include JS
	// wp_register_script( 'geometa-editor-leaflet-js', "{$geometa_editor_url}/js/leaflet{$src}.js", array(), $version );
	// wp_register_script( 'geometa-editor-leaflet-draw-js', "{$geometa_editor_url}/js/Leaflet.draw/leaflet.draw{$src}.js", array('geometa-editor-leaflet-js'), $version );
	// wp_register_script( 'geometa-editor-leaflet-locate-control-js', "{$geometa_editor_url}/js/L.Control.Locate.min.js", array('geometa-editor-leaflet-js'), $version );
	// wp_register_script( 'geometa-editor-leaflet-geocoder-js', "{$geometa_editor_url}/js/L.GeoSearch/js/l.control.geosearch.js", array('geometa-editor-leaflet-js'), $version );
	// wp_register_script( 'geometa-editor-leaflet-geocoder-osm-js', "{$geometa_editor_url}/js/L.GeoSearch/js/l.geosearch.provider.openstreetmap.js", array('geometa-editor-leaflet-geocoder-js'), $version );
	// wp_register_script( 'geometa-editor-leaflet-radius-js', "{$geometa_editor_url}/js/L.Control.Radius.js", array('geometa-editor-leaflet-js', 'geometa-editor-leaflet-geocoder-js'), $version );

	wp_register_script( 'geometa-editor', "{$geometa_editor_url}/js/geometa-editor.js", array('geometa-editor-leaflet-js', 'geometa-editor-leaflet-locate-control-js','geometa-editor-leaflet-draw-js','geometa-editor-leaflet-geocoder-osm-js','geometa-editor-leaflet-radius-js'), $version );

	if ( defined( 'GEOCODIO_API_KEY' ) ) {
		wp_localize_script( 'geometa-editor', 'geometa_editor', array(
			'geocodio_api_key' => GEOCODIO_API_KEY
		));
	}

	wp_enqueue_script('geometa-editor');

	// register & include CSS
	// wp_register_style( 'geometa-editor-leaflet-css', "{$geometa_editor_url}/css/leaflet.css", array(), $version );
	// wp_register_style( 'geometa-editor-leaflet-locate-control-css', "{$geometa_editor_url}/css/L.Control.Locate.min.css", array('geometa-editor-leaflet-css'), $version );
	// wp_register_style( 'geometa-editor-leaflet-draw-css', "{$geometa_editor_url}/js/Leaflet.draw/leaflet.draw{$src}.css", array('geometa-editor-leaflet-css'), $version );
	// wp_register_style( 'geometa-editor-leaflet-geocoder-css', "{$geometa_editor_url}/js/L.GeoSearch/css/l.geosearch.css", array('geometa-editor-leaflet-css'), $version );
	wp_register_style( 'geometa-editor', "{$geometa_editor_url}/css/geometa-editor.css", array('geometa-editor-leaflet-draw-css', 'geometa-editor-leaflet-geocoder-css'), $version );

	wp_enqueue_style('geometa-editor');
}

// function get_geometa_editor_map( $field_name, $field_value ) {
// 	$html = '<div class="geometa_editor geometa_editor_map_wrap">';
// 	// $html .= '<div class="geometa_editor_map">The map is loading...</div>';
// 	$html .= '<input type="hidden" data-name="geojson" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '">';
// 	$html .= '</div>';
// 	return $html;
// }
// 
// function geometa_editor_map( $field_name, $field_value ) {
// 	print get_geometa_editor_map( $field_name, $field_value );
// }

// function geometa_editor_latlng( $field_name, $field_value ) {
// 	$lat = '';
// 	$lng = '';
// 	if( !empty( $field_value ) ) {
// 		$json = json_decode( $field_value, true );
// 
// 		// Better safe than sorry?
// 		if (
// 			!empty( $json ) &&
// 			array_key_exists( 'type', $json ) &&
// 			$json['type'] === 'Feature' &&
// 			array_key_exists( 'geometry', $json ) &&
// 			is_array( $json['geometry'] ) &&
// 			array_key_exists( 'type', $json['geometry'] ) &&
// 			$json['geometry']['type'] === 'Point' &&
// 			array_key_exists('coordinates', $json['geometry']) &&
// 			is_array( $json['geometry']['coordinates'] )
// 		) {
// 			$lat = $json['geometry']['coordinates'][1];
// 			$lng = $json['geometry']['coordinates'][0];
// 		}
// 	}
// 
// 	echo '<div class="geometa_editor geometa_editor_ll_wrap">';
// 	echo '<label>' . esc_html__('Latitude','geometa-acf') . ' </label><br><input type="text" data-name="lat" value="' . $lat . '"><br>';
// 	echo '<label>' . esc_html__('Longitude','geometa-acf') . ' </label><br><input type="text" data-name="lng" value="' . $lng. '"><br>';
// 	echo '<input type="hidden" data-name="geojson" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '">';
// 	echo '</div>';
// }

function geometa_editor_geojson( $field_name, $field_value ) {
	echo '<div class="geometa_editor geometa_editor_geojson_wrap">';
	echo '<textarea placeholder="' . esc_attr__( 'Paste GeoJSON here', 'geometa-editor' ) . '" name="' . esc_attr($field_name) . '" >' . esc_attr($field_value) . '</textarea>';
	echo '</div>';
}

function geometa_editor_byogc( $field_name, $field_value ) {
	echo '<div class="geometa_editor geometa_editor_byogc_wrap">';

	$class = '';
	if ( WP_GeoUtil::is_geojson( $field_value ) ) {
		$class = ' has_geojson';
	}

	echo '<button class="geometa_editor_geocode_button' . $class . '">Geocode</button>';
	echo '<input type="hidden" data-name="geojson" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '">';
	echo '</div>';
}
