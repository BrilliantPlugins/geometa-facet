<?php
/**
 * GeoMeta Facet provides a spatial serach field for FacetWP search
 *
 * Plugin Name: GeoMeta Facet for FacetWP
 * Description: Run spatial searches with FacetWP
 * Author: Michael Moore
 * Author URI: http://cimbura.com
 * Version: 0.0.1
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: geometa-facet
 * Domain Path: /lang
 *
 * @package geometa-facet
 **/

require_once( dirname( __FILE__ ) . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php' );
require_once( dirname( __FILE__ ) . '/lib/leaflet-php/leaflet-php-loader.php' );
require_once( dirname( __FILE__ ) . '/class-geometa-draw-facet.php' );
require_once( dirname( __FILE__ ) . '/class-geometa-radius-facet.php' );
require_once( dirname( __FILE__ ) . '/geometa-editor/geometa-editor.php');


add_filter( 'facetwp_facet_types', function( $facet_types ) {
	$facet_types['geometa_draw'] = new GeoMeta_Draw();
	$facet_types['geometa_radius'] = new GeoMeta_Radius();
	return $facet_types;
});

add_action( 'admin_enqueue_scripts', function() {
	geometa_editor_scripts();
});

add_action( 'wp_enqueue_scripts', function() {
	geometa_editor_scripts();
});
