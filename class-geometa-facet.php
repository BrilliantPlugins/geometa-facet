<?php

class GeoMeta_Facet {
	public function __construct() {
		$this->label = __( 'GeoMeta', 'fwp' );
	}

	public function render( $params ) {
		$output = '';

		$output = get_geometa_editor_map('geojson_facet','');

		if ( $params['facet']['map_show_geocoder'] ) {
			$output .= '<div><label for="map_location_name">Search Location</label><input type="text" name="map_location_name"/><br>';
			$output .= '<label for="map_search_radius"><input type="text" name="map_search_radius" value="50"/><br>';
			$output .= '</div>';
		}

		$output .= '<script>';
		$output .= 'geometa_editor_initialize_fields();';
		$output .= 'jQuery(".facetwp-facet").find(".geometa_editor_map")[0]._geometa_map.setView(' . $params['facet']['map_center'] . ',' . $params['facet']['map_zoom'] . ');';
		$output .= '</script>';

		return $output;
	}

	public function filter_posts( $params ) {
		global $wpdb;

		// Geocode and filter
		//
		return $post_ids;
	}

	public function admin_scripts() {
?>
		<script>
		(function($) {
			wp.hooks.addAction('facetwp/load/geometa', function($this, obj) {
				$this.find('input[name="geojson_facet"]').val(obj.map_center);
				$this.find('input[name="geojson_facet"]')[0]._vals = obj;
				$this.find('input[name="map_show_geocoder"]').prop( 'checked', obj['map_show_geocoder'] );
			});

			wp.hooks.addAction('facetwp/change/geometa', function($this){
				geometa_editor_initialize_fields();
				var mapdiv = $this.closest('.facetwp-row').find('.geometa_editor_map');
				if ( mapdiv.length > 0 ) {
					var obj = $this.closest('.facetwp-row').find('input[name="geojson_facet"]')[0]._vals;
					var themap = mapdiv[0]._geometa_map;
					themap.setView(JSON.parse(obj.map_center), obj.map_zoom);
				}
			});

			wp.hooks.addFilter('facetwp/save/geometa', function($this, obj) {
				var mapdiv = $this.find('.geometa_editor_map');
				var leaflet = $this.find('.geometa_editor_map')[0]._geometa_map;
				obj['map_zoom'] = leaflet.getZoom();
				obj['map_center'] = JSON.stringify(leaflet.getCenter());
				obj['map_show_geocoder'] = $this.find('input[name="map_show_geocoder"]').prop('checked');
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
			wp.hooks.addAction('facetwp/refresh/geometa', function($this, facet_name) {
				FWP.facets[facet_name] = $this.find('.facetwp-geometa').val() || '';
			});

			wp.hooks.addAction('facetwp/ready', function() {
				$(document).on('change', '.facetwp-facet .facetwp-geometa', function() {
					FWP.refresh();
				});
			});
		})(jQuery);
		</script>
<?php
	}

	public function settings_html() {
		print '<tr><td>'; 
		print "<label>Initial Map View</label>";
		print "<p>The map shown in the facet will use the same zoom level and center as the map shown below, but will be scaled to fit the facet area.</p>";
		geometa_editor_map('geojson_facet','');
		print '<br>';

		print '<label>Show Geocoder <input type="checkbox" name="map_show_geocoder" value="show_geocoder"></label>';
		print '<p>You will need to acquire a key for <a href="https://geocod.io/" target="_blank">Geocod.io</a>, then define it with <pre>define( \'GEOCODIO_API_KEY\', \'your_key_here\' );</pre></p>';
		print '<p>If you do not do this, the geocoding input will not show up, even with this checkbox checked</p>';
		print '</td></tr>';
	}
}
