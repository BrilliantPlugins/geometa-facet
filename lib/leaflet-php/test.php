<?php

require_once( __DIR__ . '/../../../../../wp-load.php' );
require_once( __DIR__ . '/leaflet-php-loader.php' );

$map = new LeafletPHP();

print '<!DOCTYPE HTML>
	<html>
	    <head>
			<meta charset="UTF-8">
			<title>ASDF</title>
			<script src="http://test2.local/wp-admin/load-scripts.php?c=1&load%5B%5D=jquery-core,jquery-migrate,utils&ver=4.9"></script>
		</head>
	    <body>';

print $map;

print '</body></html>';
