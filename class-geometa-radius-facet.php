<?php

class GeoMeta_Radius {
	var $one_call;

	public function __construct() {
		$this->label = __( 'GeoMeta Radius', 'fwp' );
	}

	public function render( $params ) {

		$this->one_call = 'rendered';
		$output = '';

		$distance = '50';
		$location = '';

		$params['selected_values'] = array_filter( $params['selected_values'] );

		if ( !empty( $params['selected_values'] ) ) {
			$distance = array_pop( $params['selected_values'] );
			if ( empty( $distance ) ) {
				$distance = 50;
			}

			$location = implode( ',', $params['selected_values'] );
		}

		$output .= '<div class="map_radius_wrap"><label for="map_location_name">Location Name</label><input placeholder="City, State" class="facetwp-geometa_radius" type="text" name="map_location_name" value="' . $location . '"/><br>';
		$output .= '<label for="map_search_radius">Search Radius (miles)</label><input class="facetwp-geometa_radius" type="text" name="map_search_radius" value="' . $distance .'"/><br>';
		$output .= '</div>';

		$output .= '<script>';
		$output .= 'console.log( "Make geocoding work" );';
		$output .= '</script>';

		return $output;
	}

	public function geocodio( $location ) {

		$cache = get_transient( 'gmrf_' . substr($location,0,40) );

		if ( !empty( $cache ) ) {
			return $cache;
		}

		$url = 'https://api.geocod.io/v1/geocode?api_key=' . GEOCODIO_API_KEY . '&' . http_build_query( array('q' => $location ) );
	
		$json = @file_get_contents( $url );
		// if ( empty( $json ) ) {
		// 	error_log( "No Geocod.io results!" );
		// 	return false;
		// }
		$json = json_decode( $json, true );

		if ( empty( $json['results'] ) ) {
			return false;
		}

		$res = $json['results'][0];

		$this->formatted_address = $res['formatted_address'];

		$geojson = array(
			'type' => 'Feature',
			'geometry' => array(
				'type' => 'Point',
				'coordinates' => array($res['location']['lng'], $res['location']['lat'] )
			),
		);

		set_transient( 'gmrf_' . substr($location,0,40), $geojson, 3600*24*30 );

		return $geojson;
	}

	public function filter_posts( $params ) {
		global $wpdb;
		$this->one_call = 'filtered_posts';


		$params['selected_values'] = array_filter( $params['selected_values'] );

		if ( !empty( $params['selected_values'] ) ) {
			$distance = array_pop( $params['selected_values'] );
			$location = implode( ',', $params['selected_values'] );
		}

		if ( empty( $location ) ) {
			return 'continue';
		}

		$geojson = $this->geocodio( $location );

		if ( !$geojson ) {
			return array();
		}
		
		$buffered_thing = WP_GeoUtil::WP_Buffer_Point_Mi( $geojson, $distance, 8);
		$geom = WP_GeoUtil::metaval_to_geom( $buffered_thing );

		if ( isset( $params['facet']['source'] ) && 'acf/' == substr( $params['facet']['source'], 0, 4 ) ) {
				 // doctor_locations_repeater_%_doctor_location',
			$hierarchy_parts  = explode( '/', substr( $params['facet']['source'], 4 ) );
			$hierarchy = array();
			foreach( $hierarchy_parts as $field ) {
				$meta_key = get_field_object( $field );
				$hierarchy[] = $meta_key['name'];
			}

			$meta_key = implode('_%_', $hierarchy);
		} else {
			$meta_key = $params['facet']['source'];
		}

		$sql = "SELECT DISTINCT post_id FROM {$wpdb->postmeta}_geo WHERE meta_key LIKE '{$meta_key}' AND ST_Intersects( GeomFromText('{$geom}'), meta_value )";

		$post_ids = facetwp_sql( $sql, $params['facet'] );
		return $post_ids;
	}

	public function admin_scripts() {
?>
		<script>

		wp.hooks.addAction('facetwp/load/geometa_radius', function($this, obj) {
			$this.find('.facet-source').val(obj.source);
		});

		(function($) {
			wp.hooks.addFilter('facetwp/save/geometa_radius', function($this, obj) {
				obj['source'] = $this.find('.facet-source').val();
				return obj;
			});
		})(jQuery);
		</script>
<?php
	}

	public function front_scripts() {
?>
		<script>
		(function($) {
			wp.hooks.addAction('facetwp/refresh/geometa_radius', function($this, facet_name) {
				// if ( $this.find('input[name="map_location_name"]').val() !== '' && $this.find('input[name="map_search_radius"]').val() !== '' ) {
					var search_params = [
						$this.find('input[name="map_location_name"]').val() || '',
						$this.find('input[name="map_search_radius"]').val() || ''
					];
					FWP.facets[facet_name] = search_params;
				// }
			});

			wp.hooks.addAction('facetwp/ready', function() {
				$(document).on('change', '.facetwp-facet .facetwp-geometa_radius', function() {
					FWP.refresh();
				});
			});
		})(jQuery);
		</script>
<?php
	}

	public function settings_html() {
		print '<tr><td>'; 
		print '<p>You will need to acquire a key for <a href="https://geocod.io/" target="_blank">Geocod.io</a>, then define it with <pre>define( \'GEOCODIO_API_KEY\', \'your_key_here\' );</pre></p>';
		print '<p>If you do not do this, the geocoding input will not show up, even with this checkbox checked</p>';
		print '</td></tr>';
	}

	public function load_values( $params ) {

		$a = 1;

		return $params;
	}
}
