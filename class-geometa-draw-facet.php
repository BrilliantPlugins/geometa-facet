<?php

class GeoMeta_Draw {
	public function __construct() {
		$this->label = __( 'GeoMeta Draw', 'fwp' );
	}

	/**
	 * Generate the output HTML for this facet type.
	 */
	public function render( $params ) {
		$output = '';

		// $output = get_geometa_editor_map('geojson_facet','');

		if ( $params['facet']['map_show_geocoder'] ) {
			$output .= '<div><label for="map_location_name">Search Location</label><input type="text" name="map_location_name"/><br>';
			$output .= '<label for="map_search_radius"><input type="text" name="map_search_radius" value="50"/><br>';
			$output .= '</div>';
		}

		$map = new LeafletPHP(array(
			'zoom' => $params['facet']['map_zoom'],
			'center' => json_decode($params['facet']['map_center']),
			), 'geometa_map_' . $params['facet']['name'], 'geometa_draw_map');

		$map->add_layer('L.geoJSON',array(),'drawnItems');

		$map->add_control('L.Control.Draw', array(
			'edit' => array(
				'featureGroup' => '@@@drawnItems@@@'
			),
			'draw' => array(
				'circle' => false,
				'marker' => false,
				'gpsline' => false,
				),
			));

		$output .= $map;

		return $output;
	}

	/**
	 * This method returns an array of post IDs that match the selected values for this facet.
	 */
	public function filter_posts( $params ) {
		global $wpdb;

		// Geocode and filter
		//
		return $post_ids;
	}

	/**
	 * Output any scripts or CSS for the admin settings page. This method contains Javascript logic for handling the loading/saving of facet settings.
	 */
	public function admin_scripts() {
?>
		<script>

		geometa_draw_count = 1;
		geometa_draw_objects = {};

		(function($) {
			wp.hooks.addAction( 'leafletphp/preinit', function( mapwrap ){
				console.log("leafletphp/preinit");

				// Alter the div ID, so that we don't get double IDs in the dom.
				if ( mapwrap.scriptid === 'geometa_draw_map' ) {
					jQuery('#geometa_draw_map').attr('id','geometa_draw_' + geometa_draw_count);
					mapwrap.scriptid = 'geometa_draw_' + geometa_draw_count;
					geometa_draw_objects[mapwrap.scriptid] = mapwrap;
					geometa_draw_count++;

					// Store the map ID so that we can access the map later.
					jQuery('#' + mapwrap.scriptid).closest('.facetwp-row').data('mapid',mapwrap.scriptid);
				}
			});

			wp.hooks.addAction('facetwp/load/geometa_draw', function($this, obj) {
				// Store the object so we can re-initialize the map later.
				$this.closest('.facetwp-row').data('facetobj',obj);
			});

			wp.hooks.addAction('facetwp/change/geometa_draw', function($this){
				// Get the row object so we can re-access the facetobj and mapid.
				var facetrow = $this.closest('.facetwp-row');
				var leafletphp = geometa_draw_objects[facetrow.data('mapid')];
				var facetobj = facetrow.data('facetobj');
				leafletphp.map._onResize();

				if ( facetobj !== undefined && facetobj.map_zoom !== undefined && facetobj.map_center !== undefined ) {
					leafletphp.map.setView( JSON.parse( facetobj.map_center ), facetobj.map_zoom );
				}
			});

			wp.hooks.addFilter('facetwp/save/geometa_draw', function($this, obj) {
				var facetrow = $this.closest('.facetwp-row');
				var leafletphp = geometa_draw_objects[facetrow.data('mapid')];

				obj['map_zoom'] = leafletphp.map.getZoom();
				obj['map_center'] = JSON.stringify(leafletphp.map.getCenter());
				obj['map_show_geocoder'] = $this.find('input[name="map_show_geocoder"]').prop('checked');
				return obj;
			});
		})(jQuery);
		</script>
<?php
	}

	/**
	 * Output any scripts or CSS for public-facing facet pages. This method contains Javascript logic for handling facet interaction.
	 */
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

	/**
	 * (optional) Output admin settings HTML.
	 */
	public function settings_html() {
		print '<tr><td>'; 
		print "<label>Initial Map View</label>";
		print "<p>The map shown in the facet will use the same zoom level and center as the map shown below, but will be scaled to fit the facet area.</p>";
		print "
		<!-- START THE LEAFLETPHP MAP -->
		";
		$map = new LeafletPHP(array(), 'geometa_draw_map', 'geometa_draw_map');

		// Can't Geolocate if it's not SSL.
		if ( is_ssl() ) {
			$map->add_control('L.Control.Locate', array(
					'icon' => 'pointer_marker',
					'iconLoading' => 'pointer_marker_loading'
			), 'location');
		}

		print $map;
		print "
		<!-- END THE LEAFLETPHP MAP -->
		";
		print '<br>';

		print '<label>Show Geocoder <input type="checkbox" name="map_show_geocoder" value="show_geocoder"></label>';
		print '<p>You will need to acquire a key for <a href="https://geocod.io/" target="_blank">Geocod.io</a>, then define it with <pre>define( \'GEOCODIO_API_KEY\', \'your_key_here\' );</pre></p>';
		print '<p>If you do not do this, the geocoding input will not show up, even with this checkbox checked</p>';
		print '</td></tr>';
	}
}
