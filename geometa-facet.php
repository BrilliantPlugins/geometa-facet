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

require_once( dirname( __FILE__ ) . '/class-geometa-facet.php' );
require_once( dirname( __FILE__ ) . '/geometa-editor/geometa-editor.php');


add_filter( 'facetwp_facet_types', function( $facet_types ) {
	$facet_types['geometa'] = new GeoMeta_Facet();
	return $facet_types;
});

add_action( 'admin_enqueue_scripts', function() {
	geometa_editor_scripts();
});

add_action( 'wp_enqueue_scripts', function() {
	geometa_editor_scripts();
});
